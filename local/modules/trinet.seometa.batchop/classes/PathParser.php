<?php
/**
 * Created by PhpStorm.
 * User: maus <mausglov@yandex.ru>
 * Date: 21.11.16
 * Time: 13:49
 */

namespace Trinet\Seometa\Batchop;


use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Exception;

/**
 * Class PathParser
 * @package Trinet\Seometa\Batchop
 *
 * Насчет разбора url по '-is-', '-or-', '-from-', '-to-' :
 * в принципе, сейчас не особо правильно. Списковые свойства надо проверять только на  '-is-' и '-or-'
 * а диапазонные свойства надо проверять на тип.
 * Но пока сойдет и так
 */
class PathParser
{
	private $properties = [];
	private $enabledFastParse = true;
	private $sectionId = 0;
	private $iblockId = 0;
	private $conditions = [];
	private $propertyIds = [];
	private $propertyNameMap = [];

	/**
	 * PathParser constructor.
	 */
	public function __construct()
	{
		$options = Options::getInstance();
		$this->iblockId = $options->iblockId;
		
		$this->loadProperties();
	}
	
	/**
	 * разбирает строку фильтра в группу условий.
	 *
	 * @param string $path
	 * @throws Exception
	 */
	public function parseFilter($path)
	{
		/*
		 * по объективным причинам у нас :
		 * 1) корневая группа условий, объединённых по AND
		 * 2) её дети - это либо единственное значение, либо группа значений, объединённых по OR
		 *
		 *
		 */
		$segments = array_filter(explode('/', $path));
		$last = end($segments);
		if ( $last === 'apply' ) {
			array_pop($segments);
		}
		$last = end($segments);
		if ( $last === 'clear' ) {
		    throw new ArgumentException("Ошибочный путь {$path} - указан сброс фильтра");
		}

		/*
		 * я смотрел в компонент смарт-фильтра битрикса: по сути, нет кода, который бы разбирал URI в фильтр.
		 * там сразу идёт поиск среди товаров, насколько я понял ( другими словами, нельзя разобрать URI,
		 * если сейчас нет подходящих товаров). Плюс код этот - в компоненте, а не классах, так что
		 * вряд ли можно его переиспользовать в админке.
		 */
		if ( count($segments) ) {
			$this->conditions = [
				'CLASS_ID' => 'CondGroup',
				'DATA' => ['All' => 'AND', 'True' => 'True'],
				'CHILDREN' => [],
			];
			foreach ($segments as $segment) {
				if ( $this->enabledFastParse )
				{
					$this->fastParseSegment($segment);
				} else {
					$this->slowParseSegment($segment);
				}
			}
		}
	}

    /**
     *
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @throws Exception
     */
	public function initConditionsByPropertyValue($propertyName, $propertyValue)
    {
        if ( !array_key_exists($propertyName, $this->propertyNameMap) ) {
            throw new Exception("Свойство с именем {$propertyName} не найдено");
        }
        $property = $this->propertyNameMap[ $propertyName ];

        $node = $this->singleValueToNode($property, $this->getValueByOriginal($property, $propertyValue));

        $this->conditions = [
            'CLASS_ID' => 'CondGroup',
            'DATA' => ['All' => 'AND', 'True' => 'True'],
            'CHILDREN' => [$node],
        ];
        $this->propertyIds[] = $property['id'];
	}
	
	/**
	 * ,быстрый разбор фрагмента url
	 * практически, у нас только два варианта фрагмента: с одним значением и с несколькими
	 *
	 * @param $segment
	 * @throws Exception
	 */
	private function fastParseSegment( $segment )
	{
		$parts = preg_split("/-(from|to|is)-/", $segment, -1, PREG_SPLIT_DELIM_CAPTURE);
		if ( !is_array($parts) || count($parts) < 2 ) {
			throw new Exception("формат сегмента {$segment} не соответствует фильтру.");
		}
		$propCode = array_shift($parts);
		
		if ( !array_key_exists($propCode, $this->properties) )
		{
			throw new Exception("свойство с кодом {$propCode} не найдено.");
		}
		$property = $this->properties[ $propCode ];
		if ( !in_array($property['id'], $this->propertyIds) )
		{
			$this->propertyIds[] = $property['id'];
		}
		
		$operation = reset($parts);
		switch ( $operation )
		{
			case 'is':
				$this->processSegmentValues($property, $parts[1]);
				break;
			
			case 'from':
			case 'to':
				if ( (count($parts) % 2) ) {
					// обязательно четное число частей
					throw new Exception("Сегмент {$segment} не соответствует формату диапазона.");
				}
				$this->processRangeValues($property, $parts);
				break;
		}
	}
	
