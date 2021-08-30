<? define("NOT_CHECK_FILE_PERMISSIONS", true); ?>
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');
Loader::includeModule('citfact.paolo');

//mobile init
if (!CModule::IncludeModule("mobileapp"))
{
	die();
}
CMobile::Init();
?>
<!DOCTYPE html >
<html class="<?= CMobile::$platform; ?>">
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/script.js"); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/scripts.js"); ?>
	<? $APPLICATION->ShowHead(); ?>
	<? $APPLICATION->IncludeComponent("articul.geolocation.detect_ip", "", array("IBLOCK_CODE" => "city")); ?>
	<link href="/local/templates/paoloconte_mobile/template_styles.css" type="text/css" rel="stylesheet"/>
	<link href="/local/templates/paoloconte_app/default_styles.css" type="text/css" rel="stylesheet"/>
	<link href="/local/templates/paoloconte_mobile/template_styles_dop.css" type="text/css" rel="stylesheet"/>
	<script type="text/javascript" src="/local/templates/paoloconte_mobile/scripts.js" />

	<meta http-equiv="Content-Type" content="text/html;charset=<?= SITE_CHARSET ?>"/>
	<meta name="format-detection" content="telephone=no">
</head>

<script type="text/javascript">
	app.addButtons({menuButton: {
		type:    'basket',
		style:   'custom',
		callback: function()
		{
			app.openNewPage("<?=SITE_DIR?>paoloconte_app/cabinet/basket/");
		}
	}});
</script>

<?
if ($APPLICATION->GetCurPage(true) != SITE_DIR."paoloconte_app/cabinet/basket/index.php")
{
	?>
	<script type="text/javascript">
		app.addButtons({menuButton: {
			type:    'basket',
			style:   'custom',
			callback: function()
			{
				app.openNewPage("<?=SITE_DIR?>paoloconte_app/cabinet/basket/");
			}
		}});
	</script>
	<?
}
?>

<?
//$main_page=true;

if((strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/main') === false) && ($_SERVER['REQUEST_URI'] != "/")){

	$main_page=false;
	$detail_page=false;
	$action_page=false;
	$error_page=false;

	if((strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/detail') === false)){
		$detail_page=true;
	}

	if(!(strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/action-detail') === false)){
		$action_page=true;
	}

	if(!(strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/error') === false)){
		$error_page=true;
	}

} ?>

<?

$left_aside=false;
$catalog_page=false;
$cabinet_page=false;
$sertificate_page=false;

if(!(strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/catalog') === false)) {
	$catalog_page=true;

	//определяем к каталоге карточку товара
	global $catalogMode;
	$catalogMode = 'SECTION';
	// $_REQUEST["CATALOG_CODE"] заполняется в init.php
	if(isset($_REQUEST["CATALOG_CODE"]) && !empty($_REQUEST["CATALOG_CODE"])){
		$arFilter = Array("IBLOCK_ID"=>IBLOCK_CATALOG, "CODE"=>$_REQUEST["CATALOG_CODE"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), Array("ID", "IBLOCK_ID"));
		if($ob = $res->GetNextElement(false, false)){
			$catalogMode = 'ELEMENT';
			$show_title = false;
		}
	}
}

if(!(strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/cabinet') === false)) {
	$cabinet_page=true;
}

if(!(strpos($_SERVER['REQUEST_URI'], '/paoloconte_app/sertificate') === false)) {
	$sertificate_page=true;
}

if ($cabinet_page || $catalog_page || $sertificate_page){
	$left_aside=true;
}
?>

<body <? if ($APPLICATION->GetCurPage() == "/paoloconte_app/menu.php") {?>style="min-width: 250px !important;"<?} ?> <? if ($main_page==true) {?>
	class="mainpage app-overflow"
<?} else { ?>class="insidepage app-overflow" <? } ?> itemscope itemtype="http://schema.org/Organization">
<script type="text/javascript">
	app.pullDown({
		enable: true,
		callback: function ()
		{
			document.location.reload();
		},
		downtext: "<?=GetMessage("MB_PULLDOWN_DOWN")?>",
		pulltext: "<?=GetMessage("MB_PULLDOWN_PULL")?>",
		loadtext: "<?=GetMessage("MB_PULLDOWN_LOADING")?>"
	});


</script>

<div id="wrapper">
	<div id="content-container">
		<main>
			<section class="content">
				<div class="main-content">

					<? if ($main_page==false) {?>

					<? } ?>
					<? if ($cabinet_page === true) {?>
						<?/*<div class="cabinet-aside align-center"></div>*/?>
						<?/*<div class="title align-center"><?$APPLICATION->ShowTitle(false);?></div>*/?>
						<?$APPLICATION->IncludeComponent(
							"citfact:elements.list",
							"menu_cabinet_app",
							Array(
								"IBLOCK_ID" => IBLOCK_MENU_CABINET,
								"PROPERTY_CODES" => array('LINK'),
							)
						);?>
					<?}?>
