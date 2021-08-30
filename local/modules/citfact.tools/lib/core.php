<?php

namespace Citfact;

class Core
{
    const HLBLOCK_CODE_TSVET_DLYA_FILTRA = 'TSVETDLYAFILTRA';

    const NO_PHOTO = '/local/templates/paoloconte/components/bitrix/catalog.section/paoloconte/images/no_photo.png';

    /**
     * @var Core The reference to *Singleton* instance of this class
     */
    protected static $instance;

    /**
     * Returns the *Core* instance of this class.
     *
     * @return Core The *Core* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setExportMinCountOfferForSend($value)
    {
        \Bitrix\Main\Config\Option::set('sale', 'EXPORT_MIN_COUNT_OFFER_FOR_SEND', $value);
    }


    public function getExportMinCountOfferForSend()
    {
        return (int)\Bitrix\Main\Config\Option::get('sale', 'EXPORT_MIN_COUNT_OFFER_FOR_SEND');
    }

    /**
     * @param $iblockCode
     * @return string
     * @throws \Exception
     */
    public function getIblockId($iblockCode)
    {
        if (!$iblockCode) {
            throw new \Exception('Empty iblock code.');
        }
        if ($this->constants['IBLOCK_' . $iblockCode]) {
            return $this->constants['IBLOCK_' . $iblockCode];
        }

        $iblock = new \CIBlock();
        $res = $iblock->GetList([], ['CODE' => $iblockCode]);
        $item = $res->Fetch();
        if ($item['ID']) {
            return $item['ID'];
        }

        return false;
    }

    /**
     * @param $iblockCode
     * @return string
     * @throws \Exception
     */
    public function getIblockSectionId($iblockCode, $sectionCode)
    {
        $iblockId = $this->getIblockId($iblockCode);

        $obSect = new \CIBlockSection();

        $res = $obSect->GetList(array(), array('IBLOCK_ID' => $iblockId, 'CODE' => $sectionCode));
        $section = $res->Fetch();

        return $section["ID"];
    }

    /**
     * @param $hlBlockCode
     * @return string
     * @throws \Exception
     */
    public function getHlBlockId($hlBlockCode)
    {
        $hlBlock = new HLBlock();
        $hlData = $hlBlock->getHlDataByName($hlBlockCode);

        if ($hlData['ID']) {
            return $hlData['ID'];
        }

        return false;
    }

    /**
     * @param $groupCode
     * @return array
     * @throws \Exception
     */
    function GetGroupByCode($groupCode)
    {
        $cGroup = new \CGroup();

        $rsGroups = $cGroup->GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
        if (intval($rsGroups->SelectedRowsCount()) > 0) {
            while ($arGroups = $rsGroups->Fetch()) {
                $arUsersGroups[] = $arGroups['ID'];
            }
        }

        return $arUsersGroups;
    }

    public function getColors($arXmlIds = [])
    {
        $hlDataClass = (new HLBlock)->getHlEntityByName(static::HLBLOCK_CODE_TSVET_DLYA_FILTRA);
        $arFilter = array('UF_XML_ID' => $arXmlIds);
        $res = $hlDataClass::getList([
            'select' => ['*'],
            'order' => array('ID' => 'ASC'),
            'filter' => $arFilter
        ]);
        $arFiles = array();
        while ($arRes = $res->fetch()) {
            $arFiles[$arRes['UF_XML_ID']] = array(
                'FILE_PATH' => \CFile::GetPath($arRes['UF_FILE']),
                'NAME' => $arRes['UF_NAME']
            );
        }
        return $arFiles;
    }

    public function getAllColors()
    {
        if (isset($GLOBALS['CoreGetAllColors'])) {
            return $GLOBALS['CoreGetAllColors'];
        }
        $tag_cache = 'allColors';
        $obCache = new \CPHPCache();
        if ($obCache->InitCache(1800, $tag_cache, '/' . $tag_cache)) {
            $vars = $obCache->GetVars();
            $arFiles = $vars['result'];
        } else if ($obCache->StartDataCache()) {
            $hlDataClass = (new HLBlock)->getHlEntityByName(static::HLBLOCK_CODE_TSVET_DLYA_FILTRA);
            $res = $hlDataClass::getList([
                'select' => ['*'],
                'order' => array('ID' => 'ASC')
            ]);
            $arFiles = array();
            while ($arRes = $res->fetch()) {
                $arFiles[$arRes['UF_XML_ID']] = array(
                    'FILE_PATH' => \CFile::GetPath($arRes['UF_FILE']),
                    'NAME' => $arRes['UF_NAME']
                );
            }
            $obCache->EndDataCache(array('result' => $arFiles));
        }
        $GLOBALS['CoreGetAllColors'] = $arFiles;
        return $arFiles;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Core* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        global $APPLICATION;
        $this->curDir = $APPLICATION->GetCurDir();
        $this->curPage = $APPLICATION->GetCurPage();
    }


    /**
     * @return string
     */
    public function getCurDir()
    {
        return $this->curDir;
    }


    /**
     * @return string
     */
    public function getCurPage()
    {
        return $this->curPage;
    }

    public static function getDeliveryIdForShowAddressFields()
    {
        $result = \Bitrix\Main\Config\Option::get('sale', 'DELIVERY_ID_FOR_SHOW_ADDRESS_FIELDS');
        if ($result) {
            $result = array_map(function ($el) {
                return (int)trim($el);
            }, explode(',', $result));
            $result = array_filter($result, function ($el) {
                return !empty($el);
            });

            return $result;
        }
        return [];
    }

    public static function setDeliveryIdForShowAddressFields($ids)
    {
        \Bitrix\Main\Config\Option::set('sale', 'DELIVERY_ID_FOR_SHOW_ADDRESS_FIELDS', implode(',', $ids));
    }


    /**
     * Private clone method to prevent cloning of the instance of the
     * *Core* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }


    /**
     * Private unserialize method to prevent unserializing of the *Core*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

}