	/**
	 * Для обработки диапазонов.
	 *
	 * @param array $property
	 * @param array $parts
	 */
	public function processRangeValues($property, $parts)
	{
		static $logicMap = array('from' => 'EqGr', 'to' => 'EqLs',);
		
		while( count($parts) ) {
			$operation = array_shift($parts);
			$value = array_shift($parts);
			
			$node = $this->singleValueToNode( $property, $value, $logicMap[ $operation ] );
			$this->conditions['CHILDREN'][] = $node;
		}
	}
	
	/**
	 * @param $part
	 * @throws Exception
	 */
	private function slowParseSegment($part)
	{
		throw new Exception("Медленный разбор фильтра не реализован.");
	}

	public function reset()
	{
		$this->sectionId = 0;
		$this->conditions = [];
		$this->propertyIds = [];
	}
	
	/**
	 * @return int
	 */
	public function getSectionId()
	{
		return $this->sectionId;
	}
	
	/**
	 * @return array
	 */
	public function getPropertyIds()
	{
		return $this->propertyIds;
	}
	
	/**
	 * @return array
	 */
	public function getConditions()
	{
		return $this->conditions;
	}
	
	/**
	 * @param string $path
	 * @throws Exception
	 */
	public function parse($path)
	{
		$this->reset();
		// надо распарсить путь в раздел
        /*
         * @TODO учитывать хотя бы 2 типа фильтра - bitrix_chpu и bitrix_not_chpu
         * bitrix/modules/sotbit.seometa/classes/general/seometa.php:19
         */
        list($sectionPath, $filterPath) = explode('/filter/', $path);

		// @TODO исправить для случая, если код раздела неуникален
		$this->sectionId = $this->findSectionId($sectionPath);
		
		if ( !$this->sectionId ) {
			throw new Exception("Не найден раздел для '{$sectionPath}'.");
		}
		$this->parseFilter($filterPath);
	}

	public function setSectionIdByPath( $sectionPath )
    {
        // режем конечный слеш, для однообразия
        $path = preg_replace('#/$#', '', $sectionPath);
        // @TODO исправить для случая, если код раздела неуникален
        $this->sectionId = $this->findSectionId( $path );
        if ( !$this->sectionId ) {
            throw new Exception("Не найден раздел для '{$sectionPath}'.");
        }
    }

    /**
     * @param string $path та часть url, которая относится к разделу
     * @return int
     * @throws Exception
     */
	private function findSectionId($path)
	{
		static $select = ['ID', 'IBLOCK_ID'];
		static $nav = ['nTopCount' => 1];
		static $cache = [];
		if (!array_key_exists($path, $cache)) {
			$parts = explode('/', $path);
			$sectionCode = end($parts);
			if ( empty($sectionCode) ) {
                throw new Exception("Неверный формат пути: '{$path}'.");
            }
			$filter = [
				'IBLOCK_ID' => $this->iblockId,
				'=CODE' => $sectionCode,
			];

			$rs = \CIBlockSection::GetList([], $filter, false, $select, $nav);
			if ($row = $rs->Fetch()) {
				$cache[$path] = (int)$row['ID'];
			} else {
				$cache[$path] = 0;
			}
		}
		return $cache[$path];
	}

