<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])):?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?else:?>
	<?if($arResult["CAN_REPAY"]=="Y" && $arResult["PAY_SYSTEM"]["PSA_NEW_WINDOW"] != "Y"):?>
		<?
		$ORDER_ID = $ID;

		try
		{
			include($arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"]);
		}
		catch(\Bitrix\Main\SystemException $e)
		{
			if($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
				$message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
			else
				$message = $e->getMessage();

			ShowError($message);
		}

		?>
	<?endif?>
<?endif?>