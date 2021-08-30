<?php
/**
 * Created by PhpStorm.
 * User: maus
 */

namespace Trinet\Seometa\Batchop;

use CIBlock;
use Exception;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;

class Helper
{
    private static $serializedFields = ['SITES', 'SECTIONS', 'RULE', 'META',];

    /**
     * Ищет по ЧПУ урлу условие
     *
     * @param string $url
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
	public static function getEntityByUrl( $url )
	{
        $chpuEntry = SeometaUrlTable::getByNewUrl( $url );
		if ( !is_array( $chpuEntry ) ) {
			return array();
		}
		$entity = ConditionTable::getRowById( $chpuEntry['CONDITION_ID'] );
        if ( !is_array( $entity ) ) {
            return array();
        }
        self::unserializeFields($entity);
		return $entity;
	}

    /**
     * Ищет по старому (битриксовому) урлу условие
     *
     * @param string $url
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getEntityByRealUrl( $url )
    {
        $chpuEntry = SeometaUrlTable::getByRealUrlGenerate( $url );
        if ( !is_array( $chpuEntry ) ) {
            return array();
        }
        $entity = ConditionTable::getRowById( $chpuEntry['CONDITION_ID'] );
        if ( !is_array( $entity ) ) {
            return array();
        }
        self::unserializeFields($entity);
        return $entity;
    }

    /**
     *
     * @param $path
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws Exception
     */
	public static function getEntityByParsing($path)
    {
        $parser = self::getParser();
        $parser->parse($path);

        return self::getEntityListByParser($parser);
    }

    /**
     * @param string $sectionPath
     * @param string $propertyName
     * @param string $propertyValue
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws Exception
     */
    public static function getEntityBySectionPropertyValue($sectionPath, $propertyName, $propertyValue)
    {
        $parser = self::getParser();
        $parser->reset();
        $parser->setSectionIdByPath( $sectionPath );
        $parser->initConditionsByPropertyValue( $propertyName, $propertyValue );
        return self::getEntityListByParser($parser);
    }


	private static function serializeFields( &$row ) {

	    foreach (self::$serializedFields as $field) {
            if ( is_array($row[$field]) && !empty($row[$field]) ) {
                $row[$field] = serialize($row[$field]);
            }
        }
    }
	
	private static function unserializeFields( &$row ) {

	    foreach (self::$serializedFields as $field) {
            if ( empty($row[$field]) ) {
                $row[$field] = [];
            } elseif( is_string($row[$field]) ) {
                $row[$field] = unserialize($row[$field]);
            }
        }
    }

