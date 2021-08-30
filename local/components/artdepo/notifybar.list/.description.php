<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NOTIFYBAR_LIST_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("NOTIFYBAR_LIST_COMPONENT_DESCR"),
	"ICON" => "/images/notify_bar.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "artdepo",
		"SORT" => 3000,
		"NAME" => GetMessage("COMPONENTS_ARTDEPO"),
		"CHILD" => array(
			"ID" => "notofy_bar",
			"NAME" => GetMessage("NOTIFYBAR_LIST_GROUP_NAME"),
		),
	),
);
?>
