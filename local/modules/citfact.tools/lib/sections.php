<?php

namespace Citfact;

class Sections
{
    /**
     * Возвращает CDBResult со всеми дочерними категориями.
     * Или если указана строка "ID" в $arSelect - то выводится массив с id всех дочерних категорий.
     *
     * @param $IBLOCK_ID - ид инфоблока
     * @param $SECTION_ID - ид каталога
     * @param $arFilter - фильтр как в CIBlockSection::GetList
     * @param $arSelect - select как в CIBlockSection::GetList
     * @return array
     */
    public static function getChildSections($IBLOCK_ID, $SECTION_ID, $arFilter, $arSelect)
    {
        if ($arSelect == 'ID') { //если нужны только ид
            $IDon = true;
            $arSelect = ['ID', 'IBLOCK_SECTION_ID'];
        } else {
            $arSelect = array_merge(['ID', 'IBLOCK_SECTION_ID'], $arSelect);
        }

        $obSection = \CIBlockSection::GetList(
            [],
            array_merge(['IBLOCK_ID' => $IBLOCK_ID], $arFilter),
            false,
            $arSelect,
            false
        );

        $arAlId = []; //Для хранения результатов
        $arParent = []; //Для хранения детей разделов
        while ($arResult = $obSection->GetNext()) {

            $arAlId[$arResult['ID']] = $arResult;
            if (!is_array($arParent[$arResult['IBLOCK_SECTION_ID']])) { //Если родителя в списке нет, то добавляем
                $arParent[$arResult['IBLOCK_SECTION_ID']] = [];
            }
            array_push($arParent[$arResult['IBLOCK_SECTION_ID']], $arResult['ID']);

        }
        unset($obSection);

        $arR = self::GetAllSectionInSel($SECTION_ID, $arParent); //Ид всех детей и правнуков

        if (!$IDon) { //Если необходим не только ид
            $arId = $arR;
            $arR = [];
            for ($i = 0, $k = count($arId); $i < $k; $i++) {
                array_push($arR, $arAlId[$arId[$i]]);
            }
        }

        return $arR;
    }

    /**
     * Эта функция вспомогательная, чтоб смотреть подразделы
     *
     * @param $SECTION_ID
     * @param $arParent
     * @return array
     */
    private static function GetAllSectionInSel($SECTION_ID, $arParent)
    {
        $arR = [];
        for ($i = 0, $k = count($arParent[$SECTION_ID]); $i < $k; $i++) {
            array_push($arR, $arParent[$SECTION_ID][$i]);
            if (isset($arParent[$arParent[$SECTION_ID][$i]])) { //Если ребёнок является родителем
                $arR = array_merge($arR, self::GetAllSectionInSel($arParent[$SECTION_ID][$i], $arParent));
            }
        }
        return $arR;
    }
}