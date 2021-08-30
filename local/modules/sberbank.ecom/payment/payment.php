<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_class.php');

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/local/modules/sberbank.ecom/config.php");


$test_mode = (CSalePaySystemAction::GetParamValue("TEST_MODE") == 'Y') ? true : false;
$two_stage = (CSalePaySystemAction::GetParamValue("TWO_STAGE") == 'Y') ? true : false;
$logging = (CSalePaySystemAction::GetParamValue("LOGGING") == 'Y') ? true : false;

global $USER;
if ($USER->IsAdmin()) {
    $two_stage = true;
}

require_once("rbs.php");
$rbs = new RBS(CSalePaySystemAction::GetParamValue("USER_NAME"), CSalePaySystemAction::GetParamValue("PASSWORD"), $two_stage, $test_mode, $logging);


$app = \Bitrix\Main\Application::getInstance();
$request = $app->getContext()->getRequest();

$order_number = CSalePaySystemAction::GetParamValue("ORDER_NUMBER");
$entityId = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");

if (CUpdateSystem::GetModuleVersion('sale') <= "16.0.11") {
    $orderId = $order_number;
} else {
    list($orderId, $paymentId) = \Bitrix\Sale\PaySystem\Manager::getIdsByPayment($entityId);
}

if (!$order_number)
    $order_number = $orderId;
if (!$order_number)
    $order_number = $GLOBALS['SALE_INPUT_PARAMS']['ID'];
if (!$order_number)
    $order_number = $_REQUEST['ORDER_ID'];

$arOrder = CSaleOrder::GetByID($orderId);

$currency = $arOrder['CURRENCY'];

$amount = CSalePaySystemAction::GetParamValue("AMOUNT");


if (is_float($amount)) {
    $amount = intval($amount);
}

$amount = $amount * 100;


$return_url = 'https://' . $_SERVER['SERVER_NAME'] . '/sale/payment/result.php?ID=' . $order_number;


$FISCALIZATION = COption::GetOptionString("sberbank.ecom", "FISCALIZATION", serialize(array()));
$FISCALIZATION = unserialize($FISCALIZATION);

$arFiscal = array(
    'orderBundle' => array(
        'orderCreationDate' => strtotime($arOrder['DATE_INSERT']),
        'customerDetails' => array(
            'email' => false,
            'contact' => false,
        ),
        'cartItems' => array(
            'items' => array(),
        ),
    ),
    'taxSystem' => $FISCALIZATION['TAX_SYSTEM']
);

$db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);

while ($props = $db_props->Fetch()) {
    if ($props['IS_PAYER'] == 'Y') {
        $arFiscal['orderBundle']['customerDetails']['contact'] = $props['VALUE'];
    } elseif ($props['IS_EMAIL'] == 'Y') {
        $arFiscal['orderBundle']['customerDetails']['email'] = $props['VALUE'];
    }
}

if (!$arFiscal['orderBundle']['customerDetails']['email'] || !$arFiscal['orderBundle']['customerDetails']['contact']) {
    global $USER;

    if (!$arFiscal['orderBundle']['customerDetails']['email']) {
        $arFiscal['orderBundle']['customerDetails']['email'] = $USER->GetEmail();
    }
    if (!$arFiscal['orderBundle']['customerDetails']['contact']) {
        $arFiscal['orderBundle']['customerDetails']['contact'] = $USER->GetFullName();
    }
}

$measureList = array();
$dbMeasure = CCatalogMeasure::getList();
while ($arMeasure = $dbMeasure->GetNext()) {
    $measureList[$arMeasure['ID']] = $arMeasure['MEASURE_TITLE'];
}

$vatList = array();
$dbRes = CCatalogVat::GetListEx(
    array(),
    array(),
    false,
    false,
    array()
);
while ($arRes = $dbRes->Fetch()) {
    $vatList[$arRes['ID']] = $arRes['RATE'];
}

