<?php

namespace Sprint\Migration;


class add_property_metro_shop20200114154553 extends Version
{

    protected $description = "";

    public function up()
    {
        $helper = $this->getHelperManager();


        $iblockId = $helper->Iblock()->getIblockIdIfExists('shops', 'tools');


        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Ближайшее метро',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'NEAREST_METRO',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '2',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));


    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->getIblockIdIfExists('shops', 'tools');

        $properties = \CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("CODE"=>"NEAREST_METRO", "IBLOCK_ID" => $iblockId));
        if ($prop_fields = $properties->GetNext()) {
            \CIBlockProperty::Delete($prop_fields["ID"]);
        }

    }

}
