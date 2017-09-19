<?php
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

if(isset($_POST['loginBtn'])){
    $form_errors = array();

    $required_fields = array('username', 'password');
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    if(empty($form_errors)){

        $user = $_POST['username'];
        $password = $_POST['password'];

        isset($_POST['remember']) ? $remember = $_POST['remember'] : $remember = "";

        $sqlQuery = "SELECT * FROM users WHERE username = :username";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':username' => $user));

       while($row = $statement->fetch()){
          $id = $row['id'];
          $hashed_password = $row['password'];
          $username = $row['username'];

          if(password_verify($password, $hashed_password)){
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;

            $fingerprint = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            $_SESSION['last_active'] = time();
            $_SESSION['fingerprint'] = $fingerprint;

            if($remember == "yes"){
              rememberMe($id);
            }

            redirectTo('index');
          }else{
            $result = flashMessage("Invalid username or password");
          }
       }

    }else{
        if(count($form_errors) == 1){
            $result = flashMessage("There was one error in the form");
        }else{
            $result = flashMessage("There were " .count($form_errors). " error in the form");
        }
    }
}