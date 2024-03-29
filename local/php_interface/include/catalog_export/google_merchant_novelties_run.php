<?
//<title>Google Merchant Novelties</title>
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @var int $IBLOCK_ID */
/** @var string $SETUP_SERVER_NAME */
/** @var string $SETUP_FILE_NAME */
/** @var array $V */
/** @var string $XML_DATA */
use Bitrix\Currency,
    Bitrix\Iblock,
    Bitrix\Catalog,
    Bitrix\Main\Localization\Loc;

// special for test
$fastExport = 0;

Loc::loadLanguageFile(__FILE__);

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_yandex.php');
set_time_limit(0);

global $USER, $APPLICATION;
$bTmpUserCreated = false;
if (!CCatalog::IsUserExists())
{
    $bTmpUserCreated = true;
    if (isset($USER))
    {
        $USER_TMP = $USER;
        unset($USER);
    }

    $USER = new CUser();
}

$mobileUrl = 'paoloconte.ru';

$imageConfig = array('TYPE' => 'ALL', 'SIZE' => array('SMALL' => array('W' => 500, 'H' => 665)));

CCatalogDiscountSave::Disable();
/** @noinspection PhpDeprecationInspection */
CCatalogDiscountCoupon::ClearCoupon();
if ($USER->IsAuthorized())
{
    /** @noinspection PhpDeprecationInspection */
    CCatalogDiscountCoupon::ClearCouponsByManage($USER->GetID());
}

$arYandexFields = array(
    'typePrefix', 'vendor', 'vendorCode', 'model',
    'author', 'name', 'publisher', 'series', 'year',
    'ISBN', 'volume', 'part', 'language', 'binding',
    'page_extent', 'table_of_contents', 'performed_by', 'performance_type',
    'storage', 'format', 'recording_length', 'artist', 'title', 'year', 'media',
    'starring', 'director', 'originalName', 'country', 'aliases',
    'description', 'sales_notes', 'promo', 'provider', 'tarifplan',
    'xCategory', 'additional', 'worldRegion', 'region', 'days', 'dataTour',
    'hotel_stars', 'room', 'meal', 'included', 'transport', 'price_min', 'price_max',
    'options', 'manufacturer_warranty', 'country_of_origin', 'downloadable', 'adult', 'param',
    'place', 'hall', 'hall_part', 'is_premiere', 'is_kids', 'date'
);

$formatList = array(
    'none' => array(
        'vendor', 'vendorCode', 'sales_notes', 'manufacturer_warranty', 'country_of_origin',
        'adult'
    ),
    'vendor.model' => array(
        'typePrefix', 'vendor', 'vendorCode', 'model', 'sales_notes', 'manufacturer_warranty', 'country_of_origin',
        'adult'
    ),
    'book' => array(
        'author', 'publisher', 'series', 'year', 'ISBN', 'volume', 'part', 'language', 'binding',
        'page_extent', 'table_of_contents'
    ),
    'audiobook' => array(
        'author', 'publisher', 'series', 'year', 'ISBN', 'performed_by', 'performance_type',
        'language', 'volume', 'part', 'format', 'storage', 'recording_length', 'table_of_contents'
    ),
    'artist.title' => array(
        'title', 'artist', 'director', 'starring', 'originalName', 'country', 'year', 'media', 'adult'
    )
);

if (!function_exists("yandex_replace_special"))
{
    function yandex_replace_special($arg)
    {
        if (in_array($arg[0], array("&quot;", "&amp;", "&lt;", "&gt;")))
            return $arg[0];
        else
            return " ";
    }
}

if (!function_exists("getPathSection"))
{
    function getPathSection ($sectionID, $arAvailGroups) {
        $arSectionPath = getSectionParent($sectionID, $arAvailGroups);
        $arSectionPath[] = 'Главная';
        $arSectionPath = array_reverse($arSectionPath);
        $strSectionPath = implode(' &gt; ', $arSectionPath);
        $strSectionPath = yandex_text2xml($strSectionPath, true);

        return $strSectionPath;
    }
}

if (!function_exists("getAdvisableSections"))
{
    function getAdvisableSections ($arSectionsAdvisable, $arAvailGroups) {
        if (!empty($arSectionsAdvisable) && !empty($arAvailGroups)) {
            foreach ($arAvailGroups as $group) {
                if (
                    in_array($group['IBLOCK_SECTION_ID'], $arSectionsAdvisable)
                    && !in_array($group['ID'], $arSectionsAdvisable)
                ) {
                    $arSectionsAdvisable[] = $group['ID'];
                    $arSectionsAdvisable = array_merge($arSectionsAdvisable, getAdvisableSections($arSectionsAdvisable, $arAvailGroups));
                }
            }
        }
        $arSectionsAdvisable = array_unique($arSectionsAdvisable);
        return $arSectionsAdvisable;
    }
}

if (!function_exists("getImgFeed"))
{
    function  getImgFeed ($articul, $imageConfig, $address) {
        $strFile = '';
        $arAdditionalImg = array();
        $articleImage =  Citfact\Paolo::getProductImage($articul, $imageConfig);
        $key = 0;
        $firstImg = '';
        foreach ($articleImage['PHOTO'] as $img) {
            if ($key == 0) {
                $firstImg = $address.CHTTP::urnEncode($img['SMALL'], 'utf-8');
            }
            if (strpos($img['SMALL'], '_0004') !== false) {
                $strFile = $address.CHTTP::urnEncode($img['SMALL'], 'utf-8');
            } elseif (strpos($img['SMALL'], '_0018') !== false) {
                $arAdditionalImg[$key] = $address.CHTTP::urnEncode($img['SMALL'], 'utf-8');
            }
            $key++;
        }
        if (empty($strFile) && !empty($firstImg)) {
            $strFile = $firstImg;
        }
        return array('FIRST' => $strFile, 'ADDITIONAL' => $arAdditionalImg);
    }
}

if (!function_exists("getSectionParent"))
{
    function getSectionParent ($sectionID, $arAvailGroups) {
        $arSectionParent = array();
        if (!empty($sectionID) && !empty($arAvailGroups)) {
            foreach ($arAvailGroups as $group) {
                if ($group['ID'] == $sectionID) {
                    $arSectionParent[] = $group['NAME'];
                    $arSectionParent = array_merge($arSectionParent, getSectionParent($group['IBLOCK_SECTION_ID'], $arAvailGroups));
                }
            }
        }
        return $arSectionParent;
    }
}

if (!function_exists("yandex_text2xml"))
{
    function yandex_text2xml($text, $bHSC = false, $bDblQuote = false)
    {
        global $APPLICATION;

        $bHSC = (true == $bHSC ? true : false);
        $bDblQuote = (true == $bDblQuote ? true: false);

        if ($bHSC)
        {
            $text = htmlspecialcharsbx($text);
            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);
        }
        $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
        $text = str_replace("'", "&apos;", $text);
//        $text = $APPLICATION->ConvertCharset($text, LANG_CHARSET, 'windows-1251');
        return $text;
    }
}

