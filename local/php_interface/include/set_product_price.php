<?
/*ОБРАБОТЧИК ВЫГРУЗКИ 1С, УСТАНОВКА ЦЕН В ТОВАР ИЗ ТП*/
/////////////////////////////////////////////////////////////////////////////////////////
//CModule::IncludeModule("catalog");
//CModule::IncludeModule("iblock");

/*
	define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
	ob_start();
	print_r($arg2);
	$String = ob_get_contents();
	ob_end_clean();
	AddMessage2Log($String, "my");
*/


//////////////////////////////////////////////////////////////////////////////////////////
AddEventHandler('catalog', 'OnPriceUpdate', "OnPriceUpdateHandler");
function OnPriceUpdateHandler($id, $arFields){
    if(is_array($arFields) && !empty($arFields)){
        my_change_price($arFields);
    }
}
AddEventHandler('catalog', 'OnPriceAdd', "OnPriceAddHandler");
function OnPriceAddHandler($id, $arFields){
    if(is_array($arFields) && !empty($arFields)){
        my_change_price($arFields);
    }
}

/*AddEventHandler('catalog', 'OnPriceUpdate', "OnPriceUpdateHandler");
function OnPriceUpdateHandler($id, $arFields){
	define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log_after_update.txt");
	AddMessage2Log(print_r($arFields, TRUE));
	AddMessage2Log(print_r($id, TRUE));
}*/



function my_change_price($arFields) {

    CModule::IncludeModule("iblock");
	CModule::IncludeModule("catalog");

    $id_element_arfields = $arFields["PRODUCT_ID"];
    $price_new = $arFields["PRICE"];
    $group_id = $arFields["CATALOG_GROUP_ID"];

	if(!empty($id_element_arfields) /*&& !empty($price_new)*/){
        $arSelect = Array(
            "ID",
            "PROPERTY_CML2_LINK",
        );
        $arFilter = Array(
            "ID" => $id_element_arfields,
			"!PROPERTY_CML2_LINK" => false,
		);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        if($ob = $res->GetNext())
        {
            $id_parent_prod = $ob["PROPERTY_CML2_LINK_VALUE"];
            $arFields['id_parent_prod'] = $id_parent_prod;
            if (!Fact\UpdateStorage::isUpdated($arFields)){
                if(!empty($id_parent_prod)){
                    //define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log_prices.txt");

                    // Каждый раз очищаем все цены, кроме заполненных у торгового предложения
                    // Ищем заполненные цены в торговом предложении
                    $dbProductPrice = CPrice::GetListEx(
                        array(),
                        array("PRODUCT_ID" => $ob['ID'], '!PRICE' => false),
                        false,
                        false,
                        array("ID", "CATALOG_GROUP_ID", 'PRICE')
                    );
                    $arIdsCatalogGroups = array();
                    while($resPrice = $dbProductPrice->Fetch()){
                        $arIdsCatalogGroups[] = $resPrice['CATALOG_GROUP_ID'];

                        //AddMessage2Log(print_r($resPrice, TRUE));
                    }

                    // Узнаем id ценовых предложений у родительского элемента
                    $dbProductPrice = CPrice::GetListEx(
                        array(),
                        array("PRODUCT_ID" => $id_parent_prod, 'CATALOG_GROUP_ID' => $arIdsCatalogGroups),
                        false,
                        false,
                        array("ID", "CATALOG_GROUP_ID", 'PRICE')
                    );
                    $arIdsPrices = array();
                    while($resPrice = $dbProductPrice->Fetch()){
                        $arIdsPrices[] = $resPrice['ID'];

                        //AddMessage2Log(print_r($resPrice, TRUE));
                    }

                    CPrice::DeleteByProduct($id_parent_prod,$arIdsPrices);


                    // Обновляем цену
                    $arFieldsUpdate = Array(
                        "PRODUCT_ID" => $id_parent_prod,
                        "CATALOG_GROUP_ID" => $group_id,
                        "PRICE" => $price_new,
                        "CURRENCY" => "RUB",
                    );

                    // Проверяем существующие цены товара
                    $res = CPrice::GetList(
                        array(),
                        array(
                            "PRODUCT_ID" => $id_parent_prod,
                            "CATALOG_GROUP_ID" => $group_id
                        )
                    );

                    if ($arr = $res->Fetch()){
                        CPrice::Update($arr["ID"], $arFieldsUpdate);
                    }
                    else{
                        CPrice::Add($arFieldsUpdate);
                    }
                }
            }
        }
    }
}
?>