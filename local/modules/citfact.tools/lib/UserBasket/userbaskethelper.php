<?php

namespace Citfact\UserBasket;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;
use Citfact\Entity\BasketTable;

class UserBasketHelper
{
    const TIME_BACK = 3600 * 24;

    /**
     * @param int $itemID
     * @return int
     */
    public function getCountUserByBasketProductId($itemID)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('citfact.tools');
        $this->iblockElement = new \CIBlockELement();
        $dateStart = DateTime::createFromTimestamp(time() - static::TIME_BACK);
        $dateEnd = DateTime::createFromTimestamp(time() + static::TIME_BACK);
        $baskets = $this->getBasketWithOutOrder($dateStart, $dateEnd, [$itemID]);
        if (isset($baskets[$itemID])) {
            return $baskets[$itemID];
        }
        return 0;
    }

    /**
     * @param array $itemID
     * @return int
     */
    public function getCountUserByBasketProductIds($itemID)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('citfact.tools');
        $this->iblockElement = new \CIBlockELement();
        $dateStart = DateTime::createFromTimestamp(time() - static::TIME_BACK);
        $dateEnd = DateTime::createFromTimestamp(time() + static::TIME_BACK);
        $baskets = $this->getBasketWithOutOrder($dateStart, $dateEnd, $itemID);
        return $baskets;
    }

    private function getBasketWithOutOrder(DateTime $dateStart, DateTime $dateEnd, $itemIDs)
    {
        $res = \CCatalogSKU::getOffersList(
            $itemIDs // массив ID товаров
        );
        $resMass = [];
        $mapOfferProduct = [];
        foreach ($res as $productID => $res1) {
            foreach ($res1 as $res2) {
                $mapOfferProduct[$res2['ID']] = $productID;
                $resMass[] = $res2['ID'];
            }
            $resMass[] = $productID;
        }
        $basketsAll = BasketTable::getList([
            'select' => ['ID', 'FUSER_ID', 'PRODUCT_ID'],
            'filter' => [
                'PRODUCT_ID' => $resMass,
                'ORDER_ID' => [false, 0, '', null],
                '>=DATE_UPDATE' => $dateStart,
                '<=DATE_UPDATE' => $dateEnd,
                '!=FUSER_ID' => \Bitrix\Sale\Fuser::getId(true),
            ],
            'order' => ['ID' => 'desc'],
            'limit' => 500
        ])->fetchAll();

        $baskets = [];
        foreach ($basketsAll as $basket) {
            $prodId = $basket['PRODUCT_ID'];
            if (isset($mapOfferProduct[$basket['PRODUCT_ID']])) {
                $prodId = $mapOfferProduct[$basket['PRODUCT_ID']];
            }
            $baskets[$prodId][$basket['FUSER_ID']] = $basket['FUSER_ID'];
        }

        $result = [];
        foreach ($baskets as $prodId => $product) {
            $result[$prodId] = count($product);
        }

        return $result;
    }
}