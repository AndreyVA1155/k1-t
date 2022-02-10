<?php

namespace functionAll;

session_start();
$title = 'Изменение товара';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';

$id = (int)$_GET['id'];

if (!empty($_FILES)) {
    //загрузка файла на сервер
    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/img/products/';
    foreach ($_FILES as $item) {
        $name = $item['name'];
        move_uploaded_file($item['tmp_name'], "$uploaddir/$name");
    }

//определине имени файла загрузки и путь к нему
    $strName = explode('.', $name);
    $nameProoductImg = $strName[0];
    $path = 'img/products/' . $name;
}

//обновление информации в БД
if (isset($_POST['send'])) {
    $updateProducts = connect()->prepare("
    UPDATE products 
    SET 
        name = :name,
        description = :description,
        price = :price,
        count = :count,
        status = :status,
        img = :img,
        imp_path = :imp_path
    WHERE products.id = :id;
  ");
    $updateProducts->execute(
        [
            ':id' => $id,
            ':name' => $_POST['product-name'],
            ':description' => $_POST['product-description'],
            ':price' => $_POST['product-price'],
            ':count' => $_POST['product-count'],
            ':status' => $_POST['status'],
            ':img' => $nameProoductImg,
            ':imp_path' => $path
        ]);
$deleteProductToCategory = connect()->prepare("
    DELETE FROM producttocategory
    WHERE product_id = :id
  ");
$deleteProductToCategory->execute([':id' => $id]);

if (isset($_POST['section'])) {
    $idCategory = $_POST['section'];
} else {
    $idCategory = '';
}

$insertProductToCategory = connect()->prepare("
    INSERT INTO producttocategory (product_id, category_id)
    VALUES (:product_id, :category_id);
  ");
$insertProductToCategory->execute([':product_id' => $id, ':category_id' => $idCategory]);
}


require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';


//получение данных о товаре из БД
$products = connect()->prepare("
    SELECT * FROM products
    RIGHT JOIN producttocategory ON products.id = producttocategory.product_id
    RIGHT JOIN categories ON categories.id = producttocategory.category_id
    WHERE products.id = :id
  ");
$products->execute([':id' => $id]);

$category = categories();
$status = status($id);

?>
    <main class="page-add">
        <h1 class="h h--1">Изменение товара</h1>
        <form id="addProduct" class="custom-form" action="/admin/changeProducts.php?id=<?= $id ?>" method="post"
              enctype="multipart/form-data">
            <fieldset class="page-add__group custom-form__group">
                <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
                <?php foreach ($products as $item): ?>
                <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
                    название товара
                    <br>
                    <input type="text" class="custom-form__input" name="product-name" value="<?= $item['name'] ?>"
                           id="product-name">
                    <br><br>
                    Описание товара
                    <br>
                    <input type="text" class="custom-form__input" name="product-description"
                           value="<?= $item['description'] ?>" id="product-description">
                </label>
                <label for="product-price" class="custom-form__input-wrapper">
                    <br><br>
                    цена товара
                    <br>
                    <input type="text" class="custom-form__input" name="product-price" value="<?= $item['price'] ?>"
                           id="product-price">
                    <br><br>
                    количество
                    <br>
                    <input type="text" class="custom-form__input" name="product-count" value="<?= $item['count'] ?>"
                           id="product-count">
                </label>
            </fieldset>
            <fieldset class="page-add__group custom-form__group">
                <legend class="page-add__small-title custom-form__title">Фотография товара</legend>
                <ul class="add-list">
                    <li class="add-list__item add-list__item--add">
                        <input type="file" name="product-photo" id="product-photo" class="product-photo" hidden="">
                        <label for="product-photo">Добавить фотографию</label>
                    </li>
                </ul>
            </fieldset>
            <fieldset class="page-add__group custom-form__group">
                <legend class="page-add__small-title custom-form__title">Категория и статус товара</legend>
                <div class="page-add__select">
                    <select class="custom-form__select" name="section">Категория
                        <?php
                        foreach ($category as $key => $value) {
                            echo '<option value="' . $value['id'] . '">' . $value['category'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <select class="custom-form__select" ; name="status">
                    <?php
                    foreach ($status as $key => $value) {
                        if ($value['status'] == 'распродажа') {
                            echo '<option selected value="' . $value['id'] . '">' . $value['status'] . '</option>';
                            echo '<option>новинка</option>';
                            echo '<option>нет статуса</option>';
                        } elseif ($value['status'] == 'новинка') {
                            echo '<option selected value="' . $value['id'] . '">' . $value['status'] . '</option>';
                            echo '<option>распродажа</option>';
                            echo '<option>нет статуса</option>';
                        } else {
                            echo '<option selected value="' . $value['id'] . '">' . $value['status'] . '</option>';
                            echo '<option>новинка</option>';
                            echo '<option>распродажа</option>';
                        }
                    }
                    ?>
                </select>
            </fieldset>
            <?php endforeach ?>
            <button id="changeProduct" class="button" type="submit" name="send">изменить товар</button>
        </form>
    </main>
    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/footer.php';
