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

//проверяем права для модуля
$RIGHT = $APPLICATION->GetGroupRight(INGATE_SEO_MODULE_ID);
if ($RIGHT == "D")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$STEP = intval($STEP);
$postBackBtn = $request->getPost('backButton');
$postBackBtn2 = $request->getPost('backButton2');

if ($STEP <= 0)
	$STEP = 1;
if ($isPost && !empty($postBackBtn))
	$STEP = $STEP - 2;
if ($isPost && !empty($postBackBtn2))
	$STEP = 1;

$max_execution_time = intval($max_execution_time);
if ($max_execution_time <= 0)
	$max_execution_time = 0;

$CUR_LOAD_SESS_ID = (!empty($request['CUR_LOAD_SESS_ID']))
	? $request['CUR_LOAD_SESS_ID']
	: "CL".time();

$bAllLinesLoaded = true;
$CUR_FILE_POS = isset($request["CUR_FILE_POS"]) ? intval($request["CUR_FILE_POS"]) : 0;
$strError = "";
$line_num = 0;
$correct_lines = 0;
$error_lines = 0;
$killed_lines = 0;
$io = CBXVirtualIo::GetInstance();

/////////////////////////////////////////////////////////////////////
$arSeoAvailPageFields = array(
	"URL" => array(
		"field" => "URL",
		"important" => "Y",
		"name" => Loc::getMessage("INGATE_SEO_URL"),
	),
	"TITLE" => array(
		"field" => "TITLE",
		"important" => "Y",
		"name" => Loc::getMessage("INGATE_SEO_TITLE"),
	),
	"DESCRIPTION" => array(
		"field" => "DESCRIPTION",
		"important" => "Y",
		"name" => Loc::getMessage("INGATE_SEO_DESCRIPTION"),
	),
	"H1" => array(
		"field" => "H1",
		"important" => "Y",
		"name" => Loc::getMessage("INGATE_SEO_H1"),
	),
	"ACTIVE" => array(
		"field" => "ACTIVE",
		"important" => "N",
		"name" => Loc::getMessage("INGATE_SEO_ACTIVE"),
	),
	"ROBOTS" => array(
		"field" => "ROBOTS",
		"important" => "N",
		"name" => Loc::getMessage("INGATE_SEO_ROBOTS"),
	),
	"SITE_ID" => array(
		"field" => "SITE_ID",
		"important" => "N",
		"name" => Loc::getMessage("INGATE_SEO_SITE_ID"),
	),
);
/////////////////////////////////////////////////////////////////////

class CAssocData extends CCSVData
{
	var $__rows = array();
	var $__pos = array();
	var $__last_pos = 0;
	var $NUM_FIELDS = 0;
	var $tmpid = "";
	var $PK = array();
	var $GROUP_REGEX = "";

	function __construct($fields_type = "R", $first_header = false, $NUM_FIELDS = 0)
	{
		parent::__construct($fields_type, $first_header);
		$this->NUM_FIELDS = intval($NUM_FIELDS);
	}

	function GetPos()
	{
		if (empty($this->__pos))
			return parent::GetPos();
		else
			return $this->__pos[count($this->__pos) - 1];
	}

	function Fetch()
	{
		if (empty($this->__rows)) {
			$this->__last_pos = $this->GetPos();
			return parent::Fetch();
		} else {
			$this->__last_pos = array_pop($this->__pos);
			return array_pop($this->__rows);
		}
	}

	function PutBack($row)
	{
		$this->__rows[] = $row;
		$this->__pos[] = $this->__last_pos;
	}

	function AddPrimaryKey($field_name, $field_ind)
	{
		$this->PK[$field_name] = $field_ind;
	}

	function FetchAssoc()
	{
		global $line_num;
		$result = array();
		while ($ar = $this->Fetch()) {
			$line_num++;

			return $ar;
		}
		//eof

		if (empty($result))
			return $ar;
		else
			return $result;
	}
}

/////////////////////////////////////////////////////////////////////

