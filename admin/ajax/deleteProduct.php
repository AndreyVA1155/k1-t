<?php

namespace functionAll;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';

$id = $_POST['id'];

$deleteProductToCategory = connect()->prepare("
    DELETE FROM producttocategory
    WHERE product_id = :id
  ");
$deleteProductToCategory->execute([':id' => $id]);

$deleteProduct = connect()->prepare("
    DELETE FROM products
    WHERE id = :id
  ");

$deleteProduct->execute([':id' => $id]);