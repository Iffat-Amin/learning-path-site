<?php

    class Application {
        public $controller = 'homeController';
        public $action = 'index';
        public $params = [];
        public function __construct($controller) {
            if (file_exists(CONTROLLER . $controller . '.php')) {
                $this->controller = new $controller();
            }
            $this->action = $this->getAction();
            
        }

        public function getAction() {
            return isset($_REQUEST['page']) ? $_REQUEST['page'] : 'index';
        }
        public function run() {
            if (method_exists($this->controller, $this->action)) {
                call_user_func_array([$this->controller, $this->action], $this->params);
            }
            else {
                $this->controller->_404();
            }
        }
    }

?>