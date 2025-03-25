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
            }
            if(!$this->Recaptcha->verify()){
                $this->Flash->error(__('Please confirm that you are not a bot.'));
            }
            $this->Flash->error(__('The enquiry could not be sent. Please, try again.'));
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
}
