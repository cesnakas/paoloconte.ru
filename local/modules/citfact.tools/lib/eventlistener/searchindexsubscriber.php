<?php


namespace Citfact\EventListener;

use Citfact\Core;
use Citfact\Tools;

class SearchIndexSubscriber
{
    function deleteRestartedSphinx()
    {
        if (Tools::isDev()) {
            $sphinxPatch = '/home/dev/paoloconte/projects/paoloconte/paoloconte.sphinx/data/userdefined/';
        } else {
            $sphinxPatch = '/home/bitrix/sphinx/data/userdefined/';
        }

        unlink($sphinxPatch . "restarted");
    }

    function updateSearchIndex(&$arFields)
    {
        \CIBlockElement::UpdateSearch($arFields['ID'], true);
    }

    function addArticle(&$arFields)
    {
        $core = Core::getInstance();
        $catalogIblockId = $core->getIblockId('main_catalog');

        if ('iblock' != $arFields['MODULE_ID'] || $catalogIblockId != $arFields['PARAM2']) {
            return $arFields;
        }

        $arItem = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $catalogIblockId, 'ID' => $arFields['ITEM_ID']],
            false, false,
            ['IBLOCK_ID', 'ID', 'PROPERTY_CML2_ARTICLE']
        )->Fetch();

        if (!empty($arItem['PROPERTY_CML2_ARTICLE_VALUE'])) {
            $arFields["TITLE"] .= ' ' . str_replace('-', '_', $arItem['PROPERTY_CML2_ARTICLE_VALUE']);
        }

        return $arFields;
    }
}