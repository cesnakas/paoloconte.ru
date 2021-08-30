<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->SetFrameMode(true);
?>
<div class="panel-group accordion styled-accordion" id="accordion" role="tablist" aria-multiselectable="true">
	<? foreach ($arResult['ITEMS'] as $key => $arItem):?>
	<?$section_id = $arItem['PROPERTY_CATALOG_SECTION_VALUE'];?>
	<div class="panel accordion-group">
		<div class="panel-heading pts-bold" role="tab" id="heading-<?echo $key;?>">
			<a class="<?=($arItem['SELECTED'] == ''? 'collapsed':'')?>" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?echo $key;?>" aria-expanded="true" aria-controls="collapse-<?echo $key;?>">
				<?=$arItem['NAME']?>
				<i class="fa plus fa-plus"></i>
				<i class="fa minus fa-minus"></i>
			</a>
		</div>

		<?
		$arSubsections = $arResult['SECTIONS'][$section_id]['SUBSECTIONS'];
		if (!empty($arSubsections)):?>
			<div id="collapse-<?echo $key;?>" class="panel-collapse collapse <?=($arItem['SELECTED'] == 'SELECTED'? 'in':'')?>" role="tabpanel" aria-labelledby="heading-<?echo $key;?>">
				<div class="panel-body">
					<ul>
						<?foreach ($arSubsections as $arSubsection):?>
						    <li class="<?= ($arSubsection['SELECTED'] == 'SELECTED') ? " active " : "" ; ?>">
                                <? if ($arSubsection['SELECTED'] == 'SELECTED'): ?>
                                    <b class="<?= ($arSubsection['CODE'] == SECT_MAN_SHOES) || ($arSubsection['CODE'] == SECT_WOMAN_SHOES) ? ' special-sale' : ''  ?>"
                                       title="<?= $arSubsection['NAME'] ?>"><?= $arSubsection['NAME'] ?></b>
                                <? else: ?>
                                <a class="<?= ($arSubsection['CODE'] == SECT_MAN_SHOES) || ($arSubsection['CODE'] == SECT_WOMAN_SHOES) ? ' special-sale' : ''  ?>"
                                   href="<?= $arSubsection['URL'] ?>"
                                   title="<?= $arSubsection['NAME'] ?>"><?= $arSubsection['NAME'] ?></a>
                                <? endif; ?>
                            </li>
						<?endforeach?>
					</ul>
				</div>
			</div>
		<?endif?>
	</div>
	<?endforeach?>
</div>