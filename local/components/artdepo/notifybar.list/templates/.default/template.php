<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$style = '';
	?>

    <?if($arItem["USE_GRADIENT"]):
        $grFrom = "#".$arItem["DISPLAY_PROPERTIES"]["GRADIENT_FROM"]["VALUE"];
        $grTo = "#".$arItem["DISPLAY_PROPERTIES"]["GRADIENT_TO"]["VALUE"];
        $style = "background: $grFrom;";
        $style .= "background: -moz-linear-gradient(top, $grFrom, $grTo);";
        $style .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, $grFrom), color-stop(100%, $grTo));";
        $style .= "background: -webkit-linear-gradient(top, $grFrom, $grTo);";
        $style .= "background: -o-linear-gradient(top, $grFrom, $grTo);";
        $style .= "background: -ms-linear-gradient(top, $grFrom, $grTo);";
        $style .= "background: linear-gradient(to top, $grFrom, $grTo);";
        ?>
    <?endif;?>

	<div class="notifybar_top<?if($arItem["USE_GRADIENT"]):?> gradient_<?=$arItem["ID"]?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" style="<?if(is_array($arItem["DISPLAY_PROPERTIES"]["BACKGROUND"])):?>background-color: #<?=$arItem["DISPLAY_PROPERTIES"]["BACKGROUND"]["VALUE"]?>;<?endif;?><?if($arResult["BLOCK_HEIGHT"]):?>height:<?=$arResult["BLOCK_HEIGHT"]?>px;<?endif;?><?=$style?>" data-id="<?=$arItem["ID"]?>">
		<!-- Flash banner -->
		<?if($arItem["BANNER_TYPE"] == "FLASH"):?>
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["WIDTH"]?>" height="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["HEIGHT"]?>" align="middle">
				<param name="movie" value="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["VALUE"]?>" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="play" value="true" />
				<param name="loop" value="true" />
				<param name="wmode" value="window" />
				<param name="scale" value="showall" />
				<param name="menu" value="true" />
				<param name="devicefont" value="false" />
				<param name="salign" value="" />
				<param name="allowScriptAccess" value="sameDomain" />
				<!--[if !IE]>-->
				<object type="application/x-shockwave-flash" data="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["VALUE"]?>" width="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["WIDTH"]?>" height="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["HEIGHT"]?>">
					<param name="movie" value="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["VALUE"]?>" />
					<param name="quality" value="high" />
					<param name="bgcolor" value="#ffffff" />
					<param name="play" value="true" />
					<param name="loop" value="true" />
					<param name="wmode" value="window" />
					<param name="scale" value="showall" />
					<param name="menu" value="true" />
					<param name="devicefont" value="false" />
					<param name="salign" value="" />
					<param name="allowScriptAccess" value="sameDomain" />
				<!--<![endif]-->
					<a href="http://www.adobe.com/go/getflash">
						<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="��������� Adobe Flash Player" />
					</a>
			    <!--[if !IE]>-->
			    </object>
			    <!--<![endif]-->
			</object>
	    <?endif;?>

	    <!-- Image banner -->
	    <?if($arItem["BANNER_TYPE"] == "IMAGE"):?>
		    <img style="border: 0" src="<?=$arItem["DISPLAY_PROPERTIES"]["IMAGE"]["VALUE"]?>" alt="">
	    <?endif;?>

	    <!-- Text -->
	    <?if($arItem["BANNER_TYPE"] == "HTML" && $arItem["PREVIEW_TEXT"]):?>
		    <table style="border: 0; border-spacing: 0; border-collapse:collapse; margin:0; width: 100%; <?if($arResult["BLOCK_HEIGHT"]):?>height: <?=$arResult["BLOCK_HEIGHT"]?>%<?endif?>"><tr><td style="padding:0;margin:0;">
		        <div class="notifybar_text"><?echo $arItem["PREVIEW_TEXT"];?></div>
	        </td></tr></table>
	    <?endif?>

	    <!-- Target link -->
	    <?if(is_array($arItem["DISPLAY_PROPERTIES"]["LINK"]) && $arResult["USER_HAVE_ACCESS"]):?>
		    <a href="<?=$arItem["DISPLAY_PROPERTIES"]["LINK"]["VALUE"]?>" class="notifybar_clickable" target="<?=$arResult["A_TARGET"]?>"></a>
	    <?endif;?>

	    <!-- Close button -->
	    <?if($arResult["SHOW_CLOSE_BTN"] == "Y"):?>
		    <!-- <a href="#" class="notifybar_btn_close" id="notifybar_btn_close">&times;</a> -->
		    <a href="#" class="notifybar_btn_close" id="notifybar_btn_close"><img	    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAC4jAAAuIwF4pT92AAACYUlEQVR42s2XS0scQRDH18WNrzUaIV9g3Sh4SL6GED2o0fUdchEFyfcQQ/YgwSie/AB6EUkO4ivHxIsIPkgwEY16NNH4GJ38C2pgKKpnetZBtuEHu9X1mpmuru6E67oJS2rAIJgGK2ALnDJbLJsC/eCxrV8bpWdgBvxz7ccFJ1p/nwQqwDi4dgsfV2CMfUVKoIFfa1xjk9+kVQIvwInB0SGYBC2gCTxl6Hcr+Mg62jgGz8MSyLKiHD/BEEhZrJlHYBgcKH5+g4wpAVq5u4rRLCiPUC0e5Wwrxw6o1hLIK8rvQbKA4B5J9iHHO5lAg7La55TgScug8v+8Uh31/gRmhMI+SAtH9Im+8EZjCj7AOnIjIl+/RIxpL4FacCkmXwsHdWCD5+7AiBJ8hOdc1q0T829EDNrYamiiV0xQpqU+wxKwLHQctvN0+ljmH8ts6+mklMrIJXj/9o8J5em0APS/hxNxQhL0+CD0Jkm4LoTNhu/bbwikyfoMPl4K3VUSfhfCTMAiewVuArZcqqSOAPus0N8j4bkQVoWUGbXkWyW4w1UQZJsWNn9I+FcI0yFO2g0dkmRtIbbVwuYsoWy/2ZCndwI+gcM6JvtGob8dZRF2Kd9fW4Sk0xllEdqUYbchuKkMSTdnW4baRpR6yI2oSlmIoyLzSrAUUmodvsW5pBzD3soKIL+mZnTIPSKuZvQEHJmakakdL8TYjhfD2rHpQJKP4UCStzmQEGXgm6L8iV9h1OBk81nx95VjFd+hNOqxPMtPUsZn/liO5UVxMSmKq1lRXE6DrufUwH7wYfaSf68Vcj3/DymIxJU9j9SuAAAAAElFTkSuQmCC"
			    width="32" height="32"
			    alt="x"></a>
	    <?endif;?>
    </div>
<?endforeach;?>

<?
global $USER;
if(!$USER->isAdmin() && $arParams["FIXED"] == "Y"):?>
<script type="text/javascript">
	if( typeof(document.getElementsByClassName) == 'function' ) {
		var nb = document.getElementsByClassName('notifybar_top');
		if (nb) {
			var nb1 = nb[0],
				nb1_height = parseInt(nb1.clientHeight),
				parent = nb1.parentNode;
			var fakeDiv = document.createElement('div');
			fakeDiv.setAttribute('style', 'height:' + nb1_height + 'px;');
			parent.insertBefore(fakeDiv, nb1);
			nb1.className = 'notifybar_top notifybar_fixed';
		}
	}
</script>


<?endif;?>