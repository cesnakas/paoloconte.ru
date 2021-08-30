<?php

namespace Citfact\Entity;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\BooleanField,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\FloatField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class BasketTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> FUSER_ID int mandatory
 * <li> ORDER_ID int optional
 * <li> PRODUCT_ID int mandatory
 * <li> PRODUCT_PRICE_ID int optional
 * <li> PRICE double mandatory
 * <li> CURRENCY string(3) mandatory
 * <li> DATE_INSERT datetime mandatory
 * <li> DATE_UPDATE datetime mandatory
 * <li> WEIGHT double optional
 * <li> QUANTITY double optional default 0.0000
 * <li> LID string(2) mandatory
 * <li> DELAY bool ('N', 'Y') optional default 'N'
 * <li> NAME string(255) mandatory
 * <li> CAN_BUY bool ('N', 'Y') optional default 'Y'
 * <li> MODULE string(100) optional
 * <li> CALLBACK_FUNC string(100) optional
 * <li> NOTES string(250) optional
 * <li> ORDER_CALLBACK_FUNC string(100) optional
 * <li> DETAIL_PAGE_URL string(250) optional
 * <li> DISCOUNT_PRICE double mandatory
 * <li> CANCEL_CALLBACK_FUNC string(100) optional
 * <li> PAY_CALLBACK_FUNC string(100) optional
 * <li> PRODUCT_PROVIDER_CLASS string(100) optional
 * <li> CATALOG_XML_ID string(100) optional
 * <li> PRODUCT_XML_ID string(100) optional
 * <li> DISCOUNT_NAME string(255) optional
 * <li> DISCOUNT_VALUE string(32) optional
 * <li> DISCOUNT_COUPON string(32) optional
 * <li> VAT_RATE double optional default 0.0000
 * <li> SUBSCRIBE bool ('N', 'Y') optional default 'N'
 * <li> DEDUCTED bool ('N', 'Y') optional default 'N'
 * <li> RESERVED bool ('N', 'Y') optional default 'N'
 * <li> BARCODE_MULTI bool ('N', 'Y') optional default 'N'
 * <li> RESERVE_QUANTITY double optional
 * <li> CUSTOM_PRICE bool ('N', 'Y') optional default 'N'
 * <li> DIMENSIONS string(255) optional
 * <li> TYPE int optional
 * <li> SET_PARENT_ID int optional
 * <li> MEASURE_CODE int optional
 * <li> MEASURE_NAME string(50) optional
 * <li> RECOMMENDATION string(40) optional
 * <li> BASE_PRICE double optional
 * <li> VAT_INCLUDED bool ('N', 'Y') optional default 'Y'
 * <li> SORT int optional default 100
 * <li> PRICE_TYPE_ID int optional
 * <li> DATE_REFRESH datetime optional
 * <li> XML_ID string(255) optional
 * <li> MARKING_CODE_GROUP string(100) optional
 * </ul>
 *
 * @package Bitrix\Sale
 **/
class BasketTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_sale_basket';
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
                    'title' => Loc::getMessage('BASKET_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'FUSER_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_FUSER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_ORDER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'PRODUCT_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_PRODUCT_ID_FIELD')
                ]
            ),
            new IntegerField(
                'PRODUCT_PRICE_ID',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_PRODUCT_PRICE_ID_FIELD')
                ]
            ),
            new FloatField(
                'PRICE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_PRICE_FIELD')
                ]
            ),
            new StringField(
                'CURRENCY',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateCurrency'],
                    'title' => Loc::getMessage('BASKET_ENTITY_CURRENCY_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_INSERT',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_DATE_INSERT_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_UPDATE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_DATE_UPDATE_FIELD')
                ]
            ),
            new FloatField(
                'WEIGHT',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_WEIGHT_FIELD')
                ]
            ),
            new FloatField(
                'QUANTITY',
                [
                    'default' => 0.0000,
                    'title' => Loc::getMessage('BASKET_ENTITY_QUANTITY_FIELD')
                ]
            ),
            new StringField(
                'LID',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateLid'],
                    'title' => Loc::getMessage('BASKET_ENTITY_LID_FIELD')
                ]
            ),
            new BooleanField(
                'DELAY',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_DELAY_FIELD')
                ]
            ),
            new StringField(
                'NAME',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateName'],
                    'title' => Loc::getMessage('BASKET_ENTITY_NAME_FIELD')
                ]
            ),
            new BooleanField(
                'CAN_BUY',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'Y',
                    'title' => Loc::getMessage('BASKET_ENTITY_CAN_BUY_FIELD')
                ]
            ),
            new StringField(
                'MODULE',
                [
                    'validation' => [__CLASS__, 'validateModule'],
                    'title' => Loc::getMessage('BASKET_ENTITY_MODULE_FIELD')
                ]
            ),
            new StringField(
                'CALLBACK_FUNC',
                [
                    'validation' => [__CLASS__, 'validateCallbackFunc'],
                    'title' => Loc::getMessage('BASKET_ENTITY_CALLBACK_FUNC_FIELD')
                ]
            ),
            new StringField(
                'NOTES',
                [
                    'validation' => [__CLASS__, 'validateNotes'],
                    'title' => Loc::getMessage('BASKET_ENTITY_NOTES_FIELD')
                ]
            ),
            new StringField(
                'ORDER_CALLBACK_FUNC',
                [
                    'validation' => [__CLASS__, 'validateOrderCallbackFunc'],
                    'title' => Loc::getMessage('BASKET_ENTITY_ORDER_CALLBACK_FUNC_FIELD')
                ]
            ),
            new StringField(
                'DETAIL_PAGE_URL',
                [
                    'validation' => [__CLASS__, 'validateDetailPageUrl'],
                    'title' => Loc::getMessage('BASKET_ENTITY_DETAIL_PAGE_URL_FIELD')
                ]
            ),
            new FloatField(
                'DISCOUNT_PRICE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('BASKET_ENTITY_DISCOUNT_PRICE_FIELD')
                ]
            ),
            new StringField(
                'CANCEL_CALLBACK_FUNC',
                [
                    'validation' => [__CLASS__, 'validateCancelCallbackFunc'],
                    'title' => Loc::getMessage('BASKET_ENTITY_CANCEL_CALLBACK_FUNC_FIELD')
                ]
            ),
            new StringField(
                'PAY_CALLBACK_FUNC',
                [
                    'validation' => [__CLASS__, 'validatePayCallbackFunc'],
                    'title' => Loc::getMessage('BASKET_ENTITY_PAY_CALLBACK_FUNC_FIELD')
                ]
            ),
            new StringField(
                'PRODUCT_PROVIDER_CLASS',
                [
                    'validation' => [__CLASS__, 'validateProductProviderClass'],
                    'title' => Loc::getMessage('BASKET_ENTITY_PRODUCT_PROVIDER_CLASS_FIELD')
                ]
            ),
            new StringField(
                'CATALOG_XML_ID',
                [
                    'validation' => [__CLASS__, 'validateCatalogXmlId'],
                    'title' => Loc::getMessage('BASKET_ENTITY_CATALOG_XML_ID_FIELD')
                ]
            ),
            new StringField(
                'PRODUCT_XML_ID',
                [
                    'validation' => [__CLASS__, 'validateProductXmlId'],
                    'title' => Loc::getMessage('BASKET_ENTITY_PRODUCT_XML_ID_FIELD')
                ]
            ),
            new StringField(
                'DISCOUNT_NAME',
                [
                    'validation' => [__CLASS__, 'validateDiscountName'],
                    'title' => Loc::getMessage('BASKET_ENTITY_DISCOUNT_NAME_FIELD')
                ]
            ),
            new StringField(
                'DISCOUNT_VALUE',
                [
                    'validation' => [__CLASS__, 'validateDiscountValue'],
                    'title' => Loc::getMessage('BASKET_ENTITY_DISCOUNT_VALUE_FIELD')
                ]
            ),
            new StringField(
                'DISCOUNT_COUPON',
                [
                    'validation' => [__CLASS__, 'validateDiscountCoupon'],
                    'title' => Loc::getMessage('BASKET_ENTITY_DISCOUNT_COUPON_FIELD')
                ]
            ),
            new FloatField(
                'VAT_RATE',
                [
                    'default' => 0.0000,
                    'title' => Loc::getMessage('BASKET_ENTITY_VAT_RATE_FIELD')
                ]
            ),
            new BooleanField(
                'SUBSCRIBE',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_SUBSCRIBE_FIELD')
                ]
            ),
            new BooleanField(
                'DEDUCTED',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_DEDUCTED_FIELD')
                ]
            ),
            new BooleanField(
                'RESERVED',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_RESERVED_FIELD')
                ]
            ),
            new BooleanField(
                'BARCODE_MULTI',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_BARCODE_MULTI_FIELD')
                ]
            ),
            new FloatField(
                'RESERVE_QUANTITY',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_RESERVE_QUANTITY_FIELD')
                ]
            ),
            new BooleanField(
                'CUSTOM_PRICE',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'N',
                    'title' => Loc::getMessage('BASKET_ENTITY_CUSTOM_PRICE_FIELD')
                ]
            ),
            new StringField(
                'DIMENSIONS',
                [
                    'validation' => [__CLASS__, 'validateDimensions'],
                    'title' => Loc::getMessage('BASKET_ENTITY_DIMENSIONS_FIELD')
                ]
            ),
            new IntegerField(
                'TYPE',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_TYPE_FIELD')
                ]
            ),
            new IntegerField(
                'SET_PARENT_ID',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_SET_PARENT_ID_FIELD')
                ]
            ),
            new IntegerField(
                'MEASURE_CODE',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_MEASURE_CODE_FIELD')
                ]
            ),
            new StringField(
                'MEASURE_NAME',
                [
                    'validation' => [__CLASS__, 'validateMeasureName'],
                    'title' => Loc::getMessage('BASKET_ENTITY_MEASURE_NAME_FIELD')
                ]
            ),
            new StringField(
                'RECOMMENDATION',
                [
                    'validation' => [__CLASS__, 'validateRecommendation'],
                    'title' => Loc::getMessage('BASKET_ENTITY_RECOMMENDATION_FIELD')
                ]
            ),
            new FloatField(
                'BASE_PRICE',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_BASE_PRICE_FIELD')
                ]
            ),
            new BooleanField(
                'VAT_INCLUDED',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'Y',
                    'title' => Loc::getMessage('BASKET_ENTITY_VAT_INCLUDED_FIELD')
                ]
            ),
            new IntegerField(
                'SORT',
                [
                    'default' => 100,
                    'title' => Loc::getMessage('BASKET_ENTITY_SORT_FIELD')
                ]
            ),
            new IntegerField(
                'PRICE_TYPE_ID',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_PRICE_TYPE_ID_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_REFRESH',
                [
                    'title' => Loc::getMessage('BASKET_ENTITY_DATE_REFRESH_FIELD')
                ]
            ),
            new StringField(
                'XML_ID',
                [
                    'validation' => [__CLASS__, 'validateXmlId'],
                    'title' => Loc::getMessage('BASKET_ENTITY_XML_ID_FIELD')
                ]
            ),
            new StringField(
                'MARKING_CODE_GROUP',
                [
                    'validation' => [__CLASS__, 'validateMarkingCodeGroup'],
                    'title' => Loc::getMessage('BASKET_ENTITY_MARKING_CODE_GROUP_FIELD')
                ]
            ),
        ];
    }

    /**
     * Returns validators for CURRENCY field.
     *
     * @return array
     */
    public static function validateCurrency()
    {
        return [
            new LengthValidator(null, 3),
        ];
    }

    /**
     * Returns validators for LID field.
     *
     * @return array
     */
    public static function validateLid()
    {
        return [
            new LengthValidator(null, 2),
        ];
    }

    /**
     * Returns validators for NAME field.
     *
     * @return array
     */
    public static function validateName()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for MODULE field.
     *
     * @return array
     */
    public static function validateModule()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for CALLBACK_FUNC field.
     *
     * @return array
     */
    public static function validateCallbackFunc()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for NOTES field.
     *
     * @return array
     */
    public static function validateNotes()
    {
        return [
            new LengthValidator(null, 250),
        ];
    }

    /**
     * Returns validators for ORDER_CALLBACK_FUNC field.
     *
     * @return array
     */
    public static function validateOrderCallbackFunc()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for DETAIL_PAGE_URL field.
     *
     * @return array
     */
    public static function validateDetailPageUrl()
    {
        return [
            new LengthValidator(null, 250),
        ];
    }

    /**
     * Returns validators for CANCEL_CALLBACK_FUNC field.
     *
     * @return array
     */
    public static function validateCancelCallbackFunc()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for PAY_CALLBACK_FUNC field.
     *
     * @return array
     */
    public static function validatePayCallbackFunc()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for PRODUCT_PROVIDER_CLASS field.
     *
     * @return array
     */
    public static function validateProductProviderClass()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for CATALOG_XML_ID field.
     *
     * @return array
     */
    public static function validateCatalogXmlId()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for PRODUCT_XML_ID field.
     *
     * @return array
     */
    public static function validateProductXmlId()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for DISCOUNT_NAME field.
     *
     * @return array
     */
    public static function validateDiscountName()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for DISCOUNT_VALUE field.
     *
     * @return array
     */
    public static function validateDiscountValue()
    {
        return [
            new LengthValidator(null, 32),
        ];
    }

    /**
     * Returns validators for DISCOUNT_COUPON field.
     *
     * @return array
     */
    public static function validateDiscountCoupon()
    {
        return [
            new LengthValidator(null, 32),
        ];
    }

    /**
     * Returns validators for DIMENSIONS field.
     *
     * @return array
     */
    public static function validateDimensions()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for MEASURE_NAME field.
     *
     * @return array
     */
    public static function validateMeasureName()
    {
        return [
            new LengthValidator(null, 50),
        ];
    }

    /**
     * Returns validators for RECOMMENDATION field.
     *
     * @return array
     */
    public static function validateRecommendation()
    {
        return [
            new LengthValidator(null, 40),
        ];
    }

    /**
     * Returns validators for XML_ID field.
     *
     * @return array
     */
    public static function validateXmlId()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for MARKING_CODE_GROUP field.
     *
     * @return array
     */
    public static function validateMarkingCodeGroup()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }
}