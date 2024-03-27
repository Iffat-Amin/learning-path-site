<?php
    class homeController extends Controller{

        public function index(){
            $this->model('Paths');
            $data = [
                'categories' => $this->model->loadCategories()
            ];
            $this->view('index', 'Home', $data);
            $this->view->render();
        }
        public function register() {
            $this->model('Users');
            $response = $this->model->addUser();
            $data = [
                'response' => $response,
                'categories' => $this->model->loadCategories()
            ];
            $this->view('register', 'Register', $data);
            $this->view->render();
        }
        public function login() {
            $this->model('Users');
            $response = $this->model->verifyUser();
            $data = [
                'response' => $response,
                'categories' => $this->model->loadCategories()
            ];
            $this->view('login', 'Log-In', $data);
            $this->view->render();
        }
        public function paths() {
            $this->model('Paths');
            $data = [
                'paths' => $this->model->getPaths(),
                'categories' => $this->model->loadCategories(),
                'users' => $this->model->getUsers()
            ];
            if (isset($_POST['delete'])) $this->model->deletePath(); 
            $this->view('pathList', 'Paths', $data);
            $this->view->render();
        }
        public function logout() {
            $this->model('Users');
            $this->model->logout();
            $this->view('logout', 'Log-Out');
            $this->view->render();
        }
        public function resources() {
            $this->model('Paths');
            $response = '';
            if (isset($_POST['submit'])) $response = $this->model->clonePath();
            $data = [
                'response' => $response,
                'path' => $this->model->getPaths(),
                'categories' => $this->model->loadCategories(),
                'resources' => $this->model->getResourcesForPath()
            ];
            if (isset($_SESSION['USER'])) $data['user'] = $this->model->getActiveUserData();
           
            $this->view('resources', 'Resources', $data);
            $this->view->render();
        }
        public function profile() {
            $this->model('Users');
            $data = [
                'categories' => $this->model->loadCategories(),
                'user' => $this->model->getActiveUserData(),
                'userPaths' => $this->model->getPathsForUser()
            ];
            if (isset($_POST['delete'])) $this->model->deletePath(); 
            $this->view('profile', 'Profile', $data);
            $this->view->render();
        }
        public function editProfile() {
            $this->model('Users');
            $response = $this->model->editProfile();
            $data = [
                'response' => $response,
                'categories' => $this->model->loadCategories(),
                'user' => $this->model->getActiveUserData()
            ];
            $this->view('editProfile', 'Edit Profile', $data);
            $this->view->render();
        }
        public function addPath() {
            $this->model('Paths');
            $response = $this->model->createPath(); 
            $data = [
                'response' => $response,
                'categories' => $this->model->getCategoriesWithTopics()
            ];
            $this->view('addPath', 'Create a Path', $data);
            $this->view->render();
        }
        public function addStep() {
            $this->model('Paths');
            $response = $this->model->addStepToPath();
            $data = [
                'response' => $response,
                'categories' => $this->model->loadCategories(),
                'path' => $this->model->getPaths(),
                'steps' => $this->model->getResourcesForPath()
            ];
            $this->view('stepDetails', 'Add a Step', $data);
            $this->view->render();
        }
        public function editStep() {
            $this->model('Paths');
            $response = $this->model->UpdateStepInPath();
            $data = [
                'response' => $response,
                'categories' => $this->model->loadCategories(),
                'path' => $this->model->getPaths(),
                'steps' => $this->model->getResourcesForPath()
            ];
            $this->view('stepDetails', 'Edit Step', $data);
            $this->view->render();

        }
        
    }

?>