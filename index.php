<?php

namespace functionAll;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles' . '/function.php';
$title = 'Главная страница';
$where ='WHERE 1 ';

if ($_GET['auth'] == '0') {
    session_destroy();
    header("Location: /");
}

if (!empty($_GET['categories'])) {
    $categories = $_GET['categories'];
    $where .= 'AND categories.id = ' . intval($categories);
    $title = getTitle($categories)[0]['category'];
    $allProductsCategory = getAllProductsCategory($categories);

}


if ($_GET['new'] == '1') {
    $where.= 'AND products.status = "новинка"';
    $title = 'Новинки';
    $type = 'new';
    $status = 'новинка';
    $allProductStatus = getAllProductStatus($status);

}
if ($_GET['sale'] == '1') {
    $where.= 'AND products.status = "распродажа"';
    $title = 'Распродажа';
    $type = 'sale';
    $status = 'Распродажа';
    $allProductStatus = getAllProductStatus($status);
}

if (!empty($_GET['maxPrice'])) {
    $where .= ' AND price  >= ' . intval($_GET['minPrice']);
}

if (!empty($_GET['minPrice'])) {
    $where .= ' AND price  < ' . intval($_GET['maxPrice']);
}

$order = ' ORDER BY price DESC';
if ($_GET['sort']) {
    $order = 'ORDER BY' . $_GET['sort'];
}

if ($_GET['order']) {
    $order = $_GET['sort'];
}

$page = ' LIMIT 9';
if (!empty($_GET['page'])) {
    $page = ' LIMIT 9 OFFSET ' . (($_GET['page'] - 1) * 9 );
}

