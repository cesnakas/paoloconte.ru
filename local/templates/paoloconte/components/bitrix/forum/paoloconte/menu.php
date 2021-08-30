<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
********************************************************************/
/***************** URL *********************************************/
	$arParams["SHOW_AUTH_FORM"] = ($arParams["SHOW_AUTH_FORM"] == "N" ? "N" : "Y");
/***************** ADDITIONAL **************************************/
/***************** STANDART ****************************************/
	if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
		$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
	else
		$arParams["CACHE_TIME"] = 0;
/********************************************************************
				/Input params
********************************************************************/
?>
<div class="container">
	<div class="forum-info-box forum-menu-box no-border">
		<div class="forum-info-box-inner">
<?
if ($GLOBALS["USER"]->IsAuthorized())
{
?>
	<span class="forum-menu-item forum-menu-item-first forum-menu-newtopics <?if (strcmp($_GET['PAGE_NAME'],"active")==0)	echo "forum-menu-item-selected";?>"><?
	
	
		?><a href="<?=$arResult["URL_TEMPLATES"]["ACTIVE"]?>" title="<?=GetMessage("F_NEW_TOPIC_TITLE")?>"><span><?=GetMessage("F_NEW_TOPIC")?></span></a>&nbsp;</span>
	<span class="forum-menu-item forum-menu-profile <?if (strcmp($_GET['PAGE_NAME'],"profile_view")==0)	echo "forum-menu-item-selected";?>"><a href="<?=$arResult["URL_TEMPLATES"]["PROFILE"]?>"><span><?=GetMessage("F_PROFILE")?></span></a>&nbsp;</span>
<?
if ($arParams["SHOW_SUBSCRIBE_LINK"] == "Y"):
?>
		<span class="forum-menu-item forum-menu-subscribes"><a href="<?=$arResult["URL_TEMPLATES"]["SUBSCRIBES"]?>"><span><?=GetMessage("F_SUBSCRIBES")?></span></a>&nbsp;</span>
<?
endif;
	if (intval(COption::GetOptionString("forum", "UsePMVersion", "2")) > 0)
	{
		$pm = "";
		$arUserPM = array();
		$cache = new CPHPCache();
		$cache_path_main = str_replace(array(":", "//"), "/", "/".SITE_ID."/".$this->__component->__name."/");
		$cache_id = "forum_user_pm_".$GLOBALS["USER"]->GetId();
		$cache_path = $cache_path_main."user".$GLOBALS["USER"]->GetId();
		if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
		{
			$val = $cache->GetVars();
			if (is_array($val["arUserPM"]))
				$arUserPM = $val["arUserPM"];
		}
		if (!is_array($arUserPM) || empty($arUserPM))
		{
			CModule::IncludeModule("forum");
			$arUserPM = CForumPrivateMessage::GetNewPM();
			if ($arParams["CACHE_TIME"] > 0):
				$cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);
				$cache->EndDataCache(array("arUserPM"=>$arUserPM));
			endif;
		}
		if (intVal($arUserPM["UNREAD_PM"]) > 0)
		{
			$pm = " (".intVal($arUserPM["UNREAD_PM"]).")";
		}
		?>
		<span class="forum-menu-item forum-menu-messages <?if (strcmp($_GET['PAGE_NAME'],"pm_folder")==0)	echo "forum-menu-item-selected";?>"><a href="<?=$arResult["URL_TEMPLATES"]["MESSAGES"]?>"><span><?=GetMessage("F_MESSAGES")?><?=$pm?></span></a>&nbsp;</span>
		<?
	}
}
if (IsModuleInstalled("search")):
?>
		<span class="forum-menu-item <?
			?><?=($GLOBALS["USER"]->IsAuthorized() ? "" : "forum-menu-item-first")?><?
			?> forum-menu-search <?if (strcmp($_GET['PAGE_NAME'],"search")==0)	echo "forum-menu-item-selected";?>"><noindex><a href="<?=$arResult["URL_TEMPLATES"]["SEARCH"]?>" rel="nofollow"><span><?=GetMessage("F_SEARCH")?></span></a></noindex>&nbsp;</span>
<?	
endif;
?>
<? if ($arParams['SHOW_FORUM_USERS'] === 'Y')
{ ?>
		<span class="forum-menu-item <?
			?><?=($GLOBALS["USER"]->IsAuthorized() || IsModuleInstalled("search") ? "" : "forum-menu-item-first")?><?
			?> forum-menu-users <?if (strcmp($_GET['PAGE_NAME'],"user_list")==0)	echo "forum-menu-item-selected";?>"><a href="<?=$arResult["URL_TEMPLATES"]["USERS"]?>"><span><?=GetMessage("F_USERS")?></span></a>&nbsp;</span>
<?
}
?>
		<span class="forum-menu-item <?
			?><?=($arParams["SHOW_AUTH_FORM"] == "Y" ? "" : "forum-menu-item-last")?><?
			?> forum-menu-rules <?if (strcmp($_GET['PAGE_NAME'],"rules")==0)	echo "forum-menu-item-selected";?>"><a href="<?=$arResult["URL_TEMPLATES"]["RULES"]?>"><span><?=GetMessage("F_RULES")?></span></a>&nbsp;</span>
<?
if ($arParams["SHOW_AUTH_FORM"] == "Y"):
?>
		<span class="forum-menu-item forum-menu-item-last forum-menu-authorize">
			<?if ($USER->IsAuthorized()):?>
				<a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
					"login",	
					"logout",
					"register",
					"forgot_password",
					"change_password"));?>">Выйти</a>
			<?else:?>
					<a href="#" data-toggle="modal" data-target="#enterModal">Вход</a>
			<?endif;?>
		</span>
<?
endif;
?>
	</div>
</div>
<?
if ($arParams["SHOW_NAVIGATION"] != "N" && $arParams["SET_NAVIGATION"] != "N" && ($arResult["PAGE_NAME"] != "index" || $arResult["GID"] > 0)):
// text from main
	if($GLOBALS["APPLICATION"]->GetProperty("NOT_SHOW_NAV_CHAIN")=="Y")
		return false;
	CMain::InitPathVars($site, $path);
	$DOC_ROOT = CSite::GetSiteDocRoot($site);

	$path = $GLOBALS["APPLICATION"]->GetCurDir();
	$arChain = Array();
	
	while(true)
	{
		$path = rtrim($path, "/");

		$chain_file_name = $DOC_ROOT.$path."/.section.php";
		if(file_exists($chain_file_name))
		{
			$sSectionName = "";
			include($chain_file_name);
			if(strlen($sSectionName)>0)
				$arChain[] = Array("TITLE"=>$sSectionName, "LINK"=>$path."/");
		}

		if($path.'/' == SITE_DIR)
			break;

		if(strlen($path)<=0)
			break;
		$pos = bxstrrpos($path, "/");
		if($pos===false)
			break;
		$path = substr($path, 0, $pos+1);
	}
	if ($arResult["PAGE_NAME"] == "read")
	{
		$GLOBALS["FORUM_HIDE_LAST_BREADCRUMB"] = true;
	}
	$GLOBALS["APPLICATION"]->IncludeComponent(
	"bitrix:breadcrumb", ".default",
	Array(
		"START_FROM" => count($arChain) - 1, 
		"PATH" => "", 
		"SITE_ID" => "",  
	), $component, 
	array("HIDE_ICONS" => "Y")
);
endif;
?>