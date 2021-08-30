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

//echo'Detail <pre>';print_r($arResult);echo'</pre>';
$this->setFrameMode(true);
?>
<center>
<table style="width: 100%;">
<tr>
<td style="width: 50%; padding: 20px; text-align: justify;">етально о новости 2
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2</td>
<td style="width: 50%; padding: 20px; text-align: justify;"><img src="http://paoloconte.ru/upload/iblock/67e/78076190.jpg"  width="100%"></td>
</tr>
<tr>
<td style="width: 50%; padding: 20px; text-align: justify;"><img src="http://paoloconte.ru/upload/iblock/67e/78076190.jpg"  width="100%"></td>
<td style="width: 50%; padding: 20px; text-align: justify;">етально о новости 2
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 
Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2 Детально о новости 2</td>
</tr>
</table>
<br><br><br><br>
<?echo $arResult["DETAIL_TEXT"];?>
<br>
<?foreach($arResult['SLIDER'] as $arPic) {
$pic = CFile::GetPath($arPic['ID']);?>
<img src="<?=$pic;?>"  width="40%">
<br><br>
<?echo $arPic['TEXT'].'<br><br>';
}?>

<br><br><br><br>
<h1><?=$arResult['NAME']?></h1>
<br><br>
<h4>
<?echo $arResult["DETAIL_TEXT"];?>
</h4>

<br><br>

<div class="main-slider-wrap">
<div class="main-slider">
	<?foreach($arResult['SLIDER'] as $arPic) { ?>
		<?$file = CFile::ResizeImageGet($arPic['ID'], array('width'=>1920, 'height'=>740), BX_RESIZE_IMAGE_EXACT, true);  //style="width:640;height:480;"?>
		
		<div class="slide lazy">
			<div class="slide-bg" style="background-image: url('<?=$file['src']?>')"></div>

			<div class="slide-wrap">
				<div class="slider-content">
					<div class="title">
						<?=$arPic['TEXT'];?>
					</div>
					<div class="description">
						<?=$arPic['NAME']//=$arItem['PROPERTY_PODPIS_VALUE']?>
					</div>
				</div>
			</div>
			
		</div>
	<? } ?>
</div>
</div>
</center>
<?/*
<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?> 
		<img
			class="detail_picture"
			border="0"
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?>***</h3>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
</div>
*/?>