$sth = connect()->prepare(
    "SELECT * FROM products
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    " . $where . $order . $page);
$sth->execute();

$sthCount = connect()->prepare(
    "SELECT COUNT(*) FROM products 
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    " . $where . $order . $page);
$sthCount->execute();

$sthMax = connect()->prepare(
    "SELECT MAX(price) FROM products
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    " . $where . $order . $page);
$sthMax->execute();
$priceToMax = intval($sthMax->fetchAll()[0]['MAX(price)'] ?? '');

$sthMin = connect()->prepare(
    "SELECT MIN(price) FROM products
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    " . $where . $order . $page);
$sthMin->execute();

$priceFromMin = intval($sthMin->fetchAll()[0]['MIN(price)'] ?? '');

$products = $sth->fetchAll();
$count = count($products);

if (isset($_GET['page'])) {
    $count = count(array_map('count', $products)); //число продуктов на странице

    $priceArray = [];
    foreach ($products as $item) { //получение массива с ценами
        $priceArray[] = $item['price'];
    }
} else {
    foreach ($products as $item) {
        $priceArray[] = $item['price'];
    }
}

$priceFrom = $_GET['minPrice'] ?? '';
$priceTo = $_GET['maxPrice'] ?? '';

if (!isset($_GET['minPrice']) && !isset($_GET['maxPrice'])) {

    $min = null;
    $min_key = null;
    $max = null;
    $max_key = null;

    for($i = 0; $i < count($priceArray); $i++)
    {
        if($priceArray[$i] > $max or $max === null) {
            $max = $priceArray[$i];
            $max_key = $i;
        }

        if($priceArray[$i] < $min or $min === null) {
            $min = $priceArray[$i];
            $min_key = $i;
        }
    }
    $priceFrom = $min;
    $priceTo = $max;
} else {
    $priceFrom = $_GET['minPrice'];
    $priceTo = $_GET['maxPrice'];
}

if (isset($allProductsCategory)) {
    $numberPages = ($allProductsCategory % 9) +1;
} elseif (isset($allProductStatus)) {
    $numberPages = ($allProductStatus % 9) +1;
} else {
    $allProducts = getAllProducts();
    $countAllProducts = intval($allProducts["COUNT(*)"]); // колчество товаров
    $numberPages = (intdiv($countAllProducts, 9)) + 1;
}
if (isset($_GET['categories'])) {
    $cat = $_GET['categories'];
}

$data = array(
    'minPrice' => $priceFrom,
    'maxPrice' => $priceTo,
    $type => '1',
    'categories' => $cat
);
$urls = '/?' .  http_build_query($data);

//var_dump($numberPages);
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';
?>
    <main class="shop-page">
        <header class="intro">
            <div class="intro__wrapper">
                <h1 class=" intro__title">COATS</h1>
                <p class="intro__info">Collection 2018</p>
            </div>
        </header>
        <section class="shop container">
            <section class="shop__filter filter">
                <form action="<?=$urls ?>" method="get">
                    <input id="minPriceHidden" class="hidden" name="minPrice" value="">
                    <input id="maxPriceHidden" class="hidden" name="maxPrice" value="">
                    <?php if(isset($_GET['categories'])) {?>
                    <input id="idCategories" class="hidden" name="<?= $_GET['categories']?>" value="<?= $_GET['categories']?>">
                    <?php } ?>
                    <div
                        class='hidden'
                        data-min='<?= $priceFromMin ?>'
                        data-max='<?= $priceToMax ?>'
                    ></div>
                    <div class="filter__wrapper">
                        <b class="filter__title">Категории</b>
                        <ul class="filter__list">
                            <?php if ($_SERVER['REQUEST_URI'] == '/') { ?>
                            <li>
                                <a class="filter__list-item active" href="/">Все</a>
                            </li>
                            <?php } else { ?>
                            <li>
                                <a class="filter__list-item" href="/">Все</a>
                            </li>
                            <?php } ?>
                            <?php foreach (categories() as $item): ?>
                                <?php if ($_SERVER['REQUEST_URI'] == $item['path']) { ?>
                                    <li>
                                        <a id="<?=$item['id'] ?>" class="filter__list-item active" href="<?=$item['path']?>"><?=$item['category']?></a>
                                    </li>
                                <?php } else { ?>
                                <li>
                                    <a id="<?=$item['id'] ?>" class="filter__list-item" href="<?=$item['path']?>"><?=$item['category']?></a>
                                </li>
                                <?php } ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="filter__wrapper">
                        <b class="filter__title">Фильтры</b>
                        <div class="filter__range range">
                            <span class="range__info">Цена</span>
                            <div class="range__line" aria-label="Range Line"></div>
                            <div class="range__res">
                                <span id="minPrice" class="range__res-item min-price"><?=$priceFrom?>.руб</span>
                                <span id="maxPrice" class="range__res-item max-price"><?=$priceTo?>.руб</span>
                            </div>
                        </div>
                    </div>
                    <fieldset class="custom-form__group">
                        <input type="checkbox" name="new" id="new" class="custom-form__checkbox" value="1" <?php $new = $_GET['new'] ?? ''; if($new == '1') { echo 'checked';} ?>>
                        <label for="new" class="custom-form__checkbox-label custom-form__info" style="display: block;">Новинка</label>
                        <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox"  value="1" <?php $sale = $_GET['sale'] ?? '';  if($sale == '1') { echo 'checked';} ?>>
                        <label for="sale" class="custom-form__checkbox-label custom-form__info" style="display: block;">Распродажа</label>
                    </fieldset>
                    <button id="buttonIndex" class="button" type="submit" style="width: 100%" >Применить</button>
                </form>
            </section>

            <div class="shop__wrapper">
                <section class="shop__sorting">
                    <div class="shop__sorting-item custom-form__select-wrapper">
                        <select class="custom-form__select" name="category">
                            <option value="sort" hidden="">Сортировка</option>
                            <option value="price">По цене </option>
                            <option value="name">По названию </option>
                        </select>
                    </div>

                    <div class="shop__sorting-item custom-form__select-wrapper">
                        <select class="custom-form__select" name="prices">
                            <option hidden="">Порядок</option>
                            <option value="all">По возрастанию</option>
                            <option value="woman">По убыванию</option>
                        </select>
                    </div>
                    <p class="shop__sorting-res">Найдено <span class="res-sort"><?=$count?></span> моделей</p>
                </section>
                <section class="shop__list">
                    <?php foreach ($products as $product): ?>
                    <article class="shop__item product" tabindex="0">
                        <div class="product__image">
                            <img src="<?=$product['imp_path']?>" alt="<?=$product['img']?>">
                        </div>
                        <p class="product__name"><?=$product['name']?></p>
                        <span id="priceProduct" class="product__price"><?=$product['price']?> .руб</span>
                    </article>
                    <?php endforeach ?>
                </section>
                <ul class="shop__paginator paginator">
                    <?php for($i = 1; $i <= $numberPages; $i++) {?>
                        <a class="paginator__item" href="/?page=<?=$i?>
                        <?php if (isset($_SESSION['categories']) && !isset($_GET['categories'])) { echo '&categories=' . $_SESSION['categories'];}?>"><?= $i ?></a>
                        <?php if (isset($_SESSION['new']) && !isset($_GET['new'])) { echo '&new=' . $_SESSION['new'];?>"><?php echo $i; } ?></a>
                        <?php if (isset($_SESSION['sale']) && !isset($_GET['sale'])) { echo '&sale=' . $_SESSION['sale'];?>"><?php echo $i; } ?></a>
                    <?php } ?>
                </ul>
            </div>
        </section>
        <section class="shop-page__order" hidden="">
            <div class="shop-page__wrapper">
                <h2 class="h h--1">Оформление заказа</h2>
                <form id="delivery" action="<?= $urls?>"  class="custom-form js-order" name="addOrder">
                    <fieldset class="custom-form__group">
                        <legend class="custom-form__title">Укажите свои личные данные</legend>
                        <p class="custom-form__info">
                            <span class="req">*</span> поля обязательные для заполнения
                        </p>
                        <div class="custom-form__column">
                            <label class="custom-form__input-wrapper" for="surname">
                                <input id="surname" class="custom-form__input" type="text" name="surname" required="">
                                <p class="custom-form__input-label">Фамилия <span class="req">*</span></p>
                            </label>
                            <label class="custom-form__input-wrapper" for="name">
                                <input id="name" class="custom-form__input" type="text" name="name" required="">
                                <p class="custom-form__input-label">Имя <span class="req">*</span></p>
                            </label>
                            <label class="custom-form__input-wrapper" for="thirdName">
                                <input id="thirdName" class="custom-form__input" type="text" name="thirdName">
                                <p class="custom-form__input-label">Отчество</p>
                            </label>
                            <label class="custom-form__input-wrapper" for="phone">
                                <input id="phone" class="custom-form__input" type="tel" name="tel" required="">
                                <p class="custom-form__input-label">Телефон <span class="req">*</span></p>
                            </label>
                            <label class="custom-form__input-wrapper" for="email">
                                <input id="email" class="custom-form__input" type="email" name="email" required="">
                                <p class="custom-form__input-label">Почта <span class="req">*</span></p>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="custom-form__group js-radio">
                        <legend class="custom-form__title custom-form__title--radio">Способ доставки</legend>
                        <input id="dev-no" class="custom-form__radio" type="radio" name="delivery" value="0" checked="">
                        <label for="dev-no" class="custom-form__radio-label">Самовывоз</label>
                        <input id="dev-yes" class="custom-form__radio" type="radio" name="delivery" value="1">
                        <label for="dev-yes" class="custom-form__radio-label">Курьерная доставка</label>
                    </fieldset>
                    <div class="shop-page__delivery shop-page__delivery--no">
                        <table class="custom-table">
                            <caption class="custom-table__title">Пункт самовывоза</caption>
                            <tr>
                                <td class="custom-table__head">Адрес:</td>
                                <td>Москва г, Тверская ул,<br> 4 Метро «Охотный ряд»</td>
                            </tr>
                            <tr>
                                <td class="custom-table__head">Время работы:</td>
                                <td>пн-вс 09:00-22:00</td>
                            </tr>
                            <tr>
                                <td class="custom-table__head">Оплата:</td>
                                <td>Наличными или банковской картой</td>
                            </tr>
                            <tr>
                                <td class="custom-table__head">Срок доставки: </td>
                                <td class="date">13 декабря—15 декабря</td>
                            </tr>
                        </table>
                    </div>
                    <div class="shop-page__delivery shop-page__delivery--yes" hidden="">
                        <fieldset class="custom-form__group">
                            <legend class="custom-form__title">Адрес</legend>
                            <p class="custom-form__info">
                                <span class="req">*</span> поля обязательные для заполнения
                            </p>
                            <div class="custom-form__row">
                                <label class="custom-form__input-wrapper" for="city">
                                    <input id="city" class="custom-form__input" type="text" name="city">
                                    <p class="custom-form__input-label">Город <span class="req">*</span></p>
                                </label>
                                <label class="custom-form__input-wrapper" for="street">
                                    <input id="street" class="custom-form__input" type="text" name="street">
                                    <p class="custom-form__input-label">Улица <span class="req">*</span></p>
                                </label>
                                <label class="custom-form__input-wrapper" for="home">
                                    <input id="home" class="custom-form__input custom-form__input--small" type="text" name="home">
                                    <p class="custom-form__input-label">Дом <span class="req">*</span></p>
                                </label>
                                <label class="custom-form__input-wrapper" for="apartment">
                                    <input id="aprt" class="custom-form__input custom-form__input--small" type="text" name="apartment">
                                    <p class="custom-form__input-label">Квартира <span class="req">*</span></p>
                                </label>
                            </div>
                        </fieldset>
                    </div>
                    <fieldset class="custom-form__group shop-page__pay">
                        <legend class="custom-form__title custom-form__title--radio">Способ оплаты</legend>
                        <input id="cash" class="custom-form__radio" type="radio" name="pay" value="0">
                        <label for="cash" class="custom-form__radio-label">Наличные</label>
                        <input id="card" class="custom-form__radio" type="radio" name="pay" value="1" checked="">
                        <label for="card" class="custom-form__radio-label">Банковской картой</label>
                    </fieldset>
                    <fieldset class="custom-form__group shop-page__comment">
                        <legend class="custom-form__title custom-form__title--comment">Комментарии к заказу</legend>
                        <textarea class="custom-form__textarea" name="comment"></textarea>
                    </fieldset>
                    <button id="buttonAddOrder" class="button" type="submit">Отправить заказ</button>
                </form>
            </div>
        </section>
        <section class="shop-page__popup-end" hidden="">
            <div class="shop-page__wrapper shop-page__wrapper--popup-end">
                <h2 class="h h--1 h--icon shop-page__end-title">Спасибо за заказ!</h2>
                <p class="shop-page__end-message">Ваш заказ успешно оформлен, с вами свяжутся в ближайшее время</p>
                <button id="buttonContinueShopping" class="button">Продолжить покупки</button>
            </div>
        </section>
    </main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles' . '/footer.php';

