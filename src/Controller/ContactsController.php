<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Contacts Controller
 *
 * @property \App\Model\Table\ContactsTable $Contacts
 */
class ContactsController extends AppController
{
   public function initialize(): void
   {
        parent::initialize();
        $this->loadComponent('Recaptcha.Recaptcha');
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['enquiry']);
   }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Contacts->find();
        $contacts = $this->paginate($query);

        $this->set(compact('contacts'));
    }

    /**
     * View method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contact = $this->Contacts->get($id, contain: []);
        $this->set(compact('contact'));
    }

    /**
     * Enquiry method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful enquiry submission, renders view otherwise.
     */
    public function enquiry()
    {
        $this->viewBuilder()->setLayout('login');
        
        $contact = $this->Contacts->newEmptyEntity();
        if ($this->request->is('post')) {
            if($this->Recaptcha->verify()){
                $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
                if ($this->Contacts->save($contact)) {
                    $this->Flash->success(__('Thank you for your interest. We will get back to you as soon as possible.'));
                    return $this->redirect(['action' => 'enquiry']);
                }
            } else {
                $this->Flash->error(__('Please confirm that you are not a bot.'));
            }
            
            if($contact->getErrors()) {
                $this->Flash->error(__('The enquiry could not be sent. Please check the form and try again.'));
            }
        }
        $this->set(compact('contact'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $contact = $this->Contacts->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $contact = $this->Contacts->patchEntity($contact, $this->request->getData());
            if ($this->Contacts->save($contact)) {
                $this->Flash->success(__('The contact has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The contact could not be saved. Please, try again.'));
        }
        $this->set(compact('contact'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $contact = $this->Contacts->get($id);
        if ($this->Contacts->delete($contact)) {
            $this->Flash->success(__('The contact has been deleted.'));
        } else {
            $this->Flash->error(__('The contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function reply($id = null)
    {
        $contact = $this->Contacts->get($id, contain: []);
        $this->set(compact('contact'));
    }

    /**
     * Send reply method
     *
     * @param string|null $id Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful reply, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function sendReply($id = null)
    {
        $contact = $this->Contacts->get($id, contain: []);
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            try {
                $mailer = new \Cake\Mailer\Mailer('default');
                
                $mailer
                    ->setEmailFormat('both')
                    ->setTo($contact->email)
                    ->setSubject($data['subject'])
                    ->setFrom(env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'), env('EMAIL_FROM_NAME', 'ChicCharm'));

                $mailer
                    ->viewBuilder()
                    ->setTemplate('contact_reply');

                $mailer
                    ->setViewVars([
                        'first_name' => $contact->first_name,
                        'last_name' => $contact->last_name,
                        'message' => $data['message']
                    ]);

                if ($mailer->deliver()) {
                    $contact->replied = true;
                    if ($this->Contacts->save($contact)) {
                        $this->Flash->success(__('The reply has been sent successfully.'));
                        return $this->redirect(['action' => 'index']);
                    }
                }
            } catch (\Exception $e) {
                $this->log('Failed to send reply email: ' . $e->getMessage(), 'error');
                $this->Flash->error(__('The reply could not be sent. Please try again.'));
            }
        }
        
        return $this->redirect(['action' => 'reply', $id]);
    }
}

