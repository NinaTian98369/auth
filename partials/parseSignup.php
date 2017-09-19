<?php
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

if(isset($_POST['signupBtn'])){
    $form_errors = array();

    $required_fields = array('email', 'username', 'password');

    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    $fields_to_check_length = array('username' => 4, 'password' => 6);

    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    $form_errors = array_merge($form_errors, check_email($_POST));

    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    if(checkDuplicateUsername($username, $db)){
        $result = flashMessage("Username is already taken, please try another one");
    }
    else if(checkDuplicateEmail($email, $db)){
        $result = flashMessage("Email is already taken, please try another one");
    }

    else if(empty($form_errors)){
        

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try{

            $sqlInsert = "INSERT INTO users (username, email, password, join_date)
              VALUES (:username, :email, :password, now())";

            $statement = $db->prepare($sqlInsert);

            $statement->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));

            if($statement->rowCount() == 1){
                $result = flashMessage("Registration Successful","Pass");
            }
        }catch (PDOException $ex){
            $result = flashMessage("An error occurred: " .$ex->getMessage());
        }
    }
    else{
        if(count($form_errors) == 1){
            $result = flashMessage("There was 1 error in the form<br>");
        }else{
            $result = flashMessage("There were " .count($form_errors). " errors in the form <br>");
        }
    }

}