<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$bUSER_HAVE_ACCESS, $arNavigation, $pagerParameters)))
{
    if(!CModule::IncludeModule("iblock"))
    {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }

    $arFilter = array(
        "IBLOCK_LID" => SITE_ID,
        "IBLOCK_ACTIVE" => "Y",
        "ACTIVE" => "Y",
        "CHECK_PERMISSIONS" => "Y",
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    );

    if($arParams["ELEMENT_ID"] <= 0)
        $arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
            $arParams["ELEMENT_ID"],
            $arParams["~ELEMENT_CODE"],
            false,
            false,
            $arFilter
        );

    $arSelect = array_merge($arParams["FIELD_CODE"], array(
        "ID",
        "NAME",
        "IBLOCK_ID",
        "IBLOCK_SECTION_ID",
        "DETAIL_TEXT",
        "DETAIL_TEXT_TYPE",
        "PREVIEW_TEXT",
        "PREVIEW_TEXT_TYPE",
        "DETAIL_PICTURE",
        "TIMESTAMP_X",
        "ACTIVE_FROM",
        "LIST_PAGE_URL",
        "DETAIL_PAGE_URL",
    ));

    $bGetProperty = count($arParams["PROPERTY_CODE"]) > 0
        || $arParams["BROWSER_TITLE"] != "-"
        || $arParams["META_KEYWORDS"] != "-"
        || $arParams["META_DESCRIPTION"] != "-";
    if($bGetProperty)
        $arSelect[]="PROPERTY_*";
    if ($arParams['SET_CANONICAL_URL'] === 'Y')
        $arSelect[] = 'CANONICAL_PAGE_URL';

    $arFilter = Array("IBLOCK_ID"=>IBLOCK_AS_SLIDER, "IBLOCK_SECTION_ID "=>$arParams['SECTION_ID'], "ACTIVE"=>"Y");

    $rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

    if($obElement = $rsElement->GetNextElement())
    {
        $arResult = $obElement->GetFields();

        $arResult["NAV_RESULT"] = new CDBResult;
        if(($arResult["DETAIL_TEXT_TYPE"]=="html") && (strstr($arResult["DETAIL_TEXT"], "<BREAK />")!==false))
            $arPages=explode("<BREAK />", $arResult["DETAIL_TEXT"]);
        elseif(($arResult["DETAIL_TEXT_TYPE"]!="html") && (strstr($arResult["DETAIL_TEXT"], "&lt;BREAK /&gt;")!==false))
            $arPages=explode("&lt;BREAK /&gt;", $arResult["DETAIL_TEXT"]);
        else
            $arPages=array();
        $arResult["NAV_RESULT"]->InitFromArray($arPages);
        $arResult["NAV_RESULT"]->NavStart($arNavParams);
        if(count($arPages)==0)
        {
            $arResult["NAV_RESULT"] = false;
        }
        else
        {
            $navComponentParameters = array();
            if ($arParams["PAGER_BASE_LINK_ENABLE"] === "Y")
            {
                $pagerBaseLink = trim($arParams["PAGER_BASE_LINK"]);
                if ($pagerBaseLink === "")
                    $pagerBaseLink = $arResult["DETAIL_PAGE_URL"];

                if ($pagerParameters && isset($pagerParameters["BASE_LINK"]))
                {
                    $pagerBaseLink = $pagerParameters["BASE_LINK"];
                    unset($pagerParameters["BASE_LINK"]);
                }

                $navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, array("encode"=>true));
            }

            $arResult["NAV_STRING"] = $arResult["NAV_RESULT"]->GetPageNavStringEx(
                $navComponentObject,
                $arParams["PAGER_TITLE"],
                $arParams["PAGER_TEMPLATE"],
                $arParams["PAGER_SHOW_ALWAYS"],
                $this,
                $navComponentParameters
            );
            $arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();

            $arResult["NAV_TEXT"] = "";
            while($ar = $arResult["NAV_RESULT"]->Fetch())
                $arResult["NAV_TEXT"].=$ar;
        }

        if(strlen($arResult["ACTIVE_FROM"])>0)
            $arResult["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["ACTIVE_FROM"], CSite::GetDateFormat()));
        else
            $arResult["DISPLAY_ACTIVE_FROM"] = "";

        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(IBLOCK_AS_SLIDER, $arResult["ID"]);
        $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();

        $arResult["FIELDS"] = array();

        foreach($arParams["FIELD_CODE"] as $code)
            if(array_key_exists($code, $arResult))
                $arResult["FIELDS"][$code] = $arResult[$code];

        if($bGetProperty)
            $arResult["PROPERTIES"] = $obElement->GetProperties();
        $arResult["DISPLAY_PROPERTIES"]=array();
        $arResult["IBLOCK"] = GetIBlock(IBLOCK_AS_SLIDER, $arResult["IBLOCK_TYPE"]);
        $arResult["SECTION"] = array("PATH" => array());
        $arResult["SECTION_URL"] = "";

        $arResult['SLIDE'] = array();
        $sliderArOrder = Array("SORT"=>"ASC");

        $sliderArSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");
        $sliderArFilter = Array("IBLOCK_ID"=>IBLOCK_AS_SLIDER, "SECTION_ID"=>$arParams['IBLOCK_SECTION'], "ACTIVE"=>"Y");

        $res = CIBlockElement::GetList($sliderArOrder, $sliderArFilter, false, Array(), $sliderArSelect);
        while($ob = $res->GetNextElement())
        {
            $item = $ob->GetProperties();
            $rsFile = CFile::GetPath($item["SLIDE_IMAGE"]["VALUE"]);
            $arResult['SLIDE'][] = array("TEXT" => $item["SLIDE_TEXT"]["VALUE"]["TEXT"],"TYPE" => $item["SLIDE_TEXT"]["VALUE"]["TYPE"],"IMAGE"=>$rsFile);
        }

        $this->SetResultCacheKeys(array(
            "ID",
            "IBLOCK_ID",
            "NAV_CACHED_DATA",
            "NAME",
            "IBLOCK_SECTION_ID",
            "IBLOCK",
            "LIST_PAGE_URL", "~LIST_PAGE_URL",
            "SECTION_URL",
            "CANONICAL_PAGE_URL",
            "SECTION",
            "PROPERTIES",
            "IPROPERTY_VALUES",
            "TIMESTAMP_X",
        ));

        $this->IncludeComponentTemplate();
    }
    else
    {
        $this->AbortResultCache();
        \Bitrix\Iblock\Component\Tools::process404(
            trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_DETAIL_NF")
            ,true
            ,$arParams["SET_STATUS_404"] === "Y"
            ,$arParams["SHOW_404"] === "Y"
            ,$arParams["FILE_404"]
        );
    }
}


