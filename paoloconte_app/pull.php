<?php
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

if(!check_bitrix_sessid()) return;

$token = $_REQUEST["device_token"];
$arFields = Array(
    "USER_ID" => $USER->GetID(),
    "DEVICE_NAME" => $_REQUEST["device_name"],
    "DEVICE_TYPE" =>  $_REQUEST["device_type"],
    "DEVICE_ID" => $_REQUEST["uuid"],
    "DEVICE_TOKEN" => $token,
    "APP_ID" => "ru.paoloconte.app"  //!!!!!!!!!! need to change after compile real app !!!!!
);
if(!$_REQUEST["device_token"])
    break;
if(!CModule::IncludeModule("pull"))
{
    $arResult["ERROR"] = GetMessage("MOBILEAPP_PULL_NOT_INSTALLED");
    break;
}
$uuid = $_REQUEST["uuid"];
$data = array(
    "register_token" => "fail",
    "token" => $token,
    "user_id" => $USER->GetID()
);
$dbres = CPullPush::GetList(Array(), Array("=DEVICE_ID" => $uuid));
$arToken = $dbres->Fetch();
if($arToken["ID"])
{
    $res = CPullPush::Update($arToken["ID"], $arFields);
    if($res)
        $data["register_token"] = "updated";
}
else
{
    $res = CPullPush::Add($arFields);
    if($res)
        $data["register_token"] = "created";
}
$arResult["DATA"] = $data;
if(isset($arResult["ERROR"]))
    $arResult["RESULT"] = "ERROR";
else
    $arResult["RESULT"] = "OK";
$arResult = $APPLICATION->ConvertCharsetArray($arResult, SITE_CHARSET, 'utf-8');
die(json_encode($arResult));