if (($isPost || $CUR_FILE_POS > 0) && $STEP > 1 && check_bitrix_sessid()) {
	// Step 1
	if ($STEP > 1) {

		$DATA_FILE_NAME = "";

		if (isset($_FILES["DATA_FILE"]) && is_uploaded_file($_FILES["DATA_FILE"]["tmp_name"])) {

			if (strtolower(GetFileExtension($_FILES["DATA_FILE"]["name"])) != "csv") {
				$strError .= Loc::getMessage("INGATE_SEO_IMP_NOT_CSV");
			} else {

				$DATA_FILE_NAME = "/".COption::GetOptionString("main", "upload_dir", "upload")
					."/".basename($_FILES["DATA_FILE"]["name"]);

				if ($APPLICATION->GetFileAccessPermission($DATA_FILE_NAME) >= "W")
					copy($_FILES["DATA_FILE"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);
				else
					$DATA_FILE_NAME = "";
			}
		}

		if (strlen($strError) <= 0) {

			if (strlen($DATA_FILE_NAME) <= 0) {

				if (strlen($URL_DATA_FILE) > 0) {
					$URL_DATA_FILE = trim(str_replace("\\", "/", trim($URL_DATA_FILE)), "/");
					$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"], "/".$URL_DATA_FILE);

					if (
						(strlen($FILE_NAME) > 1)
						&& ($FILE_NAME === "/".$URL_DATA_FILE)
						&& $io->FileExists($_SERVER["DOCUMENT_ROOT"].$FILE_NAME)
						&& ($APPLICATION->GetFileAccessPermission($FILE_NAME) >= "W")
					) {
						$DATA_FILE_NAME = $FILE_NAME;
					}
				}
			}

			if (strlen($DATA_FILE_NAME) <= 0)
				$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_DATA_FILE");
		}

		if (strlen($strError) <= 0) {

			if (
				$CUR_FILE_POS > 0 &&
				is_set($_SESSION, $CUR_LOAD_SESS_ID) &&
				is_set($_SESSION[$CUR_LOAD_SESS_ID], "LOAD_SCHEME")
			) {
				parse_str($_SESSION[$CUR_LOAD_SESS_ID]["LOAD_SCHEME"]);
				$STEP = 4;
			}
		}

		if (strlen($strError) > 0)
			$STEP = 1;
	}

	//Step 2
	if ($STEP > 2) {
		$csvFile = new CAssocData;
		$csvFile->LoadFile($io->GetPhysicalName($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME));

		if ($fields_type != "F" && $fields_type != "R")
			$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_FILE_FORMAT");

		$arDataFileFields = array();
		if (strlen($strError) <= 0) {
			$fields_type = (($fields_type == "F") ? "F" : "R");
			$csvFile->SetFieldsType($fields_type);

			if ($fields_type == "R") {
				$first_names_r = (($first_names_r == "Y") ? "Y" : "N");
				$firstLineIsHeaders = ($first_names_r == "Y");
				$csvFile->SetFirstHeader(false);
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
					$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_DELIMITER");

				if (strlen($strError) <= 0) {
					$csvFile->SetDelimiter($delimiter_r_char);
				}
			} else {

				$first_names_f = (($first_names_f == "Y") ? "Y" : "N");
				$csvFile->SetFirstHeader(($first_names_f == "Y") ? true : false);

				if (strlen($metki_f) <= 0)
					$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_METKI");

				if (strlen($strError) <= 0) {
					$arMetki = array();
					foreach (preg_split("/[\D]/i", $metki_f) as $metka) {
						$metka = intval($metka);
						if ($metka > 0)
							$arMetki[] = $metka;
					}

					if (!is_array($arMetki) || count($arMetki) < 1)
						$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_METKI");

					if (strlen($strError) <= 0) {
						$csvFile->SetWidthMap($arMetki);
					}
				}
			}

			if (strlen($strError) <= 0) {
				$bFirstHeaderTmp = $csvFile->GetFirstHeader();
				$csvFile->SetFirstHeader(false);

				if ($arRes = $csvFile->Fetch()) {
					foreach ($arRes as $i => $ar) {
						$arDataFileFields[$i] = $ar;
					}
				} else {
					$strError .= Loc::getMessage("INGATE_SEO_IMP_NO_DATA");
				}

				$NUM_FIELDS = count($arDataFileFields);
			}
		}

		if (strlen($strError) > 0)
			$STEP = 2;
	}
	//Step 3
	if ($STEP > 3) {

		if (strlen($strError) <= 0) {

			if ($import_type == 'meta') {

				$table = '\Ingate\Seo\PageTable';
				$indexFilter = 'URL';

			} elseif ($import_type == 'redirect') {

				$table = '\Ingate\Seo\RedirectTable';
				$indexFilter = 'OLD';

			} elseif ($import_type == 'canonical') {

				$table = '\Ingate\Seo\CanonicalTable';
				$indexFilter = 'URL';

			}

			$csvFile->SetPos($CUR_FILE_POS);

			if ($CUR_FILE_POS <= 0 && $bFirstHeaderTmp) {
				$arRes = $csvFile->Fetch();
			}

			$io = CBXVirtualIo::GetInstance();

			if ($CUR_FILE_POS > 0 && is_set($_SESSION, $CUR_LOAD_SESS_ID)) {

				if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "line_num"))
					$line_num = intval($_SESSION[$CUR_LOAD_SESS_ID]["line_num"]);

				if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "correct_lines"))
					$correct_lines = intval($_SESSION[$CUR_LOAD_SESS_ID]["correct_lines"]);

				if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "error_lines"))
					$error_lines = intval($_SESSION[$CUR_LOAD_SESS_ID]["error_lines"]);

				if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "killed_lines"))
					$killed_lines = intval($_SESSION[$CUR_LOAD_SESS_ID]["killed_lines"]);
			}

			foreach ($arSeoAvailPageFields as $key => $arField) {

				if ($arField["field"] === "ID") {
					for ($i = 0; $i < $NUM_FIELDS; $i++)
						if ($key === $GLOBALS["field_".$i])
							$csvFile->AddPrimaryKey($key, $i);
				} elseif ($arField["field"] === "NAME") {
					for ($i = 0; $i < $NUM_FIELDS; $i++)
						if ($key === $GLOBALS["field_".$i])
							$csvFile->AddPrimaryKey($key, $i);
				}
			}

			$csvFile->NUM_FIELDS = $NUM_FIELDS;

			$csvLine = 0;

			while ($arRes = $csvFile->FetchAssoc()) {

				$csvLine++;
				$strErrorR = "";
				$arFilter = array();
				$arLoadProductArray = array();

				if ($csvLine == 1 && $firstLineIsHeaders) {

					$arCompareTable = $table::getCompareForCSV();
					$arCompare = array_flip($arCompareTable);

					$arHeaders = array_diff(
						array_map(
							function($val) {
								global $arCompare;
								$value = strtoupper(trim($val));
								if ($arCompare[$value]) {
									return $arCompare[$value];
								}
							},
							$arRes
						),
						array('')
					);

					continue;
				}

				$arRes = array_filter($arRes);

				foreach ($arRes as $key => $value) {

					if ($arHeaders[$key])
						$arLoadProductArray[$arHeaders[$key]] = $value;
				}

				$arLoadProductArray['ACTIVE'] = 'Y';

				if (strlen($arLoadProductArray[$indexFilter]))
					$arFilter[$indexFilter] = $arLoadProductArray[$indexFilter];

				if (count($arFilter) < 1) {
					$strErrorR .=
						Loc::getMessage("INGATE_SEO_IMP_LINE_NO")." ".$line_num.". ".
						Loc::getMessage("INGATE_SEO_IMP_NOIDNAME", array("#PROPERTY#" => $arCompareTable[$indexFilter]));
				}

				if (strlen($strErrorR) <= 0) {
					$result = $table::getList(array('filter' => $arFilter));

					if ($arr = $result->fetch()) {
						$PRODUCT_ID = $arr["ID"];
						$result = $table::update($PRODUCT_ID, $arLoadProductArray);
					} else {
						unset($arLoadProductArray['ID']);

						$result = $table::add($arLoadProductArray);
					}

					if (!$result->isSuccess()) {
						$strErrorR .=
							Loc::getMessage("INGATE_SEO_IMP_LINE_NO")." ".$line_num.". ".
							Loc::getMessage("INGATE_SEO_IMP_ERROR_LOADING")." ".
							implode('<br>', $result->getErrorMessages());
					}
				}

				if (strlen($strErrorR) <= 0) {
					$correct_lines++;
				} else {
					$error_lines++;
					$strError .= $strErrorR;
				}

				if (
					intval($max_execution_time) > 0 &&
					(getmicrotime() - START_EXEC_TIME) > intval($max_execution_time)
				) {
					$bAllLinesLoaded = false;
					break;
				}
			}


			if ($bAllLinesLoaded) {
				if (is_set($_SESSION, $CUR_LOAD_SESS_ID))
					unset($_SESSION[$CUR_LOAD_SESS_ID]);

				//README delete pages which no in datafile.
				if ($outFileAction == "D") {
					//Delete
				} elseif ($outFileAction == "F") {
					//Don't change
				} else {
					//Deactivate
				}

				//README deactive pages which are in the datafile.
				if ($inFileAction == "A") {
					//Don't change
				} elseif ($inFileAction == "F") {
					//Activate
				}
			} else {
				if (strlen($CUR_LOAD_SESS_ID) <= 0)
					$CUR_LOAD_SESS_ID = "CL".time();

				$_SESSION[$CUR_LOAD_SESS_ID]["line_num"] = $line_num;
				$_SESSION[$CUR_LOAD_SESS_ID]["correct_lines"] = $correct_lines;
				$_SESSION[$CUR_LOAD_SESS_ID]["error_lines"] = $error_lines;
				$_SESSION[$CUR_LOAD_SESS_ID]["killed_lines"] = $killed_lines;
				$paramsStr = "fields_type=".urlencode($fields_type);
				$paramsStr .= "&first_names_r=".urlencode($first_names_r);
				$paramsStr .= "&delimiter_r=".urlencode($delimiter_r);
				$paramsStr .= "&delimiter_other_r=".urlencode($delimiter_other_r);
				$paramsStr .= "&first_names_f=".urlencode($first_names_f);
				$paramsStr .= "&metki_f=".urlencode($metki_f);
				for ($i = 0; $i < $NUM_FIELDS; $i++) {
					$paramsStr .= "&field_".$i."=".urlencode(${"field_".$i});
				}
				$paramsStr .= "&outFileAction=".urlencode($outFileAction);
				$paramsStr .= "&max_execution_time=".urlencode($max_execution_time);
				$_SESSION[$CUR_LOAD_SESS_ID]["LOAD_SCHEME"] = $paramsStr;
				$curFilePos = $csvFile->GetPos();
			}
		}

		if (strlen($strError) > 0) {
			$strError .= Loc::getMessage("INGATE_SEO_IMP_TOTAL_ERRS")." ".$error_lines.".<br>";
			$strError .=
				Loc::getMessage("INGATE_SEO_IMP_TOTAL_COR1").$correct_lines." ".
				Loc::getMessage("INGATE_SEO_IMP_TOTAL_COR2")."<br>";

			$STEP = 3;
		}
		//*****************************************************************//
	}
}
/////////////////////////////////////////////////////////////////////
$APPLICATION->SetTitle(Loc::getMessage("INGATE_SEO_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
CAdminMessage::ShowMessage($strError);
$strParams = '';
?>
<form method="POST" action="<?=$sDocPath?>?lang=<?=LANG?>" ENCTYPE="multipart/form-data" name="dataload" id="dataload">
<?
$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("INGATE_SEO_IMP_TAB1"),
		"ICON" => "iblock",
		"TITLE" => Loc::getMessage("INGATE_SEO_IMP_TAB1_TITLE"),
	),
	array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("INGATE_SEO_IMP_TAB2"),
		"ICON" => "iblock",
		"TITLE" => Loc::getMessage("INGATE_SEO_IMP_TAB2_TITLE"),
	),
	array(
		"DIV" => "edit3",
		"TAB" => Loc::getMessage("INGATE_SEO_IMP_TAB3"),
		"ICON" => "iblock",
		"TITLE" => Loc::getMessage("INGATE_SEO_IMP_TAB3_TITLE"),
	),
	array(
		"DIV" => "edit4",
		"TAB" => Loc::getMessage("INGATE_SEO_IMP_TAB4"),
		"ICON" => "iblock",
		"TITLE" => Loc::getMessage("INGATE_SEO_IMP_TAB4"),
	),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<?php if ($STEP == 1):?>
	<tr>
		<td width="40%">
			<p><?=Loc::getMessage("INGATE_SEO_IMP_TYPE_SELECT")?></p>
		</td>
		<td width="60%">
			<label>
				<input checked="" type="radio" name="import_type" value="meta">
				<?=Loc::getMessage("INGATE_SEO_IMP_TYPE_META")?>
			</label><br />
			<label>
				<input type="radio" name="import_type" value="redirect">
				<?=Loc::getMessage("INGATE_SEO_IMP_TYPE_REDIRECT")?>
			</label><br />
			<label>
				<input type="radio" name="import_type" value="canonical">
				<?=Loc::getMessage("INGATE_SEO_IMP_TYPE_CANONICAL")?>
			</label><br />
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("INGATE_SEO_IMP_DATA_FILE")?></td>
		<td>
			<input type="text" name="URL_DATA_FILE" value="<?=htmlspecialcharsbx($URL_DATA_FILE)?>" size="30">
			<input type="button" value="<?=Loc::getMessage("INGATE_SEO_IMP_OPEN")?>" OnClick="BtnClick()">
			<?CAdminFileDialog::ShowScript(array(
				"event" => "BtnClick",
				"arResultDest" => array(
					"FORM_NAME" => "dataload",
					"FORM_ELEMENT_NAME" => "URL_DATA_FILE",
				),
				"arPath" => array(
					"SITE" => SITE_ID,
					"PATH" => "/".COption::GetOptionString("main", "upload_dir", "upload"),
				),
				"select" => 'F', // F - file only, D - folder only
				"operation" => 'O', // O - open, S - save
				"showUploadTab" => true,
				"showAddToMenuTab" => false,
				"fileFilter" => 'csv',
				"allowAllFiles" => true,
				"SaveConfig" => true,
			));
			?>
		</td>
	</tr>
