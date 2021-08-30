<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact;

/**
 * Class Tools
 * @package Citfact
 */
class Tools
{
    /**
     * @param $var
     * @param bool $stdin
     * @param bool $die
     * @param bool $all
     * @return mixed
     */
    public static function pre($var, $stdin = false, $die = false, $all = false, $hide = false)
    {
        global $USER;
        if ($USER->IsAdmin() || $all) {
            if ($stdin) {
                return print_r($var, $stdin);
            } ?>
            <pre data-id="CitfactToolsPre"
                 style="<?= ($hide) ? 'display: none' : '/*display: none*/'; ?>;"><? print_r($var, $stdin) ?></pre>
            <?
        }
        if ($die) die;
    }

    /**
     * @param int $number
     * @param array $titles
     *
     * @param bool $onlytitles
     * @return string word
     */
    public static function declension($number, $titles, $onlytitles = false)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $pref = $number . ' ';
        if ($onlytitles === true) {
            $pref = '';
        }
        return $pref . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @return array
     */
    public static function datediff($date_from, $date_to)
    {
        $date_from = new \DateTime($date_from);
        $date_to = new \DateTime($date_to);
        $interval = $date_from->diff($date_to);
        $arReturn = array(
            'days' => $interval->days,
            'm' => $interval->m,
            'd' => $interval->d,
            'invert' => $interval->invert,
        );

        return $arReturn;
    }

    /**
     * Сортирует массив картинок по убыванию номера ракурса
     * значение N    описание ракурса
     *    0004        40 градусов - базовый
     *    0018        180 градусов
     *    0027        270 градусов - сзади
     *    2            вид сверху
     *    3            вид подошвы
     *    4            вид изнутри (опция)
     * @param Array $Arr
     * @return array|null
     */
    public function SortPhotoArr($Arr)
    {
        if (is_array($Arr) && !empty($Arr))
            for ($i = 0; $i < count($Arr); $i++) {
                for ($j = $i; $j < count($Arr); $j++) {
                    if ($j > $i) {
                        $rest[0] = substr($Arr[$j], strrpos($Arr[$j], '_') + 1);
                        $rest[1] = substr($Arr[$i], strrpos($Arr[$i], '_') + 1);
                        if ($rest[0] < $rest[1]) {
                            $hold = $Arr[$i];
                            $Arr[$i] = $Arr[$j];
                            $Arr[$j] = $hold;
                        }
                    }
                }
            }
        return $Arr;
    }

    /**
     * Получаем список всех файлов картинок по указаному пути
     * @param string $path
     * @return array|null
     */
    public function getImageFiles($path)
    {

        if (empty($path)) return null;
        $dir = array();
        $el = new \CFile();

        $dir_raw = scandir($path);
        foreach ($dir_raw as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$el->IsImage($item)) continue;
            $dir[] = $item;
        }

