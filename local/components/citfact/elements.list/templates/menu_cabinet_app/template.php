<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?//echo "<pre style=\"display:block;\">"; print_r($arResult); echo "</pre>";?>
<div class="filter-wrap">
	<div class="panel-group" id="" role="tablist" aria-multiselectable="true">
		<div class="panel">
			<div class="title no-margin panel-heading pts-bold" role="tab" id="heading-mf1">
				<a class="collapsed dark" data-toggle="collapse" href="#collapse-mf1" aria-expanded="true" aria-controls="collapse-mf1">
					<i class="fa fa-bars"></i> Меню личного кабинета
				</a>
			</div>
			<div id="collapse-mf1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-mf1">
				<div class="panel-body">
					<div class="cabinet-menu no-margin">
						<ul>
							<?foreach ($arResult['ITEMS'] as $key => $arItem){?>
								<li><a href="/paoloconte_app<?=$arItem['PROPERTY_LINK_VALUE']?>" title="<?=$arItem['PROPERTY_LINK_VALUE']?>"><?=$arItem['NAME']?></a></li>
							<?}?>
							<? if($GLOBALS['USER']->IsAuthorized()): ?>
								<li class="exit">
									<a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
										"login",
										"logout",
										"register",
										"forgot_password",
										"change_password"));?>" title="Выход"><i class="fa fa-sign-in"></i>Выход</a>
								</li>
							<? else: ?>
							<? endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>