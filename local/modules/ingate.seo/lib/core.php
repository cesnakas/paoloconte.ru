<?php
namespace Ingate\Seo;

use Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\HttpApplication;
use \Bitrix\Main\Application;
use \Bitrix\Main\Diag\Debug;

class Core
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;

	const URL_CHECKER_LOG_FILE_PATH = '/local/logs/ingate.seo.urlchecker.log';

	public static function getCurrentUrlFromTableByField($table = '', $where = '')
	{
		if (
			empty($where) ||
			!class_exists($table) ||
			!Loader::includeModule(self::MODULE_ID)
		) {
			return false;
		}

        $request = HttpApplication::getInstance()->getContext()->getRequest();
		$server = HttpApplication::getInstance()->getContext()->getServer();

		$requestUri = $request->getRequestUri();

        $requestUri = self::checkSeoModules($requestUri);

		if (empty($requestUri)) {
			return false;
		}

		$arPattern = array(
			'\'',
			'[',
			']',
			'(',
			')',
			'|',
			'*',
			'%5B',
			'%5D',
			'%7C',
			'%2a',
			'%27',
			'%28',
			'%29',
			'%2B',
			'%3A',
			'?',
		);

		$arReplace = array(
			'[\'\']',
			'\\\\[',
			'\\\\]',
			'[(]',
			'[)]',
			'[|]',
			'[*]',
			'\\\\%5B',
			'\\\\%5D',
			'[%7C]',
			'[%2a]',
			'[%27]',
			'[%28]',
			'[%29]',
			'[%2B]',
			'[%3A]',
			'[?]',
		);

        $page = $requestUri;

        if ($exlusions = Option::get(self::MODULE_ID, INGATE_SEO_OPTION_EXCLUSIONS)) {
            $arUri = explode('?', $requestUri);
            $page = $arUri[0];

            $arExlusions = explode(PHP_EOL, $exlusions);
            $patternExlusions = '/(&|\?)('.implode('|', $arExlusions).')[^&]*/i';

            if ($arUri[1]) {
                $query = trim(
                    preg_replace($patternExlusions, '', '?'.$arUri[1]),
                    '&'
                );

                if (!empty($query))
                    $page .= '?'.trim($query, '?');
            }
        }

		$uri = str_replace($arPattern, $arReplace, $page);

        $domain = $server->getServerName();
		$uriDecoded = str_replace("'", "''", urldecode($uri));

		$connection = Application::getConnection();

		$sql = "SELECT * FROM ".$table::getTableName()
			." WHERE `".$where."` REGEXP '^(http(s)?:\/\/)?("
			.$domain.preg_replace('/(%20|\s|\+)/iu', '[+]', $uri)
			."|".$domain.preg_replace('/\s/iu', '[+]', $uriDecoded)
			.")$'"
			." AND `ACTIVE` = 'Y' ORDER BY `id` LIMIT 1";

		$recordset = $connection->query($sql);

		return $recordset->fetch();
	}

    /**
     * @param string $uri
     * @return string $uri
     */
    public static function checkSeoModules($uri){

        try {

            // проверка на подмену url модулем sotbit.seometa (для версии 1.4.9)
            if(
                IsModuleInstalled('sotbit.seometa')
                && Loader::includeModule('sotbit.seometa')
                && is_callable(['\Sotbit\Seometa\SeometaUrlTable', 'getByRealUrl']) // метод проверки актуальный для 1.4.9 доступен
            ){
                $result = \Sotbit\Seometa\SeometaUrlTable::getByRealUrl($uri);
                if ( $result && !empty($result['NEW_URL'])) {
                    return $result['NEW_URL'];
                }
            }

        } catch (\Exception $e) {
            Debug::writeToFile(
                [
                    'date' => date('Y-m-d H:i:s'),
                    'error' => $e->getMessage()
                ],
                "",
                self::URL_CHECKER_LOG_FILE_PATH
            );
        }

        return $uri;
    }

}
