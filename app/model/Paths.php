<?php
    class Paths extends Model {

        public function __construct() {
            parent::__construct();
            
        }     
        public function getPaths() {
            if (isset($_GET['category'])) {
                return $this->getPathsForCategory($_GET['category']);
            }
            else if (isset($_GET['path'])) {
                return $this->getPathByID($_GET['path']);
            }
            else if (isset($_GET['author'])) {
                return $this->getPathsByAuthor($_GET['author']);
            }
            else {
                return $this->getAllPaths();
            }
        }
        public function getPathsForCategory($categoryID) {
            $query = 
            "SELECT lp.*, u.name as author_name FROM learning_path lp JOIN users u ON lp.author_id = u.user_id
            WHERE lp.path_id in (SELECT lp.path_id
                FROM category c
                JOIN category_has_topic cht ON c.id = cht.category_id
                JOIN topic t ON cht.topic_id = t.id
                JOIN topic_has_paths thp ON t.id = thp.topic_id
                JOIN learning_path lp on thp.path_id = lp.path_id
                WHERE c.id = $categoryID);";
            $result = $this->conn->query($query);
            $paths = [];
            while ($row = $result->fetch_assoc()) $paths[] = $row;
            return $paths;
        }
        
        public function getPathByID($pathID) {
            $query = 
            "SELECT lp.*, u.name as author_name, u.email as author_email FROM learning_path lp JOIN users u ON lp.author_id = u.user_id WHERE path_id = $pathID";
            $result = $this->conn->query($query);
            return $result->fetch_assoc();
        }
        public function getPathsByAuthor($authorID) {
            $query =
            "SELECT lp.*, u.name as author_name, u.email as author_email FROM learning_path lp JOIN users u ON lp.author_id = u.user_id
            WHERE lp.author_id = $authorID";
            $result = $this->conn->query($query);
            $paths = [];
            while ($row = $result->fetch_assoc()) $paths[] = $row;
            return $paths;
        }
        public function getAllPaths() {
            $query = "SELECT lp.*, u.name as author_name FROM learning_path lp JOIN users u ON lp.author_id = u.user_id;";
            $result = $this->conn->query($query);
            $paths = [];
            while ($row = $result->fetch_assoc()) $paths[] = $row;
            return $paths;
        }
        public function getResourcesForPath() {
            $pathID = $_GET['path'] ?? false;
            if ($pathID) {
                $query = 
                "SELECT * FROM resources WHERE path_id = $pathID";
                $result = $this->conn->query($query);
                $resources = [];
                while ($row = $result->fetch_assoc()) $resources[] = $row;
                return $resources;
            }
            return null;
        }
        public function getUsers() {
            $query = "SELECT * FROM users;";
            $result = $this->conn->query($query);
            $users = [];
            while ($row = $result->fetch_assoc()) $users[] = $row;
            return $users;
        }
        public function getCategoriesWithTopics() {
            $categoriesWithTopics = [];
            $categories = $this->loadCategories();
            foreach ($categories as $category) {
                $categoryID = $category['id'];
                $query = 
                "SELECT t.id,t.name FROM category c 
                JOIN category_has_topic cht ON c.id = cht.category_id
                JOIN topic t ON cht.topic_id = t.id
                WHERE c.id = $categoryID;";
                $result = $this->conn->query($query);
                $topics = [];
                while ($row = $result->fetch_row()) $topics[] = $row;
                $category['topics'] = $topics;
                $categoriesWithTopics[] = $category;
            }
            return $categoriesWithTopics;
        }
        public function getLatestPath() {
            $paths = $this->getAllPaths();
            $latestPath = $paths[0];
            foreach ($paths as $path) {
                if ($path['path_id'] > $latestPath['path_id'])
                    $latestPath = $path;
            }
            return $latestPath;
        }
        public function createPath() {
            $response = '';
            if (isset($_POST['submit'])) {
                if (!empty($_POST['title'])) {
                    $title = $_POST['title'];
                    $author_id = intval($_GET['author']);
                    $lastUpdated = date("Y-m-d");
                    $insertPathQuery = 
                    "INSERT INTO learning_path (`title`, `author_id`, `last_updated`)
                    VALUES (?, ?, ?)";
                    $insertPathQuery = $this->conn->prepare($insertPathQuery);
                    $insertPathQuery->bind_param('sis', $title, $author_id, $lastUpdated);
                    $insertPathQuery->execute();
                }
                else {
                    $response .= 'Please enter title!';
                }
                if (!empty($_POST['title']) && $_POST['category'] != "-1" && isset($_POST['topics'])) {
                    $latestPath = $this->getLatestPath();
                    $pathID = intval($latestPath['path_id']);
                    for ($i = 0; $i < count($_POST['topics']); $i++) {
                        $topicID = intval($_POST['topics'][$i]);
                        $insertTopicHasPaths = 
                        "INSERT INTO topic_has_paths
                        VALUES (?, $pathID);";
                        $insertTopicHasPaths = $this->conn->prepare($insertTopicHasPaths);
                        $insertTopicHasPaths->bind_param('i', $topicID);
                        $insertTopicHasPaths->execute();
                    }                    
                }
                return $response;
            }
        }
        private function getLastStep() {
            $step_num = 0;
            foreach ($this->getResourcesForPath() as $step) {
              if ($step['step_num'] > $step_num)
                $step_num = $step['step_num'];
            }
            return $step_num + 1;
          }
        public function addStepToPath() {
            $response = '';
            if (isset($_POST['submit']) && $_POST['submit'] == 'Add Step') {
                if (!empty($_POST['name']) && !empty($_POST['resourceType']) && !empty($_POST['link'])) {
                    $name = $_POST['name'];
                    $resourceType = $_POST['resourceType'];
                    $link = $_POST['link'];
                    $description = empty($_POST['description']) ? "NULL" : "'" . $_POST['description'] . "'";
                    $step_num = $this->getLastStep();
                    $pathID = $_GET['path'];
                    
                    $query = 
                    "INSERT INTO resources (`step_num`, `path_id`, `name`, `link`, `type`, `description`)
                    VALUES (?, ?, ?, ?, ?, $description)";
                    $query = $this->conn->prepare($query);
                    $query->bind_param('iisss', $step_num, $pathID, $name, $link, $resourceType);
                    $query->execute();
                    $response .= "Resource added successfully!";
                    $this->updatePath();
                }
                else {
                    $response .= 'Please fill out required fields!';
                }
                return $response;
            }
        }
        public function UpdateStepInPath() {
            $response = '';
            if (isset($_POST['submit']) && $_POST['submit'] == 'Edit Step') {
                if (!empty($_POST['name']) && !empty($_POST['resourceType']) && !empty($_POST['link'])) {
                    $name = $_POST['name'];
                    $resourceType = $_POST['resourceType'];
                    $link = $_POST['link'];
                    $description = empty($_POST['description']) ? "NULL" : "'" . $_POST['description'] . "'";
                    $step_num = $_GET['step'];
                    $pathID = $_GET['path'];

                    $query = 
                    "UPDATE resources SET name = ?, link = ?, type = ?, description = $description
                    WHERE step_num = ? AND path_id = ?";
                    $query = $this->conn->prepare($query);
                    $query->bind_param('sssii', $name, $link, $resourceType, $step_num, $pathID);
                    $query->execute();
                    $response .= "Resource Updated!";
                    $this->updatePath();
                }
                else {
                    $response .= 'Please fill out all fields!';
                }
                return $response;
            }
        }
        private function updatePath() {
            $query =
            "UPDATE learning_path SET last_updated = CURDATE() WHERE path_id = ?";
            $query = $this->conn->prepare($query);
            $query->bind_param('i', $_GET['path']);
            $query->execute();
        }
        public function clonePath() {
            $response = '';
            if (isset($_POST['submit']) && $_POST['submit'] == 'Clone Path') {
                $reference = intval($_GET['path']); 
                $authorID = $this->getActiveUserData()['user_id'];
                $createPathQuery = 
                "INSERT INTO learning_path (`title`, `author_id`, `last_updated`, `reference`)
                SELECT CONCAT_WS(' ', title, '(clone)   '), ?, CURDATE(), path_id FROM learning_path lp2
                WHERE lp2.path_id = ?;";
                $createPathQuery = $this->conn->prepare($createPathQuery);
                $createPathQuery->bind_param('ii',$authorID, $reference);
                $createPathQuery->execute();
                
                $clonedPathID = $this->getLatestPath()['path_id'];

                $cloneTopicsQuery = 
                "INSERT INTO topic_has_paths
                SELECT topic_id, ? FROM topic_has_paths
                WHERE path_id = ?";
                $cloneTopicsQuery = $this->conn->prepare($cloneTopicsQuery);
                $cloneTopicsQuery->bind_param('ii', $clonedPathID, $reference);
                $cloneTopicsQuery->execute();

                $cloneResourcesQuery =
                "INSERT INTO resources
                SELECT `step_num`, ?, `name`, `link`, `type`, `description` FROM resources
                WHERE path_id = ?;";
                $cloneResourcesQuery = $this->conn->prepare($cloneResourcesQuery);
                $cloneResourcesQuery->bind_param('ii', $clonedPathID, $reference);
                $cloneResourcesQuery->execute();

                $response .= 'PATH CLONED!!';
                return $response;
            }
        }

    }

?>