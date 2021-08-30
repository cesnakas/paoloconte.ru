<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
define("RUCENTER_GEOIP_INSTALLED", true);
error_reporting(E_ERROR);

if (!CModule::IncludeModule("iblock"))
{
    //define("TARIFF_AREA", "MOSCOW");
    die();
}
if (!$arParams["IBLOCK_CODE"])
    $arParams["IBLOCK_CODE"] = "city";

// cookies settings
$domain_raw = str_replace("www.", "", $_SERVER['SERVER_NAME']);

define("COOKIE_DOMAIN", ".".$domain_raw);
$cookie_life = time()+3600*24*30;   // one month

// get user ip
$userIp = $_SERVER['REMOTE_ADDR'];
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    $userIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
else if (isset($_SERVER["HTTP_X_REAL_IP"]))
    $userIp = $_SERVER["HTTP_X_REAL_IP"];

if ($_SERVER['HTTP_NS_CLIENT_IP'] != '')
    $userIp = $_SERVER['HTTP_NS_CLIENT_IP'];

$savedCityID = $_SESSION['CITY_ID'];

// если город указан явно и ранее не обрабатывался
//$cityID = intval($_GET["city"]);
//var_dump($_GET["city"]);
//if(($_COOKIE['city-id']) !== ($_COOKIE["CITY_ID"])){
//    $cityID = intval($_COOKIE['city-id']);
//}


$cityID = intval($_COOKIE['city-id']);
//var_dump($_COOKIE['city-id']);
unset($GLOBALS[$_COOKIE['city-id']]);
setcookie('city-id', null);
//var_dump($_COOKIE['city-id']);

if ($cityID && $cityID != $_COOKIE["CITY_ID"])
{

    // пробуем получить город из ИБ городов руцентра
    $arFilter = Array(
        "IBLOCK_CODE"   => $arParams["IBLOCK_CODE"],
        "ACTIVE"        => "Y",
        "SITE_ID"       => SITE_ID,
        "ID"            => $cityID,
    );
    $arSelect = Array(
        "ID",
        "CODE",
        "XML_ID"
    );
    $rsCity = CIBlockElement::GetList(Array(), $arFilter, false, array("nTopCount"=>1), $arSelect);
    if ($arCity = $rsCity->Fetch())
    {
        SaveCity($arCity["ID"], $arCity["CODE"]);
    }
}
// если город не указан явно, проверяем, установлен ли город раньше и не сменился ли IP
// работаем только с инфоблоком областных городов РуЦентра
// если таблица городов и IP не установлена, чтобы отключить проверку, нужно задефайнить RUCENTER_GEOIP_INSTALLED = false
else if ((!$_COOKIE["CITY_ID"] || $userIp != $_COOKIE["USER_IP"]) && constant("RUCENTER_GEOIP_INSTALLED") == true)
{
    $QUERY = "
        SELECT * from rucenter_ranges AS RR
        WHERE
            RR.IP_INT_FROM<=inet_aton('".$userIp."') AND RR.IP_INT_TO>=inet_aton('".$userIp."') AND RR.COUNTRY_ID='RU'
    ";
    $rsIpInfo = $DB->Query($QUERY);
    if ($arIpInfo = $rsIpInfo->Fetch())
    {
        if (intval($arIpInfo["CITY_ID"]))
        {
            $arFilter = Array(
                "IBLOCK_CODE"   => $arParams["IBLOCK_CODE"],
                "SITE_ID"       => SITE_ID,
                "ACTIVE"        => "Y",
                "XML_ID"        => intval($arIpInfo["CITY_ID"]),
            );
            $arSelect = Array("ID", "CODE", "XML_ID");
            $rsCity = CIBlockElement::GetList(Array(), $arFilter, false, array("nTopCount"=>1), $arSelect);
            if ($arCity = $rsCity->Fetch())
            {
                SaveCity($arCity["ID"], $arCity["CODE"]);
            }
        }
    }
}
// в куках сохранен город, запишем его в сессию
else if (strlen($_COOKIE["CITY_ID"]))
{
    SaveCity($_COOKIE["CITY_ID"], $_COOKIE["CITY_CODE"]);
}

// если не получилось определить город - устанавливаем Москву
if (!strlen($_SESSION["CITY_ID"]))
{
    ResetCity($arParams['IBLOCK_CODE']);
}

setcookie("USER_IP", $userIp, $cookie_life, "/", constant("COOKIE_DOMAIN"));
//define("TARIFF_AREA", $_SESSION["TARIFF_AREA"]);
?>
<?
function SaveCity($id, $code)
{
    global $DB;
    $cookie_life = time()+3600*24*30;   // one month

    //$tariffArea = (strtoupper($code) == "MOSCOW") ? "MOSCOW" : "REGION";
    //$_SESSION["TARIFF_AREA"]    = $tariffArea;
    $_SESSION["CITY_ID"]        = intval($id);
    $_SESSION["CITY_CODE"]      = $DB->ForSql($code);

    //setcookie("TARIFF_AREA",    $_SESSION["TARIFF_AREA"],   $cookie_life,   "/",    constant("COOKIE_DOMAIN"));
    setcookie("CITY_ID",        $_SESSION["CITY_ID"],       $cookie_life,   "/",    constant("COOKIE_DOMAIN"));
    setcookie("CITY_CODE",      $_SESSION["CITY_CODE"],     $cookie_life,   "/",    constant("COOKIE_DOMAIN"));

    setcookie("BITRIX_SM_PK", 'page_region_'.$_SESSION["CITY_ID"], $cookie_life, "/", constant("COOKIE_DOMAIN"));

    if (strpos(constant('COOKIE_DOMAIN'), 'testfact')) {
        $domain = preg_replace('/^\.\w+/', '', constant('COOKIE_DOMAIN'), 1);

        setcookie("CITY_ID",        $_SESSION["CITY_ID"],       $cookie_life,   "/",    $domain);
        setcookie("CITY_CODE",      $_SESSION["CITY_CODE"],     $cookie_life,   "/",    $domain);

        setcookie("BITRIX_SM_PK", 'page_region_'.$_SESSION["CITY_ID"], $cookie_life, "/", $domain);
    }

	// Записываем в сессию информацию о ценах для города с этим ID
	$_SESSION['GEO_PRICES'] = \Citfact\Paolo::GetRegionPriceTypes($_SESSION['CITY_ID']);
}

function ResetCity($iblock_code)
{
    $arSort = array();
    $arFilter = array(
        "IBLOCK_CODE"   => $iblock_code,
        "SITE_ID"       => SITE_ID,
        "ACTIVE"        => "Y",
        "CODE"          => "moscow",
    );
	$arSelect = array(
        "ID",
        "CODE",
    );
    //$arNav = array("nTopCount" => 1);
	$arNav = false;
    $res = CIBlockElement::GetList($arSort, $arFilter, false, $arNav, $arSelect);
    if ($arFields = $res->GetNext())
    {
        SaveCity($arFields["ID"], $arFields["CODE"]);
    }
}