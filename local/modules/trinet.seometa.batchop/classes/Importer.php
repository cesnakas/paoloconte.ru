<?php
/**
 * Created by PhpStorm.
 * User: maus
 */

namespace Trinet\Seometa\Batchop;


use Exception;
use InvalidArgumentException;

class Importer
{
	/**
	 * @var Report
	 */
	private $report;
	
	/**
	 * @var xlsxReader
	 */
	private $xlsx;
	
	public function __construct()
	{
		$this->report = new Report();
	}

    /**
     * Читает эксельку
     * @param string $fname
     * @param int $skipRows
     * @param int $sheetNumber
     * @return bool успешна ли загрузка
     */
	public function loadXlsx($fname, $skipRows = 1, $sheetNumber = 1)
	{
		if (!is_string($fname)) {
			throw new InvalidArgumentException('неправильный $fname: ' . var_export($fname, 1));
		}
		if ($fname == '') {
			throw new InvalidArgumentException('Файл для импорта не указан');
		}
		
		$xlsx = new xlsxReader($fname);
		// оптимизируем геометирию
		$options = Options::getInstance();
		
		$skipRows = max(1, intval($skipRows) + 1);
		
		$linkedColNumbers = [];
		foreach ( array_keys($options::COLUMNS) as $prop ) {
			$linkedColNumbers[] = $options->$prop;
		}
		
		$xlsx->setSheetGeometry($skipRows, $xlsx::ROW_MAX, 0, max($linkedColNumbers) );
		
		$error = $xlsx->processfile([$sheetNumber]);
		if ($error !== '') {
			$this->report->addError($error);
			return false;
		}
		$this->xlsx = $xlsx;
		return true;
	}

    private function getPathFromUrl( $url )
    {
        if ( strpos($url, 'http') !== 0 ) {
            // это не url
            return '';
        }
        $urlParts = parse_url($url);
        return is_array($urlParts) ? $urlParts['path'] : '';
	}
	
	/**
	 * @return int количество строк из файла
	 * @throws Exception
	 */
	public function processXlsx()
	{
		if (!is_object($this->xlsx)) {
			throw new Exception("Файл Excel не загружен");
		}
		
		$record = new Record();
		
		$o = Options::getInstance();
		
		$total = 0;
		/*
		 * возможны дубликаты
		 */
		$chpuPathList = [];
		$realPathList = [];

		foreach ($this->xlsx->getSheets() as $sheet) {
			foreach ($sheet as $rowNumber => $row) {
                $record->title = trim($row[$o->colTitle]);
                $record->description = trim($row[$o->colDescription]);
                $record->header = trim($row[$o->colHeader]);
                $record->crumb = trim($row[$o->colCrumb]);

                if ( !$record->capableForAdd() ) {
					// бессмысленно
                    $this->report->skipped[] = $rowNumber;
					continue;
				}

                // @TODO сделать поддержку не-чпу случая
                //$filterType = $o->getFilterType();
                $realPath = $this->getPathFromUrl( $row[$o->colRealUrl] );
				if ( !empty($realPath) && array_key_exists($realPath, $realPathList) ) {
                    $this->report->addError("Строка {$rowNumber} дубликат другой строки (колонка {$o->colRealUrl}).");
                    $this->report->skipped[] = $rowNumber;
                    continue;
                }

                $sectionPath = ($o->colSectionUrl && !empty($row[$o->colSectionUrl]))
                    ? $this->getPathFromUrl( $row[$o->colSectionUrl] )
                    : '';

                $chpuPath = '';

                try
				{
                    $parsed = false;
				    /*
                     * Стратегии поиска:
                     * 1) если работаем с добавлением ЧПУ, то можно быстро искать по ЧПУ адресу или по реальному.
                     * 2) в ином случае надо искать через парсинг
                     */
                    if ( $o->withChpu() ) {
                        $chpuPath = $this->getPathFromUrl( $row[$o->colChpuUrl] );
                        if ( empty($chpuPath) ) {
                            $this->report->addError("Ошибка в колонке {$o->colChpuUrl} в строке {$rowNumber}.");
                            continue;
                        }
                        if ( array_key_exists($chpuPath, $chpuPathList) ) {
                            $this->report->addError("Строка {$rowNumber} дубликат другой строки (колонка {$o->colChpuUrl}).");
                            $this->report->skipped[] = $rowNumber;
                            continue;
                        }

                        $entity = Helper::getEntityByUrl($chpuPath);
                    }

                    if ( empty($entity) && !empty($realPath) ) {
                        $entity = Helper::getEntityByRealUrl($realPath);

                        if ( empty($entity) ) {
                            $entityList = Helper::getEntityByParsing($realPath);
                            if ( count($entityList) > 1 ) {
                                throw new Exception("при поиске через разбор нашлось много вариантов");
                            } elseif ( count($entityList) === 1 ) {
                                $entity = reset($entityList);
                            }
                            $parsed = true;
                        }
                    }

                    if ( empty($entity) && $sectionPath !== '' && $row[ $o->colPropertyName ] && $row[ $o->colPropertyValue ] ) {
                        /*
                         * последний способ: это поискать через раздел
                         */
                        $propertyName = trim( $row[ $o->colPropertyName ] );
                        if ( empty($propertyName) ) {
                            throw new Exception("Название свойства не указано");
                        }
                        $propertyValue = trim( $row[ $o->colPropertyValue ] );
                        if ( empty($propertyValue) ) {
                            throw new Exception("Значение свойства не указано");
                        }
                        $entityList = Helper::getEntityBySectionPropertyValue($sectionPath, $propertyName, $propertyValue);
                        if ( count($entityList) > 1 ) {
                            throw new Exception("при поиске через разбор (раздел, свойство, значение) нашлось много вариантов");
                        } elseif ( count($entityList) === 1 ) {
                            $entity = reset($entityList);
                        }
                        $parsed = true;

                    }

					if (empty($entity)) {
						if ( !$record->capableForAdd() ) {
                            throw new Exception("пуcтой title или h1");
                        }

                        try {
                            $parser = Helper::getParser();
                            if ( !$parsed ) {
                                $parser->parse($realPath);
                            }

                            $status = Helper::addEntity($record, $parser, $chpuPath);
                            if ( !is_object($status) ) {
                                throw new Exception("системный сбой при добавлении записи");
                            }
                            if ( !$status->isSuccess() ) {
                                throw new Exception( "<br>&nbsp;&nbsp;&nbsp;&nbsp;".implode('<br>&nbsp;&nbsp;&nbsp;&nbsp;', $status->getErrorMessages()) );
                            }
                            if ( $o->withChpu() ) {
                                Helper::addChpu( $status->getId() );
                            }

                            $this->report->added[] = $rowNumber;

                        } catch ( Exception $e ) {
                            $this->report->addError("Ошибка добавления строки {$rowNumber}: ".$e->getMessage() );
                        }
					} else {
                        $metaStatus = 'skipped';
						if (Helper::diff( $record, $entity )) {
							$status = Helper::updateEntity( $record, $entity );
							if ( is_object($status) ) {
								if ( $status->isSuccess() ) {
                                    $metaStatus = 'updated';
								} else {
                                    $metaStatus = 'failed';
									$this->report->addError("Ошибка обновления строки {$rowNumber}: <br>&nbsp;&nbsp;&nbsp;&nbsp;".implode('<br>&nbsp;&nbsp;&nbsp;&nbsp;', $status->getErrorMessages()) );
								}
							} else {
                                $metaStatus = 'failed';
								$this->report->addError("Ошибка обновления строки {$rowNumber}.");
							}
						}

						$chpuStatus = 'skipped';
						if ($o->withChpu() && $chpuPath !=='' ) {
						    // проверим, привязан ли ЧПУ адрес  к записи
                            $chpuData = Helper::getChpuByCondition( $entity['ID'] );
                            /*
                             * возможно 2 варианта:
                             * 1)ЧПУ не было ( например, из-за отсуствия ассортимента)
                             * 2) ЧПУ изменился
                             * @TODO второй случай пока не делаем
                             */
                            if ( empty($chpuData) ) {
                                try {
                                    if ( strcmp($entity['META']['TEMPLATE_NEW_URL'], $chpuPath) ) {
                                        $status = Helper::updateEntityChpu($chpuPath, $entity );
                                        if ( is_object($status) ) {
                                            if ( $status->isSuccess() ) {
                                                $metaStatus = 'updated';
                                            } else {
                                                throw new Exception( "<br>&nbsp;&nbsp;&nbsp;&nbsp;".implode('<br>&nbsp;&nbsp;&nbsp;&nbsp;', $status->getErrorMessages()) );
                                            }
                                        } else {
                                            throw new Exception("Не обновилсь поле для URL");
                                        }
                                    }
                                    Helper::addChpu( $entity['ID'] );
                                    $chpuStatus = 'updated';
                                } catch ( Exception $e ) {
                                    $chpuStatus = 'failed';
                                    $this->report->addError( "Ошибка обновления строки {$rowNumber}: ".$e->getMessage() );
                                }
                            }
                        }

                        if ( $metaStatus == 'updated' || $chpuStatus == 'updated' ) {
                            $this->report->updated[] = $rowNumber;
                        } elseif( $metaStatus == 'skipped' && $chpuStatus == 'skipped' ) {
                            $this->report->skipped[] = $rowNumber;
                        }
                        // во всех остальных случаях один из статусов == failed и есть сообщение об ошибке

					}
				} catch ( \Exception $e ) {
					$this->report->addError( "ошибка обработки строки {$rowNumber}: ". $e->getMessage() );
					$this->report->skipped[] = $rowNumber;
				}
			}
			$total += count($sheet);
		}

		return $total;
	}
	
	public function destroyXlsx()
	{
		$this->xlsx = null;
	}
	
	/**
	 * @return Report
	 */
	public function getReport()
	{
		return $this->report;
	}
	
}