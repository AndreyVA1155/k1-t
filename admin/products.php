<?php

namespace functionAll;

session_start();
$title = 'Товары';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';
if (!empty($_GET['page'])) {
    $page = ' LIMIT 10 OFFSET ' . (($_GET['page'] - 1) * 10);
} else {
    $page = ' LIMIT 10';
}

$products = connect()->prepare("
    SELECT products.name, products.id, products.price, categories.category, products.status FROM products
    RIGHT JOIN producttocategory ON products.id = producttocategory.product_id
    RIGHT JOIN categories ON categories.id = producttocategory.category_id 
    ORDER BY id ASC 
  " . $page);
$products->execute();

$numberProducts = getAllProducts();
$numberProducts = $numberProducts["COUNT(*)"];
$numberPages = intdiv($numberProducts, 10) + 1;
?>

<main class="page-products">
  <h1 class="h h--1">Товары</h1>
  <a class="page-products__button button" href="add.php">Добавить товар</a>
  <div class="page-products__header">
    <span class="page-products__header-field">название товара</span>
    <span class="page-products__header-field">ИД товара</span>
    <span class="page-products__header-field">цена товара</span>
    <span class="page-products__header-field">Категория</span>
    <span class="page-products__header-field">Новинка</span>
  </div>
  <ul class="page-products__list">
    <?php foreach ($products as $item): ?>
    <li id="<?=$item['id']?>" class="product-item page-products__item">
      <b class="product-item__name"><?=$item['name']?></b>
      <span data-product-id="<?=$item['id']?>" class="product-item__field"><?=$item['id']?></span>
      <span class="product-item__field"><?=$item['price']?></span>
      <span class="product-item__field"><?=$item['category']?></span>
      <span class="product-item__field"><?php if($item['status'] == 'новинка')  {echo 'новинка';}?></span>
      <a href="changeProducts.php?id=<?=$item['id']?>" class="product-item__edit" aria-label="Редактировать"></a>
      <button data-id="<?=$item['id']?>" class="product-item__delete"></button>
    </li>
      <?php endforeach ?>
  </ul>
  <section>
    <div class="h h--1" >
      <ul class="shop__paginator paginator">
        <li>
          <a class="paginator__item" href="/admin/products.php">1</a>
        </li>
        <li>
            <?php for($i = 2; $i <= $numberPages; $i++) {?>
                <a class="paginator__item" href="/admin/products.php?page=<?= $i ?>"><?= $i ?></a>
            <?php } ?>
        </li>
      </ul>
    </div>
  </section>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/footer.php';
