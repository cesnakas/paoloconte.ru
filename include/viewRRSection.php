<?
if (!empty($_REQUEST['SECTION_PATH'])) {
	$arSectionsCode = explode('/', $_REQUEST['SECTION_PATH']);
	$arNewSection = array();
	$db_list = CIBlockSection::GetList(array('ID' => 'ASC'), Array('IBLOCK_ID' => IBLOCK_CATALOG, 'CODE'=>$arSectionsCode, 'GLOBAL_ACTIVE'=>'Y', 'ACTIVE'=>'Y'), false, array('IBLOCK_ID','NAME', 'ID', 'SECTION_PAGE_URL'));
	while($ar_result = $db_list->GetNext())
	{
		$arNewSection[$ar_result['CODE']] = $ar_result;
	}

	if (!empty($arNewSection)) {
		$curSection = array();
		$arSectionsCode = array_reverse($arSectionsCode);
		foreach ($arSectionsCode as $code) {
			if (!empty($arNewSection[$code])) {
				$curSection = $arNewSection[$code];
				break;
			}
		}
		?>
		<div data-retailrocket-markup-block="56fa3d5165bf1934501740d4" data-category-path="<?=$curSection['SECTION_PAGE_URL'];?>"  data-category-name="<?=$curSection['NAME'];?>"></div>
		<?
	}else{?>
		<div data-retailrocket-markup-block="56fa3d5165bf1934501740d4"></div>
	<?}
}?>