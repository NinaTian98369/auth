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
}

