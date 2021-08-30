<?php

namespace Citfact\Entity\MarkingCodeGroup;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField;

Loc::loadMessages(__FILE__);

class MarkingCodeGroupTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_hlsys_marking_code_group';
    }

    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new TextField('UF_XML_ID'),
            new TextField('UF_NAME'),
        ];
    }
}
