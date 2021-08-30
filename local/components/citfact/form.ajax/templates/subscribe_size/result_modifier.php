<?php
/*ДРУГИЕ ЦВЕТА*/
$otherColors = array();
$arXmlIds = array();
$colorGroup = $arParams['COLOR_ID'];
if(!empty($colorGroup)) {
    $imageConfig = array('TYPE' => 'ONE', 'SIZE' => array('SMALL' => array('W' => 45, 'H' => 45)));
    $arFilter = array("IBLOCK_ID" => 10, "PROPERTY_GRUPPIROVKA_PO_MODELYAM_SAYT_" => $colorGroup, "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false,
        array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_CML2_ARTICLE", "PROPERTY_TSVET_DLYA_FILTRA")
    );
    while ($ob = $res->GetNext(false, false)) {
        $articul = trim($ob['PROPERTY_CML2_ARTICLE_VALUE']);
        $ob['IMAGES'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
        $otherColors[ $ob['ID'] ] = $ob;

        $arXmlIds[] = $ob['PROPERTY_TSVET_DLYA_FILTRA_VALUE'];
    }
}

$hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById(4)->fetch();
$hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
$hlDataClass = $hlentity->getDataClass();
$arFilter = array('UF_XML_ID' => $arXmlIds);
$res = $hlDataClass::getList(array('order'=>array('ID'=>'ASC'), 'filter' => $arFilter));
$arFiles = array();
while($arRes = $res->fetch())
{
    $arFiles[$arRes['UF_XML_ID']]= array(
        'FILE_PATH' => \CFile::GetPath($arRes['UF_FILE']),
        'NAME' => $arRes['UF_NAME']
    );
}

foreach ($otherColors as $key => &$arColor){
    $arColor['COLOR'] = $arFiles[$arColor['PROPERTY_TSVET_DLYA_FILTRA_VALUE']];
}
unset($arColor);

$arResult['OTHER_COLORS'] = $otherColors;

?>