    /**
     * @param Record $record
     * @param array $entity
     * @return \Bitrix\Main\Entity\UpdateResult
     * @throws \Exception
     */
	public static function updateEntity( Record $record, $entity )
	{
	    $meta = $entity['META'];
		if ( !empty($record->title) ) {
			$meta['ELEMENT_TITLE'] = $record->title;
		}
        if ( !empty($record->header) ) {
			$meta['ELEMENT_PAGE_TITLE'] = $record->header;
		}
        if ( !empty($record->description) ) {
			$meta['ELEMENT_DESCRIPTION'] = $record->description;
		}
        if ( !empty($record->crumb) ) {
			$meta['ELEMENT_BREADCRUMB_TITLE'] = $record->crumb;
		}

		$data = ['META'=> serialize($meta) ];
        $data['DATE_CHANGE'] = new Type\DateTime( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' );
		
		return ConditionTable::update( $entity['ID'] , $data);
	}

    /**
     * @param $chpu
     * @param $entity
     * @return \Bitrix\Main\Entity\UpdateResult
     * @throws Exception
     */
    public static function updateEntityChpu($chpu, $entity)
    {
        $data = ['META'=> $entity['META'] ];
        $data['META']['TEMPLATE_NEW_URL'] = $chpu;
        $data['DATE_CHANGE'] = new Type\DateTime( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' );
        return ConditionTable::update( $entity['ID'] , $data);
	}

    /**
     * @param int $id
     * @throws Exception
     */
	public static function addChpu( $id )
    {
        $writer = \Sotbit\Seometa\Link\ChpuWriter::getWriterForAutogenerator( $id );
        $link = \Sotbit\Seometa\Helper\Link::getInstance();
        $link->Generate( $id, $writer);

        $chpuData = $writer->getData();
        /* @var array $chpuData */

        if ( $chpuData === false || empty( $chpuData ) ) {
            throw new Exception("ЧПУ добавить не удалось");
        }

        self::activateChpu( array_keys( $chpuData ) );
	}
	
	private static function initData()
	{
		static $initData = null;
		if (is_null($initData) ) {
			$options = Options::getInstance();
			
			$initData = array(
                'ACTIVE' => 'Y',
                'SEARCH' => 'Y',
                'SORT' => 100,
                'SITES' => [],
                'TYPE_OF_INFOBLOCK' => 'catalog',
				'INFOBLOCK' => $options->iblockId,
                'NO_INDEX' => 'N',
                'STRONG' => 'Y',
                'CATEGORY_ID' => 0,
                // @TODO добавить поддержку bitrix_not_chpu
                'FILTER_TYPE' => 'bitrix_not_chpu',
			);
			$rs = CIBlock::GetSite($options->iblockId);
			while ( $row = $rs->Fetch() )
			{
				$initData['SITES'][] = $row['SITE_ID'];
			}
		}
		return $initData;
	}

    /**
     * @param Record $record
     * @param PathParser $parser
     * @param string $newUrl
     * @return false|\Bitrix\Main\Entity\AddResult
     * @throws \Bitrix\Main\ObjectException
     * @throws Exception
     */
	public static function addEntity(Record $record, PathParser $parser, $newUrl)
	{
		$data = self::initData();
		// схема ЧПУ хранится в bitrix/modules/iblock/install/components/bitrix/catalog.smart.filter/class.php

		$data['NAME'] = $record->header;
		$data['SECTIONS'] = [$parser->getSectionId()];
		$data['RULE'] = $parser->getConditions();
        $data['DATE_CHANGE'] = new Type\DateTime( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' );
		$meta = [
            'ELEMENT_TITLE' => $record->title,
            'ELEMENT_KEYWORDS' => '',
            'ELEMENT_DESCRIPTION' => $record->description,
            'ELEMENT_PAGE_TITLE' => $record->header,
            'ELEMENT_BREADCRUMB_TITLE' => $record->crumb,
            'TEMPLATE_NEW_URL' => $newUrl,
            'ELEMENT_TOP_DESC' => '',
            'ELEMENT_BOTTOM_DESC' => '',
            'ELEMENT_ADD_DESC' => '',
            'ELEMENT_TOP_DESC_TYPE' => '',
            'ELEMENT_BOTTOM_DESC_TYPE' => '',
            'ELEMENT_ADD_DESC_TYPE' => '',
        ];
		$data['META'] = $meta;
		self::serializeFields($data);

		return ConditionTable::add( $data);
	}

    /**
     * @param $chpuIdList
     * @throws Exception
     */
    public static function activateChpu($chpuIdList)
    {
        $data = ["fields" => ['ACTIVE'=>'Y'], ];
        foreach ($chpuIdList as $id) {
            SeometaUrlTable::update($id, $data);
        }
	}

    public static function getChpuByCondition($conditionId)
    {
        // мы работаем только с одиночными чпу, не шаблонными
        $chpuList = SeometaUrlTable::getByCondition($conditionId);
        return count($chpuList) ? reset($chpuList) : [];
	}
	
	/**
	 * @param Record $record
	 * @param array $entity
	 * @return bool
	 */
	public static function diff(Record $record, $entity)
	{
	    return ( $record->title !== $entity['META']['ELEMENT_TITLE']
		         || $record->header !== $entity['META']['ELEMENT_PAGE_TITLE']
		         || $record->description !== $entity['META']['ELEMENT_DESCRIPTION']
		         || $record->crumb !== $entity['META']['ELEMENT_BREADCRUMB_TITLE']
		);
	}
	
	/**
	 * Транслит средствами Битрикса
	 *
	 * @param string $str
	 * @return string
	 */
	public static function translit( $str )
	{
	    static $params = ['replace_space'=>'-', 'replace_other'=>'-'];
	    return \CUtil::translit( $str, 'ru', $params);
	}

    /**
     * @return null|PathParser
     */
    public static function getParser()
    {
        static $parser = null;
        if (is_null($parser)) {
            $parser = new PathParser();
        }
        return $parser;
    }

    /**
     * Функция принимает на вход подготовленный парсер,
     * на его основе строит фильтр и по этому фильтру ищет в БД
     *
     * @param $parser
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    private static function getEntityListByParser(PathParser $parser): array
    {
        $result = [];
        /**
         * по неизвестной причине ID раздела иногда сериализован как строка, а иногда - как число.
         * Делаем костыль
         */

        $filter = array_intersect_key(self::initData(), ['INFOBLOCK' => 1, 'SITES' => 1,]);
        $filter['SECTIONS'] = [strval($parser->getSectionId())];
        $filter['RULE'] = $parser->getConditions();

        $filterBackup = $filter;

        self::serializeFields($filter);

        $res = ConditionTable::getList(['filter' => $filter,]);

        while ($one = $res->fetch()) {
            self::unserializeFields($one);
            $result[$one['ID']] = $one;
        }

        if (empty($result)) {
            $filter = $filterBackup;
            $filter['SECTIONS'] = [intval($parser->getSectionId())];
            self::serializeFields($filter);

            $res = ConditionTable::getList(['filter' => $filter,]);

            while ($one = $res->fetch()) {
                self::unserializeFields($one);
                $result[$one['ID']] = $one;
            }
        }

        return $result;
    }

    /**
     * получить значения HL инфоблока, зная название таблицы в БД
     * @FIXME пока делаем "в лоб", через базу, и с заточкой под справочник цветов
     *
     * @param string $tablename
     * @return array
     */
    public static function getHlibItemsByTable( $tablename )
    {
        $db = $GLOBALS['DB'];
        /* @var \CDatabase $db */

        $result = [];

        $sql = "SELECT ID, UF_NAME, UF_XML_ID FROM {$tablename}";
        $rs = $db->Query($sql);
        while ($row = $rs->Fetch() ) {
            $row['ID'] = (int)$row['ID'];
            $result[$row['ID']] = $row;
        }
        return $result;
    }

}