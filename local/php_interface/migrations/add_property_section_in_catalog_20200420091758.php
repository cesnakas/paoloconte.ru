<?php

namespace Sprint\Migration;


class add_property_section_in_catalog_20200420091758 extends Version
{

    protected $description = "Создает свойство \"Категория\" для фильтрации.";

    public function up()
    {
        $helper = $this->getHelperManager();

        $iblockId = 10;

        // Получаем секции инфоблока.
        $arSelect = [
            'ID',
            'IBLOCK_ID',
            'IBLOCK_SECTION_ID',
            'NAME',
        ];
        $arFilter = [
            '=IBLOCK_ID' => $iblockId,
            '=ACTIVE' => 'Y',
            '=GLOBAL_ACTIVE' => 'Y',
        ];
        $rsSections = \CIBlockSection::GetList(false, $arFilter, false, $arSelect);

        $arValues = [];
        while ($arSection = $rsSections->Fetch()) {
            $arValues[] = [
                'VALUE' => $arSection['NAME'],
                'XML_ID' => 'iblock-'.$arSection['IBLOCK_ID'].'-section-'.$arSection['ID'],
                'DEF' => 'N',
                'SORT' => '500',
            ];
        }

        // Создаем свойство и задаем значения
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Категория',
            'ACTIVE' => 'Y',
            'SORT' => '25',
            'CODE' => 'SECTION',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'Y',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => 'Заполняется автоматически. Данные подставляются из списка "Разделы"',
            'SECTION_PROPERTY' => 'Y', // Показывать на странице редактирования элемента
            'SMART_FILTER' => 'Y', // Показывать в умном фильтре
            'VALUES' => $arValues,
        ]);


    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $iblockId = 10;

        $helper->Iblock()->deletePropertyIfExists($iblockId, 'SECTION');
    }

}