$vatGateway = unserialize(COption::GetOptionString("sberbank.ecom", "VAT_LIST", serialize(array())));
$vatDeliveryGateway = unserialize(COption::GetOptionString("sberbank.ecom", "VAT_DELIVERY_LIST", serialize(array())));

$itemsCnt = 0;
$arCheck = null;

if ($arOrder['PRICE_DELIVERY'] > 0)
    $productsAmount = $amount - ($arOrder['PRICE_DELIVERY'] * 100);
else
    $productsAmount = $amount;

$dbRes = CSaleBasket::GetList(array(), array('ORDER_ID' => $orderId));
$tmpArProducts = [];
$tempSum = 0;
while ($arRes = $dbRes->Fetch()) {
    $tmpArProducts[$itemsCnt] = $arRes;
    $tmpArProducts[$itemsCnt]['arProduct'] = CCatalogProduct::GetByID($arRes['PRODUCT_ID']);

    $taxType = $tmpArProducts[$itemsCnt]['arProduct']['VAT_ID'];
    $tmpArProducts[$itemsCnt]['taxTypeValue'] = 0;
    foreach ($vatGateway as $gatewayVatId => $arSiteVat) {
        if (is_array($arSiteVat) && !empty($arSiteVat) && in_array($taxType, $arSiteVat)) {
            $tmpArProducts[$itemsCnt]['taxTypeValue'] = intval($gatewayVatId);
            break;
        }
    }
    $tmpArProducts[$itemsCnt]['itemAmount'] = $arRes['PRICE'] * 100;
    if ($tmpArProducts[$itemsCnt]['itemAmount'] % 1) {
        $tmpArProducts[$itemsCnt]['itemAmount'] = round($tmpArProducts[$itemsCnt]['itemAmount']);
    }

    $tempSum += ($tmpArProducts[$itemsCnt]['itemAmount']);
    $tmpArProducts[$itemsCnt]['itemsCnt'] = $itemsCnt + 1;
    $itemsCnt++;
}

$tempSUM2 = 0;
$discountVal = $tempSum - $productsAmount;
for ($i = 0; $i < $itemsCnt-1; $i++) {
    $percentageOfTotal = ($tmpArProducts[$i]['itemAmount'] * 100) / $tempSum;
    $discount = ceil(($percentageOfTotal / 100) * $discountVal);
    $tmpArProducts[$i]['itemAmount'] = ($tmpArProducts[$i]['itemAmount'] - $discount);
    $tempSUM2 += $tmpArProducts[$i]['itemAmount'];

    $arFiscal['orderBundle']['cartItems']['items'][] = array(
        'positionId' => $tmpArProducts[$i]['itemsCnt'],
        'name' => $tmpArProducts[$i]['NAME'],
        'quantity' => array(
            'value' => 1,
            'measure' => $measureList[$tmpArProducts[$i]['arProduct']['MEASURE']],
        ),
        'itemAmount' => $tmpArProducts[$i]['itemAmount'],
        'itemCode' => $tmpArProducts[$i]['PRODUCT_ID'],
        'itemPrice' => $tmpArProducts[$i]['itemAmount'],
        'tax' => array(
            'taxType' => $tmpArProducts[$i]['taxTypeValue'],
        ),
    );
}

$tmpArProducts[$itemsCnt-1]['itemAmount'] = ($productsAmount - $tempSUM2);
$tempSUM2 += $tmpArProducts[$itemsCnt-1]['itemAmount'];
$arFiscal['orderBundle']['cartItems']['items'][] = array(
    'positionId' => $tmpArProducts[$itemsCnt-1]['itemsCnt'],
    'name' => $tmpArProducts[$itemsCnt-1]['NAME'],
    'quantity' => array(
        'value' => 1,
        'measure' => $measureList[$tmpArProducts[$itemsCnt-1]['arProduct']['MEASURE']],
    ),
    'itemAmount' => $tmpArProducts[$itemsCnt-1]['itemAmount'],
    'itemCode' => $tmpArProducts[$itemsCnt-1]['PRODUCT_ID'],
    'itemPrice' => $tmpArProducts[$itemsCnt-1]['itemAmount'],
    'tax' => array(
        'taxType' => $tmpArProducts[$itemsCnt-1]['taxTypeValue'],
    ),
);
//echo '<pre>';
//print_r($itemsCnt . "\n");
//print_r($amount . "\n");
//print_r($tempSUM2 . "\n");
//print_r($arFiscal);
//die();

