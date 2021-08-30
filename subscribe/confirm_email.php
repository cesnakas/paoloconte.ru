<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подтверждение email");?>
<?
	$str_return = '';

	$arOrder = array();
	$arFilter = array('IBLOCK_ID' => IBLOCK_SUBSCRIBE_EMAIL, 'ACTIVE' => 'Y', "PROPERTY_CONFIRMED" => false, "PROPERTY_HASH" => $_GET['confirm_code']);
	$arSelectFields = array("ID", "NAME", "IBLOCK_ID");
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	if($arElement = $rsElements->GetNext())	{
		// Если нашли элемент - проставляем ему подтверждение адреса
		$el = new \CIBlockElement;

		$el->SetPropertyValuesEx($arElement['ID'], IBLOCK_SUBSCRIBE_EMAIL, array('CONFIRMED' => 'Y'));
		$str_return = 'Поздравляем! Вы успешно подтвердили свой email.';
	}
	else{
		$str_return = 'Ошибка! Ваш email уже был подтвержден или не найден.';
	}
?>
<div class="container">
	<?=$str_return?>
<div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>