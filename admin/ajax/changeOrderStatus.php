<?php

namespace functionAll;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';

if ($_POST['statusValue'] == 1) {
    $chancheOrderStatus = connect()->prepare("
    UPDATE orders SET  status = 'processed'
    WHERE id = :id
  ");
    $chancheOrderStatus->execute([':id' => $_POST['id']]);
} elseif ($_POST['statusValue'] == 0) {
    $chancheOrderStatus = connect()->prepare("
    UPDATE orders SET  status = 'notProcessed'
    WHERE id = :id
  ");
    $chancheOrderStatus->execute([':id' => $_POST['id']]);
}