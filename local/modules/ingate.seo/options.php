<?php
include 'prolog.php';

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;

global $APPLICATION;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

Loader::includeModule(INGATE_SEO_MODULE_ID);

$module_id = INGATE_SEO_MODULE_ID; //README переменная необходима для файла прав доступа
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$RIGHT = $APPLICATION->GetGroupRight(INGATE_SEO_MODULE_ID);

if ($RIGHT >= "S") {
	$aTabs = array(
		array(
			"DIV" => "edit1",
			"TAB" => Loc::getMessage("MAIN_TAB_SET"),
			"ICON" => "subscribe_settings",
			"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
			"OPTIONS" => array(
				array(
					INGATE_SEO_OPTION_META,
					Loc::getMessage("INGATE_SEO_OPT_META"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_H1,
					Loc::getMessage("INGATE_SEO_OPT_H1"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_ROBOTS,
					Loc::getMessage("INGATE_SEO_OPT_ROBOTS"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_ROBOTS_ALL,
					Loc::getMessage("INGATE_SEO_OPT_ROBOTS_ALL"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_CANONICAL,
					Loc::getMessage("INGATE_SEO_OPT_CANONICAL"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_COUNTER,
					Loc::getMessage("INGATE_SEO_OPT_COUNTER"),
					'',
					array('checkbox')
				),
				array(
					INGATE_SEO_OPTION_EXCLUSIONS,
					Loc::getMessage("INGATE_SEO_OPT_EXCLUSIONS"),
					'',
					array('textarea')
				),
			),
		),
		array(
			'DIV' => 'links',
			'TAB' => Loc::getMessage('INGATE_SEO_OPT_LINK_TAB'),
			'ICON' => 'subscribe_settings',
			'TITLE' => Loc::getMessage('INGATE_SEO_OPT_LINK_TITLE'),
			'OPTIONS' => array(
				array(
					INGATE_SEO_OPTION_MIRROR,
					Loc::getMessage('INGATE_SEO_OPT_LINK_MIRRIR'),
					'',
					array('selectbox', array(
						'N' => Loc::getMessage("INGATE_SEO_OPT_REDIR_OFF"),
						'Y' => Loc::getMessage("INGATE_SEO_OPT_LINK_WWW"),
						'W' => Loc::getMessage("INGATE_SEO_OPT_LINK_WWW_NONE"),
					))
				),
				array(
					INGATE_SEO_OPTION_NOFOLLOW,
					Loc::getMessage('INGATE_SEO_OPT_LINK_NOFOLLOW'),
					'',
					array('selectbox', array(
						'N' => Loc::getMessage("INGATE_SEO_OPT_REDIR_OFF"),
						'W' => Loc::getMessage("INGATE_SEO_OPT_LINK_NOFOLLOW_SUB"),
						'Y' => Loc::getMessage("INGATE_SEO_OPT_LINK_NOFOLLOW_ALL"),
					))
				),
				array(
					INGATE_SEO_OPTION_MIXED,
					Loc::getMessage('INGATE_SEO_OPT_LINK_MIXED'),
					'',
					array('checkbox')
				),
			)
		),
		array(
			"DIV" => "redirects",
			"TAB" => Loc::getMessage("INGATE_SEO_OPT_REDIR_TAB"),
			"ICON" => "subscribe_settings",
			"TITLE" => Loc::getMessage("INGATE_SEO_OPT_REDIR_TITLE"),
			"OPTIONS" => array(
				array(
					INGATE_SEO_OPTION_SCHEME,
					Loc::getMessage("INGATE_SEO_OPT_REDIR_SCHEME"),
					'',
					array('selectbox', array(
						'N' => Loc::getMessage("INGATE_SEO_OPT_REDIR_OFF"),
						'S' => Loc::getMessage("INGATE_SEO_OPT_REDIR_HTTPS"),
						'W' => Loc::getMessage("INGATE_SEO_OPT_REDIR_HTTP")
					))
				),
				array(
					INGATE_SEO_OPTION_WWW,
					Loc::getMessage("INGATE_SEO_OPT_REDIR_WWW_TITLE"),
					'',
					array('selectbox', array(
						'N' => Loc::getMessage("INGATE_SEO_OPT_REDIR_OFF"),
						'Y' => Loc::getMessage("INGATE_SEO_OPT_REDIR_WWW_NONE"),
						'W' => Loc::getMessage("INGATE_SEO_OPT_REDIR_WWW")
					))
				),
				array(
					INGATE_SEO_OPTION_SLASH,
					Loc::getMessage("INGATE_SEO_OPT_REDIR_END"),
					'',
					array('selectbox', array(
						'N' => Loc::getMessage("INGATE_SEO_OPT_REDIR_OFF"),
						'S' => Loc::getMessage("INGATE_SEO_OPT_REDIR_SLASH"),
						'W' => Loc::getMessage("INGATE_SEO_OPT_REDIR_SLASH_NONE"),
						'A' => Loc::getMessage("INGATE_SEO_OPT_REDIR_ENDING")
					))
				),
				array(
					INGATE_SEO_OPTION_ENDING,
					Loc::getMessage("INGATE_SEO_OPT_REDIR_ENDING_VAL"),
					'',
					array('text')
				),
			),
		),
		array(
			"DIV" => "edit2",
			"TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
			"ICON" => "subscribe_settings",
			"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
		),
	);

	//Обработка
	if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
		foreach ($aTabs as $aTab) {
			foreach ($aTab['OPTIONS'] as $arOption) {
				if (!is_array($arOption))
					continue;

				if ($arOption['note'])
					continue;

				$optionName = $arOption[0];
				$optionValue = $request->getPost($optionName);

				Option::set(
					INGATE_SEO_MODULE_ID,
					$optionName,
					is_array($optionName) ? implode(',', $optionValue) : $optionValue
				);
			}
		}
	}

	$tabControl = new CAdminTabControl("tabControl", $aTabs);

	$tabControl->Begin();?>
<form method="POST" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode(INGATE_SEO_MODULE_ID)?>&amp;lang=<?=LANGUAGE_ID?>">
<?php
	foreach ($aTabs as $aTab):
		if ($aTab['OPTIONS']):
			$tabControl->BeginNextTab();
			__AdmSettingsDrawList(INGATE_SEO_MODULE_ID, $aTab['OPTIONS']);
		endif;
	endforeach;

	$tabControl->BeginNextTab();

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

	$tabControl->Buttons();
?>
	<input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>">
	<input type="reset" name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
	<?=bitrix_sessid_post()?>
</form>
<?
	$tabControl->End();
} // end right check
