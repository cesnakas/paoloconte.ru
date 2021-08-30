<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<? foreach ($arResult['ITEMS'] as $key => $arItem):?>
	<div class="panel">
		<div class="panel-heading pts-bold" role="tab" id="heading-1">
			<a class="clear-after collapsed" data-toggle="collapse" href="#collapse-<?=$key?>" aria-expanded="true" aria-controls="collapse-<?=$key?>">
				<?=$arItem['NAME']?>
				<i class="fa fa-caret-down"></i>
			</a>
		</div>

		<?if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])):?>
			<div id="collapse-<?=$key?>" class="panel-collapse collapse panel-submenu" role="tabpanel" aria-labelledby="heading-<?=$key?>">
				<div class="panel-body">
					<ul>
						<?foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $section_id):?>
							<?$arSection = $arResult['SECTIONS'][$section_id];?>
							<li class="lvl-2">
								<div class="pts-bold" role="tab" id="heading-2-<?=$section_id?>">
									<a class="clear-after collapsed" data-toggle="collapse" href="#collapse-2-<?=$section_id?>" aria-expanded="true" aria-controls="collapse-2-<?=$section_id?>">
										<?=$arSection['NAME']?>
										<i class="fa fa-caret-down"></i>
									</a>
								</div>
								<?if (!empty($arSection['SUBSECTIONS'])):?>
									<div id="collapse-2-<?=$section_id?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2-<?=$section_id?>">
										<div class="panel-body">
											<div class="menu-items">
												<ul>
													<?foreach ($arSection['SUBSECTIONS'] as $arSubsection):?>
														<li><a href="/paoloconte_app<?=$arSubsection['URL']?>"><?=$arSubsection['NAME']?></a></li>
													<?endforeach;?>
												</ul>
											</div>
										</div>
									</div>
								<?endif;?>
							</li>
						<?endforeach;?>
					</ul>
				</div>
			</div>
		<?endif?>
	</div>
<?endforeach?>