<?php endif; //end step 1 ?>

<?php
$tabControl->EndTab();
$tabControl->BeginNextTab();
?>

<?php if ($STEP == 2): ?>
	<tr>
		<td width="40%">&nbsp;</td>
		<td width="60%">&nbsp;</td>
	</tr>
	<input type="hidden" name="fields_type" id="fields_type_R" value="R">
	<tr id="table_r" class="heading">
		<td colspan="2"><?=Loc::getMessage("INGATE_SEO_IMP_FILE_FORMAT")?></td>
	</tr>
	<tr id="table_r1">
		<td class="adm-detail-valign-top"><?=Loc::getMessage("INGATE_SEO_IMP_RAZDEL_TYPE")?></td>
		<td>
			<input type="radio" name="delimiter_r" id="delimiter_r_TZP" value="TZP" <?
			if ($delimiter_r == "TZP" || strlen($delimiter_r) <= 0)
				echo "checked" ?>>
			<label for="delimiter_r_TZP"><?=Loc::getMessage("INGATE_SEO_IMP_TZP")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_r_ZPT" value="ZPT" <?
			if ($delimiter_r == "ZPT")
				echo "checked" ?>>
			<label for="delimiter_r_ZPT"><?=Loc::getMessage("INGATE_SEO_IMP_ZPT")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_r_TAB" value="TAB" <?
			if ($delimiter_r == "TAB")
				echo "checked" ?>>
			<label for="delimiter_r_TAB"><?=Loc::getMessage("INGATE_SEO_IMP_TAB")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_r_SPS" value="SPS" <?
			if ($delimiter_r == "SPS")
				echo "checked" ?>>
			<label for="delimiter_r_SPS"><?=Loc::getMessage("INGATE_SEO_IMP_SPS")?></label><br>
			<input type="radio" name="delimiter_r" id="delimiter_r_OTR" value="OTR" <?
			if ($delimiter_r == "OTR")
				echo "checked" ?>>
			<label for="delimiter_r_OTR"><?=Loc::getMessage("INGATE_SEO_IMP_OTR")?></label>
			<input type="text" name="delimiter_other_r" size="3" value="<?=htmlspecialcharsbx($delimiter_other_r)?>">
		</td>
	</tr>
	<tr id="table_r2">
		<td><?=Loc::getMessage("INGATE_SEO_IMP_FIRST_NAMES")?></td>
		<td>
			<input type="hidden" name="first_names_r" id="first_names_r_N" value="N">
			<input type="checkbox" name="first_names_r" id="first_names_r_Y" value="Y" checked>
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("INGATE_SEO_IMP_DATA_SAMPLES")?></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<?php
			$sContent = "";
			if (strlen($DATA_FILE_NAME) > 0) {
				$DATA_FILE_NAME = trim(str_replace("\\", "/", trim($DATA_FILE_NAME)), "/");
				$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"], "/".$DATA_FILE_NAME);
				if (
					(strlen($FILE_NAME) > 1)
					&& ($FILE_NAME == "/".$DATA_FILE_NAME)
					&& $APPLICATION->GetFileAccessPermission($FILE_NAME) >= "W"
				) {
					$f = $io->GetFile($_SERVER["DOCUMENT_ROOT"].$FILE_NAME);
					$file_id = $f->open("rb");
					$sContent = fread($file_id, 10000);
					fclose($file_id);
				}
			}
			?>
			<textarea name="data" wrap="OFF" rows="10" cols="80" style="width:100%"><?=htmlspecialcharsbx($sContent)?></textarea>
		</td>
	</tr>
