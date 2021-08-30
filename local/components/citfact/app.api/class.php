<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;
use Citfact\Entity\BasketTable;
use Citfact\Entity\FuserTable;

class AppAPI extends CBitrixComponent
{
    const CODE_GROUP = 'api_client';
    const TIME_BACK = 3600 * 24 * 30;

    private $action;
    private $user;
    private $userId;

    public function executeComponent()
    {
        header('Content-Type: application/json');
        try {
            Loader::includeModule('iblock');
            Loader::includeModule('citfact.tools');
            $this->iblockElement = new CIBlockELement();
            $this->initRequest();

            switch ($this->action) {
                case 'getContacts':
                    echo $this->getContacts();
                    break;
                default:
                    throw new \Exception('action не соответствует ни одному из имеющихся методов');
                    break;
            }
        } catch (Exception $e) {
            echo json_encode(
                [
                    'result' => 'error',
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    private function getContacts()
    {
        if (isset($_REQUEST['dateStart']) && !empty($_REQUEST['dateStart'])) {
            $dateStart = DateTime::createFromTimestamp(strtotime($_REQUEST['dateStart']));
        } else {
            $dateStart = DateTime::createFromTimestamp(time() - static::TIME_BACK);
        }
        if (isset($_REQUEST['dateEnd']) && !empty($_REQUEST['dateEnd'])) {
            $dateEnd = DateTime::createFromTimestamp(strtotime($_REQUEST['dateEnd']));
        } else {
            $dateEnd = DateTime::createFromTimestamp(time());
        }
        $baskets = $this->getBasketWithOutOrder($dateStart, $dateEnd);
        $fuserIds = array_unique(array_keys($baskets));
        $fusers = $this->getFUsersByIds($fuserIds);
        $mapUserToFUser = $this->getMapUserToFUser($fusers);
        $userIds = array_values(array_unique(array_column($fusers, 'USER_ID')));
        $userIds = array_diff($userIds, array(0, null, '', false));
        $contacts = $this->getUsersByIds($userIds);
        foreach ($contacts as $k => $contact) {
            $fuser = $mapUserToFUser[$contact['ID']];
            $basket = $baskets[$fuser];
            foreach ($basket as $item) {
                $contacts[$k]['basket'][$item['ID']] = $item;
            }
        }
        $result = $this->getResponseContact($contacts);

        return json_encode(
            [
                'result' => 'success',
                'data' => [
                    'dateStart' => $dateStart->toString(),
                    'dateEnd' => $dateEnd->toString(),
                    'count' => count($result),
                    'result' => $result
                ],
            ]
        );
    }

    private function getBasketWithOutOrder(DateTime $dateStart, DateTime $dateEnd)
    {
        $baskets = BasketTable::getList([
            'select' => ['*'],
            'filter' => [
                'ORDER_ID' => [false, 0, '', null],
                '>=DATE_UPDATE' => $dateStart,
                '<=DATE_UPDATE' => $dateEnd,
            ],
            'order' => ['ID' => 'desc'],
            'limit' => 100
        ])->fetchAll();
        $result = [];
        foreach ($baskets as $basket) {
            if ($basket['DATE_INSERT'] instanceof \Bitrix\Main\Type\DateTime) {
                $basket['DATE_INSERT'] = $basket['DATE_INSERT']->toString();
            }
            if ($basket['DATE_UPDATE'] instanceof \Bitrix\Main\Type\DateTime) {
                $basket['DATE_UPDATE'] = $basket['DATE_UPDATE']->toString();
            }
            $result[$basket['FUSER_ID']][$basket['ID']] = $basket;
        }

        return $result;
    }

    private function debug($data)
    {
        echo json_encode([
            'debug' => json_decode(json_encode($data), true),
        ]);
    }

    private function initRequest()
    {
        $userResult = \CUser::GetList($by = 'id', $order = 'asc',
            array('UF_API_KEY' => $this->getApiKey()),
            array('FIELDS' => array('ID', 'NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHONE', 'WORK_PHONE'))
        );
        $user = $userResult->fetch();

        if ($user && $this->getApiKey() !== false) {
            $this->user = $user;
            $this->userId = $user['ID'];
        } else {
            header("HTTP/1.1 403 Forbidden");
            throw new \Exception('Пользователь не найден');
        }

        if (isset($_REQUEST['action'])) {
            $this->action = $_REQUEST['action'];
        }

        if (!$this->checkAccessUser()) {
            header("HTTP/1.1 403 Forbidden");
            throw new \Exception('Нет доступа');
        }
    }

    private function getApiKey()
    {
        if (isset($_SERVER['HTTP_X_API_KEY']) && !empty($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }
        return false;
    }

    private function checkAccessUser()
    {
        $userGroups = \CUser::GetUserGroup($this->userId);
        $groupId = $this->getGroupIdApiClient();
        if (!$groupId) {
            throw new \Exception('Не существует группы для API-клиентов');
        }
        if (!in_array($groupId, $userGroups)) {
            return false;
        }

        return true;
    }

    private function getGroupIdApiClient()
    {
        return $this->getGroupIdByCode(static::CODE_GROUP);
    }

    private function getGroupIdByCode($code)
    {
        $rsGroups = \CGroup::GetList($by = "c_sort", $order = "asc", Array("STRING_ID" => $code));
        $result = $rsGroups->Fetch();
        if ($result) {
            return $result['ID'];
        }
        return false;
    }

    private function getFUsersByIds($ids)
    {
        $fusers = FuserTable::getList([
            'select' => ['*'],
            'filter' => [
                'ID' => $ids
            ],
            'order' => ['ID' => 'desc']
        ])->fetchAll();
        $result = [];
        foreach ($fusers as $fuser) {
            if ($fuser['DATE_INSERT'] instanceof \Bitrix\Main\Type\DateTime) {
                $fuser['DATE_INSERT'] = $fuser['DATE_INSERT']->toString();
            }
            if ($fuser['DATE_UPDATE'] instanceof \Bitrix\Main\Type\DateTime) {
                $fuser['DATE_UPDATE'] = $fuser['DATE_UPDATE']->toString();
            }
            $result[$fuser['ID']] = $fuser;
        }

        return $result;
    }

    private function getUsersByIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        $result = [];
        $userResult = \CUser::GetList($by = 'id', $order = 'asc',
            array('ID' => implode('|', $userIds)),
            array('FIELDS' => array('ID', 'NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHONE', 'WORK_PHONE'))
        );
        while ($user = $userResult->fetch()) {
            $result[$user['ID']] = $user;
        }
        return $result;
    }

    private function getMapUserToFUser(array $fusers)
    {
        $result = [];
        foreach ($fusers as $fuser) {
            if ($fuser['USER_ID']) {
                $result[$fuser['USER_ID']] = $fuser['ID'];
            }
        }
        return $result;
    }

    private function getResponseContact(array $contacts)
    {
        $result = [];
        foreach ($contacts as $contact) {
            $phones = [];
            if (!empty($contact['PERSONAL_PHONE'])) {
                $phones[] = $contact['PERSONAL_PHONE'];
            }
            if (!empty($contact['WORK_PHONE'])) {
                $phones[] = $contact['WORK_PHONE'];
            }
            $result[$contact['ID']] = [
                'id' => $contact['ID'],
                'name' => trim(implode(' ', [$contact['NAME'], $contact['LAST_NAME']])),
                'phone' => trim(implode(',', $phones)),
                'email' => $contact['EMAIL']
            ];
            foreach ($contact['basket'] as $basket) {
                $result[$contact['ID']]['basket'][] = [
                    'product_id' => $basket['PRODUCT_ID'],
                    'price' => $basket['PRICE'],
                    'currency' => $basket['CURRENCY'],
                    'date_insert' => $basket['DATE_INSERT'],
                    'date_update' => $basket['DATE_UPDATE'],
                    'quantity' => $basket['QUANTITY'],
                    'name' => $basket['NAME'],
                    'detail_page_url' => $basket['DETAIL_PAGE_URL'],
                ];
            }
        }
        return $result;
    }

}