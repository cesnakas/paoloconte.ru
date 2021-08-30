<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>

<table>
	<tr>
		<td>
		</td>
		<td>
		</td>
	</tr>
</table>
<?/*$APPLICATION->IncludeComponent(
	"bitrix:photogallery.imagerotator", 
	"action", 
	Array(
        "WIDTH" => "800",
        "HEIGHT" => "600",
        "ROTATETIME" => "5",
        "BACKCOLOR" => "#000000",
        "FRONTCOLOR" => "#FFFFFF",
        "LIGHTCOLOR" => "#e8e8e8",
        "SCREENCOLOR" => "#FFFFFF",
        "LOGO" => "",
        "OVERSTRETCH" => "Y",
        "SHOWNAVIGATION" => "Y",
        "USEFULLSCREEN" => "Y",
        "TRANSITION" => "random",
        "IBLOCK_TYPE" => "info",
        "IBLOCK_ID" => "48",
        "SECTION_ID" => "", //$_REQUEST["SECTION_ID"],
        "BEHAVIOUR" => "SIMPLE",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600000",
        "CACHE_NOTES" => "",
        "USER_ALIAS" => "", //$_REQUEST["USER_ALIAS"],
        "ELEMENT_SORT_FIELD" => "ID",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD1" => "TIMESTAMP_X",
        "ELEMENT_SORT_ORDER1" => "asc",
        "DETAIL_URL" => "detail.php?SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#",
        "USE_PERMISSIONS" => "N",
    )
);/?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>