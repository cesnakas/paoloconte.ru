<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
Loader::includeModule('sale');

$arReturn = array(
    'ERROR_FIELDS' => array(),
    'ERROR_TEXT' => array(),
    'RESULT' => array()
);

try {
    CBitrixComponent::includeComponentClass("citfact:form.fast.order");

    $post = \Bitrix\Main\Application::getInstance()->getContext()->getCurrent()->getRequest()->getPostList()->toArray();

    $order = new FormFastOrder;
    $order->saveOrder($post);
    $arReturn['ERROR_FIELDS'] = $order->getNotValidFields();
    $arReturn['ERROR_TEXT'] = $order->getFirstError();
    if (intval($order->getOrderID()) > 0) {
        $arReturn['RESULT'] = array(
            'SUCCESS' => true,
            'REDIRECT' => $order->getRedirect(),
            'ORDER_ID' => $order->getOrderID()
        );
    }
} catch (Exception $e) {
    $arReturn['ERROR_TEXT'] = $e->getMessage();
}

$strReturn = json_encode($arReturn);
echo $strReturn;


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
