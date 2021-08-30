<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(str_ireplace('\\', '/', dirname(__DIR__))."/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Ingate\Seo\PageTable;

Loader::includeModule(INGATE_SEO_MODULE_ID);
Loc::loadMessages(__FILE__);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$isPost = $request->isPost();

$RIGHT = $APPLICATION->GetGroupRight(INGATE_SEO_MODULE_ID);
if ($RIGHT == "D")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if ($STEP <= 0)
	$STEP = 1;
if ($isPost && strlen($backButton) > 0)
	$STEP = $STEP - 2;

$strError = "";
$DATA_FILE_NAME = "";

if ($STEP > 1) {
	if ($fields_type != "F" && $fields_type != "R")
		$strError .= Loc::getMessage("INGATE_SEO_EXP_NO_FORMAT");

	$delimiter_r_char = "";
	switch ($delimiter_r) {
		case "TAB":
			$delimiter_r_char = "\t";
			break;
		case "ZPT":
			$delimiter_r_char = ",";
			break;
		case "SPS":
			$delimiter_r_char = " ";
			break;
		case "OTR":
			$delimiter_r_char = substr($delimiter_other_r, 0, 1);
			break;
		case "TZP":
			$delimiter_r_char = ";";
			break;
	}

	if (strlen($delimiter_r_char) != 1)
		$strError .= Loc::getMessage("INGATE_SEO_EXP_NO_DELIMITER");

	$csvFile = new CCSVData();
	$csvFile->SetFieldsType($fields_type);
	if (strlen($strError) <= 0)
		$csvFile->SetDelimiter($delimiter_r_char);

	if (strlen($request["DATA_FILE_NAME"]) <= 0) {
		$strError .= Loc::getMessage("INGATE_SEO_EXP_NO_FILE_NAME");
	} elseif (
		preg_match('/[^a-zA-Z0-9\s!#\$%&\(\)\[\]\{\}+\.;=@\^_\~\/\\\\\-]/i', $request["DATA_FILE_NAME"]) ||
		preg_match('/^[a-z]+:\\/\\//i', $request["DATA_FILE_NAME"]) ||
		HasScriptExtension($request["DATA_FILE_NAME"])
	) {
		$strError .= Loc::getMessage("INGATE_SEO_EXP_FILE_NAME_ERROR");
	} else {
		$DATA_FILE_NAME = Rel2Abs("/", $request["DATA_FILE_NAME"]);
		if (strtolower(substr($DATA_FILE_NAME, strlen($DATA_FILE_NAME) - 4)) != ".csv")
			$DATA_FILE_NAME .= ".csv";
	}

	if (strlen($strError) <= 0) {
		$fp = fopen($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, "w");
		if (!is_resource($fp)) {
			$strError .= Loc::getMessage("INGATE_SEO_EXP_CANNOT_CREATE_FILE");
			$DATA_FILE_NAME = "";
		} else {
			fclose($fp);
		}
	}

	$num_rows_writed = 0;

	if (strlen($strError) <= 0) {

		if ($export_type == 'meta') {

			$table = '\Ingate\Seo\PageTable';

		} elseif ($export_type == 'redirect') {

			$table = '\Ingate\Seo\RedirectTable';

		} elseif ($export_type == 'canonical') {

			$table = '\Ingate\Seo\CanonicalTable';

		}

		if ($first_line_names == "Y") {

			$arHeaders = $table::getCompareForCSV();
			$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, array_values($arHeaders));

		}

		$result = $table::getList(array('select'=>array_flip($arHeaders)))->fetchAll();

		foreach ($result as $key => $value) {

			$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, array_values($value));
			$num_rows_writed++;

		}
	}
}

if (strlen($strError) > 0)
	$STEP = 1;

