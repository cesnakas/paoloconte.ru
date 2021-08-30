<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="personal-box ">
<?
if (!empty($arResult["ORDER"]))
{
	$arItemRR = array();
	$dbBasketItems = CSaleBasket::GetList(array("ID" => "ASC"),array("ORDER_ID" => $arResult['ORDER']['ID']),false,false,array('ID',"PRODUCT_ID", "QUANTITY", "PRICE"));
	while ($arItem = $dbBasketItems->Fetch()) {
		$dbItemsCart = CCatalogSKU::getProductList($arItem['PRODUCT_ID'], IBLOCK_SKU);
		if (!empty($dbItemsCart) && !empty($dbItemsCart[$arItem['PRODUCT_ID']])) {
			if (empty($arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']])) {
				$arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']] = array('QUANTITY' => $arItem['QUANTITY'], 'PRICE' => $arItem['PRICE']);
			}else{
				$arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['QUANTITY'] = $arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['QUANTITY'] + $arItem['QUANTITY'];
				$arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['PRICE'] = $arItem['PRICE'];
			}
		}
	}
	?>
	<?if (!empty($arItemRR)) {?>
		<script type="text/javascript">
			<?if(!empty($arResult['ORDER']['USER_EMAIL'])){?>
				(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
					rrApi.setEmail("<?=$arResult['ORDER']['USER_EMAIL'];?>");
				});
			<?}?>
			rrApiOnReady.push(function() {
				try {
					rrApi.order({
						transaction: <?=$arResult['ORDER']['ID']?>,
						items: [
						<?foreach ($arItemRR as $id => $val) {?>
						{ id: <?=$id?>, qnt: 1,  price: <?printf('%d',$val['PRICE'])?>},
						<?}?>
						]
					});
				} catch(e) {}
			})
		</script>
	<?}?>
	<div class="box-title">
		<?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?>
	</div>

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?>
				<br /><br />
				<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
			</td>
		</tr>
	</table>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<br /><br />

		<table class="sale_order_full_table">
			<tr>
				<td class="ps_logo">
					<div class="pay_name"><?=GetMessage("SOA_TEMPL_PAY")?></div>
					<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
					<div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div><br>
				</td>
			</tr>
			<?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0 && $arResult["ORDER"]['STATUS_ID'] != 'N' &&$arResult["ORDER"]['STATUS_ID'] != 'C')
			{
				?>
				<tr>
					<td>
						<?
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
							{
								?><br />
								<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
							}
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
}
else
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>
</div>
