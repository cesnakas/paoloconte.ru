<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeModuleLangFile(__FILE__);

session_start();

if (!CModule::IncludeModule('sale')) return;
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/modules/sberbank.ecom/config.php");

$isOrderConverted = \Bitrix\Main\Config\Option::get("main", "~sale_converted_15", 'N');
$errorMessage = '';

if (isset($_GET["orderId"])) {
    $order_id = $_GET["ORDER_ID"];
    $order_number = isset($_SESSION['ORDER_NUMBER']) ? $_SESSION['ORDER_NUMBER'] : $_REQUEST["ID"];

    if (!($arOrder = CSaleOrder::GetList(array(), array('ACCOUNT_NUMBER' => $order_number))->Fetch())) {
        $arOrder = CSaleOrder::GetByID($order_number);
    }
    if (!in_array($arOrder['PAY_SYSTEM_ID'], $SBER_PAY_SYSTEMS_ID)) {
        return;
    }

    $paysystem = new CSalePaySystemAction();
    $paysystem->InitParamArrays($arOrder, $arOrder["ID"]);
    $order_number = $arOrder["ID"];

    $test_mode = (CSalePaySystemAction::GetParamValue("TEST_MODE") == 'Y') ? true : false;
    $two_stage = (CSalePaySystemAction::GetParamValue("TWO_STAGE") == 'Y') ? true : false;
    $logging = (CSalePaySystemAction::GetParamValue("LOGGING") == 'Y') ? true : false;

    global $USER;
    if ($USER->IsAdmin()) {
        $two_stage = true;
    }

    require_once("rbs.php");
    $rbs = new RBS($paysystem->GetParamValue("USER_NAME"), $paysystem->GetParamValue("PASSWORD"), $two_stage, $test_mode, $logging);
    $response = $rbs->get_order_status_by_orderId($_GET["orderId"]);

    if (($response['errorCode'] == 0) && (($response['orderStatus'] == 1) || ($response['orderStatus'] == 2))) {
        $arOrderFields = array(
            "PS_SUM" => $response["amount"] / 100,
            "PS_CURRENCY" => $response["currency"],
            "PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
            "PS_STATUS" => "Y",
            "PS_STATUS_DESCRIPTION" => $response["cardAuthInfo"]["pan"] /*. ";" . $response['cardAuthInfo']["cardholderName"]*/,
            "PS_STATUS_MESSAGE" => $response["paymentAmountInfo"]["paymentState"],
            "PS_STATUS_CODE" => "Y"
        );

        CSaleOrder::PayOrder($order_number, "Y", true, true);

        $rbsOrderId = $_GET["orderId"];
        foreach ($response['attributes'] as $attribute) {
            if ($attribute['name'] == 'mdOrder') {
                $rbsOrderId = $attribute['value'];
            }
        }

        $dbP = CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $order_number, 'ORDER_PROPS_ID' => $PROPERTY_PS_ORDER_ID]);
        if ($arP = $dbP->Fetch()) {
            CSaleOrderPropsValue::Update(
                $arP['ID'],
                array(
                    'ORDER_ID' => $order_number,
                    'ORDER_PROPS_ID' => $PROPERTY_PS_ORDER_ID,
                    'NAME' => 'Платежная система: id заказа',
                    'CODE' => 'PS_ORDER_NUMBER',
                    'VALUE' => $rbsOrderId
                )
            );
        } else {
            CSaleOrderPropsValue::Add(array(
                'ORDER_ID' => $order_number,
                'ORDER_PROPS_ID' => $PROPERTY_PS_ORDER_ID,
                'NAME' => 'Платежная система: id заказа',
                'CODE' => 'PS_ORDER_NUMBER',
                'VALUE' => $rbsOrderId
            ));
        }

        if ($paysystem->GetParamValue("SHIPMENT_ENABLE") == 'Y') {
            if ($isOrderConverted != "Y") {
                CSaleOrder::DeliverOrder($order_number, "Y");
            } else {
                $r = \Bitrix\Sale\Compatible\OrderCompatibility::allowDelivery($order_number, true);
                if (!$r->isSuccess(true)) {
                    foreach ($r->getErrorMessages() as $error) {
                        $errorMessage .= " " . $error;
                    }
                }
            }
        }

        $orderNumberPrint = $paysystem->GetParamValue('ORDER_NUMBER');
        $title = GetMessage('RBS_PAYMENT_ORDER_THANK');

        if ($response['orderStatus'] == 1) {
            $message = GetMessage('RBS_PAYMENT_ORDER_AUTH', array('#ORDER_ID#' => $orderNumberPrint));
        } else {
            $message = GetMessage('RBS_PAYMENT_ORDER_FULL_AUTH', array('#ORDER_ID#' => $orderNumberPrint));
        }

        CSaleOrder::Update($order_number, $arOrderFields);
        header('Location: /sale/payment/result.php?ID=' . $_GET['ID'], true, 301);
    } else if ($response['errorCode'] == 0) {
        $arOrderFields["PS_STATUS_MESSAGE"] = "[" . $response["orderStatus"] . "] " . $response["actionCodeDescription"];
        $title = GetMessage('RBS_PAYMENT_ORDER_PAY', array('#ORDER_ID#' => $orderNumberPrint));
        $message = GetMessage('RBS_PAYMENT_ORDER_STATUS', array('#ORDER_ID#' => $response["orderStatus"], '#DESCRIPTION#' => $response["actionCodeDescription"]));
        CSaleOrder::Update($order_number, $arOrderFields);
    } else {
        $arOrderFields["PS_STATUS_MESSAGE"] = GetMessage('RBS_PAYMENT_ORDER_ERROR', array('#ERROR_CODE#' => $response["errorCode"], '#ERROR_MESSAGE#' => $response["errorMessage"]));
        $title = GetMessage('RBS_PAYMENT_ORDER_PAY', array('#ORDER_ID#' => $orderNumberPrint));
        $message = GetMessage('RBS_PAYMENT_ORDER_ERROR2', array('#ERROR_CODE#' => $response["errorCode"], '#ERROR_MESSAGE#' => $response["errorMessage"]));
        CSaleOrder::Update($order_number, $arOrderFields);
    }
} else if (isset($_GET["ID"])) {
    $title = GetMessage('RBS_PAYMENT_ORDER_THANK');
    $message = GetMessage('RBS_PAYMENT_ORDER_PAY1', array('#ORDER_ID#' => $_GET["ID"]));
    LocalRedirect("/cabinet/basket/index.php?ORDER_ID=".$_GET["ID"]);
} else {
    $title = GetMessage('RBS_PAYMENT_ORDER_ERROR3');
    $message = GetMessage('RBS_PAYMENT_ORDER_NOT_FOUND', array('#ORDER_ID#' => htmlspecialchars(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('ORDER_ID'), ENT_QUOTES)));
}

$APPLICATION->SetTitle($title);
?>

<div class="container">
    <? echo $message; ?>
</div>