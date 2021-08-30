<?php

namespace Citfact\Entity;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class FuserTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DATE_INSERT datetime mandatory
 * <li> DATE_UPDATE datetime mandatory
 * <li> USER_ID int optional
 * <li> CODE string(32) optional
 * </ul>
 *
 * @package Bitrix\Sale
 **/
class FuserTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_sale_fuser';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('FUSER_ENTITY_ID_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_INSERT',
                [
                    'required' => true,
                    'title' => Loc::getMessage('FUSER_ENTITY_DATE_INSERT_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_UPDATE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('FUSER_ENTITY_DATE_UPDATE_FIELD')
                ]
            ),
            new IntegerField(
                'USER_ID',
                [
                    'title' => Loc::getMessage('FUSER_ENTITY_USER_ID_FIELD')
                ]
            ),
            new StringField(
                'CODE',
                [
                    'validation' => [__CLASS__, 'validateCode'],
                    'title' => Loc::getMessage('FUSER_ENTITY_CODE_FIELD')
                ]
            ),
        ];
    }

    /**
     * Returns validators for CODE field.
     *
     * @return array
     */
    public static function validateCode()
    {
        return [
            new LengthValidator(null, 32),
        ];
    }
}