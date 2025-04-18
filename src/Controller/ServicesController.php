<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
/**
 * Services Controller
 *
 * @property \App\Model\Table\ServicesTable $Services
 */
class ServicesController extends AppController
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
        $query = $this->Services->find();
        $services = $this->paginate($query);

        $this->set(compact('services'));
    }

    /**
     * View method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $service = $this->Services->get($id, contain: ['Stylists']);
        $this->set(compact('service'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $service = $this->Services->newEmptyEntity();
        if ($this->request->is('post')) {
            $service = $this->Services->patchEntity($service, $this->request->getData());

            //Checks if someone has put a negative number in it
            if ($service['service_cost'] < 0) {
                $this->Flash->error(__('Service Cost cannot be less than zero.'));
                return $this->redirect(['action' => 'add']);
            }

            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($service->getErrors()) {
                foreach ($service->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The service could not be saved. Please, try again.'));
            }
        }
        $stylists = $this->Services->Stylists->find('list', limit: 200)->all();
        $this->set(compact('service', 'stylists'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $service = $this->Services->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $service = $this->Services->patchEntity($service, $this->request->getData());

            //Checks if someone has put a negative number in it
            if ($service['service_cost'] < 0) {
                $this->Flash->error(__('Service Cost cannot be less than zero.'));
                return $this->redirect(['action' => 'edit',$service['id']]);
            }
            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($service->getErrors()) {
                foreach ($service->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The service could not be saved. Please, try again.'));
            }
        }
        $stylists = $this->Services->Stylists->find('list', limit: 200)->all();
        $this->set(compact('service', 'stylists'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $service = $this->Services->get($id, [
            'contain' => ['Bookings' => function ($q) {
                return $q->where(['Bookings.status' => 'active']);
            }]
        ]);

        // Check if service has any active bookings
        if (!empty($service->bookings)) {
            $this->Flash->error(__('Cannot delete service as it has active bookings.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Services->delete($service)) {
            $this->Flash->success(__('The service has been deleted.'));
        } else {
            $this->Flash->error(__('The service could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

