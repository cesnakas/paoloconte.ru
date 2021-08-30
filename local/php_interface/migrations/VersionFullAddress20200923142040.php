<?php

namespace Sprint\Migration;


class VersionFullAddress20200923142040 extends Version
{

    protected $description = "создание свойства заказа Полный адрес";

    public function up()
    {
        $helper = $this->getHelperManager();

        $arFields = array(
            "PERSON_TYPE_ID" => 1,
            "NAME" => "Полный адрес",
            "TYPE" => "TEXT",
            "CODE" => "FULL_ADDRESS",
            "UTIL" => "Y",
            "USER_PROPS" => "N",
            "IS_LOCATION" => "N",
            "IS_LOCATION4TAX" => "N",
            "PROPS_GROUP_ID" => 2,
            "SIZE1" => 0,
            "SIZE2" => 0,
            "DESCRIPTION" => "",
            "IS_EMAIL" => "N",
            "IS_PROFILE_NAME" => "N",
            "IS_PAYER" => "N"
        );
        \CSaleOrderProps::Add($arFields);
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $db_props = \CSaleOrderProps::GetList(
            ['SORT' => 'ASC'],
            [
                'PERSON_TYPE_ID' => 1,
                'CODE' => 'FULL_ADDRESS'
            ],
            false,
            false,
            []
        );

        if ($props = $db_props->Fetch()) {
            \CSaleOrderProps::Delete($props['ID']);
        }
    }
}
