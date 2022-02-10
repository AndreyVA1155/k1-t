<?php

namespace functionAll;

use PDO;
use PDOException;
require_once $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'config.php';

/**
 * @param string connect функиция создает подключения в БД SQl, connect()
 */
function connect()
{
    static $connect;

    if (empty($connect)) {
        try {
            $connect = new PDO('mysql:host=' . HOST . '; dbname=' . DBNAME,
                USER, PASSWORD);
            if (!$connect->errorInfo()) {
                echo "\nPDO::errorInfo():\n";
                print_r($connect->errorInfo());
                die();
            }
        } catch (PDOException $exception) {
            echo 'нет доступа к базе данных ' . $exception->getMessage();
            exit;
        }

    }
    return $connect;
}

/**
 * @param string функиция categories создает запрос в БД
 * @return array возвращает массив с данными из таблицы categories
 */
function categories()
{
    $sth = connect()->prepare("SELECT * FROM categories");
    $sth->execute();

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string функиция status создает запрос в БД
 * @return array возвращает массив с данными о статусе продукта(новинка, распродажа или нет ничего) из таблицы products
 */
function status($id)
{
    $sth = connect()->prepare("SELECT status FROM products WHERE id =:id");
    $sth->execute([':id' => $id]);

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string функиция max создает запрос в БД на вывод всех товаров из категории "women"
 * @return array возвращает массив с продуктами и их даннымих
 */
function max()
{
    $sth = connect()->prepare("SELECT max(price) FROM products
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    WHERE categories.id = :id");

    $sth->execute();

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string принимает параметр id категории
 * @param string функиция getTitle создает запрос в БД на вывод названия категорий
 * @return array возвращает массив с названиями категорий
 */
function getTitle($id)
{
    $sth = connect()->prepare("
    SELECT category FROM categories
    WHERE categories.id = :id");
    $sth->execute([':id' => $id]);

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string принимает параметр $email введеный пользователем
 * @param string функиция getUser создает запрос в БД на вывод информации о пользователе
 * @return array с данными пользователя из БД
 */
function getUser($email)
{
    $sth = connect()->prepare("
    SELECT * FROM users
    LEFT JOIN users_groups ON users.id = users_groups.user_id
    LEFT JOIN status ON status.id = users_groups.groups_id
    WHERE users.email = :email");
    $sth->execute([':email' => $email]);

    return $sth->fetch();
}

/**
 * @param string функиция menu создает запрос в БД
 * @return array возвращает массив с названиями страниц и ссылками на них
 */
function menu($id)
{
    $sth = connect()->prepare("SELECT `name`, `path` FROM menu WHERE id <= :id ");
    $sth->execute([':id' => $id]);

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string принимает параметр $id из сессии
 * @param string функиция getUserById создает запрос в БД на вывод информации о пользователе
 * @return array с данными пользователя из БД
 */
function getUserById($id)
{
    $sth = connect()->prepare("
    SELECT * FROM users
    LEFT JOIN users_groups ON users.id = users_groups.user_id
    LEFT JOIN status ON status.id = users_groups.groups_id
    WHERE users.id = :id");
    $sth->execute([':id' => $id]);

    return $sth->fetch();
}

/**
 * @param string функиция getAllProducts создает запрос в БД на вывод информации о продуктах
 * @return array с колчеством всех продуктов  в БД
 */
function getAllProducts()
{
    $sth = connect()->prepare(
        "SELECT COUNT(*) FROM products");
    $sth->execute();
    return $sth->fetch();
}

/**
 * @param string функиция getAllProductsCategory создает запрос в БД на вывод информации о количестве продуктах
 * @return array с колчеством всех продуктов определенной категории в БД
 */
function getAllProductsCategory($id)
{
    $sth = connect()->prepare(
    "SELECT COUNT(*) FROM products
    LEFT JOIN producttocategory ON products.id = producttocategory.product_id
    LEFT JOIN categories ON categories.id = producttocategory.category_id
    WHERE categories.id = :id");
    $sth->execute([':id' => $id]);

    return $sth->fetch();
}

/**
 * @param string функиция getAllProductStatus создает запрос в БД на вывод информации о количестве продуктах
 * @return array с колчеством всех продуктов с определегнным статусом в БД
 */
function getAllProductStatus($status)
{
    $sth = connect()->prepare(
    "SELECT COUNT(*) FROM products
    WHERE status = :status");
    $sth->execute([':status' => $status]);

    return $sth->fetch();
}
