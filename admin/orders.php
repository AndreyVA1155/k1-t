<?php

namespace functionAll;

session_start();
$title = 'Заказы';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';

$orders = connect()->prepare("
    SELECT * FROM orders
    ORDER BY status, data_create DESC
  ");
$orders->execute();

?>

<main class="page-order">
  <h1 class="h h--1">Список заказов</h1>
  <ul class="page-order__list">
    <?php foreach ($orders as $item):?>
    <li class="order-item page-order__item">
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--id">
          <span class="order-item__title">Номер заказа</span>
          <span id="idOrder=<?=$item['id']?>" class="order-item__info order-item__info--id"><?=$item['id']?></span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Сумма заказа</span>
            <?=$item['price']?> руб.
        </div>
        <button class="order-item__toggle"></button>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--margin">
          <span class="order-item__title">Заказчик</span>
          <span class="order-item__info">
              <?php
              echo $item['surname'];
              echo ' ' . $item['name'];
              echo ' ' . $item['thirdName'];
              ?>
          </span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Номер телефона</span>
          <span class="order-item__info"><?=$item['phone']?></span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ доставки</span>
          <span class="order-item__info">
              <?php
              if ($item['delivery'] == 1) {
              echo 'Доставка' . PHP_EOL;
              } else {
              echo 'Самовывоз' . PHP_EOL;
              }
              ?>
          </span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ оплаты</span>
          <span class="order-item__info"><?php if ($item['payment'] == 1) {
                  echo 'Наличными' . PHP_EOL;
              } else {
                  echo 'Оплата картой' . PHP_EOL;
              }?></span>
        </div>
          <div class="order-item__group order-item__group--status">
              <span class="order-item__title">Статус заказа</span>
              <span data-id="<?=$item['id']?>" class=
                    <?php if ($item['status'] === 'notProcessed') {
                        ?>"order-item__info order-item__info--no">
                    <?php
                    } elseif ($item['status'] === 'processed') {
                        ?>"order-item__info order-item__info--yes">
                    <?php
                    }
                    ?>
                    <?php if ($item['status'] === 'notProcessed') {
                        echo 'Заказ не обработан' . PHP_EOL;
                    } elseif ($item['status'] === 'processed') {
                        echo 'Заказ обработан' . PHP_EOL;
                    }
                    ?>
              </span>
              <button class="order-item__btn">Изменить</button>
          </div>
      </div>

    <?php
    if ($item['delivery'] == 1) { ?>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Адрес доставки</span>
          <span class="order-item__info">
            <?php
                echo 'г.' . $item['city'] . '<br>';
                  echo 'ул.' . $item['street'] . '<br>';
                  echo 'д.' . $item['home'] . '<br>';
                  echo 'кв.' . $item['apartment'] . '<br>';
            ?>
          </span>
        </div>
      </div>
    <?php
    }
    ?>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Комментарий к заказу</span>
          <span class="order-item__info"><?=$item['comment']?></div>
      </div>
    </li>
    <?php endforeach ?>
  </ul>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/footer.php';
