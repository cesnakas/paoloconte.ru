<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Моя скидка");
?>
<?
$APPLICATION->IncludeComponent(
    "citfact:loyalty_card.page",
    "check_card_desktop",
    Array(
        "COMPONENT_TEMPLATE" => "check_card_desktop",
        "ONLY_CHECK" => "Y",
    )
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>