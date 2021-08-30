<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="actions-box-mobile">
	<div class="container">
		<div class="row">
			<?foreach($arResult["ITEMS"] as $arItem){?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
				<div class="col-xs-8">
					<div class="deal-item-events-mobile">
						<div class="action-body-events-mobile">
							<div class="text styled-text-box-events-mobile">
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
								<div class="title-events-mobile">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=TruncateText($arItem['NAME'], 100)?></a>
								</div>
								<?if ($str_time != ''):?>
									<div class="time-box-events-mobile">
										<i class="fa fa-clock-o"></i> <?=$str_time?>
									</div>
								<?endif;?>

								<div class="tite-desc-events-mobile">
									<?echo TruncateText($arItem["PREVIEW_TEXT"], 200);?>
								</div>

								<div class="title-link-events-mobile">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span>Подробнее</span> <i class="fa fa-play"></i></a>
								</div>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="image" style="background-image: url('<?=$file['src']?>')"></div></a>
							</div>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
	</div>
</div>


<div class="pagination-wrap border-top">
	<div class="pagination-box align-center">
		<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
		{
			?><? echo $arResult["NAV_STRING"]; ?><?
		}?>
	</div>
</div>
