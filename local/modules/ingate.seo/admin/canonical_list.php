<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(str_ireplace('\\', '/', dirname(__DIR__))."/prolog.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ingate\Seo\CanonicalTable;

Loader::includeModule(INGATE_SEO_MODULE_ID);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/tools.php");
Loc::loadMessages(__FILE__);

global $APPLICATION;

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$RIGHT = $APPLICATION->GetGroupRight(INGATE_SEO_MODULE_ID);

if ($RIGHT == "D")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if ($ex = $APPLICATION->GetException()) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError($ex->GetString());
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$tableName = "ingate_seo_canonical";
$adminSort = new CAdminSorting($tableName, "ID", "asc");
$adminList = new CAdminList($tableName, $adminSort);

$arFilterFields = array(
	"find_id",
	"find_active",
	"find_url",
	"find_canonical",
);

$adminList->InitFilter($arFilterFields);

$arFilter = array();

if (intval($find_id) > 0)
	$arFilter["ID"] = $find_id;

if ($find_active = trim($find_active))
	$arFilter["ACTIVE"] = $find_active;

if ($find_url = trim($find_url))
	$arFilter["URL"] = '%'.$find_url.'%';

if ($find_canonical = trim($find_canonical))
	$arFilter["CANONICAL"] = '%'.$find_canonical.'%';

if ($request->get('del_filter') == "Y")
	$arFilter = array();

if ($adminList->EditAction() && $RIGHT == "W") {

	foreach ($FIELDS as $ID => $arFields) {

		if (!$adminList->IsUpdated($ID))
			continue;

		$ID = IntVal($ID);
		$rsData = CanonicalTable::getById($ID);

		if ($arData = $rsData->fetch()) {

			foreach ($arFields as $key => $value) {
				$arData[$key] = $value;
			}

			if ($ID > 0) {
				$result = CanonicalTable::update($ID, $arData);
			}
			if (!$result->isSuccess()) {
				$adminList->AddGroupError(
					implode('<br>', $result->getErrorMessages()),
					$ID
				);
			}
		} else {
			$adminList->AddGroupError(implode('<br>', $arData->getErrorMessages()), $ID);
		}
	}
}

if (($arID = $adminList->GroupAction()) && $RIGHT == "W") {

	if ($request->get('action_target') == 'selected') {

		$rsData = CanonicalTable::getList(array('order' => array($by=>$order), 'filter' => $arFilter));

		while ($arRes = $rsData->fetch())
			$arID[] = $arRes['ID'];
	}

	foreach ($arID as $ID) {

		if (strlen($ID) <= 0)
			continue;

		$ID = IntVal($ID);

		switch ($request->get('action_button')) {
			case "delete":
				@set_time_limit(0);
				$result = CanonicalTable::delete($ID);

				if (!$result->isSuccess()) {
					$adminList->AddGroupError(implode('<br>', $result->getErrorMessages()), $ID);
				}

				if ($backurl = $request->get('backurl'))
					LocalRedirect($backurl);

				break;
			case "activate":
			case "deactivate":
				$rsData = CanonicalTable::getById($ID);
				if ($rsData->fetch()) {
					$arFields["ACTIVE"] = ($request->get('action_button') == "activate" ? "Y" : "N");
					$result = CanonicalTable::update($ID, $arFields);

					if (!$result->isSuccess())
						$adminList->AddGroupError(implode("<br>", $result->getErrorMessages()), $ID);
				} else
					$adminList->AddGroupError(Loc::getMessage('INGATE_SEO_DOES_NOT_EXIST'), $ID);

				break;
		}
	}
}

$rsData = CanonicalTable::getList(
	array(
		'order' => array($by => $order),
		'filter' => $arFilter
	)
);
$rsData = new CAdminResult($rsData, $tableName);
$rsData->NavStart("20");
$adminList->NavText($rsData->GetNavPrint(Loc::getMessage("INGATE_SEO_PAGES")));

$adminList->AddHeaders(
	array(
		array(
			"id" => "ID",
			"content" => "ID",
			"sort" => "ID",
			"default" => true,
		),
		array(
			"id" => "ACTIVE",
			"sort" => "ACTIVE",
			"content" => Loc::getMessage("INGATE_SEO_FILTER_ACTIVE"),
			"default" => true,
		),
		array(
			"id" => "URL",
			"sort" => "URL",
			"content" => Loc::getMessage("INGATE_SEO_SORT_URL"),
			"default" => true,
		),
		array(
			"id" => "CANONICAL",
			"content" => Loc::getMessage("INGATE_SEO_SORT_CANONICAL"),
			"sort" => "CANONICAL",
			"default" => true,
		),
		array(
			"id" => "TIMESTAMP_X",
			"content" => Loc::getMessage("INGATE_SEO_TIMESTAMP_X"),
			"sort" => "TIMESTAMP_X",
			"default" => true,
		),
		array(
			"id" => "DATE_CREATE",
			"content" => Loc::getMessage("INGATE_SEO_DATE_CREATE"),
			"sort" => "DATE_CREATE",
			"default" => true,
		),
	)
);

while ($arItem = $rsData->NavNext(true, "f_")) {
	$row =& $adminList->AddRow($f_ID, $arItem);

	$row->AddCheckField("ACTIVE");

	$row->AddInputField("URL", array('value' => $f_URL));
	$row->AddInputField("CANONICAL", array('value' => $f_CANONICAL));

	$row->AddViewField(
		"ACTIVE",
		($f_ACTIVE == 'Y')
			? Loc::getMessage('MAIN_YES')
			: Loc::getMessage('MAIN_NO')
	);
	$row->AddViewField("ID", $f_ID);

	$row->AddViewField(
		"URL",
		'<a href="'.INGATE_SEO_MODULE_ID.'_canonical_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'">'.$f_URL.'</a>'
	);

	$row->AddViewField("CANONICAL", $f_CANONICAL);
	$row->AddViewField("TIMESTAMP_X", $f_TIMESTAMP_X);
	$row->AddViewField("DATE_CREATE", $f_DATE_CREATE);

	$arActions = array();

	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => Loc::getMessage("INGATE_SEO_EDIT"),
		"ACTION" => $adminList->ActionRedirect(INGATE_SEO_MODULE_ID."_canonical_edit.php?ID=".$f_ID)
	);

	if ($RIGHT >= "W") {
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => Loc::getMessage("INGATE_SEO_REMOVE"),
			"ACTION" =>
				"if(confirm('".Loc::getMessage('INGATE_SEO_CONFIRM')."')) ".
				$adminList->ActionDoGroup($f_ID, "delete")
		);
	}

	$row->AddActions($arActions);
	unset($arActions);
}

