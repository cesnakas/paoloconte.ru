<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(str_ireplace('\\', '/', dirname(__DIR__))."/prolog.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Ingate\Seo\CanonicalTable;
use Bitrix\Main\SiteTable;

Loader::includeModule(INGATE_SEO_MODULE_ID);
Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$isPost = $request->isPost();

global $APPLICATION, $USER_FIELD_MANAGER;

$RIGHT = $APPLICATION->GetGroupRight(INGATE_SEO_MODULE_ID);
if ($RIGHT == "D")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if ($ID > 0)
	$APPLICATION->SetTitle(Loc::getMessage("INGATE_SEO_TITLE_EDIT"));
else
	$APPLICATION->SetTitle(Loc::getMessage("INGATE_SEO_TITLE_ADD"));

$aTabs = array (
	array(
		'DIV' => "edit1",
		'TAB' => Loc::getMessage("INGATE_SEO_TAB_ELEMENT"),
		'ICON' => "main_user_edit",
		'TITLE' => Loc::getMessage("INGATE_SEO_TAB_ELEMENT")
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);
$errors = array();

$arSites = SiteTable::getList()->fetchAll();

if ($request['ID'] > 0) {
	$filter = array(
		'filter' => array('=ID' => $request['ID'])
	);
	$arData = CanonicalTable::getList($filter)->fetch();
}

if ($isPost == "POST" && ($save != "" || $apply != "") && $RIGHT == "W" && check_bitrix_sessid()) {
	$arFields = array(
		"ACTIVE" => ($ACTIVE <> "Y" ? "N" : "Y"),
		"URL" => $URL,
		"CANONICAL" => $CANONICAL,
	);

	if ($ID > 0) {
		$result = CanonicalTable::update($ID, $arFields);
	} else {
		$result = CanonicalTable::add($arFields);
	}

	if ($result->isSuccess()) {
		if (strlen($save) > 0) {
			LocalRedirect(INGATE_SEO_MODULE_ID."_canonical_list.php?lang=".LANGUAGE_ID);
		} else {
			LocalRedirect(
				INGATE_SEO_MODULE_ID."_canonical_edit.php?ID=".
				$ID."&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam()
			);
		}
	} else {
		$errors .= implode('<br>', $result->getErrorMessages());
	}

	foreach ($arFields as $k => $v) {
		$arData[$k] = $v;
	}
}

$aMenu = array(
	array(
		"TEXT"	=> GetMessage('INGATE_SEO_RETURN_TO_LIST'),
		"TITLE"	=> GetMessage('INGATE_SEO_RETURN_TO_LIST'),
		"LINK"	=> INGATE_SEO_MODULE_ID."_canonical_list.php?lang=".LANGUAGE_ID,
		"ICON"	=> "btn_list",
	)
);
$context = new CAdminContextMenu($aMenu);

if (!empty($errors)) {
	CAdminMessage::ShowMessage(join("\n", $errors));
	echo CAdminMessage::ShowMessage(
		array(
			"DETAILS" => $errors,
			"TYPE" => "ERROR",
			"MESSAGE" => Loc::getMessage("INGATE_SEO_SAVE_ERROR"),
			"HTML" => true
		)
	);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$context->Show();
?>
<form method="POST" action="<?=$APPLICATION->GetCurPage()?>" name="form1" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<?php
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<?php if ($ID > 0): ?>
	<tr>
		<td class="adm-detail-content-cell-l" width="20%">ID:</td>
		<td class="adm-detail-content-cell-r" width="80%"><?=$ID?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_ACTIVE")?>:</td>
		<td>
			<input type="checkbox" name="ACTIVE" value="Y" <? if ($arData['ACTIVE'] == "Y") echo " checked"?>>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?=Loc::getMessage("INGATE_SEO_URL")?></td>
		<td>
			<input type="text" name="URL" value="<?=$arData['URL'];?>" size="45">
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage('INGATE_SEO_CANONICAL')?></td>
		<td>
			<input type="text" name="CANONICAL" size="120" value="<?=htmlspecialcharsbx($arData['CANONICAL'])?>">
		</td>
	</tr>
<?php
$tabControl->Buttons(array(
	"disabled" => ($RIGHT < "W"),
	"back_url" => INGATE_SEO_MODULE_ID."_canonical_list.php?lang=".LANGUAGE_ID,
));
$tabControl->End();
?>
</form>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");