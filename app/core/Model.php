<?php
    abstract class Model {
        protected static $username = 'f3412150_user';
        protected static $password = 'password123';
        protected static $host = 'f3412150.gblearn.com';
        protected static $database = 'f3412150_project';

        // protected static $username = 'root';
        // protected static $password = '';
        // protected static $host = 'localhost';
        // protected static $database = 'learningvoyage';

        protected $conn;

        public function __construct() {
            $this->conn = new mysqli(self::$host, self::$username, self::$password, self::$database);
            if ($this->conn->connect_errno > 0) {
                echo "Connection error : " . $this->conn->connect_errno;
            }
            session_set_cookie_params(3600, '/');
            session_start();
            if (!isset($_SESSION['LOGIN_STATUS'])) $_SESSION['LOGIN_STATUS'] = false;
        }
        public function loadCategories() {
            $query = "SELECT * FROM category";
            $result = $this->conn->query($query);
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            return $categories;
        }
        public function deletePath() {
            if (isset($_POST['delete'])) {
                $pathID = intval($_POST['delete']);
                $delFromTHP = 
                "DELETE FROM topic_has_paths WHERE path_id = ?";
                $delFromTHP = $this->conn->prepare($delFromTHP);
                $delFromTHP->bind_param('i', $pathID);
                $delFromTHP->execute();

                $delFromResources = 
                "DELETE FROM resources WHERE path_id = ?";
                $delFromResources = $this->conn->prepare($delFromResources);
                $delFromResources->bind_param('i', $pathID);
                $delFromResources->execute();

                $delFromLearningPath = 
                "DELETE FROM learning_path WHERE path_id = ?";
                $delFromLearningPath = $this->conn->prepare($delFromLearningPath);
                $delFromLearningPath->bind_param('i', $pathID);
                $delFromLearningPath->execute();
            }
        }
        public function getActiveUserData() {
            if (isset($_SESSION['USER'])) {
                $activeUserEmail = $_SESSION['USER'];
                $query = "SELECT * FROM users WHERE email = ?;";
                $query = $this->conn->prepare($query);
                $query->bind_param('s', $activeUserEmail);
                $query->execute();
                $result = $query->get_result();
                return $result->fetch_assoc();
            }
            return null;
        }

    }


?>