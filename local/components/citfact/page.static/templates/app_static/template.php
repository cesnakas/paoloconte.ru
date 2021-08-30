<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    $this->SetFrameMode(true);
    $db_props = CIBlockElement::GetProperty(21, 52129, "sort", "asc", Array("CODE"=>"APP_DESCRIPTION"));
    if($ar_props = $db_props->Fetch()):

    endif;
?>

<div class="container">
    <div class="aside">
        <div class="aside__sidebar">
            <? $APPLICATION->IncludeFile(
                SITE_DIR . "html/include/aside-nav.php",
                Array("MODE" => "html")
            ); ?>
        </div>
        <div class="aside__main">
            <?=$ar_props['VALUE']['TEXT']?>
        </div>
    </div>
</div>