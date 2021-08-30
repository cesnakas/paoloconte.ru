<? require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");

if(empty($_POST['STORE'])){
    $result = [
        'result' => false,
        'message' => 'Не указан магазин',
        'ids' => []
    ];
    echo json_encode($result);
    exit();
}

$success = true; // все товары в наличии
$goodsOutStock = []; // товары не в наличии

$rsStore = CCatalogStoreProduct::GetList(
    array(),
    array(
        'PRODUCT_ID' => $_SESSION["GOODS_DELIVERY_STORE"],
        "STORE_ID" => $_POST['STORE']
    ),
    false, false, array());
while ($arStore = $rsStore->Fetch()) {
    if ($success && $arStore["AMOUNT"] <= 0) {
        $success = false;
    }
    $goodsOutStock[$arStore['PRODUCT_ID']] = $arStore["AMOUNT"] > 0;
}
$result = [
    'result' => $success,
    'message' => '',
    'ids' => $goodsOutStock
];

echo json_encode($result);
exit();