        return Tools::SortPhotoArr($dir);
    }


    /**
     * @param $user_id
     * @return mixed
     */
    public static function getUserInfo($user_id)
    {
        $userData = \CUser::getList(($by = 'ID'), ($order = 'desc'), array('ID' => $user_id),
            array('SELECT' => array('UF_*'), 'FIELDS' => array(/*'ID', 'LOGIN', 'EMAIL'*/))
        )
            ->getNext(true, false);

        return $userData;
    }


    /**
     * @param $str
     * @return string
     */
    public static function my_mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }

    /**
     * @param $articul
     * @param $width
     * @param $height
     * @throws \ImagickException
     * неправильно обрабатывает белые фотки, добавил return в начало
     */
    public static function newResize($articul, $width, $height)
    {
        //неправильно обрабатывает белые фотки, пришлось отключит
        return;

        $path = CATALOG_IMG . $articul . CATALOG_IMG_PHOTO;
        $originalImages = static::getImageFiles($_SERVER['DOCUMENT_ROOT'] . $path);

        foreach ($originalImages as $image) {

            $in = new \Imagick($_SERVER['DOCUMENT_ROOT'] . '/' . $path . $image);

            $in->trimImage(20000);

            $in->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, TRUE);
            $in->setImageBackgroundColor("white");

            $w = $in->getImageWidth();
            $h = $in->getImageHeight();

            $off_top = 0;
            $off_left = 0;

            if ($w > $h) {
                $off_top = (($width - $h) / 2) * -1;
            } else {
                $off_left = (($height - $w) / 2) * -1;
            }

            $in->extentImage($width, $height, $off_left, $off_top);

            $in->writeImage($_SERVER["DOCUMENT_ROOT"] . '/upload/resize_cache/catalog/' . $articul . '/' . $width . 'x' . $height . '_' . $image);

        }
    }

    // ID инфоблока по его коду
    public static function getIdIblock($codeIblock)
    {
        $id = 0;
        $iblock = \CIBlock::GetList(array(), array("CODE" => $codeIblock), true);
        while ($ar_res = $iblock->Fetch()) {
            $id = $ar_res['ID'];
        }
        return $id;
    }

    //ID свойства заказа по его коду
    public static function getIdPropertyOrder($codeProperty)
    {
        $id = 0;
        $db_props = \CSaleOrderProps::GetList(
            array(""),
            array("CODE" => $codeProperty),
            false,
            false,
            array());
        while ($props = $db_props->Fetch()) {
            $id = $props["ID"];
        }
        return $id;
    }

    //ID свойства доставки по его XML коду
    public static function getIdDelivery($codeDelivery)
    {
        $id = 0;
        $db_dtype = \CSaleDelivery::GetList(
            array(),
            array(
                "XML_ID" => $codeDelivery
            ),
            false,
            false,
            array()
        );
        while ($delivery = $db_dtype->Fetch()) {
            $id = $delivery["ID"];
        }
        return $id;
    }

    public static function getFilterForBigData()
    {
        $arrFilter = [];
        $arrFilter['!=CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION']] = false;
        $arrFilter['PROPERTY_HAS_PHOTO'] = "Y";
        $arrFilter['>PROPERTY_OFFERS_AMOUNT'] = 0;
        return $arrFilter;
    }

    public static function isDev()
    {
        if (
            !(strpos($_SERVER['HTTP_HOST'], 'testfact') === false) ||
            !(strpos($_SERVER['HOSTNAME'], 'testfact') === false) ||
            !(strpos($_SERVER['HTTP_HOST'], '.dev.') === false)
        ) {
            return true;
        }
        return false;
    }

    public static function getNewPrice($basket, $sum, $sumDiscount)
    {
        $arNewPrice = [];
        $newSum = $prevPrice = 0;
        $coefficient = self::getCoefficient($sum, $sumDiscount);
        foreach ($basket as $basketItem) {
            if ($basketItem->getField("DELAY") == "Y" || $basketItem->getField("CAN_BUY") == "N") {
                continue;
            }

            if (!in_array($basketItem->getField("ID"), $_SESSION["CL_CART_BASKET_IDS"])) {
                continue;
            }
            $price = round($basketItem->getPrice() * $coefficient);
            $arNewPrice[$basketItem->getProductId()] = [
                'PRICE' => $price,
                'QUANTITY' => $basketItem->getQuantity(),
                'SUM' => $price * $basketItem->getQuantity()
            ];
            if ($price > $prevPrice) {
                $maxPrice = ['PRODUCT_ID' => $basketItem->getProductId(), 'PRICE' => $price];
            }
            $prevPrice = $price;
            $newSum += $price * $basketItem->getQuantity();
        }
        //если новая сумма с суммой подарочной карты, не сходятся с изначальной суммой заказа из-за округления
        //то либо прибавим, либо вычтем эту разницу у товара с максимальной ценой
        if (($newSum + $sumDiscount) > $sum) {
            $diff = ($newSum + $sumDiscount) - $sum;
            $arNewPrice[$maxPrice['PRODUCT_ID']]['PRICE'] = $arNewPrice[$maxPrice['PRODUCT_ID']]['PRICE'] - $diff;
            $arNewPrice[$maxPrice['PRODUCT_ID']]['SUM'] = $arNewPrice[$maxPrice['PRODUCT_ID']]['SUM'] - $diff;
        } elseif (($newSum + $sumDiscount) < $sum) {
            $diff = $sum - ($newSum + $sumDiscount);
            $arNewPrice[$maxPrice['PRODUCT_ID']]['PRICE'] = $arNewPrice[$maxPrice['PRODUCT_ID']]['PRICE'] + $diff;
            $arNewPrice[$maxPrice['PRODUCT_ID']]['SUM'] = $arNewPrice[$maxPrice['PRODUCT_ID']]['SUM'] + $diff;
        }
        return $arNewPrice;
    }

    protected static function getCoefficient($sum, $sumDiscount)
    {
        return round(1 - (((100 * $sumDiscount) / $sum) / 100), 2);
    }

    public static function getAvailabilityCountByProduct($idProduct, $extraParams = [])
    {
        if (!$idProduct) {
            return 0;
        }
        $productAvailabilityBuy = new ProductAvailabilityBuy();

        return $productAvailabilityBuy->getCountProductsForBuyClothes($idProduct);
    }

    public static function getAvailabilityCountByProductRetail($idStore, $products = [])
    {
        if (empty($products)) {
            return [];
        }
        $productAvailabilityBuy = new ProductAvailabilityBuy();

        return $productAvailabilityBuy->getCountProductsRetailByProducts($idStore, $products);
    }

    public static function clearNameProd($name, $article = '')
    {
        $name = str_replace($article, '', $name);
        $name = preg_replace('/\((\w+)\)/','', $name);
        return $name;
    }

    public static function hasChildSection($iblockID,$sectionID)
    {
        $hasChild = false;
        if(\CModule::IncludeModule("iblock")){
            $arFilter = Array('IBLOCK_ID'=>$iblockID, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID'=>$sectionID);
            $db_list = \CIBlockSection::GetList(Array($by=>$order), $arFilter, true,['nTopCount' => 1] );
            if($ar_result = $db_list->GetNext())
            {
                $hasChild = true;
            }
        }
        return $hasChild;
    }
}