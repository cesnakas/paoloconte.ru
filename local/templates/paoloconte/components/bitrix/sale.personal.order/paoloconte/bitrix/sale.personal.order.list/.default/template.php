<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Citfact\CloudLoyalty\Events;
?>

<div class="panel-group styled-delivery order-wrap" id="" role="tablist" aria-multiselectable="true">
    <? if (!empty($arResult['ERRORS']['FATAL'])) {
        foreach ($arResult['ERRORS']['FATAL'] as $error) {
            echo ShowError($error);
        }
    } elseif (!empty($arResult['ERRORS']['NONFATAL'])) {
        foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
            echo ShowError($error);
        }
    } ?>

    <div class="bx_my_order_switch">
        <? $nothing = !isset($_REQUEST["filter_history"]) && !isset($_REQUEST["show_all"]); ?>

        <? if ($nothing || isset($_REQUEST["filter_history"])): ?>
            <a class="bx_mo_link"
               href="<?= $arResult["CURRENT_PAGE"] ?>?show_all=Y"><?= GetMessage('SPOL_ORDERS_ALL') ?></a>
        <? endif ?>
    </div>

    <? if (!empty($arResult['ORDERS'])): ?>
        <? foreach ($arResult["ORDER_BY_STATUS"] as $key => $group): ?>
            <? foreach ($group as $i => $order): ?>
            <?
                foreach ($order['PAYMENT'] as $payment){
                    if($payment['PAY_SYSTEM_ID'] == Events::getCloudLoyaltyPaySystemId()) {
                        $order['ORDER']['FORMATED_PRICE'] = number_format((float)$order['ORDER']['PRICE'] - (float)$payment['SUM'], 0, ',', ' ') . ' руб.';
                        break;
                    }
                }    
            ?>
                <? if (!$i): ?>
                    <div class="bx_my_order_status_desc">
                        <h3><?= GetMessage("SPOL_STATUS") ?> "<?= $arResult["INFO"]["STATUS"][$key]["NAME"] ?>"</h3>
                        <div class="bx_mos_desc"><?= $arResult["INFO"]["STATUS"][$key]["DESCRIPTION"] ?></div>
                    </div>
                <? endif ?>

                <div class="panel">
                    <div class="panel-heading pts-bold" role="tab" id="heading-<? echo $key . $i; ?>">
                        <a class="collapsed clear-after" data-toggle="collapse"
                           href="#collapse-<? echo $key . $i; ?>"
                           aria-expanded="true" aria-controls="collapse-<? echo $key . $i; ?>">
                            <div class="float-left fourth">
                                <?= GetMessage('SPOL_ORDER') ?> <?= GetMessage('SPOL_NUM_SIGN') ?><?= $order["ORDER"]["ACCOUNT_NUMBER"] ?>
                                <? if (strlen($order["ORDER"]["DATE_INSERT_FORMATED"])): ?>
                                    <?= GetMessage('SPOL_FROM') ?> <?= $order["ORDER"]["DATE_INSERT_FORMATED"]; ?>
                                <? endif ?>
                            </div>
                            <div class="float-left fifth"><span
                                        class="<? /*payment-status*/ ?>"><?= ($order['ORDER']['PAYED'] == 'Y' ? 'Оплачен' : 'Не оплачен') ?></span><? //=$arResult["INFO"]["STATUS"][$key]["NAME"]?>
                            </div>
                            <span class="panel__clear"></span>
                            <div class="float-left fourth">Сумма: <?= $order["ORDER"]["FORMATED_PRICE"] ?></div>
                            <div class="float-left fourth" title="<?= $arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"] ?>"><?= $arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"] ?></div>
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>

                    <div id="collapse-<? echo $key . $i; ?>" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="heading-<? echo $key . $i; ?>">
                        <div class="panel-body">
                            <div class="order-wrap">
                                <table class="full">
                                    <tr>
                                        <th>Фото</th>
                                        <th>Характеристики</th>
                                        <th>Цена</th>
                                        <th>Сумма</th>
                                    </tr>
                                    <? foreach ($order["BASKET_ITEMS"] as $item): ?>
                                        <?
                                        $article = $arResult['ITEMS'][$item['PRODUCT_ID']]['ARTICLE'];
                                        $razmer = $arResult['ITEMS'][$item['PRODUCT_ID']]['RAZMER'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="image">
                                                    <a href="<?= $item["DETAIL_PAGE_URL"] ?>" id="">
                                                        <? if ($arResult['ITEMS'][$item['PRODUCT_ID']]['CATALOG_PHOTO']): ?>
                                                            <img src="<?= $arResult['ITEMS'][$item['PRODUCT_ID']]['CATALOG_PHOTO']['PHOTO'][0]['SMALL'] ?>"
                                                                 alt="<?= $item['NAME'] ?>">
                                                        <? else: ?>
                                                            <img src="<?= $arResult['NOPHOTO'] ?>" alt="нет фото">
                                                        <? endif; ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="name">
                                                    <? if (strlen($item["DETAIL_PAGE_URL"])): ?>
                                                    <a href="<?= $item["DETAIL_PAGE_URL"] ?>" target="_blank">
                                                        <? endif ?>
                                                        <?= $item['NAME'] ?>
                                                        <? if (strlen($item["DETAIL_PAGE_URL"])): ?>
                                                    </a>
                                                <? endif ?>
                                                </div>
                                                <? if ($razmer): ?>
                                                    <div class="size">Размер: <?= $razmer ?></div>
                                                <? endif ?>
                                            </td>
                                            <td>
                                                <?= number_format($item["PRICE"], 0, '', ' ') ?> руб.
                                            </td>
                                            <td>
                                                <?= number_format($item["PRICE"] * $item['QUANTITY'], 0, '', ' ') ?>
                                                руб.
                                            </td>
                                            <td class="order-review">
                                                <? $img = ($arResult['ITEMS'][$item['PRODUCT_ID']]['CATALOG_PHOTO']) ? $arResult['ITEMS'][$item['PRODUCT_ID']]['CATALOG_PHOTO']['PHOTO'][0]['SMALL'] : $arResult['NOPHOTO']; ?>
                                                <a href="#"
                                                   class="get-review btn btn--transparent addProductReviewLink"
                                                   data-toggle="modal" data-target="#productReviewModal"
                                                   data-item-id="<?= $item['PRODUCT_ID'] ?>"
                                                   data-item-name="<?= urlencode($item['NAME']) ?>"
                                                   data-item-link="<?= urlencode($item['DETAIL_PAGE_URL']) ?>"
                                                   data-item-src="<?= urlencode($img) ?>">
                                                    <span>Написать отзыв</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <? endforeach ?>
                                </table>
                                
                                <div class="end-cart-box align-right">
                                    Сумма: <?= $order["ORDER"]["FORMATED_PRICE"] ?>
                                    <span class="payment-status"><?= ($order['ORDER']['PAYED'] == 'Y' ? 'Оплачен' : 'Не оплачен') ?></span>
                                    <div class="payment"><?
//                                        global $USER;
//                                        if ($order["ORDER"]['STATUS_ID'] == 'S' || $USER->IsAdmin()) {
                                            $APPLICATION->IncludeComponent(
                                                "bitrix:sale.personal.order.detail",
                                                "pay_button",
                                                array(
                                                    "PATH_TO_LIST" => '',
                                                    "PATH_TO_CANCEL" => '',
                                                    "PATH_TO_PAYMENT" => '',
                                                    "SET_TITLE" => 'N',
                                                    "ID" => $order["ORDER"]["ACCOUNT_NUMBER"],
                                                    "ACTIVE_DATE_FORMAT" => 'd.m.Y',
                                                    "CACHE_TYPE" => 'A',
                                                    "CACHE_TIME" => '3600',
                                                    "CACHE_GROUPS" => 'N',
                                                    "CUSTOM_SELECT_PROPS" => ''
                                                ),
                                                false
                                            );
//                                        }
                                        ?></div>
                                    <? $status_sheepla = $arResult['STATUSES_SHEEPLA'][$order['ORDER']['ID']]['STATUS_SHEEPLA'];
                                    if ($status_sheepla != '') {
                                        $status_sheepla .= ', ' . $arResult['STATUSES_SHEEPLA'][$order['ORDER']['ID']]['SUBSTATUS_SHEEPLA'];
                                    } ?>
                                    <? if ($key != 'N'): ?>
                                        <div class="discount">
                                            <?= $arResult["INFO"]["STATUS"][$key]["NAME"] ?>
                                            <?= $status_sheepla != '' ? '(' . $status_sheepla . ')' : '' ?>
                                        </div>
                                    <? endif ?>
                                    <a href="<?= $order["ORDER"]["URL_TO_DETAIL"] ?>"><?= GetMessage('SPOL_ORDER_DETAIL') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <? endforeach ?>
        <? endforeach ?>

        <div class="pagination">
            <? if (strlen($arResult['NAV_STRING'])): ?>
                <?= $arResult['NAV_STRING'] ?>
            <? endif ?>
        </div>

    <? else: ?>
        <?= GetMessage('SPOL_NO_ORDERS') ?>
    <? endif ?>
</div>

<script>
    $(document).ready(function () {
        $('.payment > .tablebodytext').remove();
        $('.payment form p').remove();
        $('.payment form br').remove();
    });
</script>

<? // БЛОК ОТКЛЮЧЕН?>
<? if (false && !empty($arResult['ORDERS'])): ?>

    <? foreach ($arResult["ORDER_BY_STATUS"] as $key => $group): ?>
        <? foreach ($group as $k => $order): ?>
            <? if (!$k): ?>
                <div class="bx_my_order_status_desc">
                    <h2><?= GetMessage("SPOL_STATUS") ?> "<?= $arResult["INFO"]["STATUS"][$key]["NAME"] ?>"</h2>
                    <div class="bx_mos_desc"><?= $arResult["INFO"]["STATUS"][$key]["DESCRIPTION"] ?></div>
                </div>
            <? endif ?>

            <div class="bx_my_order">

                <table class="bx_my_order_table">
                    <thead>
                    <tr>
                        <td>
                            <?= GetMessage('SPOL_ORDER') ?> <?= GetMessage('SPOL_NUM_SIGN') ?><?= $order["ORDER"]["ACCOUNT_NUMBER"] ?>
                            <? if (strlen($order["ORDER"]["DATE_INSERT_FORMATED"])): ?>
                                <?= GetMessage('SPOL_FROM') ?> <?= $order["ORDER"]["DATE_INSERT_FORMATED"]; ?>
                            <? endif ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?= $order["ORDER"]["URL_TO_DETAIL"] ?>"><?= GetMessage('SPOL_ORDER_DETAIL') ?></a>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <strong><?= GetMessage('SPOL_PAY_SUM') ?>:</strong> <?= $order["ORDER"]["FORMATED_PRICE"] ?>
                            <br/>

                            <strong><?= GetMessage('SPOL_PAYED') ?>
                                :</strong> <?= GetMessage('SPOL_' . ($order["ORDER"]["PAYED"] == "Y" ? 'YES' : 'NO')) ?>
                            <br/>

                            <? // PAY SYSTEM ?>
                            <? if (intval($order["ORDER"]["PAY_SYSTEM_ID"])): ?>
                                <strong><?= GetMessage('SPOL_PAYSYSTEM') ?>
                                    :</strong> <?= $arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"] ?>
                                <br/>
                            <? endif ?>

                            <? // DELIVERY SYSTEM ?>
                            <? if ($order['HAS_DELIVERY']): ?>
                                <strong><?= GetMessage('SPOL_DELIVERY') ?>:</strong>
                                <? if (intval($order["ORDER"]["DELIVERY_ID"])): ?>
                                    <?= $arResult["INFO"]["DELIVERY"][$order["ORDER"]["DELIVERY_ID"]]["NAME"] ?> <br/>
                                <? elseif (strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false): ?>
                                    <? $arId = explode(":", $order["ORDER"]["DELIVERY_ID"]) ?>
                                    <?= $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"] ?> (<?= $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"] ?>)
                                    <br/>
                                <? endif ?>
                            <? endif ?>

                            <strong><?= GetMessage('SPOL_BASKET') ?>:</strong>
                            <ul class="bx_item_list">
                                <? foreach ($order["BASKET_ITEMS"] as $item): ?>
                                    <li>
                                        <? if (strlen($item["DETAIL_PAGE_URL"])): ?>
                                        <a href="<?= $item["DETAIL_PAGE_URL"] ?>" target="_blank">
                                            <? endif ?>
                                            <?= $item['NAME'] ?>
                                            <? if (strlen($item["DETAIL_PAGE_URL"])): ?>
                                        </a>
                                    <? endif ?>
                                        <nobr>
                                            &nbsp;&mdash; <?= $item['QUANTITY'] ?> <?= (isset($item["MEASURE_NAME"]) ? $item["MEASURE_NAME"] : GetMessage('SPOL_SHT')) ?></nobr>
                                    </li>
                                <? endforeach ?>
                            </ul>
                        </td>
                        <td>
                            <?= $order["ORDER"]["DATE_STATUS_FORMATED"]; ?>
                            <div class="bx_my_order_status <?= $arResult["INFO"]["STATUS"][$key]['COLOR'] ?>"><?= $arResult["INFO"]["STATUS"][$key]["NAME"] ?></div>
                            <? if ($order["ORDER"]["CANCELED"] != "Y"): ?>
                                <a href="<?= $order["ORDER"]["URL_TO_CANCEL"] ?>" style="min-width:140px"
                                   class="bx_big bx_bt_button_type_2 bx_cart bx_order_action"><?= GetMessage('SPOL_CANCEL_ORDER') ?></a>
                            <? endif ?>
                            <a href="<?= $order["ORDER"]["URL_TO_COPY"] ?>" style="min-width:140px"
                               class="bx_big bx_bt_button_type_2 bx_cart bx_order_action"><?= GetMessage('SPOL_REPEAT_ORDER') ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <? endforeach ?>
    <? endforeach ?>

    <div class="pagination">
        <? if (strlen($arResult['NAV_STRING'])): ?>
            <?= $arResult['NAV_STRING'] ?>
        <? endif ?>
    </div>
<? endif; ?>

<div class="modal fade productReviewModal" id="productReviewModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-new">
        <div class="modal-content">
            <? // Грузим сюда аяксом /include/ajax_product_review_popup.php ?>
        </div>
    </div>
</div>