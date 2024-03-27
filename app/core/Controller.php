<?php

    abstract class Controller {
        protected $model;
        protected $view;

        public function model($modelName) {
            if (file_exists(MODEL . $modelName .".php")) {
                $this->model = new $modelName();
            }
        }
        public function view($view, $title, $data = array()) {
            if (file_exists(VIEW . $view .".phtml")) {
                $viewPath = VIEW . $view .".phtml";
                $this->view = new View($viewPath, $title, $data);
            }
        }
        public function _404() {
            $this->view = new View(VIEW . '_404.phtml', 'Page Not Found');
            $this->view->render();
        }
    }

?>