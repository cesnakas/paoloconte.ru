<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/blue/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
endif;
/********************************************************************
				Input params
********************************************************************/
$arParams["SEO_USER"] = (in_array($arParams["SEO_USER"], array("Y", "N", "TEXT")) ? $arParams["SEO_USER"] : "Y");
$arParams["USER_TMPL"] = '<noindex><a rel="nofollow" href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a></noindex>';
if ($arParams["SEO_USER"] == "N") $arParams["USER_TMPL"] = '<a href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a>';
elseif ($arParams["SEO_USER"] == "TEXT") $arParams["USER_TMPL"] = '#NAME#';
/********************************************************************
				/Input params
********************************************************************/

$userName=$USER->GetFullName();
$userId=$USER->GetID();
$arGroups = CUser::GetUserGroup($USER->GetID($userId));

		$rsGroups=CGroup::GetList($by = "c_sort", $order = "asc",Array ("ID" => $arGroups[0]));
		$res=$rsGroups->Fetch();
		$userGroups=$res[NAME];

if (in_array("USERS_ONLINE", $arParams["SHOW"]))
{

	$arMsg = array();
	if (!empty($arResult["REGISTER"])){
		$arMsg[] = '<b>'.$arResult["REGISTER"].'</b> '.\Citfact\Tools::declension(intVal($arResult["REGISTER"]), array('пользователь','пользователя','пользователей'),true);
		}
	else{
		$arMsg[] = '<b>0</b> пользователей';
		}
	if (!empty($arResult["GUEST"])){
		$arMsg[] ='<b>'.$arResult["GUEST"].'</b> '.\Citfact\Tools::declension(intVal($arResult["GUEST"]), array('гость','гостя','гостей'),true);
		}
	else{
		$arMsg[] ='<b>0</b> гостей';
		}
	if (!empty($arResult["USERS_HIDDEN"]))
		$arMsg[] ='<b>'.$arResult["F_NOW_ONLINE_3"].'</b> '.\Citfact\Tools::declension(intVal($arResult["F_NOW_ONLINE_3"]), array('скрытый','скрытых','скрытых'),true);
  global $USER;
	
$UsersNum='<b>'.$arResult["ALL"].'</b> '.\Citfact\Tools::declension($arResult["ALL"], array('пользователь','пользователя','пользователей'),true);

$text = ($arParams["TID"] > 0 ? GetMessage("F_NOW_TOPIC_READ").":" : GetMessage("F_NOW_FORUM")." ".$UsersNum." ".GetMessage("F_USERS_ONLINE2"))." ".implode(" и ", $arMsg)." " ;
}
?>
<div class="forum-info-box forum-statistics">
		<table cellspacing="0" class="forum-table forum-statistics-table">
			<tbody>
				<tr class="statustics-raw-users">
					<td class="statustics-column-icon">
						<div class="forum-icon-container">
							<div class="forum-icon-statistics"></div>
						</div>
					</td>
					<td>
						<div class="statustics-column-users">
							<p class="forum-users-online"><?=$text?></p>
							<?if (isset($userId)){?>
							<a class="forum-user-name" href="/forum/index.php?PAGE_NAME=profile_view&UID=<?echo $userId;?>"><?echo $userName?></a>
							<?}else{?>
							<p><?echo GetMessage("F_GUEST")?></p>
							<?}?>
							<p class="forum-user-status"><?echo GetMessage("F_USER_STATUS")." ".$userGroups?></p>
						</div>
					</td>
				</tr>
			<?	
			if (in_array("STATISTIC", $arParams["SHOW"])):
			?>
				<tr class="statustics-raw">
					<td class="statustics-column-icon forum-raw-background-grey">
						<div class="forum-icon-container">
							<div class="forum-icon-statistics"></div>
						</div>
					</td>
					<td class="statustics-column-themes forum-raw-background-grey">
						<div class="forum-statistics-allusers"><p><?=GetMessage("F_REGISTER_USERS")?>:&nbsp;<b><?=intVal($arResult["STATISTIC"]["USERS_ON_FORUM"])?></b>
						<!--&nbsp;|&nbsp;</span><?=GetMessage("F_REGISTER_LAST")?>:&nbsp;<b class="forum-user-register-last"><??></b>--></p>
						</div>
						<div class="statustics-column-statustics"><p><?=GetMessage("F_POSTS_ALL")?>:&nbsp;<b><?=intVal($arResult["STATISTIC"]["POSTS"])?></b></p><p><!--<span>&nbsp;|&nbsp;</span>--><?=GetMessage("F_TOPICS_ALL")?>:&nbsp;<b><?=intVal($arResult["STATISTIC"]["TOPICS"])?></b></p>
						<p><!--<?=GetMessage("F_SECTIONS_ALL")?>:&nbsp;<b><?/*=intVal($arResult["STATISTIC"]["POSTS"])*/?></b><span>|&nbsp;</span>--><?=GetMessage("F_FORUMS_ALL")?>:&nbsp;<b><?=$arResult["STATISTIC"]["FORUMS"]?></b></p>
						<!--<p><?=GetMessage("F_OPEN_TODAY")?>:&nbsp;<b><??></b><span>&nbsp;|&nbsp;</span><?=GetMessage("F_OPEN_YESTERDAY")?>:&nbsp;<b><??></b></p>
						<p><?=GetMessage("F_MESSAGES_TODAY")?>:&nbsp;<b><??></b><span>|&nbsp;</span><?=GetMessage("F_MESSAGES_YESTERDAY")?>:&nbsp;<b><??></b></p>--></div>	
						<? /*echo "<pre>";
						print_r($arResult);
						echo "</pre>";*/?>
					</td>
				</tr>
			<?
			endif;
			?>
			</tbody>
		</table>
</div>