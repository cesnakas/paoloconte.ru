<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<?foreach($arResult["ITEMS"] as $arItem){?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="deal-item">
		<div class="container">
			<div class="action-body">
				<div class="text styled-text-box">
					<?$file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>160, 'height'=>240), BX_RESIZE_IMAGE_EXACT);
					$str_time = '';
					if (!empty($arItem['DATE_DIFF'])) {
						$days = $arItem['DATE_DIFF']['days'];
						$invert = $arItem['DATE_DIFF']['invert'];
						if ($days > 0 && $invert != 1)
							$str_time = \Citfact\Tools::declension($days, array("день", "дня", "дней")) . ' до завершения акции';
						elseif ($days == 0)
							$str_time = 'Акция завершается сегодня';
						elseif  ($days > 0 && $invert == 1)
							$str_time = 'Акция завершена';
					}
					?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="image" style="background-image: url('<?=$file['src']?>')"></div></a>

					<div class="title">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem['NAME']?></a> <?if ($str_time != ''):?><span class="time-box"><i class="fa fa-clock-o"></i> <?=$str_time?></span><?endif;?>
					</div>

					<div class="tite-desc">
						<?echo $arItem["PREVIEW_TEXT"];?>
					</div>

					<div class="title-link">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span>Подробнее</span> <i class="fa fa-play"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<? } ?>