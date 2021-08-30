<?php
namespace Ingate\Seo;

use \Ingate\Seo\Core;
use \Bitrix\Main\Config\Option;

class App
{
	protected static $_instance;
	private $data = array();

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		$this->data = Core::getCurrentUrlFromTableByField('\Ingate\Seo\PageTable', 'URL');
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public function setProperties()
	{
		if (empty($this->data))
			return;

		global $APPLICATION;

		$optionMeta = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_META);
		$optionH1 = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_H1);

		if ($optionMeta == 'Y') {

			if (!empty($this->data["TITLE"])) {
				$APPLICATION->SetPageProperty("title", $this->data["TITLE"]);
			}

			if (!empty($this->data["DESCRIPTION"])) {
				$APPLICATION->SetPageProperty(
					"description",
					$this->data["DESCRIPTION"]
				);
			}
		}

		if (!empty($this->data["H1"]) && $optionH1 == 'Y') {
			$APPLICATION->SetTitle($this->data["H1"]);
		}
	}

	public function setBufferProperties($buffer = '')
	{
		$optionMeta = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_META);
		$optionH1 = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_H1);
		$optionRobots = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_ROBOTS);
		$optionRobotsAll = Option::get(INGATE_SEO_MODULE_ID, INGATE_SEO_OPTION_ROBOTS_ALL);

		$robotsPattern = '/<meta[^>]*?name=[\'"]robots[\'"](.*)>/Usmi';

		if (empty($this->data)) {
			if ($optionRobotsAll == 'Y') {
				if (preg_match($robotsPattern, $buffer, $descMatch)) {
					$buffer = preg_replace($robotsPattern, '', $buffer);
				}
			}

			return $buffer;
		}

		$titlePattern = '/<title>(.*)<\/title>/Usmi';
		$h1Pattern = '/<h1(.*)>(.*)<\/h1>/Usmi';
		$descPattern = '/<meta[^>]*?name=[\'"]description[\'"](.*)>/Usmi';

		if ($optionMeta == 'Y') {
			if (preg_match($titlePattern, $buffer, $titleMatch) && !empty($this->data['TITLE'])) {
				if ($titleMatch[1] != $this->data['TITLE']) {
					$buffer = preg_replace($titlePattern, '<title>'.$this->data['TITLE'].'</title>', $buffer);
				}
			}

			if (!empty($this->data['DESCRIPTION'])) {

				$description = $this->data['DESCRIPTION'];

				if (preg_match($descPattern, $buffer, $descMatch)) {

					$buffer = preg_replace(
						$descPattern,
						'<meta name="description" content="'.$description.'" />', $buffer
					);

				} else {

					$buffer = preg_replace(
						'/<\/head>/i',
						'<meta name="description" content="'.$description.'" /></head>',
						$buffer
					);
				}
			}
		}

		if ($optionRobots == 'Y' && !empty($this->data['ROBOTS'])) {
			if (preg_match($robotsPattern, $buffer, $descMatch)) {
				if (!preg_match('/'.$this->data['ROBOTS'].'/ui', $descMatch[0])) {
					$buffer = preg_replace(
						$robotsPattern,
						'<meta name="robots" content="'.$this->data['ROBOTS'].'" />', $buffer
					);
				}
			} else {
                $buffer = preg_replace(
                    '/(<\/head>[^\'"])/U',
                    '<meta name="robots" content="'.$this->data['ROBOTS'].'" />$1',
                    $buffer,
                    1
                );
			}
		} elseif ($optionRobots != 'Y' && $optionRobotsAll == 'Y') {
			if (preg_match($robotsPattern, $buffer, $descMatch))
				$buffer = preg_replace($robotsPattern, '', $buffer);
		}

		if (
			preg_match($h1Pattern, $buffer, $titleMatch) &&
			!empty($this->data['H1']) &&
			$optionH1 == 'Y'
		) {
			$buffer = preg_replace($h1Pattern, '<h1$1>'.$this->data['H1'].'</h1>', $buffer);
		}

		return $buffer;
	}
}
