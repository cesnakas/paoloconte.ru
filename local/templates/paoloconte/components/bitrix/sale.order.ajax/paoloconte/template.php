<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(false);
if ($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
    return;
}
?>
<script>
	BX.message({
		TEMPLATE_PATH_CITFACT_CLOUDLOYALTY_AJAX: '<? echo $this->__folder ?>'
	});
</script>
<?
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/functions.php");
if ($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y") {
    if ($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y") {
        if (strlen($arResult["REDIRECT_URL"]) > 0) {
            $APPLICATION->RestartBuffer(); ?>
            <script type="text/javascript">
                window.top.location.href = '<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
            </script><?
            die();
        }
    }
}

$APPLICATION->SetAdditionalCSS($templateFolder . "/style_cart.css");
$APPLICATION->SetAdditionalCSS($templateFolder . "/style.css");

CJSCore::Init(array('fx', 'popup', 'window', 'ajax')); ?>

<a name="order_form"></a>

<div id="order_form_div" class="order-checkout">
    <NOSCRIPT>
        <div class="errortext"><?= GetMessage("SOA_NO_JS") ?></div>
    </NOSCRIPT>
    
    <? if (!function_exists("getColumnName")) {
        function getColumnName($arHeader)
        {
            return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_" . $arHeader["id"]);
        }
    }
    
    if (!function_exists("cmpBySort")) {
        function cmpBySort($array1, $array2)
        {
            if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
                return -1;
            
            if ($array1["SORT"] > $array2["SORT"])
                return 1;
            
            if ($array1["SORT"] < $array2["SORT"])
                return -1;
            
            if ($array1["SORT"] == $array2["SORT"])
                return 0;
        }
    } ?>

    <div class="bx_order_make">
        <? if (!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N") {
            include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/auth.php");
        } else {
            if ($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y") {
                if (strlen($arResult["REDIRECT_URL"]) == 0) {
                    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/confirm.php");
                }
            } else { ?>
                <script type="text/javascript">
                    <? if(CSaleLocation::isLocationProEnabled()) { ?>
                    <?$city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();?>
                    BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
                        'source' => $this->__component->getPath() . '/get.php',
                        'cityTypeId' => intval($city['ID']),
                        'messages' => array(
                            'otherLocation' => '--- ' . GetMessage('SOA_OTHER_LOCATION'),
                            'moreInfoLocation' => '--- ' . GetMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                            'notFoundPrompt' => '<div class="-bx-popup-special-prompt">' . GetMessage('SOA_LOCATION_NOT_FOUND') . '.<br />' . GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
                                    '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                                    '#ANCHOR_END#' => '</a>'
                                )) . '</div>'
                        )
                    ))?>);
                    <? } ?>
                    
                    var BXFormPosting = false;


                    function submitForm(val, orderClick = false) {
                        if (window.saveInputValue) {
                            window.saveInputValue.init();
                        }
                        BX.showWait();
                        if(orderClick && !validateForm()) {
                            BX.closeWait();
                            return false;
                        }
                
                        if(!checkUserCloudLoyalty(orderClick) && orderClick){
                            BX.closeWait();
                            return false;
                        }
                        
                        if (BXFormPosting === true){
                            BX.closeWait();
                            return true;
                        }
                        BXFormPosting = true;
                        if (val != 'Y')
                            BX('confirmorder').value = 'N';
                        
                        var orderForm = BX('ORDER_FORM');

                        <?if(CSaleLocation::isLocationProEnabled()):?>
                        BX.saleOrderAjax.cleanUp();
                        <?endif?>
                        setFullName();
                        window.blockCloseWait = true;
                        BX.ajax.submit(orderForm, ajaxResult);
                        return true;
                    }
                    
                    function ajaxResult(res) {
                        window.blockCloseWait = false;
                        var orderForm = BX('ORDER_FORM');
                        try {
                            var json = JSON.parse(res);
                            BX.closeWait();
                            if (json.error) {
                                BXFormPosting = false;
                                return;
                            } else if (json.redirect && json.redirect !== '') {
                                window.top.location.href = json.redirect;
                                return;
                            }
                        } catch (e) {
                          
                            // json parse failed, so it is a simple chunk of html
                            BXFormPosting = false;
                            BX('order_form_content').innerHTML = res;
                            
                            <?if(CSaleLocation::isLocationProEnabled()):?>
                            BX.saleOrderAjax.initDeferredControl();
                            <?endif?>
                        }
                        
                        BX.closeWait();
                        BX.onCustomEvent(orderForm, 'onAjaxSuccess');
                        $("select").select2();

                        $(".safe-payment").on('click', function () {
                            $(".safe-payment-descr").toggle();
                        });
                    }
                    
                    function SetContact(profileId) {
                        BX("profile_change").value = "Y";
                        submitForm();
                    }

                    function setFullName() {
                        if (document.querySelector('.property-FIO')) {
                            const fio = document.querySelector('.property-FIO'),
                                f = document.querySelector('.property-SURNAME'),
                                i = document.querySelector('.property-NAME'),
                                o = document.querySelector('.property-SECOND_NAME');

                            fio.value = `${f.value} ${i.value} ${o.value}`
                        }
                    }
                </script>
            
            <? if ($_POST["is_ajax_post"] != "Y") { ?>
                <form class="form" action="<?= $APPLICATION->GetCurPage(); ?>" method="POST" name="ORDER_FORM" id="ORDER_FORM"
                      enctype="multipart/form-data">
                    <?= bitrix_sessid_post() ?>
                    <div id="order_form_content" class="order-block order-form">
                        <? } else { ?>
                            <? $APPLICATION->RestartBuffer(); ?>
                        <? } ?>
                        <div class="order-block__inner">
                            <div class="order-block__main">
                                <div class="order-form__title">
                                    <div class="title-4">ОФОРМЛЕНИЕ ЗАКАЗА</div>

                                    <? if (!$USER->IsAuthorized()) { ?>
                                        <a class="order-form__auth mobile" href="#" data-toggle="modal" data-target="#enterModal">Войти в личный кабинет</a>
                                    <? } else { ?>
                                        <a class="order-form__auth mobile" href="/cabinet/">
                                            <?=$USER->GetFullName();?>
                                            <svg class='i-icon'>
                                                <use xlink:href='#lk'/>
                                            </svg>
                                        </a>
                                    <? } ?>
                                </div>
                                <? if ($_REQUEST['PERMANENT_MODE_STEPS'] == 1) {
                                    ?><input type="hidden" name="PERMANENT_MODE_STEPS" value="1"/><?
                                }

                                include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/person_type.php");
                                include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/props.php");

                                if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d") {
                                    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/paysystem.php");
                                    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/delivery.php");
                                } else {
                                    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/delivery.php");
                                    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/paysystem.php");
                                }

                                include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/related_props.php");
                                ?>
                                <?$APPLICATION->ShowViewContent('block_bonuses');?>
                                <div data-coment-order></div>
                                <?
                                if (strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0) {
                                    echo $arResult["PREPAY_ADIT_FIELDS"];
                                } ?>
                            </div>
                            <div class="order-block__sidebar" data-fix-sidebar>
                                <?
                                include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/summary.php");
                                ?>
                            </div>
                        </div>
                        
                        <? if ($_POST["is_ajax_post"] != "Y") { ?>
                    </div>

                    <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
                    <input type="hidden" name="profile_change" id="profile_change" value="N">
                    <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
                    <input type="hidden" name="ORDER_PROP_35" id="ORDER_PROP_35" value="">
                    <input type="hidden" name="json" value="Y">
                </form>
                
                <? if ($arParams["DELIVERY_NO_AJAX"] == "N") { ?>
                <div style="display:none;">
                    <? $APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?>
                </div>
            <? } ?>
            <? } else { ?>
                <script type="text/javascript">
                    top.BX('confirmorder').value = 'Y';
                    top.BX('profile_change').value = 'N';
                </script><?
                die();
            }
            }
        } ?>
    </div>