if(isset($arResult["ID"]))
{
    $arTitleOptions = null;
    if(CModule::IncludeModule("iblock"))
    {
        CIBlockElement::CounterInc($arResult["ID"]);

        if($USER->IsAuthorized())
        {
            if(
                $APPLICATION->GetShowIncludeAreas()
                || $arParams["SET_TITLE"]
                || isset($arResult[$arParams["BROWSER_TITLE"]])
            )
            {
                $arReturnUrl = array(
                    "add_element" => CIBlock::GetArrayByID(IBLOCK_AS_SLIDER, "DETAIL_PAGE_URL"),
                    "delete_element" => (
                    empty($arResult["SECTION_URL"])?
                        $arResult["LIST_PAGE_URL"]:
                        $arResult["SECTION_URL"]
                    ),
                );

                $arButtons = CIBlock::GetPanelButtons(
                    IBLOCK_AS_SLIDER,
                    $arResult["ID"],
                    $arResult["IBLOCK_SECTION_ID"],
                    Array(
                        "RETURN_URL" => $arReturnUrl,
                        "SECTION_BUTTONS" => false,
                    )
                );

                if($APPLICATION->GetShowIncludeAreas())
                    $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

                if($arParams["SET_TITLE"] || isset($arResult[$arParams["BROWSER_TITLE"]]))
                {
                    $arTitleOptions = array(
                        'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_element"]["ACTION"],
                        'PUBLIC_EDIT_LINK' => $arButtons["edit"]["edit_element"]["ACTION"],
                        'COMPONENT_NAME' => $this->GetName(),
                    );
                }
            }
        }
    }

    $this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

    return $arResult["ID"];
}
else
{
    return 0;
}
?>