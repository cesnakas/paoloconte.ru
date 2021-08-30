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
	<div class="btn-box align-center">
 <a href="#" class="btn btn--black" data-toggle="modal" data-target="#getResumeModal">Заполнить анкету</a>
	</div>
	<div class="social-box align-center">
		 <br/><br/>Следите за новыми вакансиями в соц.сетях:
		<div class="social">
			 <?/*<a href="#" class="twitter"><i class="fa fa-twitter"></i></a>*/?> 
			 <a href="https://www.facebook.com/Paolo.Conte.Shoes" class="facebook"><i class="fa fa-facebook"></i></a> 
			 <a href="https://vk.com/paolo.conte.shop" class="vk"><i class="fa fa-vk"></i></a> 
			 <a href="https://instagram.com/paolo.conte.shop/" class="instagram"><i class="fa fa-instagram"></i></a>
		</div>
	</div>
</div>

 <?

 // Анкета во всплывающем окне?>
<div class="modal fade getResumeModal" id="getResumeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-new">
		<div class="modal-content">
 <button type="button" class="close" data-dismiss="modal"></button>
			<div class="modal-body">
<?if(empty($arResult['PROPERTIES']['LIGHT']['VALUE']))
{
$APPLICATION->IncludeComponent(
	"citfact:form.ajax",
	"vacancy_simple",
	Array(
		"VACANCY_NAME" => $arResult['NAME'],
		"IBLOCK_ID" => 40,
		"SHOW_PROPERTIES" => array(
			'VACANCY'=>array(
				'type'=>'text',
				'placeholder'=>'Вакансия *',
				'value' => $arResult['NAME'],
				'required'=>'Y'
				),
			'FIO'=>array(
				'type'=>'text',
				'placeholder'=>'ФИО *',
				'required'=>'Y'
				),
			'PHONE'=>array(
				'type'=>'text',
				'placeholder'=>'Телефон *',
				'required'=>'Y'
				),
			'EMAIL'=>array(
				'type'=>'text',
				'placeholder'=>'E-mail *',
				'required'=>'Y'
				),
			'BIRTHDATE'=>array(
				'type'=>'text',
				'placeholder'=>'Дата рождения *',
				'required'=>'Y'
				),
			'COUNTRY'=>array(
				'type'=>'text',
				'placeholder'=>'Гражданство *',
				'required'=>'Y'
				),
			'ADDRESS'=>array(
				'type'=>'text',
				'placeholder'=>'Город проживания *',
				'required'=>'Y'
				),
			'SALARY'=>array(
				'type'=>'text',
				'placeholder'=>'Ожидания по зарплате *',
				'required'=>'Y'
				),
			'REF_FROM'=>array(
				'type'=>'text',
				'placeholder'=>'Откуда узнали о вакансии? *',
				'required'=>'Y'
				),
			),
		"EVENT_NAME" => "VACANCY_SIMPLE_FORM",
		"SUCCESS_MESSAGE" => "Ваша анкета принята. Мы свяжемся с вами в ближайшее время.",
		"ELEMENT_ACTIVE" => "Y",
		"AJAX_FILES_PATH" => "/include/ajax_fileupload/files/"
	)
);
}else{
$APPLICATION->IncludeComponent(
	"citfact:form.ajax",
	"vacancy_full",
	Array(
		"VACANCY_NAME" => $arResult['NAME'],
		"IBLOCK_ID" => 40,
		"SHOW_PROPERTIES" => array(
			'VACANCY'=>array(
				'type'=>'text',
				'placeholder'=>'Вакансия *',
				'value' => $arResult['NAME'],
				'required'=>'Y'
				),
			'FIO'=>array(
				'type'=>'text',
				'placeholder'=>'ФИО *',
				'required'=>'Y'
				),
			'PHONE'=>array(
				'type'=>'text',
				'placeholder'=>'Телефон *',
				'required'=>'Y'
				),
			'EMAIL'=>array(
				'type'=>'text',
				'placeholder'=>'E-mail *',
				'required'=>'Y'
				),
			'BIRTHDATE'=>array(
				'type'=>'text',
				'placeholder'=>'Дата рождения *',
				'required'=>'Y'
				),
			'COUNTRY'=>array(
				'type'=>'text',
				'placeholder'=>'Гражданство *',
				'required'=>'Y'
				),
			'ADDRESS'=>array(
				'type'=>'text',
				'placeholder'=>'Город проживания *',
				'required'=>'Y'
				),
			'SALARY'=>array(
				'type'=>'text',
				'placeholder'=>'Ожидания по зарплате *',
				'required'=>'Y'
				),
			'REF_FROM'=>array(
				'type'=>'text',
				'placeholder'=>'Откуда узнали о вакансии? *',
				'required'=>'Y'
				),
			'OBRAZ'=>array(
				'type'=>'text',
				'placeholder'=>'Образование *',
				'required'=>'Y'
				),
			'OPYT'=>array(
				'type'=>'textarea',
				'placeholder'=>'Опыт работы (должность, стаж, функции, достижения) *',
				'required'=>'Y'
				),
			'FILE_REZUME'=>array(
				'type'=>'file',
				'required'=>'Y'
				),
			),
		"EVENT_NAME" => "VACANCY_SIMPLE_FORM",
		"SUCCESS_MESSAGE" => "Ваша анкета принята. Мы свяжемся с вами в ближайшее время.",
		"ELEMENT_ACTIVE" => "Y",
		"AJAX_FILES_PATH" => "/include/ajax_fileupload/files/"
	)
);
}?>
			</div>
		</div>
	</div>
</div>


<?/*
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
*/?>
</div>