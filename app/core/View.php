<?php
    class View { 
        protected $viewPath;
        protected $title;
        protected $data = [];
        public function __construct($viewPath, $viewTitle, $data = []) {
            $this->viewPath = $viewPath;
            $this->title = $viewTitle;
            $this->data = $data;
        }
        public function render() {
            if (file_exists($this->viewPath)) {
                include $this->viewPath;
            }
        }
    }

?>