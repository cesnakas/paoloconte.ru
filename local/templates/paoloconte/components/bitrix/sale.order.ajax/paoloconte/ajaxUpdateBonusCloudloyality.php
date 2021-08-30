<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");

ob_start();
include $_SERVER["DOCUMENT_ROOT"]."/cabinet/basket/basket.php";
$basket = ob_get_clean();

ob_start();
include $_SERVER["DOCUMENT_ROOT"]."/local/templates/paoloconte/components/bitrix/sale.order.ajax/paoloconte/summary.php";
$comment = ob_get_clean();

$result = array('basket' => $basket, 'comment' => $comment);
echo json_encode($result);
?>
