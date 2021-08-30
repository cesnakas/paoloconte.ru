<?

namespace Citfact;

use CCatalogSku;

class SetMarkingProduct
{
    public function __construct()
    {
        \CModule::IncludeModule("iblock");
        set_time_limit(36000);
    }

    protected function getOffersByProducts($productID)
    {
        return CCatalogSKU::getOffersList(
            $productID,
            0,
            array(),
            array('ID'),
            array()
        );
    }

    protected function getIdProductsBySectionCode($IBLOCK_ID, $arSections)
    {
        \CModule::IncludeModule("iblock");
        set_time_limit(36000);

        $arSectionIds = array();

        if (!empty($arSections)) {
            $arSectionFilter = Array('IBLOCK_ID' => $IBLOCK_ID, 'CODE' => $arSections);
            $resSection = \CIBlockSection::GetList(array(), $arSectionFilter, false, array('ID'));
            while ($secResult = $resSection->GetNext()) {
                $arSectionIds[] = $secResult['ID'];
            }
        }

        $arFilter = array(
            'IBLOCK_ID' => $IBLOCK_ID,
            'INCLUDE_SUBSECTIONS' => 'Y'
        );
        if (!empty($arSectionIds)) {
            $arFilter['SECTION_ID'] = $arSectionIds;
        }

        $arSelect = array(
            'ID'
        );

        $dbList = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arElementsToUpdate = array();
        while ($arResult = $dbList->GetNext()) {
            $arElementsToUpdate[] = $arResult['ID'];
        }
        return array_unique($arElementsToUpdate);
    }

    public function setMarkingBySectionCode($arSections, $idMarking)
    {
        $idProducts = $this->getIdProductsBySectionCode(IBLOCK_CATALOG, $arSections);
        $idProducts = array_chunk($idProducts, 1000);
        foreach ($idProducts as $ids) {
            $offers = $this->getOffersByProducts($ids);
            foreach ($offers as $offer) {
                foreach ($offer as $info) {
                    \Bitrix\Catalog\Model\Product::update($info['ID'], array(
                        'UF_PRODUCT_GROUP' => $idMarking
                    ));
                }
            }
        }
    }
}