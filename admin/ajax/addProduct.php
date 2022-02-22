<?php

namespace functionAll;

if (isset($_POST['send'])) {
    header('Location: /');
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';

//данные о товаре берутся из массива POST
if(isset($_POST)) {
    $name = $_POST['product-name'];
    $price = $_POST['product-price'];
    $category = $_POST['category'];
    if(isset($_POST['new'])) {
        $status= 'новинка';
    } elseif (isset($_POST['sale'])) {
        $status= 'распродажа';
    } else {
        $status= 'нет статуса';
    }
} else {
    $name = '';
    $price = '';
    $category = '';
    $status = '';
    $nameProoductImg = '';
    $path = '';
    $_POST['category'] = '';
}

//определени имени и пути хранения картинки
if (!empty($_FILES)) {
    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/img/products/';
    foreach ($_FILES as $item) {
        $nameImg = $item['name'];
        move_uploaded_file($item['tmp_name'], "$uploaddir/$nameImg");
    }

    $strName = explode('.', $nameImg);
    $nameProoductImg = $strName[0];
    $path = 'img/products/' . $nameImg;
}

//запись в БД данных о товаре
$addProduct = connect()->prepare("
    INSERT INTO products (name, price, status, img, imp_path)
    VALUES (:name, :price, :status, :img, :imp_path)
  ");

$addProduct->execute(
    [
        ':name' => $name,
        ':price' => $price,
        ':status' => $status,
        ':img' => $nameProoductImg,
        ':imp_path' => $path
    ]);

//получение id категории по выбранной категории
$idCategory = connect()->prepare("
    SELECT id FROM categories
    WHERE category = :category
");
$idCategory->execute([':category' => $_POST['category']]);
$idCategory = $idCategory->fetchAll();
if(isset($_POST)) {
    $idCategory = $idCategory[0]['id'];
} else {
    $idCategory = '';
}

//определение последнего id  из БД таблицы products
$idLastProduct = connect()->prepare("
    SELECT id FROM products
    ORDER BY id DESC
    LIMIT 1 ;
  ");
$idLastProduct->execute();
foreach ($idLastProduct as $item){
    $idProduct = $item;
}

//запись в БД в таблицу producttocategory связи ежду продуктом и категорией
$addProductCategory = connect()->prepare("
    INSERT INTO producttocategory (product_id, category_id)
    VALUES (:product_id, :category_id)
  ");

$addProductCategory->execute(
    [
        ':product_id' => $idProduct['id'],
        ':category_id' => $idCategory
    ]);

