<?
require($_SERVER["DOCUMENT_ROOT"]."/paoloconte_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>
<script type="text/javascript">
	app.enableSliderMenu(true);
</script>

<div class="main-menu-wrap app_fix">
	<div class="menu-items">
		<a href="/paoloconte_app/" class="logo">
			<div class="logo-img">
				<img style="display: block; margin: auto; padding: 50px 0; max-width: 200px;" src="/local/templates/paoloconte_mobile/images/background/logox2.png" alt="">
			</div>
		</a>
	</div>
	<div class="aside-box mobile-menu">
		<div class="panel-group styled-mobile-menu rotate" id="" role="tablist" aria-multiselectable="true">
			<?$APPLICATION->IncludeComponent(
				"citfact:elements.list",
				"menu_left_app",
				Array(
					"IBLOCK_ID" => IBLOCK_MENU_LEFT_MOBILE,
					"IBLOCK_CATALOG_ID" => 10,
					"PROPERTY_CODES" => array('CATALOG_SECTION', 'LINK'),
				)
			);?>
		</div>
	</div>
	<div class="aside-box">
			<ul class="add-info">
				<li>
					<div class="menu-items">
						<i class="fa fa-shopping-cart"></i> <a href="/paoloconte_app/shops/">Магазины</a>
					</div>
				</li>
				<li>
					<div class="menu-items">
						<i class="fa fa-question-circle"></i> <a href="/paoloconte_app/help/oplata-i-dostavka/">Оплата и доставка</a>
					</div>
				</li>
				<li>
					<?if (!$USER->IsAuthorized()):?>
						<i class="fa fa-user small"></i> <a href="#" data-toggle="modal" data-target="#side-login">Авторизоваться</a>
					<?else:?>
						<div class="menu-items">
							<i class="fa fa-user small"></i> <a href="/paoloconte_app/cabinet/">Личный кабинет</a>
						</div>
					<?endif;?>
				</li>
				<li>
					<i class="fa fa-map-marker"></i> <a href="#" data-toggle="modal" data-target="#cityModal"><?$APPLICATION->IncludeComponent("articul.geolocation.city_current", "mobile", array(), false);?></a>
				</li>
				<li class="phone-link">
					<i class="fa fa-phone"></i> <a href="tel:+7 (800) 333 70 77" class="gray">+7 (800) 333 70 77</a>
				</li>
				<li>
					<div class="menu-items">
						<?if ($USER->IsAuthorized()):?>
							<i class="fa fa-sign-in small"></i> <a onclick="setTimeout(function() { BXMobileApp.UI.Page.reload(); }, 2000);" href="/paoloconte_app/index.php?logout=yes<?/*echo $APPLICATION->GetCurPageParam("logout=yes", array(
								"login",
								"logout",
								"register",
								"forgot_password",
								"change_password"));*/?>">Выйти</a>
						<?endif;?>
					</div>
				</li>
			</ul>
		</div>
</div>

<script>

	$(".menu-items").find("a").on("click", function() {
		BXMobileApp.UI.Slider.setState(BXMobileApp.UI.Slider.state.CENTER);
	});

	document.addEventListener("DOMContentLoaded", function ()
	{
		Menu.init(null);
	}, false);

	Menu = {
		currentItem: null,

		init: function (currentItem)
		{
			this.currentItem = currentItem;
			var items = document.getElementsByClassName("menu-items");
			var that = this;
			items.addEventListener("click", function (event)
			{
				that.onItemClick(event);
			}, false);
		},

		onItemClick: function (event)
		{
			var target = event.target;
			if (target && target.nodeType && target.nodeType == 1 && BX.hasClass(target, "menu-item"))
			{
				if (this.currentItem != null)
					this.unselectItem(this.currentItem);
				this.selectItem(target);

				var url = target.getAttribute("data-url");
				var pageId = target.getAttribute("data-pageid");

				if (BX.type.isNotEmptyString(url) && BX.type.isNotEmptyString(pageId))
					app.loadPage(url, pageId);
				else if (BX.type.isNotEmptyString(url))
					app.loadPage(url);

				this.currentItem = target;
			}

		},

		selectItem: function (item)
		{
			if (!BX.hasClass(item, "menu-item-selected"))
				BX.addClass(item, "menu-item-selected");
		},

		unselectItem: function (item)
		{
			BX.removeClass(item, "menu-item-selected");
		}
	}
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>