<?php
namespace Ingate\Seo;

use Bitrix\Main\Config\Option;

class Link
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;
	const OPTION_MIRROR = INGATE_SEO_OPTION_MIRROR;
	const OPTION_NOFOLLOW = INGATE_SEO_OPTION_NOFOLLOW;
	const OPTION_MIXED = INGATE_SEO_OPTION_MIXED;

	protected static $_instance;

	private $domain;
	private $domainWithoutWWW;

	private $optionMixed;
	private $optionMirror;
	private $optionNofollow;

	protected $enabled = false;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		$this->optionMirror = Option::get(self::MODULE_ID, self::OPTION_MIRROR);
		$this->optionNofollow = Option::get(self::MODULE_ID, self::OPTION_NOFOLLOW);
		$this->optionMixed = Option::get(self::MODULE_ID, self::OPTION_MIXED);

		if (
			($this->optionMirror && $this->optionMirror != 'N') ||
			($this->optionMixed && $this->optionMixed != 'N') ||
			($this->optionNofollow && $this->optionNofollow != 'N')
		) {
			$this->enabled = true;
		}
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public function isEnabled()
	{

		return $this->enabled;

	}

	private function setLinks($matches)
	{
		$url = $matches[0];
		$tag = $matches[1];
		$link = $matches[3];

		//README здесь по сути одно и тоже, не вижу смысла разделять данные опции
		if ($this->optionMixed != 'N' || $this->optionMirror != 'N') {
			if (preg_match('/^(https?:\/\/)(www\.)?([^\/?]*)\/?(.*)?$/iu', $link, $arMatches)) {
				if (
					$arMatches[3] == $this->domain ||
					$arMatches[3] == $this->domainWithoutWWW
				) {
					$url = str_replace($arMatches[0], '/'.$arMatches[4], $url);
				}
			}
		}

		if ($this->optionNofollow && $this->optionNofollow != 'N' && $tag == 'a') {

			$subPattern = ($this->optionNofollow == 'W') ? '(?:.*\.)?' : '';

			if (
				!preg_match('/rel=[\'"].*[\'"]/iu', $url) &&
				preg_match(
					'/(https?:)?\/\/(?!(?:www\.)?'.$subPattern.addcslashes($this->domainWithoutWWW, '.').')/iu',
					$link
				)
			) {
				$url = str_replace('<a', '<a rel="nofollow"', $url);
			}
		}

		return $url;
	}

	public function setBuffer($buffer)
	{

		if ($this->isEnabled()) {

			$this->domain = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getServer()->getServerName();
			$this->domainWithoutWWW = preg_replace('/www\./iu', '', $this->domain);

			$patternTag = '([\w]*)';
			$patternAttr = '(href|src)';

			if (
				$this->optionNofollow &&
				$this->optionNofollow != 'N' &&
				$this->optionMixed == 'N' &&
				$this->optionMirror == 'N'
			) {

				$patternTag = '(a)';
				$patternAttr = '(href)';

			}

			$buffer = preg_replace_callback(
				'/<'.$patternTag.'\s[^>]*?'.// <tag...
				$patternAttr.'=[\'"](.*)[\'"]'.//attr="content"
				'.*[\'"]?\s*?>/'.//...>
				'misU',
				'self::setLinks',
				$buffer
			);
		}

		return $buffer;
	}
}
