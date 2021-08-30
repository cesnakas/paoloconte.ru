<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Citfact;

use Bitrix\Sale\Internals;
use Bitrix\Sale\DiscountCouponsManager;

/**
* Ошибка некорректности дисконтной карты
*/
class IncorrectLoyaltyCardException extends \Exception
{
}


/**
* Ошибка при привязке купона к скидке
*/
class SaleDiscountCreateCouponException extends \Exception
{
}


class Paolo
{
	/**
	 * @return array
	 */
	public static function GetPriceTypes()
	{
		$arReturn = array();
		$dbPriceType = \CCatalogGroup::GetList(
			array("NAME" => "ASC"),
			array()
		);
		while ($arPriceType = $dbPriceType->Fetch()){
			$arReturn[] = $arPriceType;
		}
		return $arReturn;
	}

	/**
	 * @param string $region_name
	 * @return array
	 */
	public static function GetRegionSettings($region_name = '')
	{
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_REGION_SETTINGS, 'ACTIVE' => 'Y');
		if ($region_name != ''){
			$arFilter['NAME'] = $region_name;
		}
		$res = $el->GetList(array('NAME' => 'ASC'), $arFilter, false, false, array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_OKRUG', 'PROPERTY_CITIES', 'PROPERTY_REGION_PRICE', 'PROPERTY_REGION_PRICE_ACTION'));
		$arRegions = array();
		while ($arRes = $res->GetNext()){
			$arRegions[] = $arRes;
		}

		return $arRegions;
	}

	/**
	 * @param string $region_name
	 * @return array
	 */
	public static function GetCities($region_name = '')
	{
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_CITIES, 'ACTIVE' => 'Y');
		if ($region_name != ''){
			$arFilter['PROPERTY_OBLAST'] = $region_name;
		}
		$res = $el->GetList(array(), $arFilter, false, false, array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_OKRUG', 'PROPERTY_OBLAST'));
		while ($arRes = $res->GetNext()){
			$arReturn[$arRes['ID']] = $arRes;
		}

		return $arReturn;
	}


	/**
	 * @param int $city_id
	 * @return array
	 */
	public static function GetRegionPriceTypes($city_id = 0)
	{
		$arReturn = array();

		// Задаем по-умолчанию московские цены
		$priceTypeId = PRICE_ID_MOSCOW;
		$priceTypeId_action = PRICE_ID_MOSCOW_ACTION;

		$arCity = Paolo::GetCitiesByFilter(array('ID' => $city_id));
		$arCity = $arCity[0];

		// Получаем настройки региона
		$arRegion = Paolo::GetRegionSettings($arCity['PROPERTY_OBLAST_VALUE']);
		$arRegion = $arRegion[0];
		//Tools::pre($arRegion);

		// Задаем типы цен региональными
		if ($priceTypeId != '') {
			$priceTypeId = $arRegion['PROPERTY_REGION_PRICE_VALUE'];
			$priceTypeId_action = $arRegion['PROPERTY_REGION_PRICE_ACTION_VALUE'];

			// Достаем список городов в регионе
			$arCitiesSettings = unserialize($arRegion['~PROPERTY_CITIES_VALUE']['TEXT']);

			// Ищем текущий город в настройках региона, если находим, то заменяем ID типов цен на городские
			foreach ($arCitiesSettings as $arCity) {
				//Tools::pre($arCity);
				if ($arCity['CITY_ID'] == $city_id) {
					$priceTypeId = $arCity['PRICE_TYPE_ID'];
					$priceTypeId_action = $arCity['PRICE_TYPE_ACTION_ID'];
				}
			}
		}

		if ($priceTypeId != '') {
			$arFilter = array('ID' => $priceTypeId);
			if ($priceTypeId_action != ''){
				$arFilter['ID'] = array($priceTypeId, $priceTypeId_action);
			}
			$dbPriceType = \CCatalogGroup::GetList(
				array("NAME" => "ASC"),
				$arFilter
			);
			while ($arPrice = $dbPriceType->Fetch()) {
				$arReturn['FULL'][$arPrice['ID']] = $arPrice;
				$arReturn['TO_PARAMS'][] = $arPrice['NAME'];
			}
		}

		$arReturn['PRICE_ID'] = $priceTypeId;
		$arReturn['PRICE_ID_ACTION'] = $priceTypeId_action;

		return $arReturn;
	}