<?php endif; //end step 2 ?>

<?php
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
?>

<?php if($STEP == 3) : ?>
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("INGATE_SEO_IMP_ADDIT_SETTINGS")?></td>
	</tr>
	<input type="hidden" id="outFileAction_F" name="outFileAction" value="F">
	<input type="hidden" id="inFileAction_F" name="inFileAction" value="F">
	<tr>
		<td class="adm-detail-valign-top"><?=Loc::getMessage("INGATE_SEO_IMP_AUTO_STEP_TIME") ?></td>
		<td align="left">
			<input type="text" name="max_execution_time" size="6" value="<?=htmlspecialcharsbx($max_execution_time)?>">
			<br><?=Loc::getMessage("INGATE_SEO_IMP_AUTO_STEP_TIME_NOTE")?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("INGATE_SEO_IMP_DATA_SAMPLES")?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<?php
			$sContent = "";
			if (strlen($DATA_FILE_NAME) > 0) {
				$DATA_FILE_NAME = trim(str_replace("\\", "/", trim($DATA_FILE_NAME)), "/");
				$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"], "/".$DATA_FILE_NAME);
				if (
					(strlen($FILE_NAME) > 1)
					&& ($FILE_NAME == "/".$DATA_FILE_NAME)
					&& $APPLICATION->GetFileAccessPermission($FILE_NAME) >= "W"
				) {
					$f = $io->GetFile($_SERVER["DOCUMENT_ROOT"].$FILE_NAME);
					$file_id = $f->open("rb");
					$sContent = fread($file_id, 10000);
					fclose($file_id);
				}
			}
			?>
			<textarea name="data" wrap="OFF" rows="10" cols="80" style="width:100%"><?=htmlspecialcharsbx($sContent)?></textarea>
		</td>
	</tr>
