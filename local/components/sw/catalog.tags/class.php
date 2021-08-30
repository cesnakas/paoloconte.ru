<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;

class CatalogTagsComponent extends \CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {
        $arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 360000;

        if (empty($arParams['IBLOCK_ID']) or $arParams['IBLOCK_ID'] <= 0){
            $arParams['IBLOCK_ID'] = $this->getTagsIblock();
        }else{
            $arParams['IBLOCK_ID'] = (int) $arParams['IBLOCK_ID'];
        }

        return $arParams;
    }

    /**
     * Получение дефолтного ИБ с тегами, если не указан в параметрах
     * @return int
     */
    private function getTagsIblock(){
        $iblockId = \Bitrix\Iblock\IblockTable::getList([
            'filter' => [
                '=CODE' => 'tags',
                '=IBLOCK_TYPE_ID'=> 'info',
                '=ACTIVE' => 'Y'
            ],
            'cache' => [ 'ttl' => 3600000 ],
            'limit' => 1
        ])->fetch();

        return ($iblockId ? (int) $iblockId['ID']: 0);
    }


    /**
     * получение результатов
     */
    protected function getResult()
    {
        $this->arResult['ITEMS'] = [];

        if ( !empty($this->arParams['DIR']) && $this->arParams['IBLOCK_ID'] > 0 ){

            if (Loader::includeModule('sotbit.seometa')){
                $this->arParams['DIR'] = $this->checkSeoMetaUrl($this->arParams['DIR']);
            }

            $obCache = \Bitrix\Main\Data\Cache::createInstance();
            $cache_id = 'catalog.tags|'.md5(serialize($this->arParams));

            if($obCache->initCache($this->arParams["CACHE_TIME"],$cache_id,"/catalog.tags/")){
                $vars = $obCache->GetVars();
                $this->arResult['ITEMS'] = $vars['ITEMS'];
            }elseif($obCache->startDataCache()){
                $dbRes = \CIBlockElement::GetList(
                    ['SORT'=>'ASC', 'ID' => 'DESC'],
                    [
                        'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                        'ACTIVE' => 'Y',
                        'PROPERTY_URL' => $this->arParams['DIR']
                    ],
                    false,
                    false,
                    ['ID','IBLOCK_ID','NAME', 'PROPERTY_URL', 'PROPERTY_TARGET_URL']
                );

                while ($element = $dbRes->GetNext()){
                    $this->arResult['ITEMS'][$element['NAME']] = [
                        'ID' => $element['ID'],
                        'IBLOCK_ID' => $element['IBLOCK_ID'],
                        'NAME' => $element['NAME'],
                        'URL' => $element['PROPERTY_URL_VALUE'],
                        'TARGET_URL' => $element['PROPERTY_TARGET_URL_VALUE']
                    ];
                }

                if(!empty($this->arResult['ITEMS'])){
                    $obCache->endDataCache( ['ITEMS'=> $this->arResult['ITEMS'] ]);
                }else{
                    $obCache->abortDataCache();
                }
            }
        }
    }

    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        try{
            $this->getResult();
            $this->includeComponentTemplate();
        }
        catch (Exception $e){
            $this->showAdminError($e->getMessage());
        }
    }

    public function showAdminError($error){
        if (\CSite::InGroup([1])){
            ShowError($error);
        }
    }

    public function checkSeoMetaUrl($url){
        //check seo module fake url
        global $issetCondition; // global

        if (!$issetCondition){
            return $url;
        }

        $request = Context::getCurrent()->getRequest();
        $requestUri = $request->getRequestUri();

        if ( $result = SeometaUrlTable::getByRealUrl($requestUri) ) {
            $url = $result['NEW_URL'];
        }

        return $url;
    }
}