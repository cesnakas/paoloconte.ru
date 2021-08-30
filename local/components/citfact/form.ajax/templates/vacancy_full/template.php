<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
//if($USER->GetID() == 16240){echo'<pre>';print_r($arResult);echo'</pre>';}?>
<?/*<script src="<?=$this->__folder?>/jquery.ui.widget.js"></script>
<script src="<?=$this->__folder?>/jquery.fileupload.js"></script>
<script src="<?=$this->__folder?>/jquery.iframe-transport.js"></script>*/?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_vacancy_simple: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<div class="modal-title">
	Анкета соискателя
</div>

<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">

		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<div class="line">
				<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
					<input type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'PHONE'? 'mask-phone':''?> <?=$arProp['CODE'] == 'EMAIL'? 'email':''?> <?=$arProp['CODE'] == 'PRICE'? 'mask-price':''?> <?=$arProp['CODE'] == 'BIRTHDATE'? 'mask-date':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>" value = "<?=$arProp['VALUE']?>"/>
				<?elseif($arProp['PARAMS_TYPE'] == 'textarea'):?>
					<textarea name="<?=$arProp['CODE']?>" cols="30" rows="10" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
				<?elseif ($arProp['PARAMS_TYPE'] == 'hidden'):?>
					<input type="hidden" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" value="<?=$arProp['VALUE']?>"/>
				<?endif;?>
			</div>
		<?endforeach?>

		<div class="line clear-after">
			<div class="download-box upload_file float-left upload_rezume_block">
				<a href="#" id="chose_file" class="attach-file">
					<span class="icon"><i class="fa fa-paperclip"></i></span>
					<span class="text filename">Прикрепить резюме</span>
				</a>
				<input type="hidden" name="FILE_REZUME" value="" />
				<input name="files" id="fileupload_rezume" class="file_upl hide" type="file">
			</div>
			<?/*<a href="#" class="float-right link">Заполнить резюме на сайте</a>*/?>
		</div>
		<div id="files"></div>

		<div class="line">
			<input type="submit" id="vacancy_simple_submit" class="btn full btn-gray-dark" value="Отправить анкету на рассмотрение">
		</div>

		<div class="errors_cont"></div>
		<div class="success_cont"></div>
</form>

<?/*<form action="#">
	<div class="line">
		<select class="style2 full">
			<option selected>Вакансия на которую претендуете</option>
			<option>Вакансия на которую претендуете</option>
			<option>Вакансия на которую претендуете</option>
		</select>
	</div>
	<div class="line">
		<input type="text" class="style2" placeholder="Ф.И.О.">
	</div>
	<div class="line">
		<select class="style2 full">
			<option selected>Дата рождения</option>
			<option>Вакансия на которую претендуете</option>
			<option>Вакансия на которую претендуете</option>
		</select>
	</div>
	<div class="line">
		<select class="style2 full">
			<option selected>Гражданство</option>
			<option>Вакансия на которую претендуете</option>
			<option>Вакансия на которую претендуете</option>
		</select>
	</div>
	<div class="line">
		<input type="text" class="style2" placeholder="Адрес проживания">
	</div>
	<div class="line">
		<input type="text" class="style2" placeholder="Ожидание по з/п">
	</div>
	<div class="line">
		<select class="style2 full">
			<option selected>Откуда вы узнали о вакансии</option>
			<option>Вакансия на которую претендуете</option>
			<option>Вакансия на которую претендуете</option>
		</select>
	</div>
	<div class="line clear-after">
		<div class="download-box upload_file float-left">
			<a href="#" id="chose_file" class="attach-file">
				<span class="icon"><i class="fa fa-paperclip"></i></span>
				<span class="text">Прикрепить резюме</span>
			</a>
			<input name="" class="file_upl hide" type="file">
		</div>
		<a href="#" class="float-right link">Заполнить резюме на сайте</a>
	</div>
	<div class="line">
		<input type="submit" class="btn full btn-gray-dark" value="Отправить анкету на рассмотрение">
	</div>
</form>*/?>

<?//\Citfact\Tools::pre($arResult['SHOW_PROPERTIES']);?>
