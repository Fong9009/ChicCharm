<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\AdminsTable;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Cake\Utility\Security;
use Authentication\Middleware\AuthenticationMiddleware;

/**
 * Auth Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AuthController extends AppController
{
    /**
     * @var \App\Model\Table\AdminsTable $Admins
     */
    private AdminsTable $Admins;

    /**
     * Controller initialize override
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        // By default, CakePHP will (sensibly) default to preventing users from accessing any actions on a controller.
        // These actions, however, are typically required for users who have not yet logged in.
        $this->Authentication->allowUnauthenticated(['login', 'forgetPassword', 'resetPassword']);

        // CakePHP loads the model with the same name as the controller by default.
        // Since we don't have an Auth model, we'll need to load "Admins" model when starting the controller manually.
        $this->Admins = $this->fetchTable('Admins');
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        $admin = $this->Admins->newEmptyEntity();
        if ($this->request->is('post')) {
            $admin = $this->Admins->patchEntity($admin, $this->request->getData());
            if ($this->Admins->save($admin)) {
                $this->Flash->success('You have been registered. Please log in. ');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The admin could not be registered. Please, try again.');
        }
        $this->set(compact('admin'));
    }

    /**
     * Forget Password method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful email send, renders view otherwise.
     */
    public function forgetPassword()
    {
        if ($this->request->is('post')) {
            // Retrieve the admin entity by provided email address
            $admin = $this->Admins->findByEmail($this->request->getData('email'))->first();
            if ($admin) {
                // Set nonce and expiry date
                $admin->nonce = Security::randomString(128);
                $admin->nonce_expiry = new DateTime('7 days');
                if ($this->Admins->save($admin)) {
                    // Now let's send the password reset email
                    $mailer = new Mailer('default');

                    // email basic config
                    $mailer
                        ->setEmailFormat('both')
                        ->setTo($admin->email)
                        ->setSubject('Reset your account password');

                    // select email template
                    $mailer
                        ->viewBuilder()
                        ->setTemplate('reset_password');

                    // transfer required view variables to email template
                    $mailer
                        ->setViewVars([
                            'first_name' => $admin->first_name,
                            'last_name' => $admin->last_name,
                            'nonce' => $admin->nonce,
                            'email' => $admin->email,
                        ]);

                    //Send email
                    if (!$mailer->deliver()) {
                        // Just in case something goes wrong when sending emails
                        $this->Flash->error('We have encountered an issue when sending you emails. Please try again. ');

                        return $this->render(); // Skip the rest of the controller and render the view
                    }
                } else {
                    // Just in case something goes wrong when saving nonce and expiry
                    $this->Flash->error('We are having issue to reset your password. Please try again. ');

                    return $this->render(); // Skip the rest of the controller and render the view
                }
            }

            /*
             * **This is a bit of a special design**
             * We don't tell the user if their account exists, or if the email has been sent,
             * because it may be used by someone with malicious intent. We only need to tell
             * the user that they'll get an email.
             */
            $this->Flash->success('Please check your inbox (or spam folder) for an email regarding how to reset your account password. ');

            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset Password method
     *
     * @param string|null $nonce Reset password nonce
     * @return \Cake\Http\Response|null|void Redirects on successful password reset, renders view otherwise.
     */
    public function resetPassword(?string $nonce = null)
    {
        $admin = $this->Admins->findByNonce($nonce)->first();

        // If nonce cannot find the admin, or nonce is expired, prompt for re-reset password
        if (!$admin || $admin->nonce_expiry < DateTime::now()) {
            $this->Flash->error('Your link is invalid or expired. Please try again.');

            return $this->redirect(['action' => 'forgetPassword']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Used a different validation set in Model/Table file to ensure both fields are filled
            $admin = $this->Admins->patchEntity($admin, $this->request->getData(), ['validate' => 'resetPassword']);

            // Also clear the nonce-related fields on successful password resets.
            // This ensures that the reset link can't be used a second time.
            $admin->nonce = null;
            $admin->nonce_expiry = null;

            if ($this->Admins->save($admin)) {
                $this->Flash->success('Your password has been successfully reset. Please login with new password. ');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The password cannot be reset. Please try again.');
        }

        $this->set(compact('admin'));
    }

    /**
     * Change Password method
     *
     * @param string|null $id Admin id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePassword(?string $id = null)
    {
        $admin = $this->Admins->get($id, ['contain' => []]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Used a different validation set in Model/Table file to ensure both fields are filled
            $admin = $this->Admins->patchEntity($admin, $this->request->getData(), ['validate' => 'resetPassword']);
            if ($this->Admins->save($admin)) {
                $this->Flash->success('The admin has been saved.');

                return $this->redirect(['controller' => 'Admins', 'action' => 'index']);
            }
            $this->Flash->error('The admin could not be saved. Please, try again.');
        }
        $this->set(compact('admin'));
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void Redirect to location before authentication
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $user = $result->getData();
            
            // Check user type and redirect accordingly
            if ($user->type === 'admin') {
                $fallbackLocation = ['controller' => 'Admins', 'action' => 'index'];
            } else {
                $fallbackLocation = ['controller' => 'Customers', 'action' => 'dashboard'];
            }

            return $this->redirect($this->Authentication->getLoginRedirect() ?? $fallbackLocation);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Email address and/or Password is incorrect. Please try again.');
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Authentication->logout();

            $this->Flash->success('You have been logged out successfully. ');
        }

        return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
    }
}