	public static function getBasePriceId() {
	    return $_SESSION['GEO_PRICES']['PRICE_ID'];
    }

	public static function getActionPriceId() {
	    return $_SESSION['GEO_PRICES']['PRICE_ID_ACTION'];
    }

	/**
	 * @param array $arFilter_param
	 * @return array|bool
	 */
	public static function GetCitiesByFilter(array $arFilter_param)
	{
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_CITIES, 'ACTIVE' => 'Y');
		$arFilter = array_merge($arFilter, $arFilter_param);
		$res = $el->GetList(array('NAME' => 'ASC'), $arFilter, false, false, array('IBLOCK_ID', 'ID', 'NAME', 'CODE',
			'PROPERTY_OKRUG', 'PROPERTY_OBLAST', 'PROPERTY_LAT', 'PROPERTY_LONG'));
		while ($arRes = $res->GetNext()) {
			$arReturn[] = $arRes;
		}
		return $arReturn;
	}


	/**
	 * @param $city_id
	 * @return array
	 */
	public static function GetShops($city_id = '')
	{
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_SHOPS, 'ACTIVE' => 'Y');
		if ($city_id != ''){
			$arFilter['PROPERTY_CITY'] = (int)$city_id;
		}
		$res = $el->GetList(array(), $arFilter, false, false, array('IBLOCK_ID', 'ID', 'NAME', 'CODE',
			'PROPERTY_ADDRESS', 'PROPERTY_CITY', 'PROPERTY_CITY.CODE', 'PROPERTY_CITY.NAME',
			'PROPERTY_PHONE', 'PROPERTY_GRAPHICK', 'PROPERTY_IMAGES', 'PROPERTY_COORDS')
		);
		while ($arRes = $res->GetNext()){
			$arReturn[$arRes['PROPERTY_CITY_VALUE']][] = $arRes;
		}

		return $arReturn;
	}


	/**
	 * @return array
	 */
	public static function GetStores()
	{
		$arReturn = array();
		$store = new \CCatalogStore();
		$arFilter = array('ACTIVE' => 'Y');

		$res = $store->GetList(array('TITLE' => 'ASC'), $arFilter, false, false, array());
		while ($arRes = $res->GetNext()){
			$arReturn[] = $arRes;
		}

		return $arReturn;
	}


	/**
	 * @param array $arFilter_param
	 * @return array
	 */
	public static function GetShopsByFilter(array $arFilter_param){
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_SHOPS, 'ACTIVE' => 'Y');
		$arFilter = array_merge($arFilter, $arFilter_param);
		$res = $el->GetList(array(), $arFilter, false, false,
			array('IBLOCK_ID', 'ID', 'NAME', 'CODE',
				'PROPERTY_ADDRESS', 'PROPERTY_CITY', 'PROPERTY_CITY.CODE', 'PROPERTY_CITY.NAME',
				'PROPERTY_PHONE', 'PROPERTY_GRAPHICK', 'PROPERTY_IMAGES', 'PROPERTY_COORDS', 'PROPERTY_STORE_ID', 'PROPERTY_NEAREST_METRO')
		);
		while ($arRes = $res->GetNext()) {
			$arReturn[] = $arRes;
		}

		return $arReturn;
	}


	/**
	 * @param array $arFilter_param
	 * @return array
	 */
	public static function GetReviewsShopsByFilter(array $arFilter_param){
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_REVIEWS_SHOPS, 'ACTIVE' => 'Y');
		$arFilter = array_merge($arFilter, $arFilter_param);
		$res = $el->GetList(array('ID' => 'DESC'), $arFilter, false, false,
			array('IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE',
				'PROPERTY_USERNAME', 'PROPERTY_USERPHONE', 'PROPERTY_REVIEW_TEXT', 'PROPERTY_SHOP_ID')
		);
		while ($arRes = $res->GetNext()) {
			$arReturn[] = $arRes;
		}

		return $arReturn;
	}


	/**
	 * метод ресайзит картики для каталога и склыдывает их в /upload/resize_cache/catalog/$folder
	 * в случае успешного создания вернет путь до созданой картинки, иначе путь до оригинала картинки
	 * если не указаны параметры вернет false
	 * если ресайз картинка уже есть то ресайз пересоздавать не будет а вернет путь до нее
	 * @param string $name_file
	 * @param string $path_file
	 * @param string $folder
	 * @param int $w
	 * @param int $h
	 * @param string $prefix
	 * @return string | bool
	 */
	public function catalogResizeImage($name_file, $path_file, $folder, $w, $h, $prefix=''){

		if(
			empty($name_file) ||
			empty($path_file) ||
			empty($folder) ||
			empty($w) ||
			empty($h) ||
			!file_exists($_SERVER["DOCUMENT_ROOT"].$path_file.$name_file)
		)
			return false;

		if(!empty($prefix))
			$prefix = $prefix.'_';

		$new_file_image = '/upload/resize_cache/catalog/'.$folder.'/'.$w.'x'.$h.'_'.$prefix.$name_file;

		if(file_exists($_SERVER["DOCUMENT_ROOT"].$new_file_image))
			return $new_file_image;

		if(!file_exists($_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/'))
			mkdir($_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/', 0777);

		if(!file_exists($_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/'.$folder))
			mkdir($_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/'.$folder, 0777);

		$el = new \CFile();
		$resizing = $el->ResizeImageFile(
			$sourceFile = $_SERVER["DOCUMENT_ROOT"].$path_file.$name_file,
			$destinationFile = $_SERVER["DOCUMENT_ROOT"].$new_file_image,
			$arSize = array('width'=>$w, 'height'=>$h),
			$resizeType = BX_RESIZE_IMAGE_PROPORTIONAL,
			$arWaterMark = array()
		);

		$path = $resizing ? $new_file_image : $path_file.$name_file;

		return $path;
	}


	/**
	 * Метод получения картинок товара по его артикулу
	 * Принимает строку артикула и массив конфига
	 * Возвращает массив фото структурированных в соответствии с задаными размерами
	 *
	 * Пример массива конфига :
	 *  $config1 = array(
	 *      'TYPE'=>'ALL || ONE', - одно из двух
	 *	    '360'=>'Y', - если нужен обзор 360
	 *	    'PATH360' => '/65-108-10-1/swf/65-108-10_360', - путь до каталога с фото для 360 (необязательный, так как в коде есть попытка его определить, но могут быть ошибки)
	 *	    'SIZE' => array(  - размеры фото, можно задавать какие угодно, венется результат в соответствие с этим массивом (необязателен, если его не определять вернуться оригиналы фото)
	 *         'SMALL' => array('W'=>60,'H'=>80), - каждый размер обязан содержать конкретные размеры
	 *	       'BIG' => array('W'=>500,'H'=>665),
	 *         'OTHER' => array('W'=>1024,'H'=>1024),
	 *	     )
	 *	);
	 *
	 * @param string $articul
	 * @param array $config
	 * @return array
	 */
	public static function getProductImage($articul, $config){

		$arResult = array();

		//проверка конфига
		if((empty($config) || !is_array($config)) /* || ($config['TYPE'] != 'ALL' && $config['TYPE'] != 'ONE')*/)
			return $arResult;
		if(isset($config['SIZE']) && !empty($config['SIZE']))
			foreach ($config['SIZE'] as $size) {
				if(empty($size['W']) || empty($size['H']))
					return $arResult;
			}

		//формируем основные пути
		$catalog_img_path = CATALOG_IMG.$articul.'/';
		$catalog_img_photo_path = CATALOG_IMG.$articul.CATALOG_IMG_PHOTO;

		//если не существуют корневой каталог с фото к товару, то ни че делать не будем
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$catalog_img_path))
			return $arResult;

		//если не существуют каталог с конкретными фото к товару, то ни че делать не будем
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$catalog_img_photo_path))
			return $arResult;

		//берем имена файлов картинок
		$files = Tools::getImageFiles($_SERVER['DOCUMENT_ROOT'].$catalog_img_photo_path);

		//получаем ресайзы картинок
		//если надо полный набор фото
		if($config['TYPE'] == 'ALL'){
			foreach ($files as $file) {
				//если указан набор размеров
				if(!empty($config['SIZE'])) {
					foreach ($config['SIZE'] as $sizeName => $sizeValues) {
						$sizing_photo = Paolo::catalogResizeImage($file, $catalog_img_photo_path, $articul, $sizeValues['W'], $sizeValues['H']);
						if($sizing_photo != false){
							$url_photo[$sizeName] = $sizing_photo;
						}
					}
					$arResult['PHOTO'][] = $url_photo;
				}
				//если НЕ указан набор размеров то просто отдаем оригиналы
				else{
					$arResult['PHOTO'][] = $catalog_img_photo_path.$file;
				}
				unset($url_photo);
			}
		}
		//если надо только 1 фото
		else if($config['TYPE'] == 'ONE'){
			if (isset($files[0]) && !empty($files[0])) {
				//если указан набор размеров
				if(!empty($config['SIZE'])) {
					foreach ($config['SIZE'] as $sizeName => $sizeValues) {
						$sizing_photo = Paolo::catalogResizeImage($files[0], $catalog_img_photo_path, $articul, $sizeValues['W'], $sizeValues['H']);
						if($sizing_photo != false){
							$url_photo[$sizeName] = $sizing_photo;
						}
					}
					$arResult['PHOTO'][] = $url_photo;
				}
				//если НЕ указан набор размеров то просто отдаем оригинал
				else{
					$arResult['PHOTO'][] = $catalog_img_photo_path.$files[0];
				}
				unset($url_photo);
			}
		}

		// если надо набор картинок для обзора 360
		if($config['360'] == 'Y'){

			//формируем путь до панорамы 360
			$path360 = empty($config['PATH360'])? $articul.CATALOG_IMG_SWF.substr($articul,0,-2).'_360' : $config['PATH360'];
			$catalog_img_360_path = CATALOG_IMG.$path360.'/'; //вот тут может быть когда нибудь возникнет несоответствие путей

			//если такого каталога не существует, то ни че делать не будем
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$catalog_img_360_path))
				return $arResult;

			$files = Tools::getImageFiles($_SERVER['DOCUMENT_ROOT'].$catalog_img_360_path);

			foreach ($files as $file) {
				//если указан набор размеров
				if(!empty($config['SIZE'])) {
					foreach ($config['SIZE'] as $sizeName => $sizeValues) {
						$sizing_photo = Paolo::catalogResizeImage($file, $catalog_img_360_path, $articul, $sizeValues['W'], $sizeValues['H']);
						if($sizing_photo != false){
							$url_photo[$sizeName] = $sizing_photo;
						}
					}
					$arResult['360'][] = $url_photo;
				}
				//если НЕ указан набор размеров то просто отдаем оригиналы
				else{
					$arResult['360'][] = $catalog_img_360_path.$file;
				}
			}
			unset($url_photo);
		}
		return $arResult;
	}


	public static function GetUserAddresses($user_id){
		$arReturn = array();
		$el = new \CIBlockElement();
		$arFilter = array('IBLOCK_ID' => IBLOCK_ADDRESSES, 'ACTIVE' => 'Y', 'PROPERTY_USER' => (int)$user_id);
		$res = $el->GetList(array('ID' => 'DESC'), $arFilter, false, false,
			array('IBLOCK_ID', 'ID', 'NAME',
				'PROPERTY_ADDRESS', 'PROPERTY_LOCATION', 'PROPERTY_USER', 'PROPERTY_SELECTED')
		);
		$arIds = array();
		while ($arRes = $res->GetNext()) {
			$arTemp = array();
			$arTemp['ID'] = $arRes['ID'];
			$arTemp['USER'] = $arRes['PROPERTY_USER_VALUE'];
			$arTemp['ADDRESS'] = $arRes['PROPERTY_ADDRESS_VALUE'];
			$arTemp['LOCATION_ID'] = $arRes['PROPERTY_LOCATION_VALUE'];
			$arTemp['SELECTED'] = $arRes['PROPERTY_SELECTED_VALUE'];

			$arIds[] = $arRes['PROPERTY_LOCATION_VALUE'];
			$arReturn[] = $arTemp;
		}

		$arLocations = array();
		if (!empty($arIds)) {
			$db_vars = \CSaleLocation::GetList(
				array(
					"SORT" => "ASC",
					"COUNTRY_NAME_LANG" => "ASC",
					"CITY_NAME_LANG" => "ASC"
				),
				array("ID" => $arIds, "LID" => LANGUAGE_ID, /*'COUNTRY_LID' => 'ru', 'REGION_LID' => 'ru', 'CITY_LID' => 'ru'*/),
				false,
				false,
				array()
			);
			while ($vars = $db_vars->Fetch()) {
				$arLocations[$vars['ID']] = $vars;

				$db_zip = \CSaleLocation::GetLocationZIP($vars['ID']);
				if ($arZip = $db_zip->Fetch()) {
					$arLocations[$vars['ID']]['ZIP'] = $arZip['ZIP'];
				}
			}

			foreach ($arReturn as &$arAddress){
				$str_location = $arLocations[$arAddress['LOCATION_ID']]['REGION_NAME'].', '.$arLocations[$arAddress['LOCATION_ID']]['CITY_NAME'];
				$arAddress['LOCATION_NAME'] = $str_location;
				$arAddress['ZIP'] = $arLocations[$arAddress['LOCATION_ID']]['ZIP'];
				$arAddress["CITY_NAME"] = $arLocations[$arAddress['LOCATION_ID']]['CITY_NAME'];
			}
		}

		return $arReturn;
	}

	public static function GetBitrixLocation($geo_city_id){
		if ($geo_city_id == ''){
			return false;
		}
		$arCity = Paolo::GetCitiesByFilter(array('ID' => $geo_city_id));
		//echo "<pre style=\"display:block;\">"; print_r($arCity); echo "</pre>";

		$db_vars = \CSaleLocation::GetList(
			array(),
			array("LID" => LANGUAGE_ID, /*'REGION_NAME' => $arCity[0]['PROPERTY_OBLAST_VALUE'],*/ 'CITY_NAME' => $arCity[0]['NAME']),
			false,
			false,
			array()
		);
		$city_id = 0;
		if ($vars = $db_vars->Fetch()) {
			$city_id = $vars['CITY_ID'];
		}

		return $city_id;
	}


    /**
     * Возвращает объект Soap клиента для сервиса резервирования товаров
     * @return \SoapClient
     * @throws \Exception
     */
    public static function GetSoapClientReserve() {
        try {
            $client = new \SoapClient(
            'http://1cws.paoloconte.ru/v82_retail/ws/loyaltycard.1cws?wsdl',
                array(
                    "location" => "http://1cws.paoloconte.ru/v82_retail/ws/loyaltycard.1cws"
                )
            );
        } catch (\SoapFault $e) {
            throw new \Exception($e);
        }

        return $client;
    }

    /**
     * Возвращает кол-во товара на розничном складе
     * @param $storeId
     * @param $artNum
     * @param string $size
     * @param bool $soapClient
     * @return mixed
     * @throws \Exception
     */
    public static function GetReserveItemRemain($retailId, $artNum, $size, $soapClient = false) {

	    if (!$soapClient) {
            $soapClient = Paolo::GetSoapClientReserve();
        }

        $params = [
            "Data" => [
                "ИДМагазина" => $retailId,
                "Артикул" => $artNum,
                "Характеристика" => $size
            ]
        ];

	    return $soapClient->ItemRemain($params)->return;
    }

    /** Резервирует товар на розничном складе
     * @param $retailId
     * @param $name
     * @param $phone
     * @param $artNum
     * @param string $size
     * @param bool $soapClient
     * @return mixed
     * @throws \Exception
     */
    public static function SetReserve($retailId, $name, $phone, $artNum, $size, $soapClient = false) {

        if (!$soapClient) {
            $soapClient = Paolo::GetSoapClientReserve();
        }

        $params = [
            "Data" => [
                "ИДМагазина" => $retailId,
                "Покупатель" => $name,
                "Телефон" => $phone,
                "Состав" => [
                    "Артикул" => $artNum,
                    "Характеристика" => $size,
                    "Количество" => 1
                ]
            ]
        ];

        return $soapClient->Reserve($params)->return;
    }

	/**
	 * Метод возвращает SoapClient для запроса по картам
	 *
	 * @return SoapClient
	 */
	public static function GetSoapClientLoyaltyCard() {
		try {
			$client = new \SoapClient(
				//"http://1cws.paoloconte.ru:84/v82retail/ws/loyaltycard.1cws?wsdl",
				"http://1cws.paoloconte.ru/v82_retail/ws/loyaltycard.1cws?wsdl",
				array(
					"location" => "http://1cws.paoloconte.ru/v82_retail/ws/loyaltycard.1cws"
				)
			);
		} catch (\SoapFault $e) {
			throw new \Exception($e);
		}

		return $client;
	}


	/**
	 * Метод возвращает информацию по картам из 1С
	 *
	 * @param string $barcode
	 * @param SoapClient $soap_client
	 * @return array
	 */
	public static function GetLoyaltyCardsInfo($barcode, $soap_client = false) {
		if (!preg_match('/^\d{13}$/', $barcode)) {
			throw new IncorrectLoyaltyCardException("Incorrect barcode ".$barcode, 1);
		}

		if (!$soap_client) {
			$soap_client = Paolo::GetSoapClientLoyaltyCard();
		}

		$params = array("barcode" => $barcode);
		$info = $soap_client->getinfo($params)->return;

		$res = array();
		if ($info->РазмерСкидки) {
			$res["DISCOUNT_PERCENT"] = $info->РазмерСкидки;
		}
		if ($info->ИнфоПоКПП) {
			$info_text = $info->ИнфоПоКПП;
			$info_text = preg_replace("/Скидка по КПП: \d+%, /", "", $info_text);
			$res["INFO_TEXT"] = $info_text;
		}

		return $res;
	}


	/**
	 * Процедура создает купон по дисконтной карте
	 * и привязывает его к скидке
	 *
	 * @param string $coupon
	 * @param integer $discount_percent
	 */
	public static function CreateCoupon($coupon, $discount_percent, $user_id) {
		if (!preg_match('/^\d{13}$/', $coupon)) {
			throw new IncorrectLoyaltyCardException("Incorrect coupon ".$coupon, 2);
		}

        if (!\CModule::IncludeModule("sale")) {
			throw new IncorrectLoyaltyCardException("Not install  module sale");
		}

		$discount_percent = intval($discount_percent);
		$discount_db = \CSaleDiscount::GetList(
			array(),
			array("XML_ID" => "loyalty_card_".$discount_percent),
			false,
			false,
			array("ID", "NAME", "XML_ID")
		);
		$discount = $discount_db->Fetch();

		if (empty($discount)) {
			// Отсылаем ошибку администратору сайта
			$adminEmail = COption::GetOptionString('main', 'email_from');
			$to      = $adminEmail;
			$subject = 'Ошибка на сайте!';
			$message = 'Ошибка! Пришла скидка по карте ('.$discount_percent.'%), на сайте нет такой скидки.';
			$headers = 'From: '.$adminEmail . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);

			throw new SaleDiscountCreateCouponException("Sale discount (XML_ID = \"loyalty_card_".$discount_percent."\") not found.", 1);
		}

		// Существует ли уже такой купон и привязан ли он к правильной скидке?
		$getListParams = array(
			"select" => array(
				"ID",
				"COUPON",
				"DISCOUNT_ID"
			),
			"filter"	=> array("COUPON" => $coupon),
			"order"		=> array(),
		);
		$coupons_db = Internals\DiscountCouponTable::getList($getListParams);
		if ($coupon_res = $coupons_db->Fetch()) {
			// Все как надо!
			if ($discount["ID"] == $coupon_res["DISCOUNT_ID"])
				return;

			// Удаляем старый купон
			$result = Internals\DiscountCouponTable::delete($coupon_res["ID"]);
			if (!$result->isSuccess()) {
				throw new SaleDiscountCreateCouponException("Delete coupon (".$coupon.") error.\n".$errors, 2);
			}
		}

		$fields = array(
			"DISCOUNT_ID"	=> $discount["ID"],
			"COUPON"		=> $coupon,
			"ACTIVE"		=> "Y",
			"USER_ID"		=> $user_id, // Владелец купона
			"TYPE"			=> 4,	// Многоразовый купон
		);
		$result = Internals\DiscountCouponTable::add($fields);
		if (!$result->isSuccess()) {
			throw new SaleDiscountCreateCouponException("Create coupon (".$coupon.") error.\n".$errors, 3);
		}
	}


	/**
	 * Проверяет на этапе оформления заказа,
	 * использовал ли чувак купон по скидочной карте.
	 *
	 * @return [string] код использованного купона или пустая строка
	 */
	public static function getLoyaltycardCoupon() {
		global $USER;
		$rsUser = \CUser::GetByID($USER->GetID());
		$arUser = $rsUser->Fetch();
		if ($arUser["UF_USE_LOYALTY_CARD"] &&
			!empty($arUser["UF_LOYALTY_CARD"]) &&
			intval($arUser["UF_CARD_DISCOUNT"]) > 0
		) {
			$arCouponData = DiscountCouponsManager::getEnteredCoupon($arUser["UF_LOYALTY_CARD"]);
			if ($arCouponData) {
				return $arUser["UF_LOYALTY_CARD"];
			}
		}

		return "";
	}


	/**
	 * Возвращаем номер дисконтной карты, если она активирована
	 *
	 * @return [string] код карты или пустая строка
	 */
	public static function getUserLoyaltycard() {
		global $USER;
		$rsUser = \CUser::GetByID($USER->GetID());
		$arUser = $rsUser->Fetch();
		if ($arUser["UF_USE_LOYALTY_CARD"] &&
			!empty($arUser["UF_LOYALTY_CARD"])
		) {
			return $arUser["UF_LOYALTY_CARD"];
		}

		return "";
	}


	/**
	 * Переводит месяца на русский язык
	 */
	public static function getRusMonth($month) {
		$arRusMonths = array(
			"January" => "января",
			"February" => "февраля",
			"March" => "марта",
			"April" => "апреля",
			"May" => "мая",
			"June" => "июня",
			"July" => "июля",
			"August" => "августа",
			"September" => "сентября",
			"October" => "октября",
			"November" => "ноября",
			"December" => "декабря",
		);

		if (array_key_exists($month, $arRusMonths)) {
			return $arRusMonths[$month];
		}

		return $month;
	}


	public static function deleteProductImage($articul, $nameFile){
		$arResult = array('ARTICUL'=>$articul, 'NAME_FILE'=>$nameFile, 'FILES_REMOVE'=>array(), 'ERROR'=>array(),);
		if (!empty($articul) && !empty($nameFile)) {
			$Path = $_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/'.$articul.'/';
			if(!file_exists($Path)){
				$arResult['ERROR'][] = 'Не существует директории '.$Path;
			}else{
				$arFiles = scandir($Path);
				foreach ($arFiles as $file) {
					if (strpos($file, $nameFile) !== false && (strpos($file, '.jpg')!==false || strpos($file, '.jpeg')!==false || strpos($file, '.png')!==false)) {
						if(unlink($Path.$file)){
							$arResult['FILES_REMOVE'][] = $Path.$file;
						}else{
							$arResult['ERROR'][] = 'Не удалось удалить '.$Path.$file;
						}
					}
				}
			}
		}else{
			$arResult['ERROR'][] = 'Передайте параметры';
		}
		return $arResult;
	}


	public static function removeDir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($dir."/".$object))
						Paolo::removeDir($dir."/".$object);
					else
						unlink($dir."/".$object);
				}
			}
			if (rmdir($dir)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	public static function recurseCopy($src, $dst) {
		$dir = opendir($src);
		mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					Paolo::recurseCopy($src . '/' . $file, $dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		return true;
	}


	public static function deleteImages($Path){
		$arResult = array('FILES_REMOVE'=>array(), 'ERROR'=>array(), 'WARNING'=>array());
		if(!file_exists($Path)){
			$arResult['WARNING'][] = 'Не существует директории '.$Path;
		}else{
			$arFiles = scandir($Path);
			$arDelete = array('.', '..');
			foreach ($arDelete as $del) {
				$pos = array_search($del, $arFiles);
				if ($pos !== false) {
					unset($arFiles[$pos]);
				}
			}
			if (!empty($arFiles)) {
				foreach ($arFiles as $file) {
					if ((strpos($file, '.jpg')!==false || strpos($file, '.jpeg')!==false || strpos($file, '.png')!==false)) {
						if(unlink($Path.$file)){
							$arResult['FILES_REMOVE'][] = $Path.$file;
						}else{
							$arResult['ERROR'][] = array('Не удалось удалить '.$Path.$file, error_get_last());
						}
					}else{
						$arResult['WARNING'][] = 'Не является картинкой '.$Path.$file;
					}
				}
			}else{
				$arResult['WARNING'][] = 'Директория '.$Path.' пустая';
			}
		}
		return $arResult;
	}


	public static function deleteProductImageCache($articul){
		$PathCash = $_SERVER["DOCUMENT_ROOT"].'/upload/resize_cache/catalog/'.$articul.'/';
		return Paolo::deleteImages($PathCash);
	}

	public static function deleteProductImageFTP($articul){
		$PathFtp = $_SERVER["DOCUMENT_ROOT"].'/ftp_loader/product_images/'.$articul.'/photo/';
		return Paolo::deleteImages($PathFtp);
	}

	public static function deleteProductAllImage($articul){
		$arResult = array('ARTICUL'=>$articul, 'FILES_REMOVE'=>array(), 'ERROR'=>array(), 'WARNING'=>array());
		$arResult = array_merge($arResult, Paolo::deleteProductImageFTP($articul));

		$arCacheResult = Paolo::deleteProductImageCache($articul);
		foreach ($arResult as $key => $value) {
			if (!empty($arCacheResult[$key])) {
				$arResult[$key] = array_merge($arResult[$key], $arCacheResult[$key]);
			}
		}
		return $arResult;
	}

    public static function setPicturesToElement($articul, $mainImage, $hasPhotoActivate = false)
    {
        $src = $_SERVER['DOCUMENT_ROOT'].'/ftp_loader/product_images/'. $mainImage;

        $original = \CFile::MakeFileArray($src);

        if (!$original) {
            return false;
        }

        $originalId = \CFile::SaveFile($original, 'photo');
        $originalFields = \CFile::GetFileArray($originalId);

        if ($originalFields['HEIGHT'] > $originalFields['WIDTH']) {
            $sizes = array(
                'SMALL' => ['width' => 400, 'height' => 4000],
                'BIG' => ['width' => 4000, 'height' => 1200]
            );
        } else {
            $sizes = array(
                'SMALL' => ['width' => 4000, 'height' => 400],
                'BIG' => ['width' => 1200, 'height' => 4000]
            );
        }

        $preview = \CFile::ResizeImageGet($originalId, $sizes['SMALL'], BX_RESIZE_IMAGE_PROPORTIONAL);
        $detail = \CFile::ResizeImageGet($originalId, $sizes['BIG'], BX_RESIZE_IMAGE_PROPORTIONAL);

        $previewFile = \CFile::MakeFileArray($preview['src']);
        $detailFile = \CFile::MakeFileArray($detail['src']);

        $res = \CIBlockElement::GetList([],['IBLOCK_ID' => IBLOCK_CATALOG, 'PROPERTY_CML2_ARTICLE' => $articul], false, false, []);
        while ($element = $res->GetNext()) {
            $elementId = $element['ID'];
        }

        $arProperties = [];
        if (!empty($previewFile)) {
            $arFields['PREVIEW_PICTURE'] = $previewFile;
            if ($hasPhotoActivate)
            {
                $arProperties['HAS_PHOTO'] = "Y";
            }
        }
        if (!empty($detailFile)) {
            $arFields['DETAIL_PICTURE'] = $detailFile;
            if ($hasPhotoActivate)
            {
                $arProperties['HAS_PHOTO'] = "Y";
            }
        }

        if (isset($elementId) && !empty($arFields)) {
            $el = new \CIBlockElement;
            $el->Update($elementId, $arFields);
            $el->SetPropertyValuesEx($elementId, IBLOCK_CATALOG, $arProperties);
        }

        return true;
	}
}