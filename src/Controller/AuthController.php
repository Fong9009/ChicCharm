<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\AdminsTable;
use App\Model\Table\StylistsTable;
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
     * @var \App\Model\Table\StylistsTable $Stylists
     */
    private StylistsTable $Stylists;

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
        $this->Stylists = $this->fetchTable('Stylists');
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
     * @param string|null $token Reset password token
     * @return \Cake\Http\Response|null|void Redirects on successful password reset, renders view otherwise.
     */
    public function resetPassword($token = null)
    {
        $this->viewBuilder()->setLayout('login');

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $user = $this->Users->findByNonce($token)->first();

            if ($user) {
                // Check if nonce is expired
                if ($user->nonce_expiry < new \DateTime()) {
                    $this->Flash->error(__('Password reset link has expired. Please request a new one.'));
                    return $this->redirect(['action' => 'forgotPassword']);
                }

                // Different validation rules based on user type
                if ($user->type === 'admin') {
                    $validator = new \Cake\Validation\Validator();
                    $validator
                        ->requirePresence('password')
                        ->notEmptyString('password', 'Please enter a password')
                        ->minLength('password', 8, 'Password must be at least 8 characters long')
                        ->add('password', 'custom', [
                            'rule' => function ($value) {
                                return preg_match('/[A-Z]/', $value) && // Uppercase
                                       preg_match('/[a-z]/', $value) && // Lowercase
                                       preg_match('/[0-9]/', $value) && // Number
                                       preg_match('/[^A-Za-z0-9]/', $value); // Special character
                            },
                            'message' => 'Password must include uppercase, lowercase, number, and special character'
                        ]);
                } else {
                    // Customer validation - only 8 characters minimum
                    $validator = new \Cake\Validation\Validator();
                    $validator
                        ->requirePresence('password')
                        ->notEmptyString('password', 'Please enter a password')
                        ->minLength('password', 8, 'Password must be at least 8 characters long');
                }

                $errors = $validator->validate($data);
                if (!empty($errors)) {
                    foreach ($errors as $field => $error) {
                        $this->Flash->error($error['_empty'] ?? $error['minLength'] ?? $error['custom'] ?? 'Invalid password');
                    }
                    return;
                }

                $user = $this->Users->patchEntity($user, [
                    'password' => $data['password'],
                    'nonce' => null,
                    'nonce_expiry' => null
                ]);

                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your password has been updated.'));
                    return $this->redirect(['action' => 'login']);
                }
                $this->Flash->error(__('Unable to update your password.'));
            } else {
                $this->Flash->error(__('Invalid password reset link.'));
            }
        }

        $this->set(compact('token'));
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
        } elseif ($user->type === 'customer') {
            $model = $this->Customers;
        } elseif ($user->type === 'stylist') {
            $model = $this->Stylists;
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
                $this->set('entity', $formEntity);
                $this->set('userType', $user->type);
                return $this->render();
            }

            // Create validator with specific rules based on user type
            $validator = new \Cake\Validation\Validator();

            if ($user->type === 'admin') {
                $validator
                    ->requirePresence('password')
                    ->notEmptyString('password', 'Please enter a new password')
                    ->minLength('password', 8, 'Password must be at least 8 characters long')
                    ->add('password', 'custom', [
                        'rule' => function ($value) {
                            return preg_match('/[A-Z]/', $value) && // Uppercase
                                   preg_match('/[a-z]/', $value) && // Lowercase
                                   preg_match('/[0-9]/', $value) && // Number
                                   preg_match('/[^A-Za-z0-9]/', $value); // Special character
                        },
                        'message' => 'Password must include uppercase, lowercase, number, and special character'
                    ])
                    ->requirePresence('confirm_password')
                    ->notEmptyString('confirm_password', 'Please confirm your new password')
                    ->add('confirm_password', 'custom', [
                        'rule' => function ($value, $context) {
                            return $value === $context['data']['password'];
                        },
                        'message' => 'Passwords do not match'
                    ]);
            } else {
                // Customer validation - only 8 characters minimum
                $validator
                    ->requirePresence('password')
                    ->notEmptyString('password', 'Please enter a new password')
                    ->minLength('password', 8, 'Password must be at least 8 characters long')
                    ->requirePresence('confirm_password')
                    ->notEmptyString('confirm_password', 'Please confirm your new password')
                    ->add('confirm_password', 'custom', [
                        'rule' => function ($value, $context) {
                            return $value === $context['data']['password'];
                        },
                        'message' => 'Passwords do not match'
                    ]);
            }

            $errors = $validator->validate($data);
            if (!empty($errors)) {
                // Convert errors to a format that can be displayed under each field
                $fieldErrors = [];
                foreach ($errors as $field => $error) {
                    $fieldErrors[$field] = $error['_empty'] ?? $error['minLength'] ?? $error['custom'] ?? 'Invalid password';
                }

                // Set the errors in the entity for the form helper
                $formEntity->setErrors($fieldErrors);

                $this->set('entity', $formEntity);
                $this->set('userType', $user->type);
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

        // Only show the message if not logged in and coming from booking
        if (!$result->isValid() && $this->request->getQuery('redirect') === 'booking') {
            $this->Flash->info('Please login to make a booking.');
        }

        if ($result && $result->isValid()) {
            $user = $result->getData();

            // If redirected from booking, send to appropriate booking page
            if ($this->request->getQuery('redirect') === 'booking') {
                if ($user->type === 'customer') {
                    return $this->redirect(['controller' => 'Bookings', 'action' => 'customerbooking']);
                } elseif ($user->type === 'admin') {
                    return $this->redirect(['controller' => 'Bookings', 'action' => 'adminbooking']);
                }
            }

            // Default redirects if not from booking
            if ($user->type === 'admin') {
                $fallbackLocation = ['controller' => 'Admins', 'action' => 'dashboard'];
            } elseif ($user->type === 'customer') {
                $fallbackLocation = ['controller' => 'Customers', 'action' => 'dashboard'];
            } elseif ($user->type === 'stylist') {
                $fallbackLocation = ['controller' => 'Stylists', 'action' => 'dashboard'];
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

    public function guesttransfer() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $this->Authentication->getAuthenticationService()->clearIdentity($request, $response);

        return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
    }

}