if ($arOrder['PRICE_DELIVERY'] > 0) {
    $arDelivery = CSaleDelivery::GetByID($arOrder['DELIVERY_ID']);

    $taxType = $arDelivery['VAT_ID'];
    $taxTypeValue = 0;
    foreach ($vatDeliveryGateway as $gatewayVatId => $arSiteVat) {
        if (is_array($arSiteVat) && !empty($arSiteVat) && in_array($taxType, $arSiteVat)) {
            $taxTypeValue = intval($gatewayVatId);
            break;
        }
    }

    $arFiscal['orderBundle']['cartItems']['items'][] = array(
        'positionId' => ++$itemsCnt,
        'name' => GetMessage('RBS_PAYMENT_DELIVERY_TITLE'),
        'quantity' => array(
            'value' => 1,
            'measure' => GetMessage('RBS_PAYMENT_MEASURE_DEFAULT'),
        ),
        'itemAmount' => intval($arOrder['PRICE_DELIVERY'] * 100),
        'itemCode' => $arOrder['ID'] . "_DELIVERY",
        'itemPrice' => intval($arOrder['PRICE_DELIVERY'] * 100),
        'tax' => array(
            'taxType' => $taxTypeValue,
        ),
    );
}
for ($i = 0; $i <= 10; $i++) {
    $response = $rbs->register_order($order_number . '_' . $i, $amount, $return_url, $currency, $arOrder['USER_DESCRIPTION'], $arFiscal);

    if ($response['errorCode'] != 1) {
        break;
    }
} ?>


<div class="sale-paysystem-wrapper" <? if (!$response['errorCode']) { ?>style="display: none" <? } ?>>
    <? if (in_array($response['errorCode'], array(999, 1, 2, 3, 4, 5, 7, 8))) {
        $error = GetMessage('RBS_PAYMENT_PAY_ERROR_NUMBER') . ' ' . $response['errorCode'] . ': ' . $response['errorMessage'];
        ?><span><?= $error ?></span><?
    } elseif ($response['errorCode'] == 0) {
        $_SESSION['ORDER_NUMBER'] = $order_number;
        $arUrl = parse_url($response['formUrl']);
        parse_str($arUrl['query'], $arQuery);
        ?>
        <span><?= GetMessage('RBS_PAYMENT_PAY_SUM') ?><?= CurrencyFormat(CSalePaySystemAction::GetParamValue("AMOUNT"), $currency) ?></span>
        <form action="<?= $response['formUrl'] ?>" method="get">
            <? foreach ($arQuery as $key => $value) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
            <? } ?>

            <div class="sale-paysystem-button-container">
                <div class="sale-paysystem-yandex-button">
                    <input class="btn sale-paysystem-yandex-button-item"
                           value="<?= GetMessage('RBS_PAYMENT_PAY_BUTTON') ?>"
                           type="submit"/>
                    <span class="sale-paysystem-yandex-button-descrition">
				        <?= GetMessage('RBS_PAYMENT_PAY_REDIRECT') ?>
                    </span>
                </div>
            </div>

            <p>
			<span class="tablebodytext sale-paysystem-description">
                <?= GetMessage('RBS_PAYMENT_PAY_DESCRIPTION') ?>
			</span>
            </p>
        </form>
    <? } else {
        $error = GetMessage('RBS_PAYMENT_PAY_ERROR');
        ?><span><?= $error ?></span>
    <? } ?>
</div>

<script>
    if (document.forms[0]) {
        document.write('Перенаправление на систему оплаты...');
        document.forms[0].target = '';
        document.forms[0].submit();
    }
</script>
