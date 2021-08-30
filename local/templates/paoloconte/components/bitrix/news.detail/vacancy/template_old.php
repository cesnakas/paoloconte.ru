<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
//if($USER->GetID() == 16240){echo'<pre>';print_r($arResult);echo'</pre>';}
$_SESSION['LIGHT'] = $arResult['PROPERTIES']['LIGHT']['VALUE'];
$_SESSION['VACANCY'] = $arResult['NAME'];

$this->setFrameMode(true);?>

<div class="detail-action-wrap">
	<?/*$file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=>1370, 'height'=>380), BX_RESIZE_IMAGE_PROPORTIONAL);
	?>
	<?if (strlen($file['src']) > 0)
	{?>
	<div class="image">
		<img src="<?=$file['src']?>" alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>">
	</div>
	<?}*/?>
	<?foreach($arResult['DISPLAY_PROPERTIES'] as $val)
	{
		$v = ' - ';
		if(!empty($val['DISPLAY_VALUE']))$v = $val['DISPLAY_VALUE'];
	?>	
		<p>
			<strong><?=$val['NAME'];?>:</strong>
			<br/>
			<?=$v;?>
		</p>
		<br/>
		
	<?}?>

	<p><?=$arResult['DETAIL_TEXT']?></p>
	
	<a onclick="down('modal-body')" href="#anketa?utm_source=site&utm_medium=button&utm_term=anketa&utm_content=anketa2vacancy&utm_campaign=anketa-button" 
	class="btn btn-green full-franch big-2 mode2 icon-arrow-right" data-toggle="modal" data-target="#franchiseModal" >
	Заполнить анкету</a>
	<a name="anketa"></a>
 
<script type="text/javascript">
function down(id)
{
  var a = document.getElementById(id);
  if ( a.style.display == 'none' )
    a.style.display = 'block'
  else
    if ( a.style.display == 'block' )
    a.style.display = 'none';
};
</script>
	
<?/*		<div class="modal fade franchiseModal" id="franchiseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal"></button>*/?>
				<a name="anketa"></a>
				<div class="modal-body" id="modal-body" style="display: none;">
					<div class="modal-title">
						Анкета соискателя на вакансию &laquo;<?=$arResult['NAME'];?>&raquo;
					</div>
					<?$APPLICATION->IncludeComponent(
						"bitrix:form.result.new",
						"vacancy",
						Array(
							"COMPONENT_TEMPLATE" => ".default",
							"WEB_FORM_ID" => "2",
							"IGNORE_CUSTOM_TEMPLATE" => "N",
							"USE_EXTENDED_ERRORS" => "N",
							"SEF_MODE" => "N",
							"VARIABLE_ALIASES" => Array("WEB_FORM_ID"=>"WEB_FORM_ID","RESULT_ID"=>"RESULT_ID"),
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "3600",
							"LIST_URL" => "result_list.php",
							"EDIT_URL" => "result_edit.php",
							"SUCCESS_URL" => "",
							"CHAIN_ITEM_TEXT" => "",
							"CHAIN_ITEM_LINK" => "",
							"AJAX_MODE" => 'Y'
						)
					);?>
				</div>
<?/*			</div>
		</div>
	</div>
*/?>	

<div class="btn-box align-center" id="new_resume" style="display:none">
	<a href="#" class="btn btn-gray-dark mode2 icon-arrow-right" data-toggle="modal" data-target="#getResumeModal">Создать резюме</a>
</div>

<?// Анкета во всплывающем окне?>
<div class="modal fade getResumeModal" id="getResumeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
 <button type="button" class="close" data-dismiss="modal"></button>
<div class="modal-body">
			
			
<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">
	<div class="modal-title">
		Опыт работы
	</div>
	<div id="opyt">
			<div class="line">				
				<input type="text" name="company" placeholder="Название организации"/>
			</div>
			<div class="line">
				<input type="text" name="city" placeholder="Город"/>
			</div>
			<div class="line">
				<input type="text" name="sf" placeholder="Сфера деятельности"/>
			</div>
			<div class="line">
				<input type="text" name="dolg" placeholder="Должность"/>
			</div>
			<div class="line">
				<input type="text" name="date_start" style="width:auto;" placeholder="Начало работы"/>
				&nbsp;&nbsp;
				<input type="checkbox" name="ch_date"/>&nbsp;по настоящее время
			</div>
			<div class="line" id= "end" style="display:block;">
				<input type="text" name="date_end" style="width:auto;" placeholder="Окончание работы"/>
			</div>			
			<div class="line">	
					<textarea name="about" cols="30" rows="10" placeholder="Обязанности, функции, достижения"></textarea>
			</div>
			<br/>
	</div>	
			Добавить предыдущее место работы
	<div class="modal-title">
		Образование
	</div>
	<div id="obraz">
			<div class="line">	
				<select name="level" required size = "1" style="width:100%;">
					<option value="Высшее">Высшее</option>
					<option value="Неоконченное высшее">Неоконченное высшее</option>
					<option value="Среднее специальное">Среднее специальное</option>
					<option value="Среднее">Среднее</option>
					<option value="Среднее">Нет</option>
				</select>
			</div>
			<div class="line">
				<input type="text" name="univer" placeholder="Учебное заведение"/>
			</div>
			<div class="line">
				<input type="text" name="st_syty" placeholder="Город"/>
			</div>
			<div class="line">
				<input type="text" name="spec" placeholder="Специальность"/>
			</div>
			<div class="line">
				<input type="text" name="end_date" placeholder="Год окончания"/>
			</div>			
			<br/>			
	</div>		
			<div class="line">
			Добавить ещё одно место учёбы
			</div>
					
</div>
<?/*		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<div class="line">
				<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
					<input type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'PHONE'? 'mask-phone':''?> <?=$arProp['CODE'] == 'EMAIL'? 'email':''?> <?=$arProp['CODE'] == 'PRICE'? 'mask-price':''?> <?=$arProp['CODE'] == 'BIRTHDATE'? 'mask-date':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"/>
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
			<a href="#" class="float-right link">Заполнить резюме на сайте</a>
		</div>
		<div id="files"></div>
*/?>
		<div class="line">
			<input type="submit" id="vacancy_simple_submit" class="btn full btn-gray-dark" value="Отправить анкету на рассмотрение">
		</div>

		<div class="errors_cont"></div>
		<div class="success_cont"></div>
</form>


			</div>
		</div>
	</div>
</div>
<div class="social-box align-center">
		 
		<div class="social">
			 <div class="fb-like" data-href="<?=$_SERVER['SCRIPT_URI'];?>" data-layout="box_count" data-action="like" data-show-faces="true" data-share="false"></div>
			 <div id="vk_like"></div>
			 <a href="https://instagram.com/paolo.conte.shop/" class="instagram"><i class="fa fa-instagram"></i></a>
			 <!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="//vk.com/js/api/openapi.js?116"></script>

<script type="text/javascript">
VK.init({apiId: 5019263, onlyWidgets: true});
VK.Widgets.Like("vk_like", {type: "vertical"});
</script>
		</div>
	</div>

</div>