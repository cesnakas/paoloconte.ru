<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>

<?
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<?
if($arResult["bDescPageNumbering"] === true):
/*$bFirst = true;
if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
    if($arResult["bSavePage"]):
?>

        <a class="modern-page-previous" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a>
<?
    else:
        if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):
?>
        <a class="modern-page-previous" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a>
<?
        else:
?>
        <a class="modern-page-previous" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a>
<?
        endif;
    endif;

    if ($arResult["nStartPage"] < $arResult["NavPageCount"]):
        $bFirst = false;
        if($arResult["bSavePage"]):
?>
        <a class="modern-page-first" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>">1</a>
<?
        else:
?>
        <a class="modern-page-first" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a>
<?
        endif;
        if ($arResult["nStartPage"] < ($arResult["NavPageCount"] - 1)):

?>
        <a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=intVal($arResult["nStartPage"] + ($arResult["NavPageCount"] - $arResult["nStartPage"]) / 2)?>">...</a>
<?
        endif;
    endif;
endif;
do
{
    $NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;

    if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
    <span class="<?=($bFirst ? "modern-page-first " : "")?>modern-page-current"><?=$NavRecordGroupPrint?></span>
<?
    elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):
?>
    <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "modern-page-first" : "")?>"><?=$NavRecordGroupPrint?></a>
<?
    else:
?>
    <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"<?
        ?> class="<?=($bFirst ? "modern-page-first" : "")?>"><?=$NavRecordGroupPrint?></a>
<?
    endif;

    $arResult["nStartPage"]--;
    $bFirst = false;
} while($arResult["nStartPage"] >= $arResult["nEndPage"]);

if ($arResult["NavPageNomer"] > 1):
    if ($arResult["nEndPage"] > 1):
        if ($arResult["nEndPage"] > 2):
?>
    <a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] / 2)?>">...</a>
<?
        endif;
?>
    <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><?=$arResult["NavPageCount"]?></a>
<?
    endif;

?>
    <a class="modern-page-next"href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_next")?></a>
<?
endif;
*/
else:?>
	<?
	$bFirst = true;

	if ($arResult["NavPageNomer"] > 1):
		if($arResult["bSavePage"]):
			?>
			<a class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><i class="icon-arrow-left"></i></a>
			<?
		else:
			if ($arResult["NavPageNomer"] > 2):
				?>
				<a class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><i class="icon-arrow-left"></i></a>
				<?
			else:
				?>
				<a class="prev" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><i class="icon-arrow-left"></i></a>
				<?
			endif;

		endif;

		if ($arResult["nStartPage"] > 1):
			$bFirst = false;
			if($arResult["bSavePage"]):
				?>
				<a class="modern-page-first" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1">1</a>
				<?
			else:
				?>
				<a class="modern-page-first" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a>
				<?
			endif;
			if ($arResult["nStartPage"] > 2):
				?>
				<a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>">...</a>
				<?
			endif;
		endif;
	endif;

	$navSubElemCount = 0;
	$elemAfterActive = 0;
	$elemBeforeActive = 0;
	do
	{
		if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
			?>
			<b class="active"><?=$arResult["nStartPage"]?></b>
			<?
		elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
			?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "modern-page-first" : "")?>"><?=$arResult["nStartPage"]?></a>
			<?
		else:
			?>
			<?

			if($arResult["NavPageCount"] >4)
			{

				//echo $elemAfterActive;

			if($bFirst == false && ($navSubElemCount == 0 || $navSubElemCount == 3)
				&& $arResult["nStartPage"]-1 != $arResult["NavPageNomer"]
				&& $arResult["nStartPage"]+1 != $arResult["NavPageNomer"]
				&& $arResult["nStartPage"] != $arResult["NavPageCount"])
				$additionClass = ' hideOn440';
			elseif($bFirst == false && $arResult["nStartPage"] != $arResult["NavPageCount"])
				$additionClass = ' hideOn440';

			if($bFirst == false && $arResult["nStartPage"] > $arResult["NavPageNomer"]) $elemAfterActive++;
			if($bFirst == false && $arResult["nStartPage"] < $arResult["NavPageNomer"]) $elemBeforeActive++;

			if($bFirst == false && $elemAfterActive < 3 && $arResult["NavPageNomer"] == 1 )
				$additionClass = '';


			//echo '<pre>'.print_r($arResult,true).'</pre>';

				if($arResult['NavPageNomer'] == 1)
				{
					if($bFirst == false && $elemAfterActive == 1)
						$additionClass = ' hideOn320';

					if($bFirst == false && $elemAfterActive == 2)
						$additionClass = ' hideOn320';
				}
				else
				{
					if($bFirst == false && $elemAfterActive == 1)
						$additionClass = ' hideOn320';

					if($bFirst == false && $elemAfterActive == 2)
						$additionClass = ' hideOn440';
				}




			if($bFirst == false && $elemBeforeActive > 2 && $elemBeforeActive < 5 && $arResult["NavPageNomer"] == $arResult["NavPageCount"] )
				$additionClass = '';


			if($arResult["nStartPage"] == $arResult["NavPageCount"])
				$additionClass = 'modern-page-last';
			}

			?>



			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"<?
			?> class="<?=($bFirst ? "modern-page-first" : "modern-page")?><?=$additionClass?>"><?=$arResult["nStartPage"]?></a>


			<?
			if($bFirst == false) $navSubElemCount++;
		endif;
		$arResult["nStartPage"]++;
		$bFirst = false;
	} while($arResult["nStartPage"] <= $arResult["nEndPage"]);

	if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
			if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
				?>
				<a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>">...</a>
				<?
			endif;
			?>
			<a class="modern-page-last" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a>
			<?
		endif;
		?>
		<a class="next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><i class="icon-arrow-right"></i></a>
		<?
	endif;?>
<?endif;

if ($arResult["bShowAll"]):
	if ($arResult["NavShowAll"]):
		?>
		<a class="modern-page-pagen" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0"><?=GetMessage("nav_paged")?></a>
		<?
	else:
		?>
		<a class="modern-page-all" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1"><?=GetMessage("nav_all")?></a>
		<?
	endif;
endif
?>