<?php

namespace Sprint\Migration;


class AddPropertyFlagAvailability20191127141406 extends Version
{

    protected $description = "Добавление свойства: Флаг доступности";

    public function up() {
        $helper = $this->getHelperManager();

    
        //$iblockId = $helper->Iblock()->getIblockIdIfExists('catalog','catalog');
        $iblockId = 10;

        $helper->Iblock()->saveProperty($iblockId, array (
            'NAME' => 'Флаг доступности',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'FLAG_AVAILABILITY',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
            'VALUES' =>
                array (
                    0 =>
                        array (
                            'VALUE' => 'Доступен',
                            'DEF' => 'Y',
                            'SORT' => '500',
                            'XML_ID' => '001',
                        ),
                ),
        ));

    }

    public function down()
    {
        $helper = $this->getHelperManager();

        //your code ...
    }

}
