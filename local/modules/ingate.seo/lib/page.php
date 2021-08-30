<?
namespace Ingate\Seo;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Ingate\Seo\Tools;

class PageTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ingate_seo';
	}

	public static function getUfId()
	{
		return 'INGATE_SEO';
	}

	public static function getMap()
	{
		return array(
			//ID
			new Entity\IntegerField(
				'ID',
				array(
					'primary' => true,
					'autocomplete' => true
				)
			),
			//Active
			new Entity\StringField(
				'ACTIVE',
				array(
					'default_value' => 'Y',
				)
			),
			//Date update
			new Entity\DatetimeField(
				'TIMESTAMP_X',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			//Date create
			new Entity\DatetimeField(
				'DATE_CREATE',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			//Url
			new Entity\TextField(
				'URL',
				array(
					'required' => true,
				)
			),
			//title
			new Entity\StringField(
				'TITLE',
				array(
					'default_value' => '',
				)
			),
			//Description
			new Entity\TextField(
				'DESCRIPTION',
				array(
					'default_value' => '',
				)
			),

			//H1
			new Entity\StringField(
				'H1',
				array(
					'default_value' => '',
					'validation' => function() {
						return array(
							new Entity\Validator\Length(null, 255)
						);
					}
				)
			),
			//Meta robots
			new Entity\StringField(
				'ROBOTS',
				array(
					'default_value' => 'index,follow',
					'validation' => function() {
						return array(
							new Entity\Validator\Length(null, 255)
						);
					}
				)
			),
			//Site id
			new Entity\StringField(
				'SITE_ID',
				array(
					'default_value' => '',
				)
			),
		);
	}

	/**
	 * Таблица сравнения для импорта и экспорта
	 * @return array ключ - поле в таблице, значение - название столбца в файле
	 */
	public static function getCompareForCSV()
	{
		return array(
			'URL' => 'URL',
			'TITLE' => 'TITLE',
			'DESCRIPTION' => 'DESCRIPTION',
			'H1' => 'H1',
			'ROBOTS' => 'ROBOTS',
		);
	}

	public static function onBeforeAdd(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		$arMod = array();

		if (!empty($data['TITLE'])) {
			$arMod['TITLE'] = Tools::convertEmoji($data['TITLE']);
		}
		if (!empty($data['DESCRIPTION'])) {
			$arMod['DESCRIPTION'] = Tools::convertEmoji($data['DESCRIPTION']);
		}
		if (!empty($data['H1'])) {
			$arMod['H1'] = Tools::convertEmoji($data['H1']);
		}

		if (empty($data['SITE_ID'])) {
			$arMod['SITE_ID'] = Tools::getSiteId($data['URL']);
		}

		if (preg_match('/(%20|\s)/iu', $data['URL'])) {
			$arMod['URL'] = preg_replace('/(%20|\s)/iu', '+', $data['URL']);
		}

		$result->modifyFields($arMod);

		return $result;
	}

	public static function onBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		$result->modifyFields(
			array(
				'TIMESTAMP_X' => new Main\Type\DateTime(),
				'URL' => preg_replace('/(%20|\s)/iu', '+', $data['URL']),
			)
		);

		return $result;
	}
}