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

    <div id="sliderWrapper<?=$arParams['IBLOCK_SECTION']?>" class="owl-carousel owl-theme altSlider">
    <?
    foreach ($arResult['SLIDE'] as $k => $arPic)
    {
        echo '<div class="item"><img style="width: 100%" src="'.$arPic['IMAGE'].'">';
        if($arPic["TEXT"] != '')
        {
            if($arPic["TYPE"] == 'html') $arPic["TEXT"] = htmlspecialcharsBack($arPic["TEXT"]);
            echo '<div class="slideText">'.$arPic["TEXT"].'</div>';
        }
        echo '</div>';
    }?>
    </div>

    <?
    if($arParams['DISPLAY_PREVIEW'] == "Y") { ?>
        <div id="pagPlace<?=$arParams['IBLOCK_SECTION']?>" class="slPagPlace">
        <?
        foreach ($arResult['SLIDE'] as $k => $arPic)
        {
            echo '<div class="pagItem" style="background-image: url('.$arPic['IMAGE'].');"></div>';
        }
        ?>
        </div>
    <?}?>

    <script>
        $(document).ready(function(){
            function afterOWLinit<?=$arParams['IBLOCK_SECTION']?>() {
                $("#pagPlace<?=$arParams['IBLOCK_SECTION']?> .pagItem").each(function (index) {
                    $(this).on("click", function () {
                        owl = $("#sliderWrapper<?=$arParams['IBLOCK_SECTION']?>").data('owlCarousel');
                        owl.to(index);
                    });
                });
            }

            $("#sliderWrapper<?=$arParams['IBLOCK_SECTION']?>").owlCarousel(
                {
                    items:1,
                    margin:40,
                    loop: true,
                    autoplay: <? if($arParams['ACTIVE_AUTOSCROLLING'] == "Y") echo "true"; else echo "false"; ?>,
                    autoplayTimeout: 3000,
                    navigation: false,
                    pagination: false,
                    paginationSpeed : 1000,
                    goToFirstSpeed : 2000,
                    autoHeight : true,
                    afterInit: afterOWLinit<?=$arParams['IBLOCK_SECTION']?>()
                }
            );
        });
    </script>