<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');
Loader::includeModule('citfact.paolo');

if ($_GET['full'] == '0'){
	setcookie("mredir", '0', time()-3600, "paoloconte.ru");
}
$cur_page = $APPLICATION->GetCurPage(true);
$cur_page_no_index = $APPLICATION->GetCurPage(false);
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=0.78">
    <meta name="format-detection" content="telephone=no">
	<link rel="icon" type="image/png" href="/favicon.png" />
	<link rel="apple-touch-icon" href="/images/apple_touch_icons/57x57.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/images/apple_touch_icons/72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/images/apple_touch_icons/114x114.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/images/apple_touch_icons/144x144.png">

    <?//$APPLICATION->ShowHead();
    $APPLICATION->ShowMeta("robots", false, true);
    $APPLICATION->ShowMeta("keywords", false, true);
    $APPLICATION->ShowMeta("description", false, true);
	$APPLICATION->ShowCSS(true, true);
	$APPLICATION->ShowHeadStrings();
	$APPLICATION->ShowHeadScripts();
	$APPLICATION->SetAdditionalCSS("https://fonts.googleapis.com/css?family=Philosopher");
	$APPLICATION->AddHeadScript("https://maps.googleapis.com/maps/api/js?v=3.exp");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/scripts.js");
    CJSCore::Init(array('ajax'));

	$APPLICATION->IncludeComponent("articul.geolocation.detect_ip", "", array("IBLOCK_CODE" => "city"));

	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/template_styles_dop.css");
	?>

    <!--[if lt IE 9]>
    <script src="<?=SITE_TEMPLATE_PATH;?>/vendor/html5shiv/dist/html5shiv.js"></script>
    <![endif]-->

    <title><?$APPLICATION->ShowTitle()?></title>
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '2211662452407847');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=2211662452407847&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
</head>

<?
//$main_page=true;

    if((strpos($_SERVER['REQUEST_URI'], '/main') === false) && ($_SERVER['REQUEST_URI'] != "/")){

        $main_page=false;
        $detail_page=false;
        $action_page=false;
        $error_page=false;

        if((strpos($_SERVER['REQUEST_URI'], '/detail') === false)){
            $detail_page=true;
        }

        if(!(strpos($_SERVER['REQUEST_URI'], '/action-detail') === false)){
            $action_page=true;
        }

        if(!(strpos($_SERVER['REQUEST_URI'], '/error') === false)){
            $error_page=true;
        }

    } ?>

<?
$page = $APPLICATION->GetCurPage();
$left_aside=false;
$catalog_page=false;
$cabinet_page=false;
$sertificate_page=false;

    if(!(strpos($_SERVER['REQUEST_URI'], '/catalog') === false)) {
        $catalog_page=true;

		//определяем к каталоге карточку товара
		global $catalogMode;
		$catalogMode = 'SECTION';
		// $_REQUEST["CATALOG_CODE"] заполняется в init.php
		if(isset($_REQUEST["CATALOG_CODE"]) && !empty($_REQUEST["CATALOG_CODE"])){
			$arFilter = Array("IBLOCK_ID"=>IBLOCK_CATALOG, "CODE"=>$_REQUEST["CATALOG_CODE"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
			$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), Array("ID", "IBLOCK_ID"));
			if($ob = $res->GetNextElement(false, false)){
				$catalogMode = 'ELEMENT';
				$show_title = false;
			}
		}
    }

    if(!(strpos($_SERVER['REQUEST_URI'], '/cabinet') === false)) {
        $cabinet_page=true;
    }

    if(!(strpos($_SERVER['REQUEST_URI'], '/sertificate') === false)) {
        $sertificate_page=true;
    }

    if ($cabinet_page || $catalog_page || $sertificate_page){
        $left_aside=true;
    }
?>

