<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
/**
 * StylistsServices Controller
 *
 * @property \App\Model\Table\StylistsServicesTable $StylistsServices
 */
class StylistsServicesController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Check if user is admin for all actions
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->StylistsServices->find()
            ->contain(['Stylists', 'Services']);
        $stylistsServices = $this->paginate($query);

        $this->set(compact('stylistsServices'));
    }

    /**
     * View method
     *
     * @param string|null $id Stylists Service id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $stylistsService = $this->StylistsServices->get($id, contain: ['Stylists', 'Services']);
        $this->set(compact('stylistsService'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stylistsService = $this->StylistsServices->newEmptyEntity();
        if ($this->request->is('post')) {
            $stylistsService = $this->StylistsServices->patchEntity($stylistsService, $this->request->getData());
            if ($this->StylistsServices->save($stylistsService)) {
                $this->Flash->success(__('The stylists service has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The stylists service could not be saved. Please, try again.'));
        }
        $stylists = $this->StylistsServices->Stylists->find('list', limit: 200)->all();
        $services = $this->StylistsServices->Services->find('list', limit: 200)->all();
        $this->set(compact('stylistsService', 'stylists', 'services'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Stylists Service id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $stylistsService = $this->StylistsServices->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stylistsService = $this->StylistsServices->patchEntity($stylistsService, $this->request->getData());
            if ($this->StylistsServices->save($stylistsService)) {
                $this->Flash->success(__('The stylists service has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The stylists service could not be saved. Please, try again.'));
        }
        $stylists = $this->StylistsServices->Stylists->find('list', limit: 200)->all();
        $services = $this->StylistsServices->Services->find('list', limit: 200)->all();
        $this->set(compact('stylistsService', 'stylists', 'services'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Stylists Service id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stylistsService = $this->StylistsServices->get($id);
        if ($this->StylistsServices->delete($stylistsService)) {
            $this->Flash->success(__('The stylists service has been deleted.'));
        } else {
            $this->Flash->error(__('The stylists service could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