</div>

<? if (CSaleLocation::isLocationProEnabled()): ?>
    <div style="display: none">
        <? // we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.location.selector.steps",
            ".default",
            array(),
            false
        ); ?>
        
        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.location.selector.search",
            ".default",
            array(),
            false
        ); ?>
    </div>
<? endif ?>

<script>
    function scrollToErrors() {
        if ($("div").is('#order_errors_cont_personal')) {
            if ($("div").is('#order_errors_cont_personal_title_reg')) {
                window.scrollTo($('#order_errors_cont_personal_title_reg'));
            } else {
                window.scrollTo($('#order_errors_cont_personal_title'));
            }
        } else {
            if ($("div").is('#order_errors_cont_delivery')) {
                window.scrollTo($('#order_errors_cont_delivery_title'));
            } else {
                if ($("div").is('#order_errors_cont_address')) {
                    window.scrollTo($('#order_errors_cont_address'));
                }
            }
        }
    }

    (function () {
        function join(arr /*, separator */) {
            var separator = arguments.length > 1 ? arguments[1] : ", ";
            return arr.filter(function (n) {
                return n
            }).join(separator);
        }
        
        function formatName(suggestion) {
            return join([suggestion.data.surname, suggestion.data.name, suggestion.data.patronymic], " ");
        }
        
        function initFormatting() {
            var $name = $("#ORDER_PROP_1");
            if ($name.lenght > 0 && $name.suggestions) {
                $name.suggestions().setOptions({formatSelected: formatName});
            }
        }

        var dadatagranSuggestions = {
            fiasStreet: '',
            isSend: false,
            getConf(type, bounds = "", constraints, params) {
                var self = this;
                let option = {
                    serviceUrl: "https://suggestions.dadata.ru/suggestions/api/4_1/rs",
                    token: "1ba5840fa30e2807f3ebd0c28c6a8b86f044ece0",
                    type: type,
                    hint: false,
                    bounds: bounds,
                    scrollOnFocus: false,
                    onSuggestionsFetch: function (suggestions) {
                        var input = $(this);
                        if (input.hasClass('property-STREET') && suggestions[0] && self.isSend) {
                            self.fiasStreet = suggestions[0].data.street_fias_id;
                            setTimeout(function () {
                                input.suggestions().hide();
                                $('.suggestions-wrapper').show();
                            }, 100);
                            self.isSend = false;
                        }
                    }
                };

                if (constraints) {
                    option.constraints = constraints;
                }
                if (params) {
                    option['params'] = params;
                }

                return option;
            },
            async initSuggestionFields() {
                var type  = "ADDRESS",
                    cityName = '',
                    street = $(".property-STREET"),
                    house  = $(".property-HOUSE");

                var cityCode = document.querySelector('input[name=ORDER_PROP_6]').value;
                await $.getJSON('/local/templates/paoloconte/components/bitrix/sale.order.ajax/paoloconte/search.php', {'city': cityCode}, function (result) {
                    cityName = result.NAME;
                });
                // $('input.property-EMAIL').suggestions(
                //     this.getConf('EMAIL')
                // );
                if (cityName.length) {
                    street.suggestions(
                        this.getConf(
                            type, "street", {"label": "", "locations": { "city": cityName }}
                        )
                    );
                    if (street.val()) {
                        $('.suggestions-wrapper').hide();
                        street.suggestions().update();
                        house.suggestions(
                            this.getConf(type, "house", {"label": "", "locations": { "street_fias_id": this.fiasStreet }})
                        );
                    } else {
                        house.suggestions(
                            this.getConf(type, "house", street)
                        );
                    }
                }
            },
            initFormattingFields() {
                var type  = "NAME",
                    surname = $(".property-SURNAME"),
                    name = $(".property-NAME"),
                    secondName = $(".property-SECOND_NAME");

                surname.suggestions(
                    this.getConf(
                        type, "", {}, { "parts": ["SURNAME"] }
                    )
                );
                name.suggestions(
                    this.getConf(
                        type, "", {}, { "parts": ["NAME"] }
                    )
                );
                secondName.suggestions(
                    this.getConf(
                        type, "", {}, { "parts": ["PATRONYMIC"] }
                    )
                );
            }
        };

        window.saveInputValue = {
            isSentlocalStorage: false,
            surname: '',
            name: '',
            secondName: '',
            email: '',
            phone: '',
            init() {
                var surnameInput = $(".property-SURNAME"),
                    nameInput = $(".property-NAME"),
                    secondNameInput = $(".property-SECOND_NAME"),
                    emailInput = $(".property-EMAIL"),
                    phoneInput = $(".property-PHONE");

                if (surnameInput.length > 0) {
                    this.surname = surnameInput.val();
                }
                if (secondNameInput.length > 0) {
                    this.secondName = secondNameInput.val();
                }
                if (nameInput.length > 0) {
                    this.name = nameInput.val();
                }
                if (emailInput.length > 0) {
                    this.email = emailInput.val();
                }
                if (phoneInput.length > 0) {
                    this.phone = phoneInput.val();
                }
                if (!this.isSentlocalStorage) {
                    var data = JSON.parse(localStorage.getItem("saveInputValue"));
                    if (data) {
                        this.surname = data.surname;
                        this.name = data.name;
                        this.secondName = data.secondName;
                        this.email = data.email;
                        this.phone = data.phone;
                        this.returnValueInputs();
                    }
                    this.isSentlocalStorage = true;
                }
                localStorage.setItem('saveInputValue', JSON.stringify(this));
            },
            returnValueInputs() {
                var surnameInput = $(".property-SURNAME"),
                    nameInput = $(".property-NAME"),
                    secondNameInput = $(".property-SECOND_NAME"),
                    emailInput = $(".property-EMAIL"),
                    phoneInput = $(".property-PHONE");

                if (this.surname !== '') {
                    surnameInput.val(this.surname);
                }
                if (this.name !== '') {
                    nameInput.val(this.name);
                }
                if (this.secondName !== '') {
                    secondNameInput.val(this.secondName);
                }
                if (this.email !== '') {
                    emailInput.val(this.email);
                }
                if (this.phone !== '') {
                    phoneInput.val(this.phone);
                }
            }
        };

        BX.ready(function () {
            BX.addCustomEvent("onAjaxSuccess", BX.delegate(function () {
                // if ($(window).width() < 768) {
                //     $('.basket-params__item input:not(:checked)').parents('.basket-params__item').find('.basket-params__text').hide();
                // }
                scrollToErrors();
                initFormatting();
                dadatagranSuggestions.isSend = true;
                dadatagranSuggestions.initSuggestionFields();
                // dadatagranSuggestions.initFormattingFields();
                selfDeliveryManager.check();
                selfDeliveryManager.initDelivery();
                window.Application.Components.Main.blockFixOnScroll();
                window.saveInputValue.returnValueInputs();
            }, this));
        });
    })();
</script>
<style>
    .select-item {
        display: inline-block;
        border: 1px solid #0b1221;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        float: left;
        margin-right: 10px;
        position: relative;
    }

    .select-item.selected::before {
        content: '';
        position: absolute;
        border-radius: 50%;
        width: 8px;
        height: 8px;
        background: #0b1221;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    .delivery-map-select {
        max-width: 300px;
        white-space: normal;
        font-size: 12px;
    }

    .delivery-map-select small {
        display: block;
        margin: 10px 0 10px 0;
        font-size: 12px;
    }

    .basket-params .btn--transparent:hover, .basket-params .btn--transparent:hover * {
        background-color: transparent;
    }

    .order-form .location-block-wrapper .bx-sls .dropdown-icon, .order-form .location-block-wrapper .bx-slst .dropdown-icon {
        background: url(/local/templates/paoloconte/images/sprite/search.svg) no-repeat;
    }

    .location-block-wrapper > div > div > input.dropdown-field {
        display: none;
    }
</style>