    /**
     * @throws ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
	private function loadProperties()
    {
        $this->loadPropertiesForIblock($this->iblockId);
        $info = \CCatalog::GetByIDExt($this->iblockId );
        if ( is_array($info) && $info['OFFERS_IBLOCK_ID'] ) {
            $this->loadPropertiesForIblock( $info['OFFERS_IBLOCK_ID'] );
        }
    }

    /**
     * @param int $iblockId
     * @throws ArgumentException
     */
	private function loadPropertiesForIblock( $iblockId )
	{
		/*
		 * @FIXME решить проблему с дубликатами ( свойства с одинаковым кодом )
		 * @FIXME решить проблему со свойствами торговых предложений
		 */
		$options = Options::getInstance();

	    $filter = ['IBLOCK_ID' => $iblockId,];
	    $propData = PropertyTable::getList( ['filter' => $filter,])->fetchAll();
		foreach ($propData as $row) {
            $id = (int) $row['ID'];
		    if ( array_key_exists($row['NAME'], $options->propertyMap) && $id != $options->propertyMap[ $row['NAME'] ] ) {
		        continue;
            }
			$key = trim(ToLower($row['CODE']));
			if ($key === '' || $row['CODE'] == 'CML2_LINK' || $row['PROPERTY_TYPE'] == 'F' ) {
				continue;
			}
			
			$this->checkFastParse($key);
			
			$prop = [
				'id' => (int)$row['ID'],
				'type' => $row['PROPERTY_TYPE'],
                'iblock_id' => $iblockId,
                'name' => $row['NAME'],
			];

			switch ( $row['PROPERTY_TYPE']  )
			{
				case 'L':
					$prop['values'] = [];
					$prop['orig_values'] = [];

					$valData = PropertyEnumerationTable::getList( ['filter' => ['PROPERTY_ID' => $prop['id'],],])->fetchAll();
					/*
					 * борьба с дубликатами
					 * @TODO неоптимально: мы могли бы отсеивать только дубликаты,
					 * но для простоты будем работать со всеми значениями
					 * Получается немного абсурдно: если по данному свойству нет дубликатов,
					 * то можно создать запись даже при отсутствии товара.
					 * А если дубликат есть - то запись будет только при наличии товара
					 */
                    $propVariantMap = [];
                    $valueMap = [];
                    $hasDuplicate = false;
					foreach ($valData as $propVariant) {
                        $propVariant['VALUE'] = trim( $propVariant['VALUE'] );
                        $propVariant['ID'] = (int) $propVariant['ID'];
                        $propVariantMap[ $propVariant['ID'] ] = $propVariant;
                        if ( !$hasDuplicate && array_key_exists($propVariant['VALUE'], $valueMap) ) {
                            $hasDuplicate = true;
                        } else {
                            $valueMap[ $propVariant['VALUE'] ] = $propVariant['ID'];
                        }

                    }

					if ( $hasDuplicate ) {
					    // ищем элементы
                        $sql = "SELECT `VALUE`, COUNT(ID) as `CNT` FROM `b_iblock_element_property`
                          WHERE `IBLOCK_PROPERTY_ID` = {$prop['id']} AND `VALUE` IN (".implode(',', array_keys($propVariantMap)).")
                          GROUP BY `VALUE`";
                        $rs = $GLOBALS['DB']->Query($sql);
                        $goodVariants = [];
                        while ($rowGood = $rs->Fetch() ) {
                            $id = (int)$rowGood['VALUE'];
                            $goodVariants[$id] = $id;
                        }
                        $propVariantMap = array_intersect_key($propVariantMap, $goodVariants);
                        unset($goodVariants, $rs);
                    }
					/*
					 * в итоге у нас остались случаи дубликатов, когда есть элементы,
					 * привязанные к разным значениям-дублям. Может оказаться, что товары,
					 * связанные с одним из вариантов дубля, неактивны.
					 * Но более качественная проверка слишком затратна по ресурсам.
					 */

					foreach ($propVariantMap as $propVariant) {
                        $prop['orig_values'][ $propVariant['VALUE'] ] = $propVariant['ID'];
                        $variantKey = trim( $propVariant['XML_ID'] );
						if ( $variantKey === '' ) {

                            $variantKey = Helper::translit( $propVariant['VALUE'] );
                            if ( $variantKey == '-' && strlen($propVariant['VALUE']) > 1 ) {
                                $variantKey = $propVariant['VALUE'];
                            }
                        }

						if ($variantKey === '') {
							continue;
						}
						$this->checkFastParse($variantKey);

						$prop['values'][$variantKey] = $propVariant['ID'];
					}

					if (empty($prop['values'])) {
						// списковое свойство без значений для фильтра непригодно
						continue;
					}
					break;
				
				case 'S':
				    // S- справочник, привязка к другому инфоблоку

                    $hlMap = [];
                    $hlValues = [];
                    // Если это HL-инфоблок, ситуация усложняется
                    if ( !$row['LINK_IBLOCK_ID'] && $row['USER_TYPE'] == 'directory' )
                    {
                        $settings = unserialize($row['USER_TYPE_SETTINGS']);
                        $hlValues = Helper::getHlibItemsByTable($settings['TABLE_NAME']);
                        foreach ($hlValues as $hlKey => $hlValue) {
                            $hlMap[ $hlValue['UF_XML_ID'] ] = $hlKey;
                        }
                    }

					$prop['values'] = [];
					$prop['orig_values'] = [];
					$prop['crc_values'] = [];
					$prop['hl_values'] = [];
					$propKey = 'PROPERTY_'.$row['ID'];
					$filter = array('IBLOCK_ID'=> $iblockId, '!'.$propKey  => false, );
					$rs = \CIBlockElement::GetList( array(), $filter, array($propKey) );
					$propKey .= '_VALUE';
					while( $variantRow = $rs->Fetch() )
					{
                        $value = trim( $variantRow[$propKey] );
						if ( $value !== '' ) {
						    if ( !empty($hlMap) && !array_key_exists($value, $hlMap) )
                            {
                                continue;
                            }

                            $prop['orig_values'][ $value ] = $value;

							$variantKey = Helper::translit( $value );
                            if ( $variantKey == '-' && strlen($value) > 1 ) {
                                $variantKey = $value;
                            }
							$prop['values'][$variantKey] = $value;
                            if ( !empty($hlMap) ) {
                                /*
                                 * а вот тут перевернем логику.
                                 * CRC-значения пока не нужны, но я их оставлю, так как
                                 * они используются в ссылках smart-фильтра: arrFilter_249_512483465=Y
                                 *
                                 * а вот со справочниками модуль сотбит работает иначе:
                                 * искомое значение - это ID записи из таблицы
                                 */
                                $prop['crc_values'][$variantKey] = abs(crc32($value));
                                $hlId = $hlMap[$value];
                                $hlValue = $hlValues[$hlId]['UF_NAME'];
                                $prop['hl_values'][$hlValue] = $hlId;
                            }
						}
					}
					break;
			}
			$this->properties[$key] = $prop;
			$this->propertyNameMap[ $prop['name'] ] =& $this->properties[$key];
		}
	}
	
