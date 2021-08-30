<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require('constants.php');


use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::IncludeModule('highloadblock');

Loader::registerAutoLoadClasses('citfact.tools', array(
    'Citfact\Tools' => 'lib/tools.php',
    'Citfact\Paolo' => 'lib/paolo.php',
    'Citfact\Core' => 'lib/core.php',
    'Citfact\HLBlock' => 'lib/hlblock.php',
    'Citfact\ProductActivation' => 'lib/productactivation.php',
    'Citfact\ProductAvailability' => 'lib/productavailability.php',
    'Citfact\SetMarkingProduct' => 'lib/setmarkingproduct.php',
    'Citfact\ProductAvailabilityBuy' => 'lib/productavailabilitybuy.php',
    'Citfact\Lock' => 'lib/lock.php',
    'Citfact\EventListener\OrderRoundPrice' => 'lib/eventlistener/orderroundprice.php',
    'Citfact\EventListener\OrderDraggable' => 'lib/eventlistener/orderdraggable.php',
    'Citfact\CloudLoyalty\Operation' => 'lib/cloudloyalty/operation.php',
    'Citfact\CloudLoyalty\OperationManager' => 'lib/cloudloyalty/operationmanager.php',
    'Citfact\CloudLoyalty\DataLoyalty' => 'lib/cloudloyalty/dataloyalty.php',
    'Citfact\CloudLoyalty\LoyaltyLogger' => 'lib/cloudloyalty/loyaltylogger.php',
    'Citfact\CloudLoyalty\Events' => 'lib/cloudloyalty/events.php',
    'Citfact\Sections' => 'lib/sections.php',
    'Citfact\InfoBip\Proceed' => 'lib/infobip/operation.php',
    'Citfact\Smsc\Events' => 'lib/smsc/events.php',
    'Citfact\Seo\UtmManager' => 'lib/seo/utmmanager.php',
    'Citfact\Entity\MarkingCodeGroup\MarkingCodeGroupEntity' => 'lib/entity/markingcodegroup/markingcodegroupentity.php',
    'Citfact\Entity\MarkingCodeGroup\MarkingCodeGroupTable' => 'lib/entity/markingcodegroup/markingcodegroup.php',
    'Citfact\Entity\Price\PriceModal' => 'lib/entity/price/pricemodal.php',
    'Citfact\EventListener\SearchIndexSubscriber' => 'lib/eventlistener/searchindexsubscriber.php',
    'Citfact\Entity\BasketTable' => 'lib/entity/basket.php',
    'Citfact\Entity\FuserTable' => 'lib/entity/fuser.php',
    'Citfact\UserBasket\UserBasketHelper' => 'lib/userbasket/userbaskethelper.php',
    'Citfact\Entity\Sms\OrderSmsTable' => 'lib/entity/sms/ordersmstable.php',
    'Citfact\Entity\Sms\OrderSmsRepository' => 'lib/entity/sms/ordersms.php',
    'Citfact\Order\OrderLogger' => 'lib/order/OrderLogger.php',
));

class CitfactToolsEventsHandler
{
    public function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/citfact_tools.css');
        $aGlobalMenu['global_menu_citfact'] = array(
            'menu_id' => 'citfact',
            'page_icon' => 'citfact_title_icon',
            'index_icon' => 'citfact_page_icon',
            'text' => 'Студия «Факт»',
            'title' => 'Студия «Факт»',
            //'url' => '#',
            'sort' => '70',
            'items_id' => 'global_menu_citfact',
            'help_section' => 'citfact',
            'items' => array()
        );
    }
}