<?php endif; //end step 3?>

<?php
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
?>

<?php if($STEP == 4): ?>
	<tr>
		<td>
		<?echo CAdminMessage::ShowMessage(array(
			"TYPE" => "PROGRESS",
			"MESSAGE" => !$bAllLinesLoaded
				? Loc::getMessage("INGATE_SEO_IMP_AUTO_REFRESH_CONTINUE")
				: Loc::getMessage("INGATE_SEO_IMP_SUCCESS"),
			"DETAILS" =>

				Loc::getMessage("INGATE_SEO_IMP_SU_ALL").' <b>'.$line_num.'</b><br>'
				.Loc::getMessage("INGATE_SEO_IMP_SU_COR").' <b>'.$correct_lines.'</b><br>'
				.Loc::getMessage("INGATE_SEO_IMP_SU_HEADERS").' <b>1</b><br>'
				.Loc::getMessage("INGATE_SEO_IMP_SU_ER").' <b>'.$error_lines.'</b><br>'
				.($outFileAction == "D"
					? Loc::getMessage("INGATE_SEO_IMP_SU_KILLED")." <b>".$killed_lines."</b>"
					: ($outFileAction == "F"
						? ""
						: Loc::getMessage("INGATE_SEO_IMP_SU_HIDED")." <b>".$killed_lines."</b>"
					)
				),
			"HTML" => true,
		)) ?>
		</td>
	</tr>
