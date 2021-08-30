<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<div class="your-city">
	<div class="city">
		Выберите город
	</div>
</div>
<form action="#">
	<div class="modal-body">
		<div class="city-list-wrap">
				<div class="city-list invacancy active">
					<ul>
						<?
						$count = count($arResult['ITEMS']);
						$count_incol = floor($count/4);
						if ($count_incol <= 1){
							$count_incol = $count;
						}
						if ($count%$count_incol > 0){
							$count_incol++;
						}
						?>
						<?
						$count = 0;
						foreach($arResult['ITEMS'] as $arCity):?>
							<li>
								<a href="<?=$APPLICATION->GetCurPageParam("city=".$arCity['ID'], array("city"));?>" title="<?=$arCity['PROPERTY_OBLAST_VALUE'];?>"><?=$arCity['NAME'];?></a>
							</li>
							<?if (($count+1)%$count_incol == 0 && $count != 0):?></ul><ul><?endif;?>
						<?
						$count++;
						endforeach;?>
					</ul>
				</div>
		</div>
	</div>
</form>