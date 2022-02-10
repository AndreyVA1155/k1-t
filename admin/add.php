<?php

namespace functionAll;

session_start();
$title = 'Добавление товара';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/admin' . '/ajax/addProduct.php';
?>

<main class="page-add">
  <h1 class="h h--1">Добавление товара</h1>
  <form id="addProduct" class="custom-form" action="/" method="post"  enctype="multipart/form-data">
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
      <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
        <input type="text" class="custom-form__input" name="product-name" id="product-name">
        <p class="custom-form__input-label">
          Название товара
        </p>
      </label>
      <label for="product-price" class="custom-form__input-wrapper">
        <input type="text" class="custom-form__input" name="product-price" id="product-price">
        <p class="custom-form__input-label">
          Цена товара
        </p>
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
      <legend class="page-add__small-title custom-form__title">Раздел</legend>
      <div class="page-add__select">
        <select name="category" class="custom-form__select" multiple="multiple">
          <option hidden="">Название раздела</option>
          <option value="Женщины">Женщины</option>
          <option value="Мужчины">Мужчины</option>
          <option value="Дети">Дети</option>
          <option value="Аксессуары">Аксессуары</option>
        </select>
      </div>
      <input type="checkbox" name="new" id="new" class="custom-form__checkbox">
      <label for="new" class="custom-form__checkbox-label">Новинка</label>
      <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox">
      <label for="sale" class="custom-form__checkbox-label">Распродажа</label>
    </fieldset>
    <button id="buttonAddProduct" class="button" type="submit" name="send">Добавить товар</button>
  </form>
  <section class="shop-page__popup-end page-add__popup-end" hidden="">
    <div class="shop-page__wrapper shop-page__wrapper--popup-end">
      <h2 class="h h--1 h--icon shop-page__end-title">Товар успешно добавлен</h2>
    </div>
  </section>
</main>

<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/footer.php';