<?php endif;//end step 4 ?>

<?php
	$tabControl->EndTab();
	$tabControl->Buttons();
?>

<?php if ($STEP < 4): ?>
	<input type="hidden" name="STEP" value="<?echo $STEP + 1;?>">
	<?=bitrix_sessid_post()?>
	<?php if ($STEP > 1): ?>
	<input type="hidden" name="URL_DATA_FILE" value="<?=htmlspecialcharsbx($DATA_FILE_NAME); ?>">
	<input type="hidden" name="import_type" value="<?=htmlspecialcharsbx($import_type); ?>">
	<?php endif; ?>

	<?php if ($STEP <> 2): ?>
	<input type="hidden" name="fields_type" value="<?=htmlspecialcharsbx($fields_type)?>">
	<input type="hidden" name="delimiter_r" value="<?=htmlspecialcharsbx($delimiter_r)?>">
	<input type="hidden" name="delimiter_other_r" value="<?=htmlspecialcharsbx($delimiter_other_r)?>">
	<input type="hidden" name="first_names_r" value="<?=htmlspecialcharsbx($first_names_r)?>">
	<input type="hidden" name="metki_f" value="<?=htmlspecialcharsbx($metki_f)?>">
	<input type="hidden" name="first_names_f" value="<?=htmlspecialcharsbx($first_names_f)?>">
	<?php endif; ?>

	<?php if ($STEP <> 3):
		$postList = $request->getPostList();
		foreach ($postList as $name => $value):
			if (preg_match("/^field_(\\d+)$/", $name)): ?>
				<input type="hidden" name="<?=$name?>" value="<?=htmlspecialcharsbx($value)?>">
			<?
			endif;
		endforeach;?>
	<input type="hidden" name="outFileAction" value="<?=htmlspecialcharsbx($outFileAction)?>">
	<input type="hidden" name="inFileAction" value="<?=htmlspecialcharsbx($inFileAction)?>">
	<input type="hidden" name="max_execution_time" value="<?=htmlspecialcharsbx($max_execution_time)?>">
	<?php endif; ?>

	<?php if ($STEP > 1): ?>
	<input type="submit" name="backButton" value="&lt;&lt; <?=Loc::getMessage("INGATE_SEO_IMP_BACK")?>">
	<?php endif; ?>

	<input type="submit" value="<?=Loc::getMessage("INGATE_SEO_IMP_NEXT_STEP")?> &gt;&gt;" name="submit_btn" class="adm-btn-save">
<?php else: ?>
	<input type="submit" name="backButton2" value="&lt;&lt; <?=Loc::getMessage("INGATE_SEO_IMP_TO_FIRST")?>" class="adm-btn-save">
<?php endif; ?>

<?php $tabControl->End(); ?>
</form>

<script language="JavaScript">
	<!--
<?if ($STEP < 2): ?>
	tabControl.SelectTab("edit1");
	tabControl.DisableTab("edit2");
	tabControl.DisableTab("edit3");
	tabControl.DisableTab("edit4");
<?elseif ($STEP == 2): ?>
	tabControl.SelectTab("edit2");
	tabControl.DisableTab("edit1");
	tabControl.DisableTab("edit3");
	tabControl.DisableTab("edit4");
<?elseif ($STEP == 3): ?>
	tabControl.SelectTab("edit3");
	tabControl.DisableTab("edit1");
	tabControl.DisableTab("edit2");
	tabControl.DisableTab("edit4");
<?elseif ($STEP > 3): ?>
	tabControl.SelectTab("edit4");
	tabControl.DisableTab("edit1");
	tabControl.DisableTab("edit2");
	tabControl.DisableTab("edit3");
<?endif; ?>
	//-->
</script>
<? require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");