<?php

namespace functionAll;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';



$priceProduct = explode(' ',$_POST['priceProduct']);
if(empty($_POST['thirdName'])) {
    $_POST['thirdName'] = NULL;
}
if($_POST['delivery'] == 0) {
    $_POST['city'] = NULL;
    $_POST['street'] = NULL;
    $_POST['home'] = NULL;
    $_POST['apartment'] = NULL;
}
if ($_POST['delivery'] == 1 && $priceProduct < MINCOSTORDER) {
    $priceProduct = $priceProduct + COSTDELIVERY;
}
var_dump($_POST);
$addOrder = connect()->prepare("
    INSERT INTO orders ( name, surname, thirdName, phone, email, delivery, city, street, home, apartment, payment, comment, status, data_create, price)
    VALUES (:name, :surname, :thirdName, :phone, :email, :delivery, :city, :street, :home, :apartment, :payment, :comment, 'notProcessed', NOW(), $priceProduct[0])
  ");

$addOrder->execute(
    [
        ':email' => $_POST['email'],
        ':surname' => $_POST['surname'],
        ':phone' => $_POST['tel'],
        ':name' => $_POST['name'],
        ':thirdName' => $_POST['thirdName'],
        ':city' => $_POST['city'],
        ':street' => $_POST['street'],
        ':home' => $_POST['home'],
        ':delivery' => $_POST['delivery'],
        ':apartment' => $_POST['apartment'],
        ':payment' => $_POST['pay'],
        ':comment' => $_POST['comment']
    ]);



//':name' => $_POST['name'], ':surname' => $_POST['surname'], ':phone' => $_POST['tel'],