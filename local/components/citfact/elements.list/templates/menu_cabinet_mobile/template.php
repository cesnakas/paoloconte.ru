<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?
global $USER;
?>
<div class="filter-wrap">
	<div class="panel-group" id="" role="tablist" aria-multiselectable="true">
		<div class="panel">
			<div class="title no-margin panel-heading pts-bold" role="tab" id="heading-mf1">
				<a class="<?= $arParams['IS_OPEN']=='Y'?'':'collapsed'; ?> dark" data-toggle="collapse" href="#collapse-mf1" aria-expanded="true" aria-controls="collapse-mf1">
					<i class="fa fa-bars"></i> Меню личного кабинета
				</a>
			</div>
			<div id="collapse-mf1" class="panel-collapse collapse <?= $arParams['IS_OPEN']=='Y'?'in':''; ?>" role="tabpanel" aria-labelledby="heading-mf1">
				<div class="panel-body">
					<div class="cabinet-menu no-margin">
						<ul>
							<?foreach ($arResult['ITEMS'] as $key => $arItem){?>
								<li><a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" title="<?=$arItem['PROPERTY_LINK_VALUE']?>"><?=$arItem['NAME']?></a></li>
							<?}?>
							<?if ($USER->IsAuthorized())
							{?>
							<li class="exit">
								<a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
									"login",
									"logout",
									"register",
									"forgot_password",
									"change_password"));?>" title="Выход"><i class="fa fa-sign-in"></i>Выход</a>

							</li>
							<?}?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>