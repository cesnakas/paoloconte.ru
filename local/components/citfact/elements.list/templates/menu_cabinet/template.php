<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs(__DIR__ . "/script.js");
?>
<?if (!empty($arResult['BLOCKS']['top'])):?>
<div class="aside-nav">
    <?foreach($arResult['BLOCKS']['top'] as $arItem):?>
        <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"
           title="<?=$arItem['NAME']?>"
           class="aside-nav__link <?=$arItem['SELECTED'] == 'SELECTED'? 'active':''?>">
            <span data-target="toggle">
                <svg class='i-icon'> 
                    <use xlink:href='#cart'/>
                </svg>
            </span>   
            <span <?=$arItem['SELECTED'] == 'SELECTED'? 'data-target="self" ':''?>><?=$arItem['NAME']?></span>
        </a>
    <?endforeach?>
</div>
<?endif?>

<?if (!empty($arResult['BLOCKS']['bottom'])):?>
<div class="aside-nav">
    <?foreach($arResult['BLOCKS']['bottom'] as $arItem):?>
        <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"
           title="<?=$arItem['NAME']?>"
           class="aside-nav__link <?=$arItem['SELECTED'] == 'SELECTED'? 'active':''?>">
            <span data-target="toggle">
                <svg class='i-icon'> 
                    <use xlink:href='#cart'/>
                </svg>
            </span>   
            <span <?=$arItem['SELECTED'] == 'SELECTED'? 'data-target="self" ':''?>><?=$arItem['NAME']?></span>
        </a>
    <?endforeach?>

    <?if ($USER->IsAuthorized()):?>
        <a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
            "login",
            "logout",
            "register",
            "forgot_password",
            "change_password"));?>"
           class="aside-nav__link aside-nav__link--logout"
           title="Выход">
            <svg class='i-icon'>
                <use xlink:href='#arrow-lk'/>
            </svg>
            Выход
        </a>
    <?endif?>
</div>
<?endif?>

<?if (!empty($arResult['BLOCKS']['about'])):?>
    <div class="aside-nav">
        <?foreach($arResult['BLOCKS']['about'] as $arItem):?>
            <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"
               title="<?=$arItem['NAME']?>"
               class="aside-nav__link <?=$arItem['SELECTED'] == 'SELECTED'? 'active':''?>">
                <span data-target="toggle">
                    <svg class='i-icon'> 
                        <use xlink:href='#cart'/>
                    </svg>
                </span>   
                <span <?=$arItem['SELECTED'] == 'SELECTED'? 'data-target="self" ':''?>><?=$arItem['NAME']?></span>
            </a>
        <?endforeach?>
    </div>
<?endif?>

<?if (!empty($arResult['BLOCKS']['help'])):?>
    <div class="aside-nav">
        <?foreach($arResult['BLOCKS']['help'] as $arItem):?>
            <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"
               title="<?=$arItem['NAME']?>"
               class="aside-nav__link <?=$arItem['SELECTED'] == 'SELECTED'? 'active':''?>">
                <span data-target="toggle">
                    <svg class='i-icon'> 
                        <use xlink:href='#cart'/>
                    </svg>
                </span>    
                <span <?=$arItem['SELECTED'] == 'SELECTED'? 'data-target="self" ':''?>><?=$arItem['NAME']?></span>
            </a>
        <?endforeach?>
    </div>
<?endif?>

<style>
    @media (max-width: 767px) {
        .aside-nav__link {
            display: block;
        }

        .aside-nav__link:hover {
            color: #aaa;
        }

        .aside-nav__link svg {
            visibility: hidden;
        }
    }
</style>