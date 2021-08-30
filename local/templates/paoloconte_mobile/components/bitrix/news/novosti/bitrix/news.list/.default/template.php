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
//echo'List <pre>';print_r($arResult['ITEMS']);echo'</pre>';
$this->setFrameMode(true);
?>
<?/*
<div class="main-slider">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?$file = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width'=>1920, 'height'=>740), BX_RESIZE_IMAGE_EXACT, true);?>
		<div class="slide lazy">
			<div class="slide-bg" style="background-image: url('<?=$file['src']?>')"></div>

			<div class="slide-wrap">
				<div class="slider-content">
					<div class="title">
						<?=$arItem['NAME']?>
					</div>
					<div class="description">
						<?=$arItem['PREVIEW_TEXT']//=$arItem['PROPERTY_PODPIS_VALUE']?>
					</div>
					<?if ($arItem['DETAIL_PAGE_URL'] != ''):?>
					<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-icon btn-gold small mode2 icon-arrow-right">
						Подробнее
					</a>
					<?endif;?>
				</div>
			</div>
		</div>
	<? } ?>
</div>

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/css/owl_carousel.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/css/owl_transitions.css");?>
<style>
.header_slider_block {
    display: block;
    float: left;
    width: 550px;
    height: 250px;
    margin: 0 0 0 50px;
    overflow: hidden;
	}
.owl-carousel {
    display: none;
    position: relative;
    width: 100%;
	}
.owl-carousel .owl-wrapper-outer {
    overflow: hidden;
    position: relative;
    width: 100%;
	}
</style>


		<div class="header_slider_block">
			<div class="owl_carousel">
				<?foreach ($arResult['ITEMS'] as $item) { ?>
					<div>
					<a href="<?=$item['DETAIL_PAGE_URL']?>" target="_blank"><h2><?=$item['NAME']?></h2></a>
					<img src="<?=$item['DETAIL_PICTURE']['SRC']?>" width="550" height="250" >
					</div>
				<? } ?>
			</div>
		</div>
		<script>
			$(document).ready(function(){
				$('.header_slider_block .owl_carousel').owlCarousel({
					autoPlay: 900,
					slideSpeed: 100,
					pagination:false,
					//paginationSpeed: 400,
					singleItem: true,
					mouseDrag: false,
					transitionStyle: "fade",
					rewindNav: true
				});
			});
		</script>
*/?>
<div class="actions-box">

	<?foreach($arResult["ITEMS"] as $arItem){?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="deal-item">
			<div class="container">
				<div class="action-body">
					<div class="text styled-text-box">
						<?$file = CFile::ResizeImageGet($arItem['DETAIL_PICTURE']['ID'], array('width'=>160, 'height'=>240), BX_RESIZE_IMAGE_EXACT);
						?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="image"><img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" width="480px; ></div></a>

						<div class="title">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem['NAME']?></a> <span class="time-box">
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
</div>

<?/*
<div class="container">
	<div class="pagination-wrap emulate-table full">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<br /><?=$arResult["NAV_STRING"]?>
		<?endif;?>
	</div>
</div>

<?/*
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
						class="preview_picture"
						border="0"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
						height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
						style="float:left"
						/></a>
			<?else:?>
				<img
					class="preview_picture"
					border="0"
					src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
					width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
					height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
					title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
					style="float:left"
					/>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
			<?else:?>
				<b><?echo $arItem["NAME"]?></b><br />
			<?endif;?>
		<?endif;?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>
		<?foreach($arItem["FIELDS"] as $code=>$value):?>
			<small>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			</small><br />
		<?endforeach;?>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<small>
			<?=$arProperty["NAME"]?>:&nbsp;
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</small><br />
		<?endforeach;?>
	</p>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
*/?>
