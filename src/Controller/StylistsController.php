<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Stylists Controller
 *
 * @property \App\Model\Table\StylistsTable $Stylists
 */
class StylistsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Stylists->find();
        $stylists = $this->paginate($query);

        $this->set(compact('stylists'));
    }

    /**
     * View method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $stylist = $this->Stylists->get($id, contain: ['Bookings', 'Services']);
        $this->set(compact('stylist'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stylist = $this->Stylists->newEmptyEntity();
        if ($this->request->is('post')) {
            $stylist = $this->Stylists->patchEntity($stylist, $this->request->getData());
            if ($this->Stylists->save($stylist)) {
                $this->Flash->success(__('The stylist has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($stylist->getErrors()) {
                foreach ($stylist->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The stylist could not be saved. Please, try again.'));
            }
        }
        $bookings = $this->Stylists->Bookings->find('list', limit: 200)->all();
        $services = $this->Stylists->Services->find('list', limit: 200)->all();
        $this->set(compact('stylist', 'bookings', 'services'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $stylist = $this->Stylists->get($id, contain: ['Services']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();


            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $stylist->password;
            } else {
                $data['password'] = $password;
            }
            $stylist = $this->Stylists->patchEntity($stylist, $data);
            if ($this->Stylists->save($stylist)) {
                $this->Flash->success(__('The stylist has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($stylist->getErrors()) {
                foreach ($stylist->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The stylist could not be saved. Please, try again.'));
            }
        }
        $bookings = $this->Stylists->Bookings->find('list', limit: 200)->all();
        $services = $this->Stylists->Services->find('list', limit: 200)->all();
        $this->set(compact('stylist', 'bookings', 'services'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stylist = $this->Stylists->get($id);
        if ($this->Stylists->delete($stylist)) {
            $this->Flash->success(__('The stylist has been deleted.'));
        } else {
            $this->Flash->error(__('The stylist could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
