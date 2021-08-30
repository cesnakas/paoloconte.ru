<?php

/**
 * https://api.aqsi.ru/#tag/Orders/paths/~1v2~1Orders~1simple/post - Добавление заказа в aQsi
 */

namespace Citfact\Aqsi\Orders;

/**
 * Работа с заказами
 *
 * @author Arseny Mogilev
 */
class Create extends \Citfact\Aqsi\Orders {

    /**
     * Объект заказа в Bitrix
     * @var \Bitrix\Sale\Order 
     */
    private $bxOrder;

    /**
     * Объект корзины Bitrix
     * @var Bitrix\Sale\Basket 
     */
    private $bxBasket;

    /**
     * Значения НДС для aQsi
     * @var array 
     */
    private $vatCodes = [
        "20%" => 1,
        "10%" => 2,
        "0%" => 5
    ];

    /**
     * Признак способа расчета
     * @var int 4 - Полный расчёт
     */
    private $paymentMethodType = 4;

    /**
     *  Признак предмета расчета
     * @var int 1 - Товар, 4 - Услуга
     */
    private $paymentSubjectType = 1;

    /**
     * Признак расчета
     * @var int 1 - Приход
     */
    private $paymentType = 1;

    /**
     * Тип маркировки 4 - Обувь
     * @var array 
     */
    private $markingType = ["Обувь" => 4];

    /**
     * Конструктор
     * @param string $key ключ авторизавии API
     */
    public function __construct(string $key) {
        parent::__construct($key);
        $this->func = "create";
        $this->curlHttpMethod = "POST";
    }

    /**
     * Динамический метод
     * @param string $func
     * @param array $args
     */
    public function add(int $orderId = 0) {
        if (!empty($orderId)) {
            $this->setOrderId($orderId);
        }
        $this->bxOrder();
        $this->curlUrl();
        $this->dataForCurl();
    }

    /**
     * Сохранение данных и выпонение запроса в aQsi
     */
    public function save() {
        $this->addCustomer();
        return parent::save();
    }

    /**
     * Устанавивает идентификатор магазина
     * @param string $shop
     */
    public function setShop(string $shop) {
        $this->shop = $shop;
    }

    /**
     * Устанавливает ID заказа
     * @param int $orderId
     */
    public function setOrderId(int $orderId) {
        $this->bxOrderId = $orderId;
    }

    /**
     * Устанавливает признак способа расчета
     * @param int $paymentMethodType
     */
    public function setPaymentMethodType(int $paymentMethodType) {
        $this->paymentMethodType = $paymentMethodType;
    }

    /**
     * Устанавивает признак предмета расчета
     * @param int $paymentSubjectType
     */
    public function setPaymentSubjectType(int $paymentSubjectType) {
        $this->paymentSubjectType = $paymentSubjectType;
    }

    /**
     * Устанавливает признак расчета
     * @param int $paymentType
     */
    public function setPaymentType(int $paymentType) {
        $this->paymentType = $paymentType;
    }

    /**
     * Формирует данные для cUrl запроса и записывает в переменную экземпляра класса
     */
    protected function dataForCurl() {
        $this->arPostFields = [];
        $this->arPostFields["id"] = $this->genUuid();
        $this->arPostFields["number"] = (string) $this->getOrderId();
        $this->arPostFields["dateTime"] = $this->getDateInsert();
        $this->arPostFields["shop"] = $this->getShop();
        $this->arPostFields["content"] = $this->getContent();
        $deliveryAddress = $this->getDeliveryAddress();
        if (!empty($deliveryAddress)) {
            $this->arPostFields["deliveryAddress"] = $deliveryAddress;
        }
        $comment = $this->bxOrder->getField("USER_DESCRIPTION");
        if (!empty($comment)) {
            $this->arPostFields["comment"] = $comment;
        }
        //$this->arPostFields["pickAddress"] = "";
    }

