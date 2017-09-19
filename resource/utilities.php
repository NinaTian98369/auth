<?php
/**
 * @param 
 * @return 
 */
function check_empty_fields($required_fields_array){
    $form_errors = array();

    foreach($required_fields_array as $name_of_field){
        if(!isset($_POST[$name_of_field]) || $_POST[$name_of_field] == NULL){
            $form_errors[] = $name_of_field . " is a required field";
        }
    }

    return $form_errors;
}

/**
 * @param 
 * @return 
 */
function check_min_length($fields_to_check_length){
    $form_errors = array();

    foreach($fields_to_check_length as $name_of_field => $minimum_length_required){
        if(strlen(trim($_POST[$name_of_field])) < $minimum_length_required && $_POST[$name_of_field] != NULL){
            $form_errors[] = $name_of_field . " is too short, must be {$minimum_length_required} characters long";
        }
    }
    return $form_errors;
}

/**
 * @param 
 * @return 
 */
function check_email($data){
    $form_errors = array();
    $key = 'email';
    if(array_key_exists($key, $data)){

        if($_POST[$key] != null){

            $key = filter_var($key, FILTER_SANITIZE_EMAIL);

            if(filter_var($_POST[$key], FILTER_VALIDATE_EMAIL) === false){
                $form_errors[] = $key . " is not a valid email address";
            }
        }
    }
    return $form_errors;
}

/**
 * @param 
 * @return 
 */
function show_errors($form_errors_array){
    $errors = "<p><ul style='color: red;'>";

    foreach($form_errors_array as $the_error){
        $errors .= "<li> {$the_error} </li>";
    }
    $errors .= "</ul></p>";
    return $errors;
}

function flashMessage($message, $passOrFail = "Fail"){
    if($passOrFail == "Pass"){
        $data = "<div class='alert alert-success'>{$message}</p>";
    }else{
        $data = "<div class='alert alert-danger'>{$message}</p>";
    }
    return $data;
}

function redirectTo($page){
    header("location: {$page}.php");
}

function checkDuplicateUsername($value, $db){
    try{
        $sqlQuery = "SELECT username FROM users WHERE username=:username";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':username' => $value));

        if($row = $statement->fetch()){
            return true;
        }
        return false;
    }catch(PDOExeption $ex){

    }
}

function checkDuplicateEmail($value, $db){
    try{
        $sqlQuery = "SELECT email FROM users WHERE email=:email";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':email' => $value));

        if($row = $statement->fetch()){
            return true;
        }
        return false;
    }catch(PDOExeption $ex){

    }
}

/**
 * @param $user_id
 */

function rememberMe($user_id){
    $encryptCookieData = base64_encode("UaQteh5i4y3dntstemYODEC{$user_id}");
    setcookie("rememberUserCookie", $encryptCookieData, time()+60*60*24*100, "/");
}

/**
 * @param 
 * @return 
 */

function isCookieValid($db){
    $isValid = false;

    if(isset($_COOKIE['rememberUserCookie'])){
        $decryptCookieData = base64_decode($_COOKIE['rememberUserCookie']);
        $user_id = explode("UaQteh5i4y3dntstemYODEC", $decryptCookieData);
        $userID = $user_id[1];

        $sqlQuery = "SELECT * FROM users WHERE id = :id";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':id' => $userID));

        if($row = $statement->fetch()){
            $id = $row['id'];
            $username = $row['username'];

            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $isValid = true;
        }else{
            $isValid = false;
            $this->signout();
        }
    }
    return $isValid;
}

function signout(){
    unset($_SESSION['username']);
    unset($_SESSION['id']);

    if(isset($_COOKIE['rememberUserCookie'])){
        unset($_COOKIE['rememberUserCookie']);
        setcookie('rememberUserCookie', null, -1, '/');
    }
    session_destroy();
    session_regenerate_id(true);
    redirectTo('index');
}

function guard(){

    $isValid = true;
    $inactive = 60 * 10;
    $fingerprint = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    
    if((isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] != $fingerprint)){
        $isValid = false;
        signout();
    }else if((isset($_SESSION['last active']) && (time() - $_SESSION['last active']) > $inactive) && $_SESSION['username']) {
        $isValid = false;
        signout();
    }else{
        $_SESSION['last active'] = time();
    }
    return $isValid;
}

