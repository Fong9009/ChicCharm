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
     * @var \App\Model\Table\CustomersTable $Customers
     */
    private $Customers;

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

        // Load both Admins and Customers tables
        $this->Admins = $this->fetchTable('Admins');
        $this->Customers = $this->fetchTable('Customers');
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
            $email = $this->request->getData('email');

            // First check for admin account
            $admin = $this->Admins->findByEmail($email)->first();

            // If admin exists, process admin password reset
            if ($admin) {
                // Set nonce and expiry date
                $admin->nonce = Security::randomString(128);
                $admin->nonce_expiry = new DateTime('7 days');
                if ($this->Admins->save($admin)) {
                    // Send password reset email
                    try {
                        $mailer = new Mailer('default');

                        // Debug information
                        $this->log("Email settings - From address: " . env('EMAIL_FROM_ADDRESS', 'not-set'), 'debug');
                        $this->log("Email settings - Username: " . env('EMAIL_TRANSPORT_DEFAULT_USERNAME', 'not-set'), 'debug');
                        $this->log("Email settings - Port: " . env('EMAIL_TRANSPORT_DEFAULT_PORT', '465'), 'debug');
                        $this->log("Email settings - SSL: enabled", 'debug');

                        // Set from address for delivery
                        $mailer->setFrom(env('EMAIL_FROM_ADDRESS', 'chayfong9009@gmail.com'), env('EMAIL_FROM_NAME', 'ChicCharm'));

                        $mailer
                            ->setEmailFormat('both')
                            ->setTo($admin->email)
                            ->setSubject('Reset your account password');

                        $mailer
                            ->viewBuilder()
                            ->setTemplate('reset_password');

                        $mailer
                            ->setViewVars([
                                'first_name' => $admin->first_name,
                                'last_name' => $admin->last_name,
                                'nonce' => $admin->nonce,
                                'email' => $admin->email,
                                'userType' => 'admin'
                            ]);

                        // Debug the reset link that would be in the email
                        $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/auth/reset-password/' . $admin->nonce . '/admin';
                        $this->log("Reset link: " . $resetLink, 'debug');

                        $result = $mailer->deliver();
                        if (!$result) {
                            // Log the error but don't tell the user for security reasons
                            $this->log("Failed to send password reset email to admin: {$admin->email}", 'error');
                        } else {
                            $this->log("Successfully sent password reset email to admin: {$admin->email}", 'info');
                        }
                    } catch (\Exception $e) {
                        // Log the detailed exception message for debugging
                        $this->log("Exception sending email: " . $e->getMessage(), 'error');
                    }
                } else {
                    // Log the error but don't tell the user for security reasons
                    $this->log("Failed to save nonce for admin: {$admin->email}", 'error');
                }
            } else {
                // Check for customer account
                $customer = $this->Customers->findByEmail($email)->first();

                if ($customer) {
                    // Set nonce and expiry date
                    $customer->nonce = Security::randomString(128);
                    $customer->nonce_expiry = new DateTime('7 days');
                    if ($this->Customers->save($customer)) {
                        // Send password reset email
                        try {
                            $mailer = new Mailer('default');

                            // Debug information
                            $this->log("Email settings - From address: " . env('EMAIL_FROM_ADDRESS', 'not-set'), 'debug');
                            $this->log("Email settings - Username: " . env('EMAIL_TRANSPORT_DEFAULT_USERNAME', 'not-set'), 'debug');
                            $this->log("Email settings - Port: " . env('EMAIL_TRANSPORT_DEFAULT_PORT', '465'), 'debug');
                            $this->log("Email settings - SSL: enabled", 'debug');

                            // Set from address for delivery
                            $mailer->setFrom(env('EMAIL_FROM_ADDRESS', 'chayfong9009@gmail.com'), env('EMAIL_FROM_NAME', 'ChicCharm'));

                            $mailer
                                ->setEmailFormat('both')
                                ->setTo($customer->email)
                                ->setSubject('Reset your account password');

                            $mailer
                                ->viewBuilder()
                                ->setTemplate('reset_password');

                            $mailer
                                ->setViewVars([
                                    'first_name' => $customer->first_name,
                                    'last_name' => $customer->last_name,
                                    'nonce' => $customer->nonce,
                                    'email' => $customer->email,
                                    'userType' => 'customer'
                                ]);

                            $result = $mailer->deliver();
                            if (!$result) {
                                // Log the error but don't tell the user for security reasons
                                $this->log("Failed to send password reset email to customer: {$customer->email}", 'error');
                            } else {
                                $this->log("Successfully sent password reset email to customer: {$customer->email}", 'info');
                            }
                        } catch (\Exception $e) {
                            // Log the detailed exception message for debugging
                            $this->log("Exception sending email: " . $e->getMessage(), 'error');
                        }
                    } else {
                        // Log the error but don't tell the user for security reasons
                        $this->log("Failed to save nonce for customer: {$customer->email}", 'error');
                    }
                } else {
                    // No user found with this email, log it but don't tell the user
                    $this->log("Password reset requested for non-existent email: {$email}", 'notice');
                }
            }

            /*
             * Always show success message regardless of result for security reasons
             * to prevent email enumeration attacks
             */
            $this->Flash->success('Please check your inbox (or spam folder) for an email regarding how to reset your account password.');
            
            // Add null checks before logging environment variables
            $email_username = env('EMAIL_TRANSPORT_DEFAULT_USERNAME');
            $email_password = env('EMAIL_TRANSPORT_DEFAULT_PASSWORD');
            
            if ($email_username !== null) {
                $this->log($email_username, 'debug');
            }
            
            if ($email_password !== null) {
                $this->log($email_password, 'debug');
            }

            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset Password method
     *
     * @param string|null $nonce Reset password nonce
     * @param string|null $type User type (admin or customer)
     * @return \Cake\Http\Response|null|void Redirects on successful password reset, renders view otherwise.
     */
    public function resetPassword(?string $nonce = null, ?string $type = null)
    {
        // Determine which model to use based on user type
        if ($type === 'customer') {
            $user = $this->Customers->findByNonce($nonce)->first();
            $model = $this->Customers;
        } else {
            // Default to admin if type not specified or is 'admin'
            $user = $this->Admins->findByNonce($nonce)->first();
            $model = $this->Admins;
        }

        // If nonce cannot find the user, or nonce is expired, prompt for re-reset password
        if (!$user || $user->nonce_expiry < DateTime::now()) {
            $this->Flash->error('Your link is invalid or expired. Please try again.');
            return $this->redirect(['action' => 'forgetPassword']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Used a different validation set in Model/Table file to ensure both fields are filled
            $user = $model->patchEntity($user, $this->request->getData(), ['validate' => 'resetPassword']);

            // Also clear the nonce-related fields on successful password resets.
            // This ensures that the reset link can't be used a second time.
            $user->nonce = null;
            $user->nonce_expiry = null;

            if ($model->save($user)) {
                $this->Flash->success('Your password has been successfully reset. Please login with your new password.');
                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The password cannot be reset. Please try again.');
        }

        $this->set(compact('user', 'type'));
    }

    /**
     * Change Password method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePassword(?string $id = null)
    {
        $user = $this->Authentication->getIdentity();

        // If no ID provided, use the current user's ID
        if (!$id) {
            $id = $user->id;
        }

        // Only allow users to change their own password
        // For admins, they can only change customer passwords, not other admin passwords
        if ($user->id != $id) {
            if ($user->type !== 'admin') {
                $this->Flash->error('Access denied. You can only change your own password.');
                return $this->redirect(['action' => 'login']);
            }
            
            // Check if target user is an admin
            $targetAdmin = $this->Admins->find()->where(['id' => $id])->first();
            if ($targetAdmin) {
                $this->Flash->error('Access denied. Admins cannot change other admin passwords.');
                return $this->redirect(['controller' => 'Admins', 'action' => 'index']);
            }
        }

        // Get the appropriate model and entity based on who is being edited
        if ($user->type === 'admin') {
            $model = $this->Admins;
        } else {
            $model = $this->Customers;
        }

        try {
            $entity = $model->get($id, [
                'fields' => ['id', 'first_name', 'last_name', 'password']
            ]);
            
            // Create a clean entity for the form that only includes name fields
            $formEntity = $model->newEmptyEntity();
            $formEntity->id = $entity->id;
            $formEntity->first_name = $entity->first_name;
            $formEntity->last_name = $entity->last_name;
            
        } catch (\Exception $e) {
            $this->Flash->error('User not found.');
            return $this->redirect(['controller' => 'Admins', 'action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verify current password against the original entity
            if (!password_verify($data['current_password'], $entity->password)) {
                $this->Flash->error('Current password is incorrect.');
                return $this->render();
            }

            // Update password on the original entity
            $entity = $model->patchEntity($entity, [
                'password' => $data['password'],
                'confirm_password' => $data['confirm_password']
            ], ['validate' => 'resetPassword']);

            if ($model->save($entity)) {
                $this->Flash->success('Password has been updated successfully.');

                // Redirect based on user type
                if ($user->type === 'admin') {
                    return $this->redirect(['controller' => 'Admins', 'action' => 'index']);
                } else {
                    return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
                }
            }
            $this->Flash->error('The password could not be updated. Please, try again.');
        }

        // Pass the clean entity to the view
        $this->set('entity', $formEntity);
        $this->set('userType', $user->type);
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
                $fallbackLocation = ['controller' => 'Admins', 'action' => 'dashboard'];
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

            $this->getRequest()->getSession()->destroy();

            $this->Flash->success('You have been logged out successfully. ');
        }

        return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
    }

}
