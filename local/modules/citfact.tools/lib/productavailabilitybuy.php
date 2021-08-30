<?php

namespace Citfact;

use CCatalogSku;
use CCatalogStore;
use CIBlockProperty;
use CIBlockSection;
use CIBlockElement;
use CCatalogProduct;
use Bitrix\Main\Loader;

class ProductAvailabilityBuy
{
    const XML_ID_STORE_ONLINE = '627ecd56-6a52-11e0-8146-00151781686c';
    const XML_ID_STORES_FOR_BUY_CLOTHES = [
        self::XML_ID_STORE_ONLINE,
        'fae72679-7c9f-11e9-80d5-e4434b260396'
    ];
    const CODE_SECTION_CLOTHES = 'odezhda';
    const CATALOG_ID = 10;
    const SKU_ID = 11;
    const LOG_FILE = "/local/var/logs/cron/active_offers_clothes.log" ;
    const LAST_DATE_FILE = "/cron/last.txt";
    const PROPERTY_BLOCK_PRODUCTS = 'BLOK_TOVARA';

    protected $productsOffers = [];
    protected $product = [];
    protected $updatedOffers = [];

    public function __construct()
    {
        Loader::IncludeModule('iblock');
    }

    public static function getSectionsParent($sectionsCode) {
        return self::getSectionsParentByFilter(['CODE' => $sectionsCode]);
    }

    protected static function getSectionsParentByFilter($filter){
        $filter['IBLOCK_ID'] = self::CATALOG_ID;
        $rsSect = CIBlockSection::GetList(array('ID' => 'asc'), $filter, false, ['NAME', 'CODE', 'ID']);
        $sectionsParent = [];
        $sections = [];
        while ($arSect = $rsSect->GetNext())
        {
            $sectionsParent[] = $arSect['ID'];
            $sections[$arSect['ID']] = $arSect;
        }

        $rsSect = CIBlockSection::GetList(array('ID' => 'asc'),['IBLOCK_ID' => self::CATALOG_ID, 'SECTION_ID' => $sectionsParent], false, ['NAME', 'CODE', 'ID']);
        while ($arSect = $rsSect->GetNext())
        {
            $sections[$arSect['ID']] = $arSect;
        }
        return $sections;
    }

    public static function getSectionsParentExcept($sectionsCode) {
        $sectionsExcept = array_keys(self::getSectionsParent($sectionsCode));
        return self::getSectionsParentByFilter(['!ID' => $sectionsExcept]);
    }

    private function getPropertyValueId($code, $value)
    {
        $propertyEnums = \CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array( 'IBLOCK_ID' => self::SKU_ID, "CODE"=>$code));

