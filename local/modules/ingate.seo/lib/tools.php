<?php
namespace Ingate\Seo;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\HttpApplication;

Loc::loadMessages(__FILE__);

class Tools
{
	/**
	 * Конвертируем emodji в html-код
	 * @param string $str
	 * @return string
	 */
	public static function convertEmoji($str = "")
	{
		preg_match_all("/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u", $str, $matches);

		if (!empty($matches[0])) {
			foreach ($matches[0] as $value) {
			  $arHtmlEmoji[] = '&#'.hexdec(bin2hex(mb_convert_encoding($value, 'UTF-32', 'UTF-8'))).';';
			}

			return str_replace($matches[0], $arHtmlEmoji, $str);
		}

		return $str;
	}

	/**
	 * Получаем идентификатор сайта
	 * @param string $url - url адрес
	 * @return string
	 */
	public static function getSiteId($url = '') {

		$filter = array();

		if (!empty($url) && preg_match('/\/\/(.+)\//U', $url, $matches)) {

			$filter['SERVER_NAME'] = $matches[1];
		} else {
			$filter['DEF'] = 'Y';
		}

		$result = SiteTable::getList(array(
			'filter' => $filter
		))->fetch();

		$siteId = ($result['LID']) ? $result['LID'] : '';

		return $siteId;
	}

	public static function checkCurrentVersionWith($version = '')
	{
		if (empty($version)) {
			return false;
		}

		$currentVersion = '';

		if (
			class_exists("\Bitrix\Main\ModuleManager") &&
			method_exists('\Bitrix\Main\ModuleManager', 'getVersion')
		) {
			$currentVersion = ModuleManager::getVersion('main');
		} elseif (defined('SM_VERSION')) {
			$currentVersion = SM_VERSION;
		}

		return CheckVersion($currentVersion, $version);
	}

	public static function isCron()
	{
		$sapi_type = php_sapi_name();

		if ($sapi_type == 'cli' || $sapi_type == 'cli-server') {
			return true;
		}

		return false;
	}

	public static function primaryCheckBeforeEvent()
	{
		$request = HttpApplication::getInstance()->getContext()->getRequest();
		$requestUri = $request->getRequestUri();

		if (
			!$request->isAdminSection() &&
			!empty($requestUri) &&
			!self::isCron()
		) {
			return true;
		}

		return false;
	}
}