	/**
	 * @param $property
	 * @param $v
	 * @return int|string
	 * @throws Exception
	 */
	private function getValue($property, $v)
	{
		switch ($property['type'])
		{
			case 'L';
			case 'S';
				if (array_key_exists($v, $property['values'])) {
					$value = $property['values'][$v];
				} else {
					throw new Exception("Для свойства {$property['id']} не найдено значение с кодом {$v}.");
				}
				break;
			
			default:
				throw new Exception("не реализована поддержка типов для свойства {$property['id']}");
		}
		
		return $value;
	}

	/**
	 * Эта функция ищет по исходным значениям, а не транслитерированным ( которые в урлах )
     *
     * @param $property
	 * @param $v
	 * @return int|string
	 * @throws Exception
	 */
	private function getValueByOriginal($property, $v)
	{
		switch ($property['type'])
		{
			case 'L';
			case 'S';
			    $found = false;
				if (array_key_exists($v, $property['orig_values'])) {
					$value = $property['orig_values'][$v];
					$found = true;
				} elseif( !empty($property['hl_values']) && array_key_exists($v, $property['hl_values']) ) {
				    $value = $property['hl_values'][$v];
                    $found = true;
                }

				if ( !$found ) {
					throw new Exception("Для свойства {$property['id']} не найдено значение {$v}.");
				}
				break;

			default:
				throw new Exception("не реализована поддержка типов для свойства {$property['id']}");
		}

		return $value;
	}

	/**
	 * @param array $property
	 * @param string|int $value
	 * @param string $logic
	 * @return array
	 */
	private function singleValueToNode($property, $value, $logic = 'Equal')
	{
	    return  [
			'CLASS_ID' => "CondIBProp:{$property['iblock_id']}:{$property['id']}",
			'DATA' => ['logic' => $logic, 'value' => $value,]
		];
	}

    /**
     * @param array $property
     * @param string $str
     * @throws Exception
     */
	private function processSegmentValues($property, $str)
	{
		$segmentValues = explode('-or-', $str);
		if (count($segmentValues) == 1) {
			$node = $this->singleValueToNode( $property, $this->getValue($property, reset($segmentValues) ) );
		} else {
			$node = [
				'CLASS_ID' => 'CondGroup',
				'DATA' => ['All' => 'OR', 'True' => 'True'],
				'CHILDREN' => [],
			];
			foreach ( $segmentValues as $value ) {
				$node['CHILDREN'][] = $this->singleValueToNode( $property, $this->getValue($property, $value) );
			}
		}
		$this->conditions['CHILDREN'][] = $node;
	}
	
	/**
	 * Отключает быстрый разбор, если ключ или значение содержат в себе разделитель
     *
     * в текущий момент отключение быстрого разбора означает неработоспособность модуля
     *
     * @param $str
	 */
	private function checkFastParse($str)
	{
		if ($this->enabledFastParse && (
				strpos($str, '-is-') !== false
				|| strpos($str, '-from-') !== false
				|| strpos($str, '-to-') !== false
			)
		) {
			// быстрый разбор недопустим
			$this->enabledFastParse = false;
		}
	}
}