if (isset($row))
	unset($row);

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $rsData->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$adminList->AddGroupActionTable(array(
	"activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
));

if ($RIGHT == 'W')
	$adminList->AddGroupActionTable(array("delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE")));

$arContext = array(
	array(
		"TEXT" => Loc::getMessage("MAIN_ADD"),
		"LINK" => INGATE_SEO_MODULE_ID."_canonical_edit.php?lang=".LANG,
		"TITLE" => Loc::getMessage("MAIN_ADD_TITLE"),
		"ICON" => "btn_new",
	),
);

$adminList->AddAdminContextMenu($arContext);
$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('INGATE_SEO_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$filterUrl = $APPLICATION->GetCurPageParam();
$oFilter = new CAdminFilter(
	$tableName."_filter",
	array(
		"ID" => 'ID',
		"ACTIVE" => Loc::getMessage('INGATE_SEO_FILTER_ACTIVE'),
		"URL" => Loc::getMessage('INGATE_SEO_SORT_URL'),
		"CANONICAL" => Loc::getMessage('INGATE_SEO_SORT_CANONICAL'),
	),
	array("table_id" => $tableName, "url" => $filterUrl)
);
?>
<form method="get" name="find_form" id="find_form" action="<?=$APPLICATION->GetCurPage()?>">
<?$oFilter->Begin();?>
	<tr>
		<td>ID:</td>
		<td>
			<input type="text" name="find_id" size="10" value="<?=htmlspecialcharsex($find_id)?>">
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_FILTER_ACTIVE")?>:</td>
		<td>
			<select name="find_active" >
				<option value="" <?=($find_active == "") ? " selected" : ''?>><?=Loc::getMessage("INGATE_SEO_SELECT")?></option>
				<option value="Y" <?=($find_active == "Y") ? " selected" : ''?>><?=Loc::getMessage("MAIN_YES")?></option>
				<option value="N" <?=($find_active == "N") ? " selected" : ''?>><?=Loc::getMessage("MAIN_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_SORT_URL")?>:</td>
		<td>
			<input type="text" name="find_url" size="47" value="<?=htmlspecialcharsex($find_url)?>">
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_SORT_CANONICAL")?>:</td>
		<td>
			<input type="text" name="find_canonical" size="47" value="<?=htmlspecialcharsbx($find_canonical)?>">
		</td>
	</tr>
<?php
$oFilter->Buttons(array("table_id" => $tableName, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
$oFilter->End();
?>
</form>
<?php
$adminList->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");