<body <? if ($main_page==true) {?>
        class="mainpage"
    <?} else { ?>class="insidepage" <? } ?> itemscope itemtype="http://schema.org/Organization">
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5R29MN"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5R29MN');</script>
<!-- End Google Tag Manager --> 
       <div id="wrapper">
		<?$APPLICATION->ShowPanel();?>

       <nav id="side-search" class="moved-panel from-top">
           <div class="moved-panel-wrap">
               <div class="moved-panel-body">
                    <div class="search-top">
						<?$APPLICATION->IncludeComponent(
							"bitrix:search.form",
							".default",
							array(
								"PAGE" => "#SITE_DIR#search/"
							),
							false
						);?>
						<?/*<input type="text" placeholder="Введите артикул товара">
                        <a href="#" class="search-btn">
                            <i class="fa fa-search"></i>
                        </a>
                        <input type="submit" class="hide">*/?>
                    </div>
               </div>
           </div>
       </nav>

       <nav id="side-main-menu" class="moved-panel">
           <div class="moved-panel-wrap">
			   <div class="moved-panel-head">
					<div class="head-icon">
						<i class="fa fa-bars close-moved-pannel"></i>
					</div>
			   </div>

			   <div class="moved-panel-body">
				  <div class="main-menu-wrap">
					  <div class="aside-box">
						<div class="panel-group styled-mobile-menu rotate" id="" role="tablist" aria-multiselectable="true">
							<?$APPLICATION->IncludeComponent(
								"citfact:elements.list",
								"menu_left_mobile",
								Array(
									"IBLOCK_ID" => IBLOCK_MENU_LEFT_MOBILE,
									"IBLOCK_CATALOG_ID" => 10,
									"PROPERTY_CODES" => array('CATALOG_SECTION', 'LINK'),
									"TEMPLATE_NAME" => 'menu_left_mobile',
                                    "IS_AUTH" => ($USER->IsAuthorized())?'Y':'N',
                                    "PAGE" => $cur_page,
                                    "PAGE_NO_INDEX" => $cur_page_no_index,
								)
							);?>
					  </div>
					  </div>
					  <div class="aside-box">
						  <ul class="add-info">
							<?$APPLICATION->IncludeComponent(
								"citfact:elements.list",
								"menu_left_bottom_mobile",
								Array(
									"IBLOCK_ID" => IBLOCK_MENU_LEFT_BOTTOM_MOBILE,
									"PROPERTY_CODES" => array(
										"LINK",
										"ICON",
										"AUTH",
										"PARAMS",
										"GEOLOCATION",
									),
                                    "TEMPLATE_NAME" => 'menu_left_bottom_mobile',
                                    "USER_CITY" => $_SESSION['CITY_ID'],
                                    "IS_AUTH" => ($USER->IsAuthorized())?'Y':'N',
								)
							);?>
							<li>
								<?if ($USER->IsAuthorized()):?>
									<i class="fa fa-sign-in small"></i> <a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
										"login",
										"logout",
										"register",
										"forgot_password",
										"change_password"));?>">Выйти</a>
								  <?endif;?>
							</li>
						  </ul>
					  </div>
				  </div>
			   </div>
		   </div>
       </nav>

       <nav id="side-to-favorite" class="moved-panel side-to-favorite">
           <div class="moved-panel-wrap">
               <div class="moved-panel-head" class="">
                   <h6>
                       Товар добавлен в избранное
                   </h6>
                       <span class="close close-moved-pannel">
                       </span>
               </div>
               <div class="moved-panel-body">
                    <div class="aside-box">
                        <a href="#">
                            <div class="image">
                                <img src="<?=SITE_TEMPLATE_PATH?>/images/content/catalog-item-<?echo rand(1,3);?>.jpg">
                            </div>
                        </a>

                        Товар добавлен в избранное.
                        Вы можете найти его в соответствующем разделе “Желаемые товары” в личном кабинете.
                        <div class="btn-box">

                            <a href="#" class="btn btn-gray-dark full close-moved-pannel">Вернуться к покупкам</a>

                            <a href="/cabinet/favorites/" class="btn btn-gray-dark full" <?/*data-type="getMovedPanel" data-target="#side-login"*/?>><span>Личный кабинет</span></a>
                        </div>
                    </div>
               </div>
           </div>
       </nav>

	   <? $APPLICATION->ShowViewContent('detail_add_review_block'); ?>

	   <? $APPLICATION->ShowViewContent('detail_subscribe_price_block'); ?>

	   <? $APPLICATION->ShowViewContent('detail_fast_order_block'); ?>

       <nav id="side-login" class="moved-panel side-login">
           <div class="moved-panel-wrap">
               <div class="moved-panel-head" class="">
                   <h6>
                       Авторизация
                   </h6>
                       <span class="close close-moved-pannel">
                       </span>
               </div>
               <div class="moved-panel-body">
                   <div class="aside-box">
                       <div class="review-form">
                           <?/*<form action="#">
                                <div class="line">
                                    <input type="text" placeholder="Введите ваш e-mail">
                                </div>
                               <div class="line">
                                    <input type="password" placeholder="Введите пароль">
                               </div>

                               <div class="btn-box">
                                   <input type="submit" class="btn btn-gray-dark full" value="Войти">
                               </div>

                               <div class="align-center">
                                   Войти через соц.сети:
                                   <div class="social">
                                       <a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
                                       <a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
                                       <a href="#" class="vk"><i class="fa fa-vk"></i></a>
                                       <a href="#" class="instagram"><i class="fa fa-instagram"></i></a>
                                   </div>
                               </div>
                           </form>*/?>
							<?if (!$USER->IsAuthorized()):?>
							   <?$APPLICATION->IncludeComponent(
								   "citfact:authorize.ajax",
								   "mobile",
								   Array(
									   "REDIRECT_TO" => '',
									   "FORM_ID" => 'popup'
								   )
							   );?>
						   	<?endif;?>
                       </div>
                   </div>

                   <ul class="add-link">
                       <li>
                           <a href="/forgotpassword/">Забыли пароль?</a>
                       </li>
                       <li>
                           <a href="/register/">Регистрация</a>
                       </li>
                   </ul>

               </div>
           </div>
       </nav>


          <div id="content-container">
                   <main>
                        <header <? if ($main_page==false) {?>class="white"<?}?> >

                                <a href="/" class="logo">
                                    <div class="logo-img">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/images/background/logox2.png" alt="">
                                    </div>
                                </a>

                            <div class="main-menu clear-after">
                                <a href="#" class="item menu-menu" data-type="getMovedPanel" data-target="#side-main-menu">
                                    <i class="fa fa-bars"></i>
                                </a>

                                <a href="#" class="item menu-search"  data-type="getMovedPanel" data-target="#side-search">
                                     <i class="fa fa-search"></i>
                                </a>

								<?$APPLICATION->IncludeComponent(
									"bitrix:sale.basket.basket.line",
									"mobile_small_basket",
									Array(
										"PATH_TO_BASKET" => SITE_DIR."cabinet/basket/",
										"SHOW_NUM_PRODUCTS" => "Y",
										"SHOW_TOTAL_PRICE" => "N",
										"SHOW_EMPTY_VALUES" => "N",
										"SHOW_PERSONAL_LINK" => "N",
										"PATH_TO_PERSONAL" => SITE_DIR."personal/",
										"SHOW_AUTHOR" => "N",
										"PATH_TO_REGISTER" => SITE_DIR."login/",
										"PATH_TO_PROFILE" => SITE_DIR."personal/",
										"SHOW_PRODUCTS" => "Y",
										"POSITION_FIXED" => "N"
									)
								);?>
                                <?/*<a href="#" class="item menu-cart to-cart">
                                    <img src="<?=SITE_TEMPLATE_PATH?>/images/svg/cart.svg">
                                    <span class="count">5</span>
                                </a>*/?>
                            </div>
                     </header>

                        <section class="content">
                            <div class="main-content">

                            <? if ($main_page==false) {?>

                            <? } ?>

							<? if ($cabinet_page === true) {?>
								<div class="cabinet-aside align-center">
									<div class="title align-center"><?$APPLICATION->ShowTitle(false);?></div>
									<?$APPLICATION->IncludeComponent(
										"citfact:elements.list",
										"menu_cabinet_mobile",
										Array(
											"IBLOCK_ID" => IBLOCK_MENU_CABINET,
											"PROPERTY_CODES" => array('LINK'),
                                            "TEMPLATE_NAME" => 'menu_cabinet_mobile',
                                            "IS_OPEN" => ($USER->IsAuthorized() && $page=='/cabinet/index.php')?'Y':'N',
                                            "IS_AUTH" => ($USER->IsAuthorized())?'Y':'N',
										)
									);?>
								</div>
							<?}?>