if (!function_exists('yandex_get_value'))
{
    function yandex_get_value($arOffer, $param, $PROPERTY, $arProperties, $arUserTypeFormat, $usedProtocol)
    {
        global $iblockServerName;
        global $APPLICATION;

        $strProperty = '';
        $bParam = (strncmp($param, 'PARAM_', 6) == 0);

        if (isset($arProperties[$PROPERTY]) && !empty($arProperties[$PROPERTY]))
        {
            $PROPERTY_CODE = $arProperties[$PROPERTY]['CODE'];
            if (!isset($arOffer['PROPERTIES'][$PROPERTY_CODE]) && !isset($arOffer['PROPERTIES'][$PROPERTY]))
                return $strProperty;
            $arProperty = (
            isset($arOffer['PROPERTIES'][$PROPERTY_CODE])
                ? $arOffer['PROPERTIES'][$PROPERTY_CODE]
                : $arOffer['PROPERTIES'][$PROPERTY]
            );

            $arProperty['VALUE'] = ToLower($arProperty['VALUE']);

            $addParams = '';
            switch ($PROPERTY_CODE) {
                case 'SEZONNOST':
                    $paramName = 'g:custom_label_2';
                    break;
                case 'TOVARNAYA_GRUPPA_MARKETING':
                    $paramName = 'g:custom_label_1';
                    break;
                case 'MATERIAL_PODKLADKI_MARKETING':
                    $paramName = 'g:custom_label_0';
                    break;
                case 'MATERIAL_VERKHA_MARKETING':
                    $paramName = 'g:material';
                    break;
                case 'TSVET_MARKETING':
                    $arProperty['NAME'] = Loc::getMessage('PROPERTY_TSVET_MARKETING');
//                    $arProperty['NAME'] = $APPLICATION->ConvertCharset($arProperty['NAME'], 'windows-1251', LANG_CHARSET);
                    $paramName = 'g:color';
                    break;
                case 'VYSOTA_GOLENISHCHA_PROIZVODSTVO':
                case 'VYSOTA_KABLUKA':
                case 'VYSOTA_PLATFORMY':
                    $sm = Loc::getMessage('YANDEX_SANTIMETR');
                    $addParams = 'unit="'.$sm.'"';
                    break;
                case 'POL':
                    $paramName = 'g:gender';
                    break;
            }
            $value = '';
            $description = '';
            switch ($arProperties[$PROPERTY]['PROPERTY_TYPE'])
            {
                case 'USER_TYPE':
                    if ($arProperty['MULTIPLE'] == 'Y')
                    {
                        if (!empty($arProperty['~VALUE']))
                        {
                            $arValues = array();
                            foreach($arProperty["~VALUE"] as $oneValue)
                            {
                                $isArray = is_array($oneValue);
                                if (
                                    ($isArray && !empty($oneValue))
                                    || (!$isArray && $oneValue != '')
                                )
                                {
                                    $arValues[] = call_user_func_array($arUserTypeFormat[$PROPERTY],
                                        array(
                                            $arProperty,
                                            array("VALUE" => $oneValue),
                                            array('MODE' => 'SIMPLE_TEXT'),
                                        )
                                    );
                                }
                            }
                            $value = implode(', ', $arValues);
                        }
                    }
                    else
                    {
                        $isArray = is_array($arProperty['~VALUE']);
                        if (
                            ($isArray && !empty($arProperty['~VALUE']))
                            || (!$isArray && $arProperty['~VALUE'] != '')
                        )
                        {
                            $value = call_user_func_array($arUserTypeFormat[$PROPERTY],
                                array(
                                    $arProperty,
                                    array("VALUE" => $arProperty["~VALUE"]),
                                    array('MODE' => 'SIMPLE_TEXT'),
                                )
                            );
                        }
                    }
                    break;
                case Iblock\PropertyTable::TYPE_ELEMENT:
                    if (!empty($arProperty['VALUE']))
                    {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE']))
                        {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else
                        {
                            foreach ($arProperty['VALUE'] as &$intValue)
                            {
                                $intValue = (int)$intValue;
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue))
                        {
                            $dbRes = CIBlockElement::GetList(
                                array(),
                                array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue),
                                false,
                                false,
                                array('NAME')
                            );
                            while ($arRes = $dbRes->Fetch())
                            {
                                $value .= ($value ? ', ' : '').$arRes['NAME'];
                            }
                        }
                    }
                    break;
                case Iblock\PropertyTable::TYPE_SECTION:
                    if (!empty($arProperty['VALUE']))
                    {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE']))
                        {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else
                        {
                            foreach ($arProperty['VALUE'] as &$intValue)
                            {
                                $intValue = (int)$intValue;
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue))
                        {
                            $dbRes = CIBlockSection::GetList(
                                array(),
                                array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue),
                                false,
                                array('NAME')
                            );
                            while ($arRes = $dbRes->Fetch())
                            {
                                $value .= ($value ? ', ' : '').$arRes['NAME'];
                            }
                        }
                    }
                    break;
                case Iblock\PropertyTable::TYPE_LIST:
                    if (!empty($arProperty['VALUE']))
                    {
                        if (is_array($arProperty['VALUE']))
                            $value .= implode(', ', $arProperty['VALUE']);
                        else
                            $value .= $arProperty['VALUE'];
                    }
                    break;
                case Iblock\PropertyTable::TYPE_FILE:
                    if (!empty($arProperty['VALUE']))
                    {
                        if (is_array($arProperty['VALUE']))
                        {
                            foreach ($arProperty['VALUE'] as &$intValue)
                            {
                                $intValue = (int)$intValue;
                                if ($intValue > 0)
                                {
                                    if ($ar_file = CFile::GetFileArray($intValue))
                                    {
                                        if(substr($ar_file["SRC"], 0, 1) == "/")
                                            $strFile = $usedProtocol.$iblockServerName.CHTTP::urnEncode($ar_file['SRC'], 'utf-8');
                                        else
                                            $strFile = $ar_file["SRC"];
                                        $value .= ($value ? ', ' : '').$strFile;
                                    }
                                }
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        else
                        {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if ($arProperty['VALUE'] > 0)
                            {
                                if ($ar_file = CFile::GetFileArray($arProperty['VALUE']))
                                {
                                    if(substr($ar_file["SRC"], 0, 1) == "/")
                                        $strFile = $usedProtocol.$iblockServerName.CHTTP::urnEncode($ar_file['SRC'], 'utf-8');
                                    else
                                        $strFile = $ar_file["SRC"];
                                    $value = $strFile;
                                }
                            }
                        }
                    }
                    break;
                default:
                    if ($bParam && $arProperty['WITH_DESCRIPTION'] == 'Y')
                    {
                        $description = $arProperty['DESCRIPTION'];
                        $value = $arProperty['VALUE'];
                    }
                    else
                    {
                        $value = is_array($arProperty['VALUE']) ? implode(', ', $arProperty['VALUE']) : $arProperty['VALUE'];
                    }
            }

            // !!!! check multiple properties and properties like CML2_ATTRIBUTES

            if (empty($paramName)) {
                $paramName = 'param name="'.yandex_text2xml($arProperty['NAME'], true).'" '.$addParams;
                $endParamName = 'param';
            } else if (empty($endParamName)) {
                $endParamName = $paramName;
            }

            if ($bParam)
            {
                if (is_array($description))
                {
                    foreach ($value as $key => $val)
                    {
                        $strProperty .= $strProperty ? "\n" : "";
                        $strProperty .= '<'.$paramName.'>'.yandex_text2xml($val, true).'</'.$endParamName.'>';
                    }
                }
                else
                {
                    $strProperty .= '<'.$paramName.'>'.yandex_text2xml($value, true).'</'.$endParamName.'>';
                }
            }
            else
            {
                $param_h = yandex_text2xml($param, true);
                $strProperty .= '<'.$param_h.'>'.yandex_text2xml($value, true).'</'.$param_h.'>';
            }
            if (empty($value))
                $strProperty = '';
        }

        return $strProperty;
    }
}

$arRunErrors = array();

if ($XML_DATA && CheckSerializedData($XML_DATA))
{
    $XML_DATA = unserialize(stripslashes($XML_DATA));
    if (!is_array($XML_DATA)) $XML_DATA = array();
}
if (!is_array($XML_DATA))
    $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_XML_DATA');

$yandexFormat = 'none';
if (isset($XML_DATA['TYPE']) && isset($formatList[$XML_DATA['TYPE']]))
    $yandexFormat = $XML_DATA['TYPE'];

$productFormat = ($yandexFormat != 'none' ? ' type="'.htmlspecialcharsbx($yandexFormat).'"' : '');

$fields = array();
$parametricFields = array();
$fieldsExist = !empty($XML_DATA['XML_DATA']) && is_array($XML_DATA['XML_DATA']);
$parametricFieldsExist = false;
if ($fieldsExist)
{
    foreach ($XML_DATA['XML_DATA'] as $key => $value)
    {
        if ($key == 'PARAMS')
            $parametricFieldsExist = (!empty($value) && is_array($value));
        if (is_array($value))
            continue;
        $value = (string)$value;
        if ($value == '')
            continue;
        $fields[$key] = $value;
    }
    unset($key, $value);
    $fieldsExist = !empty($fields);
}

if ($parametricFieldsExist)
    $parametricFields = $XML_DATA['XML_DATA']['PARAMS'];

$needProperties = !empty($XML_DATA['XML_DATA']) && is_array($XML_DATA['XML_DATA']);

$IBLOCK_ID = (int)$IBLOCK_ID;
$db_iblock = CIBlock::GetByID($IBLOCK_ID);
if (!($ar_iblock = $db_iblock->Fetch()))
{
    $arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_FOUND_EXT'));
}
else
{
    $SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

    if (strlen($SETUP_SERVER_NAME) <= 0)
    {
        if (strlen($ar_iblock['SERVER_NAME']) <= 0)
        {
            $b = "sort";
            $o = "asc";
            $rsSite = CSite::GetList($b, $o, array("LID" => $ar_iblock["LID"]));
            if($arSite = $rsSite->Fetch())
                $ar_iblock["SERVER_NAME"] = $arSite["SERVER_NAME"];
            if(strlen($ar_iblock["SERVER_NAME"])<=0 && defined("SITE_SERVER_NAME"))
                $ar_iblock["SERVER_NAME"] = SITE_SERVER_NAME;
            if(strlen($ar_iblock["SERVER_NAME"])<=0)
                $ar_iblock["SERVER_NAME"] = COption::GetOptionString("main", "server_name", "");
        }
    }
    else
    {
        $ar_iblock['SERVER_NAME'] = $SETUP_SERVER_NAME;
    }
    $ar_iblock['PROPERTY'] = array();
    $rsProps = CIBlockProperty::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
    );
    while ($arProp = $rsProps->Fetch())
    {
        $arProp['ID'] = (int)$arProp['ID'];
        $arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
        $arProp['CODE'] = (string)$arProp['CODE'];
        $ar_iblock['PROPERTY'][$arProp['ID']] = $arProp;
    }
}

global $iblockServerName;
$iblockServerName = $ar_iblock["SERVER_NAME"];

$arProperties = array();
if (isset($ar_iblock['PROPERTY']))
    $arProperties = $ar_iblock['PROPERTY'];

$boolOffers = false;
$arOffers = false;
$arOfferIBlock = false;
$intOfferIBlockID = 0;
$arSelectOfferProps = array();
$arSelectedPropTypes = array(
    Iblock\PropertyTable::TYPE_STRING,
    Iblock\PropertyTable::TYPE_NUMBER,
    Iblock\PropertyTable::TYPE_LIST,
    Iblock\PropertyTable::TYPE_ELEMENT,
    Iblock\PropertyTable::TYPE_SECTION
);
$arOffersSelectKeys = array(
    YANDEX_SKU_EXPORT_ALL,
    YANDEX_SKU_EXPORT_MIN_PRICE,
    YANDEX_SKU_EXPORT_PROP,
);
$arCondSelectProp = array(
    'ZERO',
    'NONZERO',
    'EQUAL',
    'NONEQUAL',
);
$arPropertyMap = array();
$arSKUExport = array();

$arCatalog = CCatalog::GetByIDExt($IBLOCK_ID);
if (empty($arCatalog))
{
    $arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_IS_CATALOG'));
}
else
{
    $arOffers = CCatalogSku::GetInfoByProductIBlock($IBLOCK_ID);
    if (!empty($arOffers['IBLOCK_ID']))
    {
        $intOfferIBlockID = $arOffers['IBLOCK_ID'];
        $rsOfferIBlocks = CIBlock::GetByID($intOfferIBlockID);
        if (($arOfferIBlock = $rsOfferIBlocks->Fetch()))
        {
            $boolOffers = true;
            $rsProps = CIBlockProperty::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC'),
                array('IBLOCK_ID' => $intOfferIBlockID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
            );
            while ($arProp = $rsProps->Fetch())
            {
                $arProp['ID'] = (int)$arProp['ID'];
                if ($arOffers['SKU_PROPERTY_ID'] != $arProp['ID'])
                {
                    $arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
                    $arProp['CODE'] = (string)$arProp['CODE'];
                    $ar_iblock['OFFERS_PROPERTY'][$arProp['ID']] = $arProp;
                    $arProperties[$arProp['ID']] = $arProp;
                    if (in_array($arProp['PROPERTY_TYPE'], $arSelectedPropTypes))
                        $arSelectOfferProps[] = $arProp['ID'];
                    if ($arProp['CODE'] !== '')
                    {
                        foreach ($ar_iblock['PROPERTY'] as &$arMainProp)
                        {
                            if ($arMainProp['CODE'] == $arProp['CODE'])
                            {
                                $arPropertyMap[$arProp['ID']] = $arMainProp['CODE'];
                                break;
                            }
                        }
                        if (isset($arMainProp))
                            unset($arMainProp);
                    }
                }
            }
            $arOfferIBlock['LID'] = $ar_iblock['LID'];
        }
        else
        {
            $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_OFFERS_IBLOCK_ID');
        }
    }
    if ($boolOffers)
    {
        if (empty($XML_DATA['SKU_EXPORT']))
        {
            $arRunErrors[] = GetMessage('YANDEX_ERR_SKU_SETTINGS_ABSENT');
        }
        else
        {
            $arSKUExport = $XML_DATA['SKU_EXPORT'];;
            if (empty($arSKUExport['SKU_EXPORT_COND']) || !in_array($arSKUExport['SKU_EXPORT_COND'],$arOffersSelectKeys))
            {
                $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_CONDITION_ABSENT');
            }
            if (YANDEX_SKU_EXPORT_PROP == $arSKUExport['SKU_EXPORT_COND'])
            {
                if (empty($arSKUExport['SKU_PROP_COND']) || !is_array($arSKUExport['SKU_PROP_COND']))
                {
                    $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
                }
                else
                {
                    if (empty($arSKUExport['SKU_PROP_COND']['PROP_ID']) || !in_array($arSKUExport['SKU_PROP_COND']['PROP_ID'],$arSelectOfferProps))
                    {
                        $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
                    }
                    if (empty($arSKUExport['SKU_PROP_COND']['COND']) || !in_array($arSKUExport['SKU_PROP_COND']['COND'],$arCondSelectProp))
                    {
                        $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_COND_ABSENT');
                    }
                    else
                    {
                        if ($arSKUExport['SKU_PROP_COND']['COND'] == 'EQUAL' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
                        {
                            if (empty($arSKUExport['SKU_PROP_COND']['VALUES']))
                            {
                                $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_VALUES_ABSENT');
                            }
                        }
                    }
                }
            }
        }
    }
}

$arUserTypeFormat = array();
foreach($arProperties as $key => $arProperty)
{
    $arProperty["USER_TYPE"] = (string)$arProperty["USER_TYPE"];
    $arUserTypeFormat[$arProperty["ID"]] = false;
    if ($arProperty["USER_TYPE"] !== '')
    {
        $arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
        if (isset($arUserType["GetPublicViewHTML"]))
        {
            $arUserTypeFormat[$arProperty["ID"]] = $arUserType["GetPublicViewHTML"];
            $arProperties[$key]['PROPERTY_TYPE'] = 'USER_TYPE';
        }
    }
}

$bAllSections = false;
$arSections = array();
if (empty($arRunErrors))
{
    if (is_array($V))
    {
        foreach ($V as $key => $value)
        {
            if (trim($value)=="0")
            {
                $bAllSections = true;
                break;
            }
            $value = (int)$value;
            if ($value > 0)
            {
                $arSections[] = $value;
            }
        }
    }

    if (!$bAllSections && empty($arSections))
    {
        $arRunErrors[] = GetMessage('YANDEX_ERR_NO_SECTION_LIST');
    }
}

if (!empty($XML_DATA['PRICE']))
{
    if ((int)$XML_DATA['PRICE'] > 0)
    {
        $rsCatalogGroups = CCatalogGroup::GetGroupsList(array('CATALOG_GROUP_ID' => $XML_DATA['PRICE'],'GROUP_ID' => 2));
        if (!($arCatalogGroup = $rsCatalogGroups->Fetch()))
        {
            $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
        }
    }
    else
    {
        $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
    }
}

$usedProtocol = (isset($USE_HTTPS) && $USE_HTTPS == 'Y' ? 'https://' : 'http://');
$filterAvailable = (isset($FILTER_AVAILABLE) && $FILTER_AVAILABLE == 'Y');
$disableReferers = (isset($DISABLE_REFERERS) && $DISABLE_REFERERS == 'Y');

if (strlen($SETUP_FILE_NAME) <= 0)
{
    $arRunErrors[] = GetMessage("CATI_NO_SAVE_FILE");
}
elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
{
    $arRunErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
}
else
{
    $SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
}
if (empty($arRunErrors))
{
    /*	if ($GLOBALS["APPLICATION"]->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
        {
            $arRunErrors[] = str_replace('#FILE#', $SETUP_FILE_NAME,GetMessage('YANDEX_ERR_FILE_ACCESS_DENIED'));
        } */
}

if (empty($arRunErrors))
{
    CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

    if (!$fp = @fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb"))
    {
        $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
    }
    else
    {
        if (!@fwrite($fp, '<? $disableReferers = '.($disableReferers ? 'true' : 'false').';'."\n"))
        {
            $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_SETUP_FILE_WRITE'));
            @fclose($fp);
        }
        else
        {
            if (!$disableReferers)
            {
                fwrite($fp, 'if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "googlem";'."\n");
                fwrite($fp, 'if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "googlem";'."\n");
                fwrite($fp, '$strReferer1 = htmlspecialchars($_GET["referer1"]);'."\n");
                fwrite($fp, 'if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";'."\n");
                fwrite($fp, '$strReferer2 = htmlspecialchars($_GET["referer2"]);'."\n");
            }
        }
    }
}

if (empty($arRunErrors))
{
    /** @noinspection PhpUndefinedVariableInspection */
    fwrite($fp, 'header("Content-Type: text/xml; charset=utf-8");'."\n");
    fwrite($fp, 'echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">"?>');
    /*fwrite($fp, 'echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">"?>');*/
    //    fwrite($fp, "\n".'<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n");
    fwrite($fp, '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n");
    fwrite($fp, '<channel>'."\n");
    //    fwrite($fp, '<yml_catalog date="'.date("Y-m-d H:i").'">'."\n");
    //    fwrite($fp, '<shop>'."\n");

    fwrite($fp, '<title>'.$APPLICATION->ConvertCharset(htmlspecialcharsbx(COption::GetOptionString('main', 'site_name', '')), LANG_CHARSET, 'utf-8')."</title>\n");

    fwrite($fp, '<description>'.$APPLICATION->ConvertCharset(htmlspecialcharsbx(COption::GetOptionString('main', 'site_name', '')), LANG_CHARSET, 'utf-8')."</description>\n");
    fwrite($fp, '<link>'.$usedProtocol.htmlspecialcharsbx($ar_iblock['SERVER_NAME'])."</link>\n");
//    fwrite($fp, '<platform>1C-Bitrix</platform>'."\n");

    //    $strTmp = '<currencies>'."\n";
    //
    //    $RUR = 'RUB';
    //    $currencyIterator = Currency\CurrencyTable::getList(array(
    //        'select' => array('CURRENCY'),
    //        'filter' => array('=CURRENCY' => 'RUR')
    //    ));
    //    if ($currency = $currencyIterator->fetch())
    //        $RUR = 'RUR';
    //    unset($currency, $currencyIterator);
    //
    //    $arCurrencyAllowed = array($RUR, 'USD', 'EUR', 'UAH', 'BYR', 'BYN', 'KZT');
    //
    //    $BASE_CURRENCY = Currency\CurrencyManager::getBaseCurrency();
    //    if (is_array($XML_DATA['CURRENCY']))
    //    {
    //        foreach ($XML_DATA['CURRENCY'] as $CURRENCY => $arCurData)
    //        {
    //            if (in_array($CURRENCY, $arCurrencyAllowed))
    //            {
    //                if ($CURRENCY == 'RUB')
    //                    $strTmp.= '<currency id="'.$CURRENCY.'" rate="1" />'."\n";
    //                else
    //                    $strTmp.= '<currency id="'.$CURRENCY.'" rate="CBRF" />'."\n";
    //            }
    //        }
    //        unset($CURRENCY, $arCurData);
    //    }
    //    else
    //    {
    //        $currencyIterator = Currency\CurrencyTable::getList(array(
    //            'select' => array('CURRENCY', 'SORT'),
    //            'filter' => array('@CURRENCY' => $arCurrencyAllowed),
    //            'order' => array('SORT' => 'ASC', 'CURRENCY' => 'ASC')
    //        ));
    //        while ($currency = $currencyIterator->fetch())
    //            $strTmp .= '<currency id="'.$currency['CURRENCY'].'" rate="'.(CCurrencyRates::ConvertCurrency(1, $currency['CURRENCY'], $RUR)).'" />'. "\n";
    //        unset($currency, $currencyIterator);
    //    }
    //    $strTmp .= "</currencies>\n";
    //
    //    fwrite($fp, $strTmp);
    //    unset($strTmp);

    //*****************************************//


    //*****************************************//
    $intMaxSectionID = 0;

    $strTmpCat = '';
    $strTmpOff = '';

    $arSectionIDs = array();
    $arAvailGroups = array();
    if (!$bAllSections)
    {
        for ($i = 0, $intSectionsCount = count($arSections); $i < $intSectionsCount; $i++)
        {
            $sectionIterator = CIBlockSection::GetNavChain($IBLOCK_ID, $arSections[$i], array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN'));
            $curLEFT_MARGIN = 0;
            $curRIGHT_MARGIN = 0;
            while ($section = $sectionIterator->Fetch())
            {
                $section['ID'] = (int)$section['ID'];
                $section['IBLOCK_SECTION_ID'] = (int)$section['IBLOCK_SECTION_ID'];
                if ($arSections[$i] == $section['ID'])
                {
                    $curLEFT_MARGIN = (int)$section['LEFT_MARGIN'];
                    $curRIGHT_MARGIN = (int)$section['RIGHT_MARGIN'];
                    $arSectionIDs[] = $section['ID'];
                }
                $arAvailGroups[$section['ID']] = array(
                    'ID' => $section['ID'],
                    'IBLOCK_SECTION_ID' => $section['IBLOCK_SECTION_ID'],
                    'NAME' => $section['NAME']
                );
                if ($intMaxSectionID < $section['ID'])
                    $intMaxSectionID = $section['ID'];
            }
            unset($section, $sectionIterator);

            $filter = array("IBLOCK_ID"=>$IBLOCK_ID, ">LEFT_MARGIN"=>$curLEFT_MARGIN, "<RIGHT_MARGIN"=>$curRIGHT_MARGIN, "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
            $sectionIterator = CIBlockSection::GetList(array("LEFT_MARGIN"=>"ASC"), $filter, false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
            while ($section = $sectionIterator->Fetch())
            {
                $section["ID"] = (int)$section["ID"];
                $section["IBLOCK_SECTION_ID"] = (int)$section["IBLOCK_SECTION_ID"];
                $arSectionIDs[] = $section["ID"];
                $arAvailGroups[$section["ID"]] = $section;
                if ($intMaxSectionID < $section["ID"])
                    $intMaxSectionID = $section["ID"];
            }
            unset($section, $sectionIterator);
        }
        if (!empty($arSectionIDs))
            $arSectionIDs = array_unique($arSectionIDs);
    }
    else
    {
        $filter = array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
        $sectionIterator = CIBlockSection::GetList(array("LEFT_MARGIN"=>"ASC"), $filter, false, array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
        while ($section = $sectionIterator->Fetch())
        {
            $section["ID"] = (int)$section["ID"];
            $section["IBLOCK_SECTION_ID"] = (int)$section["IBLOCK_SECTION_ID"];
            $arAvailGroups[$section["ID"]] = $section;
            if ($intMaxSectionID < $section["ID"])
                $intMaxSectionID = $section["ID"];
        }
        unset($section, $sectionIterator);

        if (!empty($arAvailGroups))
            $arSectionIDs = array_keys($arAvailGroups);
    }

    foreach ($arAvailGroups as &$value)
    {
        $strTmpCat.= '<category id="'.$value['ID'].'"'.($value['IBLOCK_SECTION_ID'] > 0 ? ' parentId="'.$value['IBLOCK_SECTION_ID'].'"' : '').'>'.yandex_text2xml($value['NAME'], true).'</category>'."\n";
    }
    if (isset($value))
        unset($value);

    $intMaxSectionID += 100000000;

    //*****************************************//
    $boolNeedRootSection = false;

    CCatalogProduct::setPriceVatIncludeMode(true);
    CCatalogProduct::setUsedCurrency($BASE_CURRENCY);
    CCatalogProduct::setUseDiscount(true);

    if ($arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_CATALOG || $arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_OFFERS)
    {
        $arSelect = array(
            "ID", "LID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME",
            "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE", "LANG_DIR", "DETAIL_PAGE_URL", "DETAIL_TEXT", "DETAIL_TEXT_TYPE",
            "CATALOG_AVAILABLE",
            "CANONICAL_PAGE_URL",
        );

        $filter = array("IBLOCK_ID" => $IBLOCK_ID);
        if (!$bAllSections && !empty($arSectionIDs))
        {
            $filter["INCLUDE_SUBSECTIONS"] = "Y";
            $filter["SECTION_ID"] = $arSectionIDs;
        }
        $filter["ACTIVE"] = "Y";
        $filter["ACTIVE_DATE"] = "Y";
        if ($filterAvailable)
            $filter['CATALOG_AVAILABLE'] = 'Y';

        $filter['PROPERTY_NEW_VALUE'] = 'Y';

        $resElementCNT = CIBlock::GetElementCount($IBLOCK_ID);
        $arNavStartParamsItem = false;
        if (intval($resElementCNT) > 0) {
            $arNavStartParamsItem = array('nTopCount' => $resElementCNT);
        }
        if ($fastExport) {
            $arNavStartParamsItem = array('nTopCount' => 10);
//            $filter['ID'] = '36968';
        }
        $res = CIBlockElement::GetList(array('ID' => 'ASC'), $filter, false, $arNavStartParamsItem, $arSelect);
        $total_sum = 0;
        $is_exists = false;
        $cnt = 0;
        while ($obElement = $res->GetNextElement()) {
            continue;
            $cnt++;
            $arAcc = $obElement->GetFields();
            if ($needProperties)
                $arAcc["PROPERTIES"] = $obElement->GetProperties();

            $str_AVAILABLE = ' available="' . ($arAcc['CATALOG_AVAILABLE'] == 'Y' ? 'true' : 'false') . '"';

            $fullPrice = 0;
            $minPrice = 0;
            $minPriceRUR = 0;
            $minPriceGroup = 0;
            $minPriceCurrency = "";

            if ($XML_DATA['PRICE'] > 0) {
                $rsPrices = CPrice::GetListEx(array(), array('PRODUCT_ID' => $arAcc['ID'], 'CATALOG_GROUP_ID' => $XML_DATA['PRICE'], 'CAN_BUY' => 'Y', 'GROUP_GROUP_ID' => array(2), '+<=QUANTITY_FROM' => 1, '+>=QUANTITY_TO' => 1,));
                if ($arPrice = $rsPrices->Fetch()) {
                    if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice($arAcc['ID'], 1, array(2), // anonymous
                        'N', array($arPrice), $ar_iblock['LID'], array())
                    ) {
                        $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                        $fullPrice = $arOptimalPrice['RESULT_PRICE']['BASE_PRICE'];
                        $minPriceCurrency = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
                        if ($minPriceCurrency == $RUR)
                            $minPriceRUR = $minPrice; else
                            $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                        $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
                    }
                }
            } else {
                if ($arPrice = CCatalogProduct::GetOptimalPrice($arAcc['ID'], 1, array(2), // anonymous
                    'N', array(), $ar_iblock['LID'], array())
                ) {
                    $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                    $fullPrice = $arPrice['RESULT_PRICE']['BASE_PRICE'];
                    $minPriceCurrency = $arPrice['RESULT_PRICE']['CURRENCY'];
                    if ($minPriceCurrency == $RUR)
                        $minPriceRUR = $minPrice; else
                        $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                    $minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
                }
            }

            if ($minPrice <= 0)
                continue;

            $strFile = '';
            $itemArticle = '';
            foreach ($arAcc['PROPERTIES'] as $itemProperty) {
                if ($itemProperty['CODE'] == 'CML2_ARTICLE') {
                    $itemArticle = $itemProperty['VALUE'];
                    $arAcc['GET_ARTICUL_IMAGE'] = Citfact\Paolo::getProductImage($itemArticle, $imageConfig);
                    if (!empty($arAcc['GET_ARTICUL_IMAGE']['PHOTO'][0]['SMALL'])) {
                        $strFile = $usedProtocol . $ar_iblock['SERVER_NAME'] . CHTTP::urnEncode($arAcc['GET_ARTICUL_IMAGE']['PHOTO'][0]['SMALL'], 'utf-8');
                    }
                    break;
                } elseif ($itemProperty['CODE'] == 'NAIMENOVANIE_MARKETING' && !empty($itemProperty['VALUE'])) {
                    $arAcc['~NAME'] = $itemProperty['VALUE'];
                }
            }

            if (!empty($strFile)) {
                $boolCurrentSections = false;
                $bNoActiveGroup = true;
                $strTmpOff_tmp = "";
                $db_res1 = CIBlockElement::GetElementGroups($arAcc["ID"], false, array('ID', 'ADDITIONAL_PROPERTY_ID'));
                while ($ar_res1 = $db_res1->Fetch()) {
                    if (0 < (int)$ar_res1['ADDITIONAL_PROPERTY_ID'])
                        continue;
                    $boolCurrentSections = true;
                    if (in_array((int)$ar_res1["ID"], $arSectionIDs)) {
                        $strTmpOff_tmp .= "<categoryId>" . $ar_res1["ID"] . "</categoryId>\n";
                        $bNoActiveGroup = false;

                    }
                }
                if (!$boolCurrentSections) {
                    $boolNeedRootSection = true;
                    $strTmpOff_tmp .= "<categoryId>" . $intMaxSectionID . "</categoryId>\n";
                } else {
                    if ($bNoActiveGroup)
                        continue;
                }

                if (strlen($arAcc['~CANONICAL_PAGE_URL']) > 0)
                    $arAcc['~DETAIL_PAGE_URL'] = str_replace(' ', '%20', $arAcc['~CANONICAL_PAGE_URL']);
                elseif (strlen($arAcc['~DETAIL_PAGE_URL']) <= 0)
                    $arAcc['~DETAIL_PAGE_URL'] = $usedProtocol . $ar_iblock['SERVER_NAME'] . '/';
                else
                    $arAcc['~DETAIL_PAGE_URL'] = $usedProtocol . $ar_iblock['SERVER_NAME'] . str_replace(' ', '%20', $arAcc['~DETAIL_PAGE_URL']);

                $strTmpOff .= '<item>'."\n";
                $strTmpOff .= '<g:id>' . $arAcc["ID"] . '</g:id>'."\n";
                $referer = '';
                if (!$disableReferers)
                    $referer = (strpos($arAcc['DETAIL_PAGE_URL'], '?') === false ? '?' : '&amp;') . 'r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?>';

                $strTmpOff .= "<url>" . htmlspecialcharsbx($arAcc["~DETAIL_PAGE_URL"]) . $referer . "</url>\n";

                $strTmpOff .= "<price>" . $minPrice . "</price>\n";
                if ($minPrice < $fullPrice)
                    $strTmpOff .= "<oldprice>" . $fullPrice . "</oldprice>\n";
                $strTmpOff .= "<currencyId>" . $minPriceCurrency . "</currencyId>\n";

                $strTmpOff .= $strTmpOff_tmp;

                $arAcc["DETAIL_PICTURE"] = (int)$arAcc["DETAIL_PICTURE"];
                $arAcc["PREVIEW_PICTURE"] = (int)$arAcc["PREVIEW_PICTURE"];
                $strTmpOff .= "<picture>" . $strFile . "</picture>\n";
                $strTmpOff .= "<delivery>true</delivery>\n";
                $strTmpOff .= "<manufacturer_warranty>true</manufacturer_warranty>\n";

                $y = 0;
                foreach ($arYandexFields as $key) {
                    switch ($key) {
                        case 'name':
                            if ($yandexFormat == 'vendor.model' || $yandexFormat == 'artist.title')
                                continue;

                            $strTmpOff .= "<name>" . yandex_text2xml($arAcc["NAME"], true) . "</name>\n";
                            break;
                        case 'description':
                            $text = yandex_text2xml(TruncateText(($arAcc["DETAIL_TEXT_TYPE"] == "html" ? strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arAcc["~DETAIL_TEXT"])) : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arAcc["~DETAIL_TEXT"])), 175), true);
                            if (empty($text))
                                $text = Loc::getMessage('YANDEX_CUSTOM_DESCRIPTION');
                            $strTmpOff .= "<description>" . $text . "</description>\n";
                            break;
                        case 'vendor':
                            $strTmpOff .= "<vendor>" . Loc::getMessage('YANDEX_VENDOR_PAOLO_CONTE') . "</vendor>\n";
                            break;
                        case 'vendorCode':
                            $strTmpOff .= "<vendorCode>" . $itemArticle . "</vendorCode>\n";
                            break;
                        case 'param':
                            if ($parametricFieldsExist) {
                                foreach ($parametricFields as $paramKey => $prop_id) {
                                    $strParamValue = '';
                                    if ($prop_id) {
                                        $strParamValue = yandex_get_value($arAcc, 'PARAM_' . $paramKey, $prop_id, $arProperties, $arUserTypeFormat, $usedProtocol);
                                    }
                                    if ('' != $strParamValue)
                                        $strTmpOff .= $strParamValue . "\n";
                                }
                                unset($paramKey, $prop_id);
                            }
                            break;
                        case 'model':
                        case 'title':
                            if (!$fieldsExist || !isset($fields[$key])) {
                                if ($key == 'model' && $yandexFormat == 'vendor.model' || $key == 'title' && $yandexFormat == 'artist.title')

                                    $strTmpOff .= "<" . $key . ">" . yandex_text2xml($arAcc["~NAME"], true) . "</" . $key . ">\n";
                            } else {
                                $strValue = '';
                                $strValue = yandex_get_value($arAcc, $key, $fields[$key], $arProperties, $arUserTypeFormat, $usedProtocol);
                                if ('' != $strValue)
                                    $strTmpOff .= $strValue . "\n";
                            }
                            break;
                        case 'year':
                        default:
                            if ($key == 'year') {
                                $y++;
                                if ($yandexFormat == 'artist.title') {
                                    if ($y == 1)
                                        continue;
                                } else {
                                    if ($y > 1)
                                        continue;
                                }
                            }
                            if ($fieldsExist && isset($fields[$key])) {
                                $strValue = '';
                                $strValue = yandex_get_value($arAcc, $key, $fields[$key], $arProperties, $arUserTypeFormat, $usedProtocol);
                                if ('' != $strValue)
                                    $strTmpOff .= $strValue . "\n";
                            }
                    }
                }

                $strTmpOff .= "</item>\n";
            }
            if (100 <= $cnt)
            {
                $cnt = 0;
                CCatalogDiscount::ClearDiscountCache(array(
                    'PRODUCT' => true,
                    'SECTIONS' => true,
                    'PROPERTIES' => true
                ));
            }
        }
    }
    elseif ($arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_PRODUCT || $arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_FULL)
    {
        $arOfferSelect = array(
            "ID", "LID", "IBLOCK_ID", "NAME",
            "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "DETAIL_TEXT", "DETAIL_TEXT_TYPE",
            "CATALOG_AVAILABLE", "CATALOG_TYPE", "CANONICAL_PAGE_URL"
        );
        $arOfferFilter = array('IBLOCK_ID' => $intOfferIBlockID, '=PROPERTY_'.$arOffers['SKU_PROPERTY_ID'] => 0, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
        if (YANDEX_SKU_EXPORT_PROP == $arSKUExport['SKU_EXPORT_COND'])
        {
            $strExportKey = '';
            $mxValues = false;
            if ($arSKUExport['SKU_PROP_COND']['COND'] == 'NONZERO' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
                $strExportKey = '!';
            $strExportKey .= 'PROPERTY_'.$arSKUExport['SKU_PROP_COND']['PROP_ID'];
            if ($arSKUExport['SKU_PROP_COND']['COND'] == 'EQUAL' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL')
                $mxValues = $arSKUExport['SKU_PROP_COND']['VALUES'];
            $arOfferFilter[$strExportKey] = $mxValues;
        }
        if ($filterAvailable)
            $arOfferFilter['CATALOG_AVAILABLE'] = 'Y';

        $arSelect = array(
            "ID", "LID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME",
            "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "DETAIL_TEXT", "DETAIL_TEXT_TYPE",
            "CATALOG_AVAILABLE", "CATALOG_TYPE", "CATALOG_TYPE", "CANONICAL_PAGE_URL"
        );

        $arFilter = array("IBLOCK_ID" => $IBLOCK_ID);
        if (!$bAllSections && !empty($arSectionIDs))
        {
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
            $arFilter["SECTION_ID"] = $arSectionIDs;
        }
        $arFilter["ACTIVE"] = "Y";
        $arFilter["ACTIVE_DATE"] = "Y";
        if ($filterAvailable)
            $arFilter['CATALOG_AVAILABLE'] = 'Y';

        $strOfferTemplateURL = '';
        if (!empty($arSKUExport['SKU_URL_TEMPLATE_TYPE']))
        {
            switch($arSKUExport['SKU_URL_TEMPLATE_TYPE'])
            {
                case YANDEX_SKU_TEMPLATE_PRODUCT:
                    $strOfferTemplateURL = '#PRODUCT_URL#';
                    break;
                case YANDEX_SKU_TEMPLATE_CUSTOM:
                    if (!empty($arSKUExport['SKU_URL_TEMPLATE']))
                        $strOfferTemplateURL = $arSKUExport['SKU_URL_TEMPLATE'];
                    break;
                case YANDEX_SKU_TEMPLATE_OFFERS:
                default:
                    $strOfferTemplateURL = '';
                    break;
            }
        }

        $arSectionsAdvisable = array('122', '167');
        $arSectionsAdvisable = getAdvisableSections($arSectionsAdvisable, $arAvailGroups);

        $cnt = 0;
        $arrFilterFunc = getCatalogFilter();
        $arFilter = array_merge($arFilter, $arrFilterFunc);
        $arFilter['PROPERTY_NEW_VALUE'] = 'Y';
        $resElementCNT = CIBlock::GetElementCount($IBLOCK_ID);
        $arNavStartParamsItem = false;

        if (intval($resElementCNT) > 0) {
            $arNavStartParamsItem = array('nTopCount' => $resElementCNT);
        }
        if ($fastExport) {
            $arNavStartParamsItem = array('nTopCount' => 10);
//            $arFilter['ID'] = ['36968', '36980', '36986', '37001', '37237', '78829', '78818', '78814', '79560', '87720', '87712'];
        }

        $rsItems = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, $arNavStartParamsItem, $arSelect);
        while ($obItem = $rsItems->GetNextElement())
        {
            $cnt++;
            $arCross = array();
            $arItem = $obItem->GetFields();

            $arItem['PROPERTIES'] = $obItem->GetProperties();
            if (!empty($arItem['PROPERTIES']))
            {
                foreach ($arItem['PROPERTIES'] as &$arProp)
                {
                    $arCross[$arProp['ID']] = $arProp;
                }
                if (isset($arProp))
                    unset($arProp);
                $arItem['PROPERTIES'] = $arCross;
            }
            $boolItemExport = false;
            $boolItemOffers = false;
            $arItem['OFFERS'] = array();

            $boolCurrentSections = false;
            $boolNoActiveSections = true;
            $strSections = '';

            $onePath = '';
            $twoPath = '';

            $rsSections = CIBlockElement::GetElementGroups($arItem["ID"], false, array('ID', 'ADDITIONAL_PROPERTY_ID'));
            while ($arSection = $rsSections->Fetch())
            {
                if (0 < (int)$arSection['ADDITIONAL_PROPERTY_ID'])
                    continue;

                $arSection['ID'] = (int)$arSection['ID'];
                $boolCurrentSections = true;
                if (in_array($arSection['ID'], $arSectionIDs))
                {
                    if (in_array($arSection['ID'], $arSectionsAdvisable) && empty($twoPath)) {
                        $twoPath = getPathSection($arSection["ID"], $arAvailGroups);
                    } elseif (empty($onePath)) {
                        $onePath = getPathSection($arSection["ID"], $arAvailGroups);
                    }
                    $boolNoActiveSections = false;
                }
            }


            if (!empty($onePath)) {
                $strSections .= "<g:product_type> ".$onePath." </g:product_type>\n";
            } elseif(!empty($twoPath)) {
                $strSections .= "<g:product_type> ".$twoPath." </g:product_type>\n";
            }
            if (!$boolCurrentSections)
            {
                $boolNeedRootSection = true;
                $strSections .= "<g:product_type> ".getPathSection($intMaxSectionID, $arAvailGroups)." </g:product_type>\n";
            }
            else
            {
                if ($boolNoActiveSections)
                    continue;
            }

            $arItem['YANDEX_CATEGORY'] = $strSections;

            $strFile = '';
            $countOffers = 0;
            $strFile = '';
            $arAdditionalImg = '';
            foreach ($arItem['PROPERTIES'] as $itemProperty) {
                if ($itemProperty['CODE'] == 'CML2_ARTICLE') {
                    $itemArticle = $itemProperty['VALUE'];
                    $arImgFeed = getImgFeed($itemArticle, $imageConfig, $usedProtocol.$ar_iblock['SERVER_NAME']);
                    $strFile = $arImgFeed['FIRST'];
                    $arAdditionalImg = $arImgFeed['ADDITIONAL'];
                } elseif($itemProperty['CODE'] == 'OFFERS_AMOUNT') {
                    $countOffers = intval($itemProperty['VALUE']);
                } elseif ($itemProperty['CODE'] == 'NAIMENOVANIE_MARKETING' && !empty($itemProperty['VALUE'])) {
                    $arItem['~NAME'] = $itemProperty['VALUE'];
                }
            }
            $arItem['YANDEX_PICT'] = $strFile;

            $arItem['YANDEX_DESCR'] = yandex_text2xml(TruncateText(
                ($arItem["DETAIL_TEXT_TYPE"]=="html"?
                    strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arItem["~DETAIL_TEXT"])) : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arItem["~DETAIL_TEXT"])),
                175), true);

            if ($arItem['CATALOG_TYPE'] == Catalog\ProductTable::TYPE_SKU)
            {
                $arOfferFilter['=PROPERTY_'.$arOffers['SKU_PROPERTY_ID']] = $arItem['ID'];
                $arNavStartParams = array('nTopCount' => $countOffers);
                $rsOfferItems = CIBlockElement::GetList(array('ID' => 'ASC'), $arOfferFilter, false, $arNavStartParams, $arOfferSelect);

                if (!empty($strOfferTemplateURL))
                    $rsOfferItems->SetUrlTemplates($strOfferTemplateURL);
                if (YANDEX_SKU_EXPORT_MIN_PRICE == $arSKUExport['SKU_EXPORT_COND'])
                {
                    $arCurrentOffer = false;
                    $arCurrentPrice = false;
                    $dblAllMinPrice = 0;
                    $boolFirst = true;

                    while ($obOfferItem = $rsOfferItems->GetNextElement())
                    {
                        $arOfferItem = $obOfferItem->GetFields();
                        $fullPrice = 0;
                        $minPrice = 0;

                        $minPriceRUR = 0;
                        $minPriceCurrency = '';
                        $minPriceGroup = 0;

                        $arPrices = array(
                            PRICE_ID_MOSCOW,
                            PRICE_ID_MOSCOW_ACTION
                        );

                        $rsPrices = CPrice::GetListEx(
                            array(),
                            array(
                                'PRODUCT_ID' => $arOfferItem['ID'],
                                'CATALOG_GROUP_ID' => $arPrices,
                                'CAN_BUY' => 'Y',
                                'GROUP_GROUP_ID' => array(2),
                                '+<=QUANTITY_FROM' => 1,
                                '+>=QUANTITY_TO' => 1,
                            )
                        );
                        while ($arPrice = $rsPrices->Fetch())
                        {
                            if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                                $arOfferItem['ID'],
                                1,
                                array(2),
                                'N',
                                array($arPrice),
                                $arOfferIBlock['LID'],
                                array()
                            ))
                            {
                                if (
                                    $arPrice['CATALOG_GROUP_ID'] == PRICE_ID_MOSCOW
                                    && intval($arPrice['PRICE']) > 0
                                ) {
                                    $fullPrice = $arPrice['PRICE'];
                                }
                                $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                                $minPriceCurrency = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
                                if ($minPriceCurrency == $RUR)
                                    $minPriceRUR = $minPrice;
                                else
                                    $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                                $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
                            }
                        }



//                        if ($XML_DATA['PRICE'] > 0)
//                        {
//                            $rsPrices = CPrice::GetListEx(array(), array(
//                                    'PRODUCT_ID' => $arOfferItem['ID'],
//                                    'CATALOG_GROUP_ID' => $XML_DATA['PRICE'],
//                                    'CAN_BUY' => 'Y',
//                                    'GROUP_GROUP_ID' => array(2),
//                                    '+<=QUANTITY_FROM' => 1,
//                                    '+>=QUANTITY_TO' => 1,
//                                )
//                            );
//                            if ($arPrice = $rsPrices->Fetch())
//                            {
//                                if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
//                                    $arOfferItem['ID'],
//                                    1,
//                                    array(2),
//                                    'N',
//                                    array($arPrice),
//                                    $arOfferIBlock['LID'],
//                                    array()
//                                )
//                                )
//                                {
//                                    $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
//                                    $fullPrice = $arOptimalPrice['RESULT_PRICE']['BASE_PRICE'];
//                                    $minPriceCurrency = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
//                                    if ($minPriceCurrency == $RUR)
//                                        $minPriceRUR = $minPrice;
//                                    else
//                                        $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
//                                    $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
//                                }
//                            }
//                        }
//                        else
//                        {
//                            if ($arPrice = CCatalogProduct::GetOptimalPrice(
//                                $arOfferItem['ID'],
//                                1,
//                                array(2), // anonymous
//                                'N',
//                                array(),
//                                $arOfferIBlock['LID'],
//                                array()
//                            )
//                            )
//                            {
//                                $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
//                                $fullPrice = $arPrice['RESULT_PRICE']['BASE_PRICE'];
//                                $minPriceCurrency = $arPrice['RESULT_PRICE']['CURRENCY'];
//                                if ($minPriceCurrency == $RUR)
//                                    $minPriceRUR = $minPrice;
//                                else
//                                    $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
//                                $minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
//                            }
//                        }
                        if ($minPrice <= 0)
                            continue;
                        if ($boolFirst)
                        {
                            $dblAllMinPrice = $minPriceRUR;
                            $arCross = (!empty($arItem['PROPERTIES']) ? $arItem['PROPERTIES'] : array());
                            $arOfferItem['PROPERTIES'] = $obOfferItem->GetProperties();
                            if (!empty($arOfferItem['PROPERTIES']))
                            {
                                foreach ($arOfferItem['PROPERTIES'] as $arProp)
                                {
                                    $arCross[$arProp['ID']] = $arProp;
                                }
                            }
                            $arOfferItem['PROPERTIES'] = $arCross;

                            $arCurrentOffer = $arOfferItem;
                            $arCurrentPrice = array(
                                'FULL_PRICE' => $fullPrice,
                                'MIN_PRICE' => $minPrice,
                                'MIN_PRICE_CURRENCY' => $minPriceCurrency,
                                'MIN_PRICE_RUR' => $minPriceRUR,
                                'MIN_PRICE_GROUP' => $minPriceGroup,
                            );
                            $boolFirst = false;
                        }
                        else
                        {
                            if ($dblAllMinPrice > $minPriceRUR)
                            {
                                $dblAllMinPrice = $minPriceRUR;
                                $arCross = (!empty($arItem['PROPERTIES']) ? $arItem['PROPERTIES'] : array());
                                $arOfferItem['PROPERTIES'] = $obOfferItem->GetProperties();
                                if (!empty($arOfferItem['PROPERTIES']))
                                {
                                    foreach ($arOfferItem['PROPERTIES'] as $arProp)
                                    {
                                        $arCross[$arProp['ID']] = $arProp;
                                    }
                                }
                                $arOfferItem['PROPERTIES'] = $arCross;

                                $arCurrentOffer = $arOfferItem;
                                $arCurrentPrice = array(
                                    'FULL_PRICE' => $fullPrice,
                                    'MIN_PRICE' => $minPrice,
                                    'MIN_PRICE_CURRENCY' => $minPriceCurrency,
                                    'MIN_PRICE_RUR' => $minPriceRUR,
                                    'MIN_PRICE_GROUP' => $minPriceGroup,
                                );
                            }
                        }
                    }
                    if (!empty($arCurrentOffer) && !empty($arCurrentPrice))
                    {
                        $strFile = '';
                        $arAdditionalImg = array();
                        $ofArticle = '';
                        $arOfferItemName = $arItem['~NAME'];
                        foreach ($arCurrentOffer['PROPERTIES'] as $ofProperty) {
                            if ($ofProperty['CODE'] == 'CML2_ARTICLE') {
                                $ofArticle = $ofProperty['VALUE'];
                                $arImgFeed = getImgFeed($ofArticle, $imageConfig, $usedProtocol.$ar_iblock['SERVER_NAME']);
                                $strFile = $arImgFeed['FIRST'];
                                $arAdditionalImg = $arImgFeed['ADDITIONAL'];
                            } elseif ($ofProperty['CODE'] == 'NAIMENOVANIE_MARKETING' && !empty($ofProperty['VALUE'])) {
                                $arOfferItemName = $ofProperty['VALUE'];
                            }
                        }

                        if (empty($strFile) && !empty($arItem['YANDEX_PICT'])) {
                            $strFile = $arItem['YANDEX_PICT'];
                        }
                        if (!empty($strFile)) {
                            $arOfferItem = $arCurrentOffer;
                            $arOfferItem['~NAME'] = $arOfferItemName;
                            $fullPrice = $arCurrentPrice['FULL_PRICE'];
                            $minPrice = $arCurrentPrice['MIN_PRICE'];
                            $minPriceCurrency = $arCurrentPrice['MIN_PRICE_CURRENCY'];
                            $minPriceRUR = $arCurrentPrice['MIN_PRICE_RUR'];
                            $minPriceGroup = $arCurrentPrice['MIN_PRICE_GROUP'];

                            $arOfferItem['YANDEX_AVAILABLE'] = ($arOfferItem['CATALOG_AVAILABLE'] == 'Y' ? 'true' : 'false');


                            if (strlen($arItem['~CANONICAL_PAGE_URL']) > 0) {
                                $arOfferItem['~DETAIL_PAGE_URL'] = $arItem['~CANONICAL_PAGE_URL'];
                                $mobileDetailUrl = $arItem['~CANONICAL_PAGE_URL'];
                                //$mobileDetailUrl = str_replace('https', 'http', $mobileDetailUrl);
                                $mobileDetailUrl = str_replace($ar_iblock['SERVER_NAME'], $mobileUrl, $mobileDetailUrl);
                                $arOfferItem['~MOBILE_DETAIL_PAGE_URL'] = $mobileDetailUrl;
                            } elseif (strlen($arOfferItem['~DETAIL_PAGE_URL']) <= 0) {
                                $arOfferItem['~DETAIL_PAGE_URL'] = $usedProtocol.$ar_iblock['SERVER_NAME'].'/';
                                $arOfferItem['~MOBILE_DETAIL_PAGE_URL'] = $usedProtocol.$mobileUrl.'/';
                            } else {
                                $arOfferItem['~DETAIL_PAGE_URL'] = $usedProtocol.$ar_iblock['SERVER_NAME'].str_replace(' ', '%20', $arOfferItem['~DETAIL_PAGE_URL']);
                                $arOfferItem['~MOBILE_DETAIL_PAGE_URL'] = $usedProtocol.$mobileUrl.str_replace(' ', '%20', $arOfferItem['~DETAIL_PAGE_URL']);;
                            }

                            $arOfferItem['YANDEX_TYPE'] = $productFormat;

                            $strOfferYandex = '';
                            $strOfferYandex .= '<item>'."\n";
                            $strOfferYandex .= "<g:id>" . $ofArticle . "</g:id>\n";
                            $strOfferYandex .= "<g:title>" . yandex_text2xml($arItem["~NAME"], true) . "</g:title>\n";
                            $strOfferYandex .= "<g:description>";
                            $text = '';
                            if (strlen($arOfferItem['~DETAIL_TEXT']) <= 0)
                            {
                                $text = $arItem['YANDEX_DESCR'];
                            }
                            else
                            {
                                $text = yandex_text2xml(TruncateText(
                                    ($arOfferItem["DETAIL_TEXT_TYPE"] == "html" ?
                                        strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arOfferItem["~DETAIL_TEXT"])) : $arOfferItem["~DETAIL_TEXT"]),
                                    175),
                                    true);
                            }
                            if (empty($text)) {
                                $text = Loc::getMessage('YANDEX_CUSTOM_DESCRIPTION');
                            }

                            $strOfferYandex .= $text;
                            $strOfferYandex .= "</g:description>\n";

                            $referer = '';
                            if (!$disableReferers)
                                $referer = (strpos($arOfferItem['~DETAIL_PAGE_URL'], '?') === false ? '?' : '&amp;').'r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?>';

                            $strOfferYandex .= "<g:link>".htmlspecialcharsbx($arOfferItem["~DETAIL_PAGE_URL"]).$referer."</g:link>\n";
                            $strOfferYandex .= "<g:mobile_link>".htmlspecialcharsbx($arOfferItem["~MOBILE_DETAIL_PAGE_URL"]).$referer."</g:mobile_link>\n";

                            $strOfferYandex .= "<g:availability>" . Loc::getMessage('YANDEX_IN_STOCK') . "</g:availability>"."\n";

                            if ($minPrice < $fullPrice) {
                                $strOfferYandex .= "<g:price>".$fullPrice." ".$minPriceCurrency."</g:price>\n";
                                $strOfferYandex .= "<g:sale_price>".$minPrice." ".$minPriceCurrency."</g:sale_price>\n";
                            } else {
                                $strOfferYandex .= "<g:price>".$minPrice." ".$minPriceCurrency."</g:price>\n";
                            }

                            $strOfferYandex .= $arItem['YANDEX_CATEGORY'];

                            if (!empty($strFile))
                            {
                                $strOfferYandex .= "<g:image_link>".$strFile."</g:image_link>\n";
                            }
                            if (!empty($arAdditionalImg)) {
                                foreach ($arAdditionalImg as $img) {
                                    $strOfferYandex .= "<g:additional_image_link>".$img."</g:additional_image_link>\n";
                                }
                            }
                            $strOfferYandex .= "<g:brand>" . Loc::getMessage('YANDEX_VENDOR_PAOLO_CONTE') . "</g:brand>\n";
                            $strOfferYandex .= "<g:condition>" . Loc::getMessage('YANDEX_NEW') . "</g:condition>\n";
                            $strOfferYandex .= "<g:adult>" . Loc::getMessage('YANDEX_NO') . "</g:adult>\n";
                            $strOfferYandex .= "<g:age_group>" . Loc::getMessage('YANDEX_ADULT') . "</g:age_group>\n";
                            $strOfferYandex .= "<g:mpn>" . $ofArticle . "</g:mpn>\n";
                            $strOfferYandex .= "<g:google_product_category >" ."187" . "</g:google_product_category >\n";

                            $y = 0;
                            foreach ($arYandexFields as $key)
                            {
                                switch ($key)
                                {
                                    case 'param':
                                        if ($parametricFieldsExist)
                                        {
                                            foreach ($parametricFields as $paramKey => $prop_id)
                                            {
                                                $strParamValue = '';
                                                if ($prop_id)
                                                {
                                                    $strParamValue = yandex_get_value($arOfferItem, 'PARAM_'.$paramKey, $prop_id, $arProperties, $arUserTypeFormat, $usedProtocol);
                                                }
                                                if ('' != $strParamValue)
                                                    $strOfferYandex .= $strParamValue."\n";
                                            }
                                            unset($paramKey, $prop_id);
                                        }
                                        break;
                                    default:
                                        if ($fieldsExist && isset($fields[$key]))
                                        {
                                            $strValue = '';
                                            $strValue = yandex_get_value($arOfferItem, $key, $fields[$key], $arProperties, $arUserTypeFormat, $usedProtocol);
                                            if ('' != $strValue)
                                                $strOfferYandex .= $strValue."\n";
                                        }
                                }
                            }

                            $strOfferYandex .= "</item>\n";
                            $arItem['OFFERS'][] = $strOfferYandex;
                            $boolItemOffers = true;
                            $boolItemExport = true;
                        }
                    }
                }
                else
                {
                    while ($obOfferItem = $rsOfferItems->GetNextElement())
                    {
                        $arOfferItem = $obOfferItem->GetFields();
                        $arCross = (!empty($arItem['PROPERTIES']) ? $arItem['PROPERTIES'] : array());
                        $arOfferItem['PROPERTIES'] = $obOfferItem->GetProperties();
                        if (!empty($arOfferItem['PROPERTIES']))
                        {
                            foreach ($arOfferItem['PROPERTIES'] as $arProp)
                            {
                                $arCross[$arProp['ID']] = $arProp;
                            }
                        }
                        $arOfferItem['PROPERTIES'] = $arCross;

                        $arOfferItem['YANDEX_AVAILABLE'] = ($arOfferItem['CATALOG_AVAILABLE'] == 'Y' ? 'true' : 'false');

                        $fullPrice = 0;
                        $minPrice = 0;

                        $minPriceCurrency = '';

                        if ($XML_DATA['PRICE'] > 0)
                        {
                            $rsPrices = CPrice::GetListEx(array(), array(
                                    'PRODUCT_ID' => $arOfferItem['ID'],
                                    'CATALOG_GROUP_ID' => $XML_DATA['PRICE'],
                                    'CAN_BUY' => 'Y',
                                    'GROUP_GROUP_ID' => array(2),
                                    '+<=QUANTITY_FROM' => 1,
                                    '+>=QUANTITY_TO' => 1,
                                )
                            );
                            if ($arPrice = $rsPrices->Fetch())
                            {
                                if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                                    $arOfferItem['ID'],
                                    1,
                                    array(2),
                                    'N',
                                    array($arPrice),
                                    $arOfferIBlock['LID'],
                                    array()
                                )
                                )
                                {
                                    $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                                    $fullPrice = $arOptimalPrice['RESULT_PRICE']['BASE_PRICE'];
                                    $minPriceCurrency = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
                                    if ($minPriceCurrency == $RUR)
                                        $minPriceRUR = $minPrice;
                                    else
                                        $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                                    $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
                                }

                            }
                        }
                        else
                        {
                            if ($arPrice = CCatalogProduct::GetOptimalPrice(
                                $arOfferItem['ID'],
                                1,
                                array(2), // anonymous
                                'N',
                                array(),
                                $arOfferIBlock['LID'],
                                array()
                            )
                            )
                            {
                                $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                                $fullPrice = $arPrice['RESULT_PRICE']['BASE_PRICE'];
                                $minPriceCurrency = $arPrice['RESULT_PRICE']['CURRENCY'];
                                if ($minPriceCurrency == $RUR)
                                    $minPriceRUR = $minPrice;
                                else
                                    $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                                $minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
                            }
                        }
                        if ($minPrice <= 0)
                            continue;

                        $strFile = '';
                        $ofArticle = '';
                        $arOfferItem['~NAME'] = $arItem['~NAME'];
                        foreach ($arOfferItem['PROPERTIES'] as $ofProperty) {
                            if ($ofProperty['CODE'] == 'CML2_ARTICLE') {
                                $ofArticle = $ofProperty['VALUE'];
                                $arImgFeed = getImgFeed($ofArticle, $imageConfig, $usedProtocol.$ar_iblock['SERVER_NAME']);
                                $strFile = $arImgFeed['FIRST'];
                                $arAdditionalImg = $arImgFeed['ADDITIONAL'];
                            } elseif ($ofProperty['CODE'] == 'NAIMENOVANIE_MARKETING' && !empty($ofProperty['VALUE'])) {
                                $arOfferItem['~NAME'] = $ofProperty['VALUE'];
                            }
                        }
                        if (empty($strFile) && !empty($arItem['YANDEX_PICT'])) {
                            $strFile = $arItem['YANDEX_PICT'];
                        }

                        if (!empty($strFile)) {
                            if (strlen($arItem['~CANONICAL_PAGE_URL']) > 0)
                                $arOfferItem['~DETAIL_PAGE_URL'] = $arItem['~CANONICAL_PAGE_URL'];
                            elseif (strlen($arOfferItem['~DETAIL_PAGE_URL']) <= 0)
                                $arOfferItem['~DETAIL_PAGE_URL'] = $usedProtocol . $ar_iblock['SERVER_NAME'] . '/';
                            else
                                $arOfferItem['~DETAIL_PAGE_URL'] = $usedProtocol . $ar_iblock['SERVER_NAME'] . str_replace(' ', '%20', $arOfferItem['~DETAIL_PAGE_URL']);

                            $arOfferItem['YANDEX_TYPE'] = $productFormat;

                            $strOfferYandex = '';
                            $strOfferYandex .= '<offer id="' . $arOfferItem["ID"] . '"' . $productFormat . ' available="' . $arOfferItem['YANDEX_AVAILABLE'] . '">' . "\n";
                            $referer = '';
                            if (!$disableReferers)
                                $referer = (strpos($arOfferItem['~DETAIL_PAGE_URL'], '?') === false ? '?' : '&amp;') . 'r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?>';
                            $strOfferYandex .= "<url>" . htmlspecialcharsbx($arOfferItem["~DETAIL_PAGE_URL"]) . $referer . "</url>\n";

                            $strOfferYandex .= "<price>" . $minPrice . "</price>\n";
                            if ($minPrice < $fullPrice)
                                $strOfferYandex .= "<oldprice>" . $fullPrice . "</oldprice>\n";
                            $strOfferYandex .= "<currencyId>" . $minPriceCurrency . "</currencyId>\n";

                            $strOfferYandex .= $arItem['YANDEX_CATEGORY'];

                            $strOfferYandex .= "<picture>" . (!empty($strFile) ? $strFile : $arItem['YANDEX_PICT']) . "</picture>\n";
                            $strOfferYandex .= "<delivery>true</delivery>\n";
                            $strOfferYandex .= "<manufacturer_warranty>true</manufacturer_warranty>\n";

                            $y = 0;
                            foreach ($arYandexFields as $key) {
                                switch ($key) {
                                    case 'name':
                                        if ($yandexFormat == 'artist.title')
                                            continue;

                                        $strOfferYandex .= "<name>" . yandex_text2xml($arItem["NAME"], true) . "</name>\n";
                                        break;
                                    case 'description':
                                        $strOfferYandex .= "<description>";
                                        if (strlen($arOfferItem['~DETAIL_TEXT']) <= 0) {
                                            $text = $arItem['YANDEX_DESCR'];
                                        } else {
                                            $text = yandex_text2xml(TruncateText(($arOfferItem["DETAIL_TEXT_TYPE"] == "html" ? strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arOfferItem["~DETAIL_TEXT"])) : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arOfferItem["~DETAIL_TEXT"])), 175), true);
                                        }
                                        if (empty($text))
                                            $text = Loc::getMessage('YANDEX_CUSTOM_DESCRIPTION');
                                        $strOfferYandex .= $text;
                                        $strOfferYandex .= "</description>\n";
                                        break;
                                    case 'vendor':
                                        $strOfferYandex .= "<vendor>" . Loc::getMessage('YANDEX_VENDOR_PAOLO_CONTE') . "</vendor>\n";
                                        break;
                                    case 'vendorCode':
                                        $strOfferYandex .= "<vendorCode>" . $ofArticle . "</vendorCode>\n";
                                        break;
                                    case 'param':
                                        if ($parametricFieldsExist) {
                                            foreach ($parametricFields as $paramKey => $prop_id) {
                                                $strParamValue = '';
                                                if ($prop_id) {
                                                    $strParamValue = yandex_get_value($arOfferItem, 'PARAM_' . $paramKey, $prop_id, $arProperties, $arUserTypeFormat, $usedProtocol);
                                                }
                                                if ('' != $strParamValue)
                                                    $strOfferYandex .= $strParamValue . "\n";
                                            }
                                            unset($paramKey, $prop_id);
                                        }
                                        break;
                                    case 'model':
                                    case 'title':
                                        if (!$fieldsExist || !isset($fields[$key])) {
                                            if ($key == 'model' && $yandexFormat == 'vendor.model' || $key == 'title' && $yandexFormat == 'artist.title')
                                                $strOfferYandex .= "<" . $key . ">" . yandex_text2xml($arOfferItem["~NAME"], true) . "</" . $key . ">\n";
                                        } else {
                                            $strValue = '';
                                            $strValue = yandex_get_value($arOfferItem, $key, $fields[$key], $arProperties, $arUserTypeFormat, $usedProtocol);
                                            if ('' != $strValue)
                                                $strOfferYandex .= $strValue . "\n";
                                        }
                                        break;
                                    case 'year':
                                    default:
                                        if ($key == 'year') {
                                            $y++;
                                            if ($yandexFormat == 'artist.title') {
                                                if ($y == 1)
                                                    continue;
                                            } else {
                                                if ($y > 1)
                                                    continue;
                                            }
                                        }
                                        if ($fieldsExist && isset($fields[$key])) {
                                            $strValue = '';
                                            $strValue = yandex_get_value($arOfferItem, $key, $fields[$key], $arProperties, $arUserTypeFormat, $usedProtocol);
                                            if ('' != $strValue)
                                                $strOfferYandex .= $strValue . "\n";
                                        }
                                }
                            }

                            $strOfferYandex .= "</offer>\n";
                            $arItem['OFFERS'][] = $strOfferYandex;
                            $boolItemOffers = true;
                            $boolItemExport = true;
                        }
                    }
                }
            }
            elseif ($arCatalog['CATALOG_TYPE'] == CCatalogSku::TYPE_FULL && $arItem['CATALOG_TYPE'] == Catalog\ProductTable::TYPE_PRODUCT) {
                $str_AVAILABLE = ' available="' . ($arItem['CATALOG_AVAILABLE'] == 'Y' ? 'true' : 'false') . '"';

                $fullPrice = 0;
                $minPrice = 0;
                $minPriceRUR = 0;
                $minPriceGroup = 0;
                $minPriceCurrency = "";

                if ($XML_DATA['PRICE'] > 0) {
                    $rsPrices = CPrice::GetListEx(array(), array('PRODUCT_ID' => $arItem['ID'], 'CATALOG_GROUP_ID' => $XML_DATA['PRICE'], 'CAN_BUY' => 'Y', 'GROUP_GROUP_ID' => array(2), '+<=QUANTITY_FROM' => 1, '+>=QUANTITY_TO' => 1,));
                    if ($arPrice = $rsPrices->Fetch()) {
                        if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice($arItem['ID'], 1, array(2), 'N', array($arPrice), $ar_iblock['LID'], array())) {
                            $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                            $fullPrice = $arOptimalPrice['RESULT_PRICE']['BASE_PRICE'];
                            $minPriceCurrency = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];
                            if ($minPriceCurrency == $RUR)
                                $minPriceRUR = $minPrice; else
                                $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                            $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
                        }
                    }
                } else {
                    if ($arPrice = CCatalogProduct::GetOptimalPrice($arItem['ID'], 1, array(2), // anonymous
                        'N', array(), $ar_iblock['LID'], array())
                    ) {
                        $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                        $fullPrice = $arPrice['RESULT_PRICE']['BASE_PRICE'];
                        $minPriceCurrency = $arPrice['RESULT_PRICE']['CURRENCY'];
                        if ($minPriceCurrency == $RUR)
                            $minPriceRUR = $minPrice; else
                            $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $minPriceCurrency, $RUR);
                        $minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
                    }
                }

                if ($minPrice <= 0)
                    continue;

                if (strlen($arItem['~CANONICAL_PAGE_URL']) > 0) {
                    $arItem['~DETAIL_PAGE_URL'] = $arItem['~CANONICAL_PAGE_URL'];
                    $mobileDetailUrl = $arItem['~CANONICAL_PAGE_URL'];
                    //$mobileDetailUrl = str_replace('https', 'http', $mobileDetailUrl);
                    $mobileDetailUrl = str_replace($ar_iblock['SERVER_NAME'], $mobileUrl, $mobileDetailUrl);
                    $arItem['~MOBILE_DETAIL_PAGE_URL'] = $mobileDetailUrl;
                } elseif (strlen($arItem['~DETAIL_PAGE_URL']) <= 0) {
                    $arItem['~DETAIL_PAGE_URL'] = $usedProtocol.$ar_iblock['SERVER_NAME'].'/';
                    $arItem['~MOBILE_DETAIL_PAGE_URL'] = $usedProtocol.$mobileUrl.'/';
                } else {
                    $arItem['~DETAIL_PAGE_URL'] = $usedProtocol.$ar_iblock['SERVER_NAME'].str_replace(' ', '%20', $arItem['~DETAIL_PAGE_URL']);
                    $arItem['~MOBILE_DETAIL_PAGE_URL'] = $usedProtocol.$mobileUrl.str_replace(' ', '%20', $arItem['~DETAIL_PAGE_URL']);;
                }

                if (!empty($arItem['YANDEX_PICT'])) {
                    $strOfferYandex = '';
                    $strOfferYandex .= '<item>'."\n";
                    $strOfferYandex .= "<g:id>" . $itemArticle . "</g:id>\n";
                    $strOfferYandex .= "<g:title>" . yandex_text2xml($arItem["~NAME"], true) . "</g:title>\n";

                    $text = yandex_text2xml(TruncateText(($arItem["DETAIL_TEXT_TYPE"] == "html" ? strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arItem["~DETAIL_TEXT"])) : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arItem["~DETAIL_TEXT"])), 175), true);
                    if (empty($text))
                        $text = Loc::getMessage('YANDEX_CUSTOM_DESCRIPTION');
                    $strOfferYandex .= "<g:description>" . $text . "</g:description>\n";

                    $referer = '';
                    if (!$disableReferers)
                        $referer = (strpos($arItem['~DETAIL_PAGE_URL'], '?') === false ? '?' : '&amp;') . 'r1=<?=$strReferer1; ?>&amp;r2=<?=$strReferer2; ?>';

                    $strOfferYandex .= "<g:link>".htmlspecialcharsbx($arItem["~DETAIL_PAGE_URL"]).$referer."</g:link>\n";
                    $strOfferYandex .= "<g:mobile_link>".htmlspecialcharsbx($arItem["~MOBILE_DETAIL_PAGE_URL"]).$referer."</g:mobile_link>\n";

                    $strOfferYandex .= "<g:availability>" . Loc::getMessage('YANDEX_IN_STOCK') . "</g:availability>"."\n";

                    if ($minPrice < $fullPrice) {
                        $strOfferYandex .= "<g:price>".$fullPrice." ".$minPriceCurrency."</g:price>\n";
                        $strOfferYandex .= "<g:sale_price>".$minPrice." ".$minPriceCurrency."</g:sale_price>\n";
                    } else {
                        $strOfferYandex .= "<g:price>".$minPrice." ".$minPriceCurrency."</g:price>\n";
                    }

                    if (!empty($strFile))
                    {
                        $strOfferYandex .= "<g:image_link>".$strFile."</g:image_link>\n";
                    }
                    if (!empty($arAdditionalImg)) {
                        foreach ($arAdditionalImg as $img) {
                            $strOfferYandex .= "<g:additional_image_link>".$img."</g:additional_image_link>\n";
                        }
                    }

                    $strOfferYandex .= "<g:brand>" . Loc::getMessage('YANDEX_VENDOR_PAOLO_CONTE') . "</g:brand>\n";
                    $strOfferYandex .= "<g:condition>" . Loc::getMessage('YANDEX_NEW') . "</g:condition>\n";
                    $strOfferYandex .= "<g:adult>" . Loc::getMessage('YANDEX_NO') . "</g:adult>\n";
                    $strOfferYandex .= "<g:age_group>" . Loc::getMessage('YANDEX_ADULT') . "</g:age_group>\n";
                    $strOfferYandex .= "<g:mpn>" . $itemArticle . "</g:mpn>\n";
                    $strOfferYandex .= "<g:google_product_category >" ."187" . "</g:google_product_category >\n";

                    $strOfferYandex .= $arItem['YANDEX_CATEGORY'];

                    $y = 0;
                    foreach ($arYandexFields as $key) {
                        $strValue = '';
                        switch ($key) {
                            case 'param':
                                if ($parametricFieldsExist) {
                                    foreach ($parametricFields as $paramKey => $prop_id) {
                                        $strParamValue = '';
                                        if ($prop_id) {
                                            $strParamValue = yandex_get_value($arItem, 'PARAM_' . $paramKey, $prop_id, $arProperties, $arUserTypeFormat, $usedProtocol);
                                        }
                                        if ('' != $strParamValue)
                                            $strValue .= $strParamValue . "\n";
                                    }
                                    unset($paramKey, $prop_id);
                                }
                                break;
                        }
                        if ('' != $strValue)
                            $strOfferYandex .= $strValue;
                    }

                    $strOfferYandex .= "</item>\n";
                }
                if ('' != $strOfferYandex)
                {
                    $arItem['OFFERS'][] = $strOfferYandex;
                    unset($strOfferYandex);
                    $boolItemOffers = true;
                    $boolItemExport = true;
                }
            }
            if (100 <= $cnt)
            {
                $cnt = 0;
                CCatalogDiscount::ClearDiscountCache(array(
                    'PRODUCT' => true,
                    'SECTIONS' => true,
                    'PROPERTIES' => true
                ));
            }
            if (!$boolItemExport)
                continue;
            foreach ($arItem['OFFERS'] as $strOfferItem)
            {
                $strTmpOff .= $strOfferItem;
            }
        }
    }

    //    fwrite($fp, "<categories>\n");
    //    if ($boolNeedRootSection)
    //    {
    //        $strTmpCat .= "<category id=\"".$intMaxSectionID."\">".yandex_text2xml(GetMessage('YANDEX_ROOT_DIRECTORY'), true)."</category>\n";
    //    }
    //    fwrite($fp, $strTmpCat);
    //    fwrite($fp, "</categories>\n");

    fwrite($fp, $strTmpOff);

    fwrite($fp, "</channel>\n");
    fwrite($fp, "</rss>\n");

    fclose($fp);
}

CCatalogDiscountSave::Enable();

if (!empty($arRunErrors))
    $strExportErrorMessage = implode('<br />',$arRunErrors);

if ($bTmpUserCreated)
{
    unset($USER);
    if (isset($USER_TMP))
    {
        $USER = $USER_TMP;
        unset($USER_TMP);
    }
}