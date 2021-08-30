<?php

namespace Citfact\Entity\Sms;

use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

class OrderSmsTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'site_core_order_sms';
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap(): array
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('USER_ID', [
                'required' => true,
            ]),
            new StringField('ORDER_ID', [
                'required' => true,
            ]),
            new StringField('MESSAGE_ID', [
                'required' => true,
            ]),
            new StringField('MESSAGE'),
            new StringField('STATUS'),
            new DatetimeField('DATE_UPDATE', [
                'default_value' => static function () {
                    return new DateTime();
                }
            ]),
        ];
    }
}