$APPLICATION->SetTitle(Loc::getMessage("INGATE_SEO_EXP_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

CAdminMessage::ShowMessage($strError);
?>
<form method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>" ENCTYPE="multipart/form-data" name="dataload">
	<input type="hidden" name="STEP" value="<?=$STEP + 1?>">
	<?=bitrix_sessid_post()?>
<?php
	$aTabs = array(
		array(
			"DIV" => "edit1",
			"TAB" => Loc::getMessage("INGATE_SEO_EXP_TAB1"),
			"ICON" => "iblock",
			"TITLE" => Loc::getMessage("INGATE_SEO_EXP_TAB1_ALT")
		),
		array(
			"DIV" => "edit2",
			"TAB" => Loc::getMessage("INGATE_SEO_EXP_TAB2"),
			"ICON" => "iblock",
			"TITLE" => Loc::getMessage("INGATE_SEO_EXP_TAB2_ALT")
		),
	);

	$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
	$tabControl->Begin();

	$tabControl->BeginNextTab();

	if ($STEP < 2) { ?>
	<tr class="heading">
		<td colspan="2">
			<?=Loc::getMessage("INGATE_SEO_EXP_CHOOSE_TYPE")?>
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-valign-top"></td>
		<td width="60%">
			<label><input checked="" type="radio" name="export_type" value="meta"><?=Loc::getMessage("INGATE_SEO_EXP_META")?></label><br />
			<label><input type="radio" name="export_type" value="redirect"><?=Loc::getMessage("INGATE_SEO_EXP_REDIRECTS")?></label><br />
			<label><input type="radio" name="export_type" value="canonical"><?=Loc::getMessage("INGATE_SEO_EXP_CANONICAL")?></label><br />
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2">
			<?=Loc::getMessage("INGATE_SEO_EXP_CHOOSE_FORMAT")?>
			<input type="hidden" name="fields_type" value="R">
		</td>
	</tr>
	<tr>
		<td width="40%" class="adm-detail-valign-top"><?=Loc::getMessage("INGATE_SEO_tab1_f_delim")?></td>
		<td width="60%">
			<input type="radio" name="delimiter_r" id="delimiter_TZP" value="TZP" checked>
			<label for="delimiter_TZP"><?=Loc::getMessage("INGATE_SEO_EXP_DELIM_TZP")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_ZPT" value="ZPT">
			<label for="delimiter_ZPT"><?=Loc::getMessage("INGATE_SEO_EXP_DELIM_ZPT")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_TAB" value="TAB">
			<label for="delimiter_TAB"><?=Loc::getMessage("INGATE_SEO_EXP_DELIM_TAB")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_SPS" value="SPS">
			<label for="delimiter_SPS"><?=Loc::getMessage("INGATE_SEO_EXP_DELIM_SPS")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_OTR" value="OTR">
			<label for="delimiter_OTR"><?=Loc::getMessage("INGATE_SEO_EXP_DELIM_OTR")?></label>
			<input type="text" name="delimiter_other_r" size="3" value="">
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_EXP_FIRST_LINE_NAMES")?></td>
		<td>
			<input type="checkbox" name="first_line_names" value="Y" checked>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("INGATE_SEO_EXP_FIELDS_MAPPING")?></td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_EXP_ENTER_FILE_NAME")?></td>
		<td>
<?php
		if (strlen($DATA_FILE_NAME) > 0) {
			$exportFileName = $DATA_FILE_NAME;
		} else {
			$exportFileName = "/".COption::GetOptionString("main", "upload_dir", "upload")."/ingate_seo_";
			$exportFileName .= date('d.m.Y_H-i');
			$exportFileName .= '.csv';
		}
?>
			<input type="text" name="DATA_FILE_NAME" size="40" value="<?=htmlspecialcharsbx($exportFileName);?>"><br>
			<?=Loc::getMessage("INGATE_SEO_EXP_FILE_WARNING")?>
		</td>
	</tr>
<?php
	}// end step 1

	$tabControl->EndTab();
	$tabControl->BeginNextTab();

	if ($STEP == 2) {
?>
	<tr>
		<td>
			<?echo CAdminMessage::ShowMessage(array(
				"TYPE" => "PROGRESS",
				"MESSAGE" => Loc::getMessage("INGATE_SEO_EXP_SUCCESS"),
				"DETAILS" =>
					Loc::getMessage(
						"INGATE_SEO_EXP_LINES_EXPORTED",
						array(
							"#LINES#" => "<b>".intval($num_rows_writed)."</b>"
						)
					)
					.'<br>'.
					Loc::getMessage(
						"INGATE_SEO_EXP_DOWNLOAD_RESULT",
						array(
							"#HREF#" =>
								"<a href=\"".htmlspecialcharsbx($DATA_FILE_NAME)."\" target=\"_blank\">".
								htmlspecialcharsex($DATA_FILE_NAME)."</a>"
						)
					),
				"HTML" => true,
			)) ?>
		</td>
	</tr>
	<?
	} //end step 2

	$tabControl->EndTab();
	$tabControl->Buttons();?>

	<?php if ($STEP > 1):?>
	<input type="submit" name="backButton" value="&lt;&lt; <?=Loc::getMessage("INGATE_SEO_EXP_BACK_BUTTON")?>" class="adm-btn-save">
	<?php else:?>
	<input type="submit" value="<?=Loc::getMessage("INGATE_SEO_EXP_FINISH_BUTTON")?> &gt;&gt;" name="submit_btn" class="adm-btn-save">
	<?php endif; ?>

<?php
	$tabControl->End();
?>
	<script type="text/javaScript">
		BX.ready(function () {
			<?if ($STEP < 2):?>
			tabControl.SelectTab("edit1");
			tabControl.DisableTab("edit2");
			<?elseif ($STEP == 2):?>
			tabControl.SelectTab("edit2");
			tabControl.DisableTab("edit1");
			<?endif;?>
		});
	</script>
</form>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>