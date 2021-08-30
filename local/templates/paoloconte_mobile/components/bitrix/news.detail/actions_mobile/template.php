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
<style>
    #centerLayer {
        max-width: 800px !important; /* Ширина слоя в пикселах */
        width: 100% !important;
        margin: 0 auto; /* Отступ слева и справа */
        padding: 10px; /* Поля вокруг текста */
        text-align: center; /* Выравнивание содержимого слоя по левому краю */
        line-height: 1.8;
    }
    .slideText{
        position: absolute;
        bottom: 0px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        text-align: center;
        padding: 15px 10px;
        width: 100%;
        float: left;
    }

    #pagPlace{
        display: none;
        width: 100%;
        height: 70px;
    }

    #pagPlace .pagItem{
        cursor: pointer;
        float: left;
        margin: 10px 5px;
        background-size: cover;
        background-position: center;
        width: 70px;
        height: 40px;
        border: 3px solid white;
        -webkit-box-shadow: 0px 0px 1px 1px rgba(0,0,0,0.52);
        -moz-box-shadow: 0px 0px 1px 1px rgba(0,0,0,0.52);
        box-shadow: 0px 0px 1px 1px rgba(0,0,0,0.52);
    }

    .owl-controls{
        display: block;
    }

    .owl-controls .owl-dots{
        bottom: -45px!important;
    }

    .owl-controls .owl-dots .owl-dot span:after{
        background: #D4D4D4;
    }

    .owl-controls .owl-dots .owl-dot:before{
        border-top: 1px dotted #D4D4D4;
    }


    <? if($arResult['PROPERTIES']['SLIDER_SHOW_PREVIEW']['VALUE'] != "Да")
    {
    echo ".owl-controls{display:none!important;}";
    }
    ?>

</style>


<div class="detail-action-wrap" >
	<?$file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=>1370, 'height'=>740), BX_RESIZE_IMAGE_PROPORTIONAL);
	$str_time = '';
	if (!empty($arResult['DATE_DIFF'])) {
		$days = $arResult['DATE_DIFF']['days'];
		$invert = $arResult['DATE_DIFF']['invert'];
		if ($days > 0 && $invert != 1)
			$str_time = \Citfact\Tools::declension($days, array("день", "дня", "дней")) . ' до завершения акции';
		elseif ($days == 0)
			$str_time = 'Акция завершается сегодня';
		elseif  ($days > 0 && $invert == 1)
			$str_time = 'Акция завершена';
	}
	?>
	<?if (strlen($file['src']) > 0)
	{?>
	<div class="image">
		<img src="<?=$file['src']?>" alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>">
	</div>
	<?}?>

	<?if ($str_time != ''):?>
		<?$this->SetViewTarget("action_time");?>
			<?//данный код будет перемещен в контейнер "action_time" в header.php?>
			<span class="time-box"><i class="fa fa-clock-o"></i> <?=$str_time?></span>
		<?$this->EndViewTarget();?>
	<?endif;?>

	<?=$arResult['DETAIL_TEXT']?>

	<?if (!empty($arResult['CITIES_LIST'])):?>
		<div class="cities">Акция действует в городах:
			<span class="list">
				<ul>
				<?foreach ($arResult['CITIES_LIST'] as $arCity) {?>
					<li><a href="/shops/<?=$arCity['CODE']?>/"><?=$arCity['NAME']?></a></li>
				<?}?>
				</ul>
			</span>
		</div>
	<?endif?>
</div>


<div id="sliderWrapper" class="owl-carousel owl-theme" style="margin-bottom: 100px">
<?
foreach ($arResult['SLIDER'] as $k => $arPic)
{
    echo '<div class="item"><img style="width: 100%" src="'.$arPic['SRC'].'">';
    if($arPic["TEXT"] != '')
    {
        echo '<div class="slideText">'.$arPic["TEXT"].'</div>';
    }
    echo '</div>';
}?>
</div>

<script>
    $(document).ready(function(){

        function afterOWLinit() {
            $("#pagPlace .pagItem").each(function (index) {
                $(this).on("click", function () {
                    owl = $(".owl-carousel").data('owlCarousel');
                    owl.to(index);
                });
            });
        }

        $(".owl-carousel").owlCarousel(
            {
                items:1,
                margin:40,
                loop: true,
                autoplay: <? if($arResult['PROPERTIES']['SLIDER_AUTOPLAY']['VALUE'] == "Да") echo "true"; else echo "false"; ?>,
                autoplayTimeout: 3000,
                navigation: false,
                pagination: false,
                paginationSpeed : 1000,
                goToFirstSpeed : 2000,
                autoHeight : true,
                afterInit: afterOWLinit() // do some work after OWL init
            }
        );

    });
</script>