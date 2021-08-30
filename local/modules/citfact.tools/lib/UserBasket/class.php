<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;
use Citfact\Entity\BasketTable;
use Citfact\Entity\FuserTable;

class GetAbondenedBasket extends CBitrixComponent
{
    const TIME_BACK = 3600 * 24 * 30;
    const DATA_START ='2021-03-03';
    const DATA_END = '2021-04-03';

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
        $dateStart = DATA_START;
        $dateEnd = DATA_END;
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
}