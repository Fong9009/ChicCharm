<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Services Controller
 *
 * @property \App\Model\Table\ServicesTable $Services
 */
class ServicesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['servicePage', 'serviceView']);
    }

    /**
     * Verification for User
     *
     * @return void
     */
    private function verification(): void
    {
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->verification();
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
        $this->verification();
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
        $this->verification();
        $service = $this->Services->newEmptyEntity();
        if ($this->request->is('post')) {
            $service = $this->Services->patchEntity($service, $this->request->getData());

            //Checks if someone has put a negative number in it
            if ($service['service_cost'] < 0) {
                $this->Flash->error(__('Service Cost cannot be less than zero.'));
                return $this->redirect(['action' => 'add']);
            }

            $imageResult = $this->addNewImage();
            if (!$imageResult['error']) {
                $service->service_image = $imageResult['filename'];

                if ($this->Services->save($service)) {
                    $this->Flash->success(__('The service has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
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
     * Adds the image into the new service
     *
     * @return array
     */
    private function addNewImage(): array
    {
        //Service Image
        $serviceImage = $this->request->getData('service_image');
        $data = ['error' => false, 'filename' => null];
        if ($serviceImage->getClientFilename() !== '' && $serviceImage->getClientFilename() !== null) {
            if ($serviceImage && $serviceImage->getClientFilename()) {
                //Max Size to prevent massive files from being inserted
                //This is measured in MB so max = 4MB
                $maxSize = 4 * 1024 * 1024;
                if ($serviceImage->getSize() > $maxSize) {
                    $data['error'] = true;
                    $this->Flash->error(__('The service image is too big please use something smaller than 4MB.'));
                }

                //Check if the file is a real image
                $tmpFile = $serviceImage->getStream()->getMetadata('uri');
                if (!getimagesize($tmpFile)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The uploaded file is not a valid image.'));
                }

                //Check Filetype
                $allowedFileTypes = ['image/jpeg', 'image/png','image/jpg'];
                if (!in_array($serviceImage->getClientMediaType(), $allowedFileTypes)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The service image must be a jpeg/jpg or png format.'));
                }
                if (!$data['error']) {
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($serviceImage->getClientFilename());
                    $serviceImage->moveTo(WWW_ROOT . 'img/service/' . $filename);
                    $data['filename'] = $filename;
                }
            } else {
                $data['filename'] = null;
            }
        } else {
            $data['filename'] = null;
        }

        return $data;
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
        $this->verification();
        $service = $this->Services->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $service = $this->Services->patchEntity($service, $this->request->getData());

            //Checks if someone has put a negative number in it
            if ($service['service_cost'] < 0) {
                $this->Flash->error(__('Service Cost cannot be less than zero.'));
                return $this->redirect(['action' => 'edit',$service['id']]);
            }

            $imageResult = $this->replaceImage($id);
            if (!$imageResult['error']) {
                $service->service_image = $imageResult['filename'];

                if ($this->Services->save($service)) {
                    $this->Flash->success(__('The service has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
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
     * This is to replace the service image
     *
     * @param string $id
     * @return array
     */
    private function replaceImage(string $id): array
    {
        //Service Image Updater
        $service = $this->Services->get($id, contain: []);
        $serviceImage = $this->request->getData('service_image');
        $data = ['error' => false, 'filename' => null];
        if ($serviceImage->getClientFilename() !== '' && $serviceImage->getClientFilename() !== null) {
            if ($serviceImage && $serviceImage->getClientFilename()) {
                //Max Size to prevent massive files from being inserted
                //This is measured in MB so max = 4MB
                $maxSize = 4 * 1024 * 1024;
                if ($serviceImage->getSize() > $maxSize) {
                    $data['error'] = true;
                    $this->Flash->error(__('The service image is too big please use something smaller than 4MB.'));
                }

                //Check if the file is a real image
                $tmpFile = $serviceImage->getStream()->getMetadata('uri');
                if (!getimagesize($tmpFile)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The uploaded file is not a valid image.'));
                }

                //Check Filetype
                $allowedFileTypes = ['image/jpeg', 'image/png','image/jpg'];
                if (!in_array($serviceImage->getClientMediaType(), $allowedFileTypes)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The service image must be a jpeg/jpg or png format.'));
                }

                //Delete old Image if there is one
                if ($service->service_image != null && !$data['error']) {
                    $oldPath = WWW_ROOT . 'img/service/' . $service->service_image;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($serviceImage->getClientFilename());
                    $serviceImage->moveTo(WWW_ROOT . 'img/service/' . $filename);
                    $data['filename'] = $filename;
                }
                if ($service->service_image === null && !$data['error']) {
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($serviceImage->getClientFilename());
                    $serviceImage->moveTo(WWW_ROOT . 'img/service/' . $filename);
                    $data['filename'] = $filename;
                }
            } else {
                $data['filename'] = null;
            }
        } else {
            $data['filename'] = $service->service_image;
        }

        return $data;
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
        $this->verification();
        $this->request->allowMethod(['post', 'delete']);
        $service = $this->Services->get($id, [
            'contain' => ['Bookings' => function ($q) {
                return $q->where(['Bookings.status' => 'active']);
            }],
        ]);

        if ($service->service_image != null) {
            $oldPath = WWW_ROOT . 'img/service/' . $service->service_image;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

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

    public function servicePage() {
        $this->paginate = [
            'limit' => 12,
        ];

        //Search functionality
        $query = $this->Services->find();
        $search = $this->request->getQuery('search');
        if ($search) {
            $query->where([
                'OR' => [
                    'service_name LIKE' => '%' . $search . '%',
                    'service_desc LIKE' => '%' . $search . '%',
                ],
            ]);
        }
        $services = $this->paginate($query);
        $this->set(compact('services'));
    }

    /**
     * Used To Display Stylists for that service
     *
     * @param $id
     * @return void
     */
    public function serviceView($id = null)
    {
        $this->paginate = [
            'limit' => 6,
        ];
        $service = $this->Services->get($id, contain: ['Stylists','Stylists.Services']);
        $stylistTable = $this->fetchTable('Stylists');
        //Query stylists with the related services
        $stylistsQuery =  $stylistTable->find()
            ->contain(['Services'])
            ->matching('Services')
            ->distinct('stylist_id')
            ->orderBy(['Stylists.first_name' => 'ASC']);

        $search = $this->request->getQuery('search');
        if ($search) {
            $stylistsQuery->where([
                'OR' => [
                    'first_name LIKE' => '%' . $search . '%',
                    'last_name LIKE' => '%' . $search . '%',
                    'service_name LIKE' => '%' . $search . '%',
                ],
            ]);
        }

        $stylists = $this->paginate($stylistsQuery);

        $this->set(compact('service', 'stylists'));
    }
}

