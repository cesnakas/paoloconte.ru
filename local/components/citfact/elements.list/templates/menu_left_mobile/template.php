<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
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
											<ul>
												<? foreach ($arSection['SUBSECTIONS'] as $arSubsection): ?>
													<li>
                                                        <? if ($arSubsection['SELECTED'] == 'SELECTED'): ?>
                                                            <b href="<?=$arSubsection['URL']?>"><?=$arSubsection['NAME']?></b>
                                                        <? else: ?>
                                                            <a href="<?=$arSubsection['URL']?>"><?=$arSubsection['NAME']?></a>
                                                        <? endif; ?>
                                                    </li>
												<? endforeach; ?>
											</ul>
										</div>
									</div>
								<?endif;?>
							</li>
						<?endforeach;?>
						<?/*<li>
							<a href="#">Мужская аксессуары</a>
						</li>
						<li>
							<a href="#">Аксессуары для обуви</a>
						</li>
						<li>
							<a href="#">Новая коллекция</a>
						</li>*/?>
					</ul>
				</div>
			</div>
		<?endif?>
	</div>
<?endforeach?>


<?/*<div class="panel">
	<div class="panel-heading pts-bold" role="tab" id="heading-1">
		<a class="clear-after collapsed" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
			Для женщин
			<i class="fa fa-caret-down"></i>
		</a>
	</div>

	<div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1" style="height: 167px;">
		<div class="panel-body">
			<ul>
				<li class="lvl-2">
					<div class="panel-heading pts-bold" role="tab" id="heading-2-1">
						<a class="clear-after collapsed" data-toggle="collapse" href="#collapse-2-1" aria-expanded="true" aria-controls="collapse-2-1">
							Женская обувь
							<i class="fa fa-caret-down"></i>
						</a>
					</div>
					<div id="collapse-2-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2-1" style="height: 168px;">
						<div class="panel-body">
							<ul>
								<li>111</li>
								<li>222</li>
								<li>333</li>
								<li>444</li>
							</ul>
						</div>
					</div>
				</li>
				<li>
					<a href="#">Мужская аксессуары</a>
				</li>
				<li>
					<a href="#">Аксессуары для обуви</a>
				</li>
				<li>
					<a href="#">Новая коллекция</a>
				</li>
			</ul>
		</div>
	</div>
</div>*/?>