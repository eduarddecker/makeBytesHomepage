<?php
// Configure your Subject Prefix and Recipient here
$subjectPrefix = 'Contact via makebytes.de';
$emailTo       = 'info@makebytes.de';
$errors = array(); // array to hold validation errors
$data   = array(); // array to pass back data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['name']));
    $email   = stripslashes(trim($_POST['email']));
    $phone = stripslashes(trim($_POST['phone']));
    $message = stripslashes(trim($_POST['message']));
    
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }else{
        $name = '<strong>Name: </strong>'.$name.'<br />';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email is invalid.';
    }else{
        $email = '<strong>Email: </strong>'.$email.'<br />';
    }
    
    if(!empty($phone)){
        $phone = '<strong>Phone: </strong>'.$phone.'<br />';
    }else{
        $phone = '';
    }     
    if (empty($message)) {
        $errors['message'] = 'Message is required.';
    }else{
        $message = '<strong>Message: </strong>'.nl2br($message).'<br />';
    }
    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        $subject =  $subjectPrefix;        
        $body    =  $name.$email.$phone.$message;        
        $headers  = "MIME-Version: 1.1" . PHP_EOL;
        $headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
        $headers .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
        $headers .= "Date: " . date('r', $_SERVER['REQUEST_TIME']) . PHP_EOL;
        $headers .= "Message-ID: <" . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;
        $headers .= "From: " . "=?UTF-8?B?".base64_encode($name)."?=" . "<$email>" . PHP_EOL;
        $headers .= "Return-Path: $emailTo" . PHP_EOL;
        $headers .= "Reply-To: $email" . PHP_EOL;
        $headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;
        $headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . PHP_EOL;
        
        mail($emailTo, "=?utf-8?B?" . base64_encode($subject) . "?=", $body, $headers);
        
        $data['success'] = true;
        $data['message'] = 'Congratulations. Your message has been sent successfully';
    }
    // return all our data to an AJAX call
    echo json_encode($data);
}