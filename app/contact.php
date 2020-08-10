<?php

$name = filter_input(INPUT_POST, 'imie', FILTER_SANITIZE_STRING); // Imię
 $surname = filter_input(INPUT_POST, 'nazwisko', FILTER_SANITIZE_STRING); // Nazwisko
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING); // E-mail
$phone = filter_input(INPUT_POST, 'telefon', FILTER_SANITIZE_STRING); // Nr tel
$company = filter_input(INPUT_POST, 'nazwa_firmy', FILTER_SANITIZE_STRING); // Nr tel
$message = nl2br(filter_input(INPUT_POST, 'wiadomosc', FILTER_SANITIZE_STRING)); // Wiadomość
// $agree = 1;
$agree = filter_input(INPUT_POST, 'zgoda', FILTER_SANITIZE_STRING);
$agree2 = filter_input(INPUT_POST, 'zgoda2', FILTER_SANITIZE_STRING);

if(!$validate->length($name, ['min'=> 1])){
    $response['errors'][] = 'Podaj imię';
}

// if(!$validate->length($surname, ['min'=> 1])){
//     $response['errors'][] = 'Podaj nazwisko';
// }

if(!$validate->length($email, ['min'=> 1])){
    $response['errors'][] = 'Podaj e-mail';
}

if(!$validate->email($email)){
    $response['errors'][] = 'Podaj poprawny e-mail';
}

if(!$validate->length($phone, ['min'=> 1])){
    $response['errors'][] = 'Podaj numer telefonu';
}

if(!$validate->phone($phone)) {
    $response['errors'][] = 'Podaj poprawny numer telefonu';
}

// if ($agree !== 'tak') {

//     $response['errors'][] = 'Proszę zaznaczyć zgodę';
// }
$subject = 'Formularz kontaktowy';

$html = <<<HTML
    <h1>Formularz kontaktowy</h1>
    <p>Imię i nazwisko: <b>{$name}</b></p>
    <p>Nazwa firmy: <b>{$company}</b></p>
    <p>E-mail: <b>{$email}</b></p>
    <p>Numer telefonu: <b>{$phone}</b></p>
    <p>Wiadomość: <b>{$message}</b></p>
    <p>Zgoda1: <b>{$agree}</b></p>
    <p>Zgoda2: <b>{$agree2}</b></p>

HTML; 

?>