    /**
     * Получает адрес доставки
     * @return string
     */
    private function getDeliveryAddress(): string {
        $deliveryAddress = "";
        $propertyCollection = $this->bxOrder->getPropertyCollection();
        foreach ($propertyCollection as $prop) {
            $code = $prop->getField("CODE");
            switch ($code) {
                case "LOCATION":
                case "ADDRESS":
                    ${$code} = $prop->getValue();
                    break;
            }
        }
        if (!empty($LOCATION)) {
            $res = \Bitrix\Sale\Location\LocationTable::getList([
                        "filter" => ["=NAME.LANGUAGE_ID" => \LANGUAGE_ID, "=ID" => $LOCATION],
                        "select" => ["*", "NAME_RU" => "NAME.NAME", "TYPE_CODE" => "TYPE.CODE"]
            ]);
            while ($itemLoc = $res->fetch()) {
                $deliveryAddress .= $itemLoc["NAME_RU"];
            }
        }
        if (!empty($ADDRESS)) {
            $deliveryAddress .= !empty($deliveryAddress) ? ", " . $ADDRESS : $ADDRESS;
        }
        return $deliveryAddress;
    }

    /**
     * Добавление клиента к заказу
     */
    private function addCustomer() {
        $clientData = $this->makeCustomerData();
        $client = new \Citfact\Aqsi\Clients\Read($this->key);
        $aQsiClientData = $client->get($clientData["id"]);
        if (empty($aQsiClientData) || !empty($aQsiClientData["errors"])) {
            $this->createCustomer($clientData);
        }
        $this->arPostFields["clientId"] = $clientData["id"];
    }

    /**
     * Создает нового клиента в aQsi
     * @global string $aQsiUserGroupId
     * @param array $clientData
     * @return string
     */
    private function createCustomer(array $clientData) {
        global $aQsiUserGroupId;
        $client = new \Citfact\Aqsi\Clients\Create($this->key);
        if (!empty($aQsiUserGroupId)) {
            $client->setGroupId($aQsiUserGroupId);
        }
        $client->add($clientData);
        return $client->save();
    }

    /**
     * Формирует массив данных для создания нового клиента
     * @return array
     */
    private function makeCustomerData(): array {
        $clientData = ["id" => $this->bxOrder->getUserId()];
        $propertyCollection = $this->bxOrder->getPropertyCollection();
        foreach ($propertyCollection as $prop) {
            $arProp = $prop->getProperty();
            $cell = "";
            switch ($arProp["CODE"]) {
                case "FIO":
                    $cell = "fio";
                    break;
                case "PHONE":
                    $cell = "mainPhone";
                    break;
                case "EMAIL":
                    $cell = "email";
                    break;
            }
            if (!empty($cell) && ((bool) ($val = trim($prop->getValue())))) {
                $clientData[$cell] = $val;
            }
        }
        return $clientData;
    }

    /**
     * Объекты классов \Bitrix\Sale\Order и Bitrix\Sale\Basket
     * @return bool
     */
    private function bxOrder(): bool {
        \Bitrix\Main\Loader::includeModule("sale");
        $orderId = $this->getOrderId();
        if (!empty($orderId)) {
            $this->bxOrder = \Bitrix\Sale\Order::load($orderId);
            $this->bxBasket = $this->bxOrder->getBasket();
        }
        return (!empty($this->bxOrder) && !empty($this->bxBasket));
    }

    /**
     * Дата в формате стандарта ISO 8601
     * @return string
     */
    private function getDateInsert(): string {
        return $this->bxOrder->getDateInsert()->format("c");
    }

    /**
     * Возвращает ID заказа
     * @return int
     */
    private function getOrderId(): int {
        return $this->bxOrderId;
    }

    /**
     * Идентификатор магазина в aQsi
     * @return string
     */
    private function getShop(): string {
        return $this->shop;
    }

    /**
     * Формирование поля content
     * @return array
     */
    private function getContent(): array {
        $content = [
            "type" => $this->getPaymentType(),
            "positions" => $this->getPositions()
        ];
        return $content;
    }

