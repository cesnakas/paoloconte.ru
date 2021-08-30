<?php
namespace Ingate\Seo;

use Bitrix\Main;
use Bitrix\Main\Entity;

class CanonicalTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ingate_seo_canonical';
	}

	public static function getUfId()
	{
		return 'INGATE_SEO_CANONICAL';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField(
				'ID',
				array(
					'primary' => true,
					'autocomplete' => true
				)
			),
			new Entity\StringField(
				'ACTIVE',
				array(
					'default_value' => 'Y',
				)
			),
			new Entity\DatetimeField(
				'TIMESTAMP_X',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			new Entity\DatetimeField(
				'DATE_CREATE',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			new Entity\TextField(
				'URL',
				array(
					'required' => true,
				)
			),
			new Entity\TextField(
				'CANONICAL',
				array(
					'required' => true,
				)
			),
		);
	}

	public static function onBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		$result->modifyFields(
			array(
				'TIMESTAMP_X' => new Main\Type\DateTime(),
			)
		);

		return $result;
	}

	/**
	 * Таблица сравнения для импорта и экспорта
	 * @return array ключ - поле в таблице, значение - название столбца в файле
	 */
	public static function getCompareForCSV()
	{
		return array(
			"URL" => "URL",
			"CANONICAL" => "CANONICAL",
		);
	}
}