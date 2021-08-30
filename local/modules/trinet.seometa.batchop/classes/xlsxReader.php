<?php
namespace Trinet\Seometa\Batchop;

use DOMDocument;
use InvalidArgumentException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Simple Excel 2007 reader
 *
 * (c) 2012 Livach Michael <mausglov@yandex.ru>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */
class xlsxReader
{
    /*
    * @FIXME костыль! ограничимся 10 листами
    */
    const SHEETS_MAX = 10;
    const COL_MAX = 26; // 256
    const ROW_MAX = 65536;

    protected $fname = null;

    protected $sheets = array();
    protected $strings = array();

    protected $geometry = array('row' => array(0, self::ROW_MAX), 'col' => array(0, self::COL_MAX));

    function __construct($fname)
    {
        if (is_file($fname) && is_readable($fname)) {
            $this->fname = $fname;
        } else {
            throw new InvalidArgumentException('file not accessible, filename: ' . var_export($fname, 1));
        }
    }

    /**
     * ограничиваем считываемую геометрию, а то куча пустоты
     * @param int $startRow
     * @param int $endRow
     * @param int $startCol
     * @param int $endCol
     */
    public function setSheetGeometry($startRow = 0, $endRow = 0, $startCol = 0, $endCol = 0)
    {
        // на выходе неотрицательные числа не выше заданных пределов, каждый $start* не больше, чем $end*
        $endRow = max(min(intval($endRow), self::ROW_MAX), 0);
        $endCol = max(min(intval($endCol), self::COL_MAX), 0);
        $startRow = min(max(intval($startRow), 0), $endRow);
        $startCol = min(max(intval($startCol), 0), $endCol);
        $this->geometry = array('row' => array($startRow, $endRow), 'col' => array($startCol, $endCol));
    }

    /**
     * @thanks Алексей Рембиш http://habrahabr.ru/users/DonRamon/
     *
     * @param array|int $sheetNumbers . Если значение пусто, то будут выбраны все листы
     * @return string
     */
    public function processfile($sheetNumbers = array())
    {
        if (!is_array($sheetNumbers)) {
            $sheetNumbers = is_int($sheetNumbers) ? array($sheetNumbers) : array();
        }

        $hasSheetNumbers = (count($sheetNumbers) > 0);
        $sheetNumbers = array_unique($sheetNumbers);
        $sheetNumbers = array_filter($sheetNumbers, 'is_int');
        foreach ($sheetNumbers as $k => $v) {
            if ($v > self::SHEETS_MAX) {
                unset($sheetNumbers[$k]);
            }
        }

        if (empty($sheetNumbers)) {
            if ($hasSheetNumbers) {
                return 'wrong sheet numbers';
            } else {
                $sheetNumbers = range(1, self::SHEETS_MAX);
            }
        }

        // Создаёт "реинкарнацию" zip-архива...
        $zip = new ZipArchive;
        // И пытаемся открыть переданный zip-файл
        $result = '';
        $status = $zip->open($this->fname);
        if ($status === true) {
            // В случае успеха ищем в архиве файл с данными
            $contentFile = 'xl/sharedStrings.xml';
            $xml = $this->_readfile($zip, $contentFile);
            if ($xml instanceof SimpleXMLElement) {
                $this->_parseSharedStrings($xml);
            }

            $this->sheets = array();
            foreach ($sheetNumbers as $i) {
                $contentFile = "xl/worksheets/sheet{$i}.xml";
                $xml = $this->_readfile($zip, $contentFile);
                if ($xml instanceof SimpleXMLElement) {
                    $this->_parseSheet($i, $xml);
                } else {
                    // листов больше нет
                    break;
                }
            }
            $zip->close();
        } else {
            $statuses = array(
                ZipArchive::ER_EXISTS => 'File already exists.',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
                ZipArchive::ER_INVAL => 'Invalid argument.',
                ZipArchive::ER_MEMORY => 'Malloc failure.',
                ZipArchive::ER_NOENT => 'No such file.',
                ZipArchive::ER_NOZIP => 'Not a zip archive.',
                ZipArchive::ER_OPEN => 'Can\'t open file.',
                ZipArchive::ER_READ => 'Read error.',
                ZipArchive::ER_SEEK => 'Seek error.',

            );
            $result = $statuses[$status];
        }
        return $result;
    }

    /**
     * @param ZipArchive $zip
     * @param string $contentFile
     *
     * @return bool|SimpleXMLElement
     */
    protected function _readfile(ZipArchive $zip, $contentFile)
    {
	    $result = false;
        if (($index = $zip->locateName($contentFile)) !== false) {
	        
	        $result = simplexml_load_string(
		        $zip->getFromIndex($index),
	            'SimpleXMLElement',
		        LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING
	        );
        }
        return $result;
    }

    /**
     * @param SimpleXMLElement $xml
     */
    protected function _parseSharedStrings(SimpleXMLElement $xml)
    {
        $this->strings = array();
        foreach ($xml->children() as $item) {
            $this->strings[] = self::getNodeHtml($item);
        }
    }

    /**
     * @static
     *
     * @param SimpleXMLElement $node
     *
     * @return string
     */
    public static function getNodeHtml(SimpleXMLElement $node)
    {
        $t = (string)$node->t;
        if ($t === '' && $node->r) {
            foreach ($node->r as $r) {
                $t .= self::getNodeHtml($r);
            }
        }
        if ($node->rPr) {
            $openTags = '';
            $closeTags = '';
            if ($node->rPr->b) {
                $openTags = '<strong>';
                $closeTags = '</strong>';
            }
            if ($node->rPr->i) {
                $openTags .= '<em>';
                $closeTags = '</em>' . $closeTags;
            }

            $t = $openTags . $t . $closeTags;
        }
        return $t;
    }

    public static function getLetters()
    {
        // @FIXME только 26 столбцов из 255
        static $letters = null;
        if (is_null($letters)) {
            $letters = range('A', 'Z');
            array_unshift($letters, '');
            unset($letters[0]);
            $letters = array_flip($letters);
        }
        return $letters;
    }

    /**
     * @param int $i
     * @param SimpleXMLElement $xml
     */
    protected function _parseSheet($i, SimpleXMLElement $xml)
    {
        $this->sheets[$i] = array();
        $rowpos = max(1, $this->geometry['row'][0]);

        // @todo учесть вариант с R1C1 схемой
        $letters = self::getLetters();

        foreach ($xml->sheetData->row as $row) {
            $rowNumber = (int)$row['r'];
            if ($rowNumber < $this->geometry['row'][0]) {
                // просто проматываем
                continue;
            }
            if ($rowNumber <= $this->geometry['row'][1]) {
                // фикс на пропущенные строки
                if ($rowNumber > $rowpos) {
                    for ($k = $rowNumber - $rowpos; $k > 0; $k--) {
                        $this->sheets[$i][$rowpos] = array();
                        $rowpos++;
                    }
                }
                $this->sheets[$i][$rowNumber] = array();
                /* @var SimpleXMLElement $cell */
                foreach ($row as $cell) {
                    $attr = $cell->attributes();
                    $letter = strtoupper(preg_replace('/[^a-z]/i', '', $attr['r']));
                    // @fixme костыль, у меня пока диапазон однобуквенный
                    if (strlen($letter) > 1 || strcmp('Z', $letter) < 0) {
                        break;
                    }

                    $cellNumber = $letters[$letter];
                    if ($cellNumber >= $this->geometry['col'][0] && $cellNumber <= $this->geometry['col'][1]) {
                        $value = isset($cell->v) ? (string)$cell->v : false;
                        $this->sheets[$i][$rowNumber][$cellNumber] = (isset($attr['t']) && $attr['t'] == 's')
                            ? $this->strings[$value] : $value;
                    }
                }
            } else {
                // прошли максимальную строку, выходим
                break;
            }
            $rowpos++;
        }
    }

    /**
     * @return array
     */
    public function getSheets()
    {
        return $this->sheets;
    }

    /**
     * @param $n
     *
     * @return array
     */
    public function getSheet($n)
    {
        return (array_key_exists($n, $this->sheets) ? $this->sheets[$n] : array());
    }

}