    /**
     * Формирует Список предметов расчета
     * @return array
     */
    private function getPositions(): array {
        $positions = [];
        $i = 0;
        foreach ($this->bxBasket as $item) {
            if (!$item->canBuy()) {
                continue;
            }
            $arProduct = $this->getDataProduct((int) $item->getProductId());
            $positions[$i] = [
                "quantity" => $item->getQuantity(),
                "price" => $item->getPrice(),
                "tax" => $this->getTax((float) $item->getField("VAT_RATE"), true),
                "text" => $item->getField("NAME"),
                "paymentMethodType" => $this->getPaymentMethodType(),
                "paymentSubjectType" => $this->getPaymentSubjectType()
            ];
            if (!empty($arProduct["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])) {
                $positions[$i]["sku"] = $arProduct["PROPERTIES"]["CML2_ARTICLE"]["VALUE"];
            }
            if (!empty($arProduct["PROPERTIES"]["BLOK_TOVARA"]["VALUE_ENUM"]) && !empty($this->markingType[$arProduct["PROPERTIES"]["BLOK_TOVARA"]["VALUE_ENUM"]])) {
                $positions[$i]["markingType"] = $this->markingType[$arProduct["PROPERTIES"]["BLOK_TOVARA"]["VALUE_ENUM"]];
            }
            /**
             * @todo Удалить $positions[$i]["markingType"] = 4; после того как решится проблема на стороне aQsi с удалением товаров без признаков маркировки из кассового термирнала
             */
            $positions[$i]["markingType"] = 4;
            $i++;
        }
        $delivery = $this->getDelivery();
        if (!empty($delivery)) {
            $positions[] = $delivery;
        }
        return $positions;
    }

    /**
     * Данные о товаре
     * @param int $productId
     * @return array
     */
    private function getDataProduct(int $productId): array {
        \Bitrix\Main\Loader::includeModule("iblock");
        $resItem = \Bitrix\Iblock\ElementTable::getById($productId);
        $arItem = $resItem->fetch();
        if (!empty($arItem)) {
            foreach (["CML2_ARTICLE", "BLOK_TOVARA"] as $code) {
                $resProps = \CIBlockElement::GetProperty($arItem["IBLOCK_ID"], $arItem["ID"], ["sort" => "asc"], ["CODE" => $code]);
                if ($arProps = $resProps->Fetch()) {
                    $arItem["PROPERTIES"][$code] = $arProps;
                }
            }
            return $arItem; 
        }
        return [];
    }

    private function getDelivery(): array {
        $delivery = [];
        $deliveryPrice = $this->bxOrder->getDeliveryPrice();
        $this->setPaymentSubjectType(4); // Признак предмета расчета услуга
        if (!empty($deliveryPrice)) {
            $delivery = [
                "quantity" => 1,
                "price" => $deliveryPrice,
                "tax" => $this->getTax(0.2, true),
                "text" => "Доставка",
                "paymentMethodType" => $this->getPaymentMethodType(),
                "paymentSubjectType" => $this->getPaymentSubjectType()
            ];
        }
        $this->setPaymentSubjectType(1); // возвращаем Признак предмета расчета товар
        return $delivery;
    }

    /**
     * Получает код НДС для aQsi
     * @param float $vat ставка НДС
     * @param bool $useDefault возвращать значение по умолчанию НДС 20% если не найдено значение
     * @return int возвращает код для aQsi или 0
     */
    private function getTax(float $vat, $useDefault = false): int {
        $vatStr = ((string) ($vat * 100)) . "%";
        if (isset($this->vatCodes[$vatStr])) {
            return $this->vatCodes[$vatStr];
        }
        if ($useDefault) {
            return $this->vatCodes["20%"];
        }
        return 0;
    }

    /**
     * Возвращает признак способа расчета
     * @return int
     */
    private function getPaymentMethodType(): int {
        return $this->paymentMethodType;
    }

    /**
     * Возвращает признак предмета расчета
     * @return int
     */
    private function getPaymentSubjectType(): int {
        return $this->paymentSubjectType;
    }

    /**
     * Возвращает признак расчета
     * @return int
     */
    private function getPaymentType(): int {
        return $this->paymentType;
    }

}
