<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Services Controller
 *
 * @property \App\Model\Table\ServicesTable $Services
 */
class ServicesController extends AppController
{
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
            $this->Flash->error(__('The service could not be saved. Please, try again.'));
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
        $service = $this->Services->get($id, contain: ['Stylists']);
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
            $this->Flash->error(__('The service could not be saved. Please, try again.'));
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
        $service = $this->Services->get($id);
        if ($this->Services->delete($service)) {
            $this->Flash->success(__('The service has been deleted.'));
        } else {
            $this->Flash->error(__('The service could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