        while($enumFields = $propertyEnums->GetNext())
        {
            if ($enumFields['VALUE'] == $value) {
                return $enumFields['ID'];
            }
        }
        $id = 0;
        return $id;
    }

    private function getPropertyForUpdate($value)
    {
        $propertyToUpdate = array(
            'CODE' => self::PROPERTY_BLOCK_PRODUCTS,
            'NAME' => 'Обувь',
            'VALUE_ID' => self::getPropertyValueId(self::PROPERTY_BLOCK_PRODUCTS, $value),
        );
        if ($propertyToUpdate['VALUE_ID']) {
            return $propertyToUpdate;
        } else {
            return [];
        }
    }

    public function activeOffersSection() {
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, "\n" . print_r('START ', true) . "\n" . date('d-m-Y H:i:s') . "\n" );
        $lastUpdate = $this->checkLastUpdate();
        $sectionsOther = self::getSectionsParentExcept([self::CODE_SECTION_CLOTHES]);
        $offers = $this->getOffers($sectionsOther, [self::XML_ID_STORE_ONLINE], $lastUpdate);
        $this->updateOffers($offers, self::getPropertyForUpdate('Обувь'));

        $sections = self::getSectionsParent([self::CODE_SECTION_CLOTHES]);
        $offers = $this->getOffers($sections, self::XML_ID_STORES_FOR_BUY_CLOTHES, $lastUpdate);
        $this->updateOffers($offers, self::getPropertyForUpdate('Одежда'));

        $el = new CIBlockElement;
        foreach ($this->productsOffers as $idProd => $offers) {
            $isActiveProd = false;
            foreach ($offers as $idOffer => $offer) {
                if($offer['active'] === true) {
                    $isActiveProd = true;
                    break;
                }
            }
            if($isActiveProd) {
                if ($this->product[$idProd]['ACTIVE'] == 'Y') {
                    continue;
                }
                if ($res = $el->Update($idProd, ['ACTIVE' => 'Y'])) {
                    $str = "Update (activate) catalog element: "  .  $idProd . '<br>' . "\r\n";
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                } else {
                    $str = "Error Update (activate) catalog element: " . $idProd . ' - ' . $el->LAST_ERROR . '<br>';
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                }
            } else {
                if ($this->product[$idProd]['ACTIVE'] == 'N') {
                    continue;
                }
                if ($res = $el->Update($idProd, ['ACTIVE' => 'N'])) {
                    $str = "Update (deactivate) catalog element: "  .  $idProd . '<br>' . "\r\n";
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                } else {
                    $str = "Error Update (deactivate) catalog element: " . $idProd . ' - ' . $el->LAST_ERROR . '<br>';
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                }

            }
        }
        $this->setLastUpdate();
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, "\n" . print_r('END ', true) . "\n" . date('d-m-Y H:i:s') . "\n", FILE_APPEND);
    }

    protected function updateOffers($offers, $propertyToUpdate = []) {
        Loader::includeModule('catalog');
        $obProduct = new CCatalogProduct();
        $el = new CIBlockElement;
        foreach ($offers as $offer) {
            if($offer['COUNT'] > 0) {
                if($offer['ACTIVE'] != 'Y') {
                    if ($res = $el->Update($offer['ID'], ['ACTIVE' => 'Y'])) {
                        $str = "Update (activate) element SKU: " . $offer['NAME'] . ' - ' . $offer['ID'] . '<br>' . "\r\n";
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                    } else {
                        $str = "Error Update (activate) element SKU: " . $offer['NAME'] . ' - ' . $offer['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                    }
                }
                $this->productsOffers[$offer['PARENT_ID']][$offer['ID']]['active'] = true;
                $obProduct->Update($offer['ID'], ['QUANTITY' => $offer['COUNT']]);
            } else {
                if($offer['ACTIVE'] == 'Y') {
                    if ($res = $el->Update($offer['ID'], ['ACTIVE' => 'N'])) {
                        $str = "Update (deactivate) element SKU: " . $offer['NAME'] . ' - ' . $offer['ID'] . '<br>' . "\r\n";
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                    } else {
                        $str = "Error Update (deactivate) element SKU: " . $offer['NAME'] . ' - ' . $offer['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
                    }
                }
                $this->productsOffers[$offer['PARENT_ID']][$offer['ID']]['active'] = false;
                $obProduct->Update($offer['ID'], ['QUANTITY' => 0]);
            }
            if (!empty($propertyToUpdate)) {
                \CIBlockElement::SetPropertyValuesEx(
                    $offer['ID'],
                    false,
                    array($propertyToUpdate['CODE'] => $propertyToUpdate['VALUE_ID'])
                );
            }
        }
    }

    protected function getOffers($sections, $xmlIdStores, $lastUpdate) {
        //шаг первый -- ищем изменившиеся тп
        global $DB;
        $arFilter = Array(
            "IBLOCK_ID"=>self::SKU_ID,
        );
        if ($lastUpdate) {
            $arFilter['>=TIMESTAMP_X'] = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL")), $lastUpdate);
        }

        $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, ['ID', 'NAME', 'CODE', 'ACTIVE', 'TIMESTAMP_X']);

        while($ar = $res->fetch())
        {
            $this->updatedOffers[$ar['ID']] = $ar;
            $str = "Selected updated SKU element: "  .  $ar['ID'] . ' - ' . $ar['NAME'] . ' - ' . $ar['TIMESTAMP_X'] . '<br>' . "\r\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
        }
        if (empty($this->updatedOffers)) {
            return array();
        }

        //шаг второй -- ищем товары для изменившихся торговых предложений

        $this->product = array();
        $productsForUpdate = \CCatalogSKU::getProductList(array_keys($this->updatedOffers), self::SKU_ID);
        $productsForUpdate = array_column($productsForUpdate, 'ID');

        $arFilter = Array(
            'ID' => $productsForUpdate,
            "IBLOCK_ID"=>self::CATALOG_ID,
//            "ACTIVE"=>"Y",
            "SECTION_ID"=> array_keys($sections),
            "INCLUDE_SUBSECTIONS"=> 'Y',
        );

        $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, ['ID', 'NAME', 'CODE', 'ACTIVE', 'TIMESTAMP_X']);

        while($ar = $res->fetch())
        {
            $this->product[$ar['ID']] = $ar;
            $str = "Selected catalog element: "  .  $ar['ID'] . ' - ' . $ar['NAME'] . ' - ' . $ar['TIMESTAMP_X'] . '<br>' . "\r\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
        }

        //шаг третий -- ищем торговые предложения, которые содержатся в полученных товарах
        $offers = [];
        $res = CCatalogSKU::getOffersList(array_keys($this->product), self::CATALOG_ID, [], ['ID', 'CODE', 'NAME', 'IBLOCK_ID', 'ACTIVE', 'TIMESTAMP_X'], []);

        foreach ($res as $item) {
            foreach ($item as $offer) {
                $offer['COUNT'] = $this->getCountOffersOnStores($offer['ID'], $xmlIdStores);
                $offers[$offer['ID']] = $offer;
                $this->productsOffers[$offer['PARENT_ID']][$offer['ID']] = [
                    'id' => $offer['ID'],
                    'active' => false
                ];
                $str = "Selected SKU: "  .  $offer['ID'] . ' - ' . $offer['NAME'] . ' - ' . $offer['TIMESTAMP_X'] . '<br>' . "\r\n";
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . self::LOG_FILE, print_r($str, true), FILE_APPEND);
            }
        }
        return $offers;
    }

    public function getCountProductsForBuyClothes($idProduct)
    {
        return $this->getCountProductOnStores($idProduct, static::XML_ID_STORES_FOR_BUY_CLOTHES);
    }

    public function getCountProductOnStores($id, $xmlIds)
    {
        $items = array($id);
        $offers = $this->getOffersByProducts($id);
        if (isset($offers[$id])) {
            $items = array_merge($items, array_column($offers[$id], 'ID'));
        }
        return $this->getCountOffersOnStores($items, $xmlIds);
    }

    public function getCountProductsRetailByProducts($idStore, $idProduct)
    {
        return $this->getCountProductsByStore($idStore, $idProduct);
    }

    public function getCountProductsByStore($idStore, $idProduct)
    {
        $dbResult = CCatalogStore::GetList(
            array('PRODUCT_ID' => 'ASC', 'ID' => 'ASC'),
            array(
                'ACTIVE' => 'Y',
                'PRODUCT_ID' => $idProduct,
                'ID' => $idStore
            ),
            false,
            false,
            array('ID', 'ELEMENT_ID', 'PRODUCT_AMOUNT')
        );
        $result = [];
        while ($store = $dbResult->GetNext()) {
            $result[$store['ELEMENT_ID']] = $store['PRODUCT_AMOUNT'];
        }
        return $result;
    }

    public function getCountOffersOnStores($items, $xmlIds){
        $dbResult = CCatalogStore::GetList(
            array('PRODUCT_ID' => 'ASC', 'ID' => 'ASC'),
            array(
                'ACTIVE' => 'Y',
                'PRODUCT_ID' => $items,
                'XML_ID' => $xmlIds
            ),
            false,
            false,
            array('ID', 'PRODUCT_AMOUNT')
        );
        $stores = [];
        while ($store = $dbResult->GetNext()) {
            $stores[] = $store;
        }
        return array_sum(array_column($stores, 'PRODUCT_AMOUNT'));
    }

    protected function getOffersByProducts($productID)
    {
        return CCatalogSKU::getOffersList(
            $productID,
            0,
            array('ACTIVE' => 'Y'),
            array('ID', 'NAME', 'CODE', 'PARENT_ID'),
            array()
        );
    }

    protected function getParentSections($id)
    {
        $tt = CIBlockSection::GetList(array(), array('ID' => $id));
        $as = $tt->GetNext();
        $a = [];
        $a[] = $as;
        if ($as['DEPTH_LEVEL'] <= 1 || empty($as)) {
            return $a;
        }
        $a = array_merge($a, $this->getParentSections($as['IBLOCK_SECTION_ID']));

        return $a;
    }

    public function isSectionChildClothes($idSection)
    {
        $sections = $this->getParentSections($idSection);
        if (in_array(static::CODE_SECTION_CLOTHES, array_column($sections, 'CODE'))) {
            return true;
        }
        return false;
    }

    protected function checkLastUpdate ()
    {
        $dateLastUpdateFile = $_SERVER['DOCUMENT_ROOT'] . self::LAST_DATE_FILE;
        if (file_exists($dateLastUpdateFile)) {
            return file_get_contents($dateLastUpdateFile);
        } else {
            return 0;
        }
    }

    protected function setLastUpdate()
    {
        $dateLastUpdateFile = $_SERVER['DOCUMENT_ROOT'] . self::LAST_DATE_FILE;
        file_put_contents($dateLastUpdateFile, (time() - (60 * 15)));
    }
}