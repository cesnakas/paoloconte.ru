<?
/* @var $USER CUser */
if(!$USER->IsAdmin()) {
    return;
}

\Bitrix\Main\Loader::includeModule( basename(__DIR__) );

$iblocks = array(
    'reference' => array( '-' ),
    'reference_id' => array(0),
);
$rs = CCatalog::GetList(array( 'NAME'=>'ASC' ), array('IBLOCK_ACTIVE'=>'Y'));
while( $row = $rs->Fetch() ) {
    $iblocks['reference'][] = $row['NAME'];
    $iblocks['reference_id'][] = (int)$row['IBLOCK_ID'];
}

$xlsxColumns = array(
    'reference_id' => range(0, Trinet\Seometa\Batchop\xlsxReader::COL_MAX ),
);
$xlsxColumns['reference'] = $xlsxColumns['reference_id'];
$xlsxColumns['reference'][0] = '-';

/* @var $APPLICATION CMain */
/* @var $Apply string */
/* @var $RestoreDefaults string */
/* @var $mid string */

$module = new Trinet\Seometa\Batchop\Installer();

$options = Trinet\Seometa\Batchop\Options::getInstance();

$tmpCols = array(
    'colTitle' => 'Title',
    'colDescription' => 'Description',
    'colHeader' => 'H1',
    'colCrumb' => 'Хлебная крошка',
    'colChpuUrl' => 'ЧПУ url',
    'colRealUrl' => "Url фильтра (старый url)",
    'colSectionUrl' => "Url раздела",
    'colPropertyName' => "Свойство",
    'colPropertyValue' => "Значение",
);

$arAllOptions = array(
    array("iblockId", "* ID инфоблока", "", array("selectbox", $iblocks ), 'int'),
);
foreach ( $tmpCols as $k => $v ) {
    $caption = "Номер колонки в Excel с {$v}";
    if ( $options::COLUMNS[$k] ) {
        $caption = "* ".$caption;
    }
    $arAllOptions[] = array($k, $caption, $options::COLUMNS[$k], array("selectbox", $xlsxColumns ), 'int', );
}
$arAllOptions[] = array("addChpu", "Добавлять ЧПУ?", "N", array("checkbox" ), 'bool');
$arAllOptions[] = array("propertyMap", "карта свойств (ID:название)", "", array("multitext", 28 ), 'string_array');
unset($tmpCols);

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => 'Основные', ),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if( $_SERVER['REQUEST_METHOD'] == "POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid() )
{
    if(strlen($RestoreDefaults)>0)
    {
        Bitrix\Main\Config\Option::delete($module->MODULE_ID);
    }
    else
    {
        $stringTypes = array('string', 'bool', 'int_array', 'string_array', );
        foreach($arAllOptions as $arOption)
        {
            $name=$arOption[0];
            $val=$_REQUEST[$name];

            if ( $arOption[4] == 'int_array' ) {
                $val = implode(',', array_filter( $val ));
            }
            if ( $arOption[4] == 'string_array' ) {
                if ( is_array($val) ) {
                    foreach( $val as &$v ) {
                        $v = trim( $v );
                    }
                    $val = array_values( array_filter( $val ) );

                } else {
                    $val = array();
                }
                $val = serialize( $val );
            }

            if($arOption[4]=="bool" && $val!="Y") {
                $val = "N";
            }

            if($arOption[4]=="int" && $arOption['3'][0] === 'selectbox' ) {
                $val = (int) $val;
                if ( !in_array( $val, $arOption['3'][1]['reference_id'] ) ) {
                    // что-то я не увидел, как выдавать ошибки
                    // поэтому сбрасываем на первое допустимое
                    $val = reset($arOption['3'][1]['reference_id']);
                }
            }
            Bitrix\Main\Config\Option::set($module->MODULE_ID, $name, $val);
        }
        // @TODO сделать проверку, что у свойств привязаны разные столбцы
    }
    if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

if ( !$options->isValid() ) {
    ?><p style="color: red">Настройки заданы неверно</p><?php
}

$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
    <?
    $tabControl->BeginNextTab();
    foreach($arAllOptions as $arOption)
    {
        $val = COption::GetOptionString($module->MODULE_ID, $arOption[0], $arOption[2]);
        $type = $arOption[3];

        $addClass = ($type[0]=="textarea") ? 'class="adm-detail-valign-top"' : '';
        ?>
        <tr>
            <td width="40%" <?=$addClass?> style="white-space: nowrap;" >
                <?
                $target = $arOption[0]; //
                if ( $type[0] == 'multitext' ) {
                    $target .= '[0]';
                } elseif( $type[0] == 'multiselectbox' ) {
                    $target .= '[]';
                }
                $target = preg_replace('/[^\d\-a-z]/i', '-', $arOption[0]);
                ?>
                <label for="<?=$target?>"><?=$arOption[1]?>:</label>
            </td>
            <td width="60%">
                <?
                switch ( $type[0] ) {
                    case 'checkbox':
                        $checked = ($val=="Y") ? 'checked="checked"' : '';
                        ?><input type="checkbox" id="<?=$target?>" name="<?=htmlspecialcharsbx($arOption[0])?>" value="Y" <?=$checked?> ><?
                        break;

                    case 'text':
                        ?><input type="text" id="<?=$target?>" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($arOption[0])?>"><?
                        break;

                    case 'multitext':
                        $val = unserialize( $val );
                        $i = 0;
                        foreach( $val as $v ) {
                            $addId = ( $i ==0 ) ? "id='{$target}'" : '';
                            ?><input type="text" <?=$addId?> size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($v)?>" name="<?echo htmlspecialcharsbx($arOption[0])?>[<?=$i?>]"><br /><?
                            $i++;
                        }
                        ?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="" name="<?echo htmlspecialcharsbx($arOption[0])?>[<?=$i?>]"><?

                        break;

                    case 'textarea':
                        ?><textarea id="<?=$target?>" rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($arOption[0])?>"><?echo htmlspecialcharsbx($val)?></textarea><?
                        break;

                    case 'selectbox':
                        echo SelectBoxFromArray($arOption[0], $arOption[3][1], $val, '', "id='{$target}' class='typeselect'" );
                        break;
                    case 'multiselectbox':
                        $val = explode(',', $val);
                        echo SelectBoxMFromArray($arOption[0].'[]', $arOption[3][1], $val, '', false, 20, "id='{$target}' class='typeselect'" );
                        break;
                }
                ?>
            </td>
        </tr>
        <?
    }
    ?>
    <?$tabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
    <input type="submit" name="Apply" value="Применить" title="Применить">
    <?if(strlen($_REQUEST["back_url_settings"])>0):?>
        <input type="button" name="Cancel" value="Вернуться" title="Вернуться" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
        <input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
    <?endif?>
    <?=bitrix_sessid_post();?>
    <?$tabControl->End();?>
</form>