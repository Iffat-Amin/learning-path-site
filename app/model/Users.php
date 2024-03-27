<?php
    class Users extends Model {

        public function __construct() {
            parent::__construct();
        } 

        private function loadUsers() {
            $sql = "SELECT * FROM USERS";
            $result = $this->conn->query($sql);
            var_dump($result->fetch_all());
            echo '<br>';
        }

        public function addUser() {
            $error = '';
            if (isset($_POST['register'])) {
                if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                    if (str_contains($_POST['email'], '@')) {
                        $name = $_POST['name'];
                        $email = strtolower($_POST['email']);
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $query = "INSERT INTO `users`(`name`, `email`, `password`) VALUES (?, ?, ?);";
                        $query = $this->conn->prepare($query);
                        $query->bind_param('sss', $name, $email, $password);
                        $query->execute();
                        $query->close();
                    }
                    else {
                        $response .= 'Invalid Email!';
                    }
                }
                else {
                    $error .= 'Please fill all fields!';
                }
                return $error;
            }
        }

        public function verifyUser() {
            $response = '';
            if ($_GET['page'] == 'login' && isset($_POST['login'])) {
                if (!empty($_POST['email']) && !empty($_POST['password'])) {
                    $email = $_POST['email'];
                    $passwordInput = $_POST['password'];

                    $query = "SELECT `password` FROM `users` WHERE `email` = ?;";
                    $query = $this->conn->prepare($query);
                    $query->bind_param('s', $email);
                    $query->execute();
                    $result = $query->get_result();
                    $realPass = $result->fetch_assoc();
                    if (isset($realPass['password']) && password_verify($passwordInput, $realPass['password'])) {
                        $this->login($email);
                        echo '<script>location="?page=index"</script>';
                    }
                    else {
                        $response .= "Invalid Credentials!";
                    }
                }
                else {
                    $response .= "Please fill all fields!";
                }
                
                return $response;
            }
        }
        public function login($email) {
            $_SESSION['LOGIN_STATUS'] = true;
            $_SESSION['USER'] = $email;
        }
        public function logout() {
            unset($_SESSION);
            setcookie(session_name(), time() - 3600);
            session_destroy();
        }
        public function getPathsForUser() {
            if (isset($_SESSION['USER'])) {
                $query = 
                "SELECT lp.*, u.name as author_name FROM learning_path lp
                JOIN users u ON lp.author_id = u.user_id
                WHERE author_id = 
                (SELECT user_id FROM users WHERE email = ?);";
                $query = $this->conn->prepare($query);
                $query->bind_param('s', $_SESSION['USER']);
                $query->execute();
                $result = $query->get_result();
                $paths = [];
                while ($row = $result->fetch_assoc()) $paths[] = $row;
                return $paths;
            }
        }
        public function editProfile() {
            $response = '';
            if (isset($_POST['submit'])) {
                if ($_FILES['pfp']['tmp_name'] != "") {
                    $target_dir = 'app/data/img/';
                    $target_file = $target_dir . explode('@', $_SESSION['USER'])[0] . '.jpg';
                    $imageFileType = getimagesize($_FILES["pfp"]["tmp_name"])['mime'];
                    if ($imageFileType == "image/jpeg") {
                        if (file_exists($target_file))
                            unlink($target_file);
                        move_uploaded_file($_FILES['pfp']['tmp_name'], $target_file);
                    }
                    else {
                        $response .= 'Image must be JPG!<br>';
                    }
                }
                if (!empty($_POST['name'])) {
                    $name = $_POST['name'];
                    $bio = empty($_POST['bio']) ? "NULL" : "'".$_POST['bio']."'";
                    $userID = $_GET['user'];
                    $query = "UPDATE users SET name='$name', bio=$bio WHERE user_id=$userID";
                    $this->conn->query($query);
                }
                else {
                    $response .= 'Please Enter Valid Name!';
                }   
                return $response;            
            }            
        }
    }

?>