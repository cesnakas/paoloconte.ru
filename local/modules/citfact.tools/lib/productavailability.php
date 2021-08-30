<?php

namespace Citfact;
use Citfact\Lock;
use Bitrix\Main\Diag\Debug;

/**
 * Class ProductAvailability
 * @package Citfact
 */
class ProductAvailability {
    const IBLOCK_CAT_ID = IBLOCK_CATALOG;
    const PATH_TO_LOG = '/local/var/logs/product_availability.log';

    private $idYes = '';

    /**
     * Запускает метод установки свойства "Флаг доступности"
     */
    function setAvailabilityProductsExec($methodName = '') {
        $lock = new Lock(Lock::PRODUCT_AVAILABILITY);

        if (!$lock->lock()) {
            return;
        }

        try {
            self::setAvailabilityProducts($methodName);
        } catch (Exception $e) {
        }

        $lock->unlock();
    }

    /**
     * Устанавливает значение свойства "Флаг доступности"
     */
    function setAvailabilityProducts($methodName = '') {
        Debug::writeToFile($methodName, date('d-m-Y H:i:s'), self::PATH_TO_LOG);
        $iblockId = self::IBLOCK_CAT_ID;

        $arConditionsValues = $this->getSectionsInfoByType();
        $arConditions = $arConditionsValues['CONDITIONS'];
        $arCondValues = $arConditionsValues['COND_VALUES'];

        $this->idYes = '';
        $property_enums = \CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockId, "CODE"=>"FLAG_AVAILABILITY"));
        while($enum_fields = $property_enums->GetNext())
        {
            if ($enum_fields["XML_ID"] == '001') {
                $this->idYes = $enum_fields["ID"];
            }
        }
        if (!empty($this->idYes)) {
            $arElements = [];
            foreach ($arConditions as $key => $value) {
                $res = $this->getElementsByConditions($arCondValues[$key], $value);
                if (!empty($res)) {
                    $arElements = array_merge($arElements, $res);
                }
            }

            $arElementsNew = [];
            foreach ($arElements as $key => $value) {
                $arElementsNew[$value['ID']] = $value;
            }

            $this->setPropFlagAvailability($arElementsNew);
        }

        Debug::writeToFile("\n", date('d-m-Y H:i:s'), self::PATH_TO_LOG);
    }

    private function getUserFiledValues($fieldName) {
        $result = [];
        // Получаем ID пользовательского поля по коду
        $rsData = \CUserTypeEntity::GetList(array(), array("FIELD_NAME" => $fieldName));
        if ($arRes = $rsData->Fetch()) {
            // Используем полученный ID и вытаскиваем список значений пользовательского поля
            $rsEnum = \CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => $arRes["ID"]));

            while ($arEnum = $rsEnum->Fetch()) {
                $result[] = $arEnum;
            }
        }
        return $result;
    }

    private function getElementsByConditions($types, $sectionIds) {
        $iblockId = self::IBLOCK_CAT_ID;

        $arrFilterFunc = getCatalogFilterByType([], $types, $sectionIds);
        $arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID", "PROPERTY_FLAG_AVAILABILITY");
        $arFilter = Array("IBLOCK_ID"=> $iblockId, /*"ID" => [229381, 133120]*/);
        $arFilter = array_merge($arFilter, $arrFilterFunc);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        $count = 0;
        $arItems = [];
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arItems[$arFields['ID']] = $arFields;
            $count++;
        }
        Debug::writeToFile("Всего доступных товаров: ".$count, date('d-m-Y H:i:s'), self::PATH_TO_LOG);

        return $arItems;
    }

    private function setPropFlagAvailability($arElements) {
        $iblockId = self::IBLOCK_CAT_ID;

        $arSelect = Array("ID", "NAME", "PROPERTY_FLAG_AVAILABILITY");
        $arFilter = Array("IBLOCK_ID"=> $iblockId);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        $count = 0;
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arRes[] = $arFields;
            $count++;
        }
        Debug::writeToFile("Всего товаров: ".$count, date('d-m-Y H:i:s'), self::PATH_TO_LOG);

        $count = 0;
        foreach ($arRes as $value) {
            if (isset($arElements[$value['ID']])) {
                if (strval($value['PROPERTY_FLAG_AVAILABILITY_ENUM_ID']) != $this->idYes) {
                    // Update Property "Доступен"
                    \CIBlockElement::SetPropertyValuesEx($value['ID'], false, array('FLAG_AVAILABILITY' => $this->idYes));
                    $count++;
                }
            } else {
                if (strval($value['PROPERTY_FLAG_AVAILABILITY_ENUM_ID']) != '') {
                    // Update Property "Недоступен"
                    \CIBlockElement::SetPropertyValuesEx($value['ID'], false, array('FLAG_AVAILABILITY' => false));
                    $count++;
                }
            }
        }
        if ($count >= 0) {
            // https://sagip.ru/pages/bitrix/bitrix-ochistka-kesha-pri-izmenenii-svojstv-infobloka
            \CIBlock::clearIBlockTagCache($iblockId);
        }
        Debug::writeToFile("Всего товаров для изменения статусов: ".$count, date('d-m-Y H:i:s'), self::PATH_TO_LOG);
    }

    /**
     * Выбираем все разделы, у которых заполнено доп.свойство "Условия отображения"
     * @return array
     */
    public function getSectionsInfoByType() {
        $iblockId = self::IBLOCK_CAT_ID;
        $level = 1;

        $arDisplayCondition = $this->getUserFiledValues('UF_DISPLAY_CONDITION');
        foreach ($arDisplayCondition as $key => $value) {
            $arDisplayConditionValues[$value['ID']] = $value;
        }

        $arFilter = Array('IBLOCK_ID' => $iblockId);
        $db_list = \CIBlockSection::GetList(Array(), $arFilter, false, Array('ID', 'IBLOCK_SECTION_ID', 'UF_DISPLAY_CONDITION'), false);
        while ($arSect = $db_list->GetNext()) {
            $arValues = [];
            foreach ($arSect['UF_DISPLAY_CONDITION'] as $key => $value) {
                $arValues[] = $arDisplayConditionValues[$value]['XML_ID'];
            }
            // Массив всех разделов
            $arSections[$arSect['ID']] = [
                'ID' => $arSect['ID'],
                'PARENT_ID' => $arSect['IBLOCK_SECTION_ID'],
                'UF_DISPLAY_CONDITION' => $arSect['UF_DISPLAY_CONDITION'],
                'UF_DISPLAY_CONDITION_XML' => $arValues,
                'UF_DISPLAY_CONDITION_STRING' => implode('_', $arSect['UF_DISPLAY_CONDITION']),
            ];

            // Собираем в массив разделы первого уровня (без родителя)
            if (empty($arSect['IBLOCK_SECTION_ID'])) {
                $arSectionsNew[$arSect['ID']] = $arSections[$arSect['ID']];
                $arSectionsNew[$arSect['ID']]['LEVEL'] = $level;
                $keysLevel[$level][] = $arSect['ID'];
                unset($arSections[$arSect['ID']]);
            }
            $conditionsString = $arSectionsNew[$arSect['ID']]['UF_DISPLAY_CONDITION_STRING'];
            if (!empty($conditionsString)) {
                $arConditions[$conditionsString][] = $arSect['ID'];
                $arCondValues[$conditionsString] = $arSectionsNew[$arSect['ID']]['UF_DISPLAY_CONDITION_XML'];
            }
        }
        $arLevels = [2,3,4,5];
        foreach ($arLevels as $level) {
            // Собираем в массив разделы по каждому уровню
            foreach ($arSections as $key => $value) {
                if (!empty($value['LEVEL'])) {
                    continue;
                }
                $id = $value['ID'];
                $parentId = $value['PARENT_ID'];
                if (in_array($parentId, $keysLevel[$level - 1])) {
                    $arSectionsNew[$id] = $value;
                    $arSectionsNew[$id]['LEVEL'] = $level;
                    $keysLevel[$level][] = $id;
                    if (empty($arSectionsNew[$id]['UF_DISPLAY_CONDITION'])) {
                        $arSectionsNew[$id]['UF_DISPLAY_CONDITION'] = $arSectionsNew[$parentId]['UF_DISPLAY_CONDITION'];
                        $arSectionsNew[$id]['UF_DISPLAY_CONDITION_XML'] = $arSectionsNew[$parentId]['UF_DISPLAY_CONDITION_XML'];
                        $arSectionsNew[$id]['UF_DISPLAY_CONDITION_STRING'] = $arSectionsNew[$parentId]['UF_DISPLAY_CONDITION_STRING'];
                    }
                    $conditionsString = $arSectionsNew[$id]['UF_DISPLAY_CONDITION_STRING'];
                    if (!empty($conditionsString)) {
                        $arConditions[$conditionsString][] = $id;
                        $arCondValues[$conditionsString] = $arSectionsNew[$id]['UF_DISPLAY_CONDITION_XML'];
                    }
                    unset($arSections[$id]);
                }
            }
        }

        return [
            'CONDITIONS' => $arConditions,
            'COND_VALUES' => $arCondValues,
        ];
    }

}