<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$cartStyle = 'bx_cart_block';
$cartId = $cartStyle . $component->getNextNumber();
$arParams['cartId'] = $cartId;

if ($arParams['SHOW_PRODUCTS'] == 'Y')
    $cartStyle .= ' bx_cart_sidebar btn-box';

if ($arParams['POSITION_FIXED'] == 'Y') {
    $cartStyle .= " bx_cart_fixed {$arParams['POSITION_HORIZONTAL']} {$arParams['POSITION_VERTICAL']}";
    if ($arParams['SHOW_PRODUCTS'] == 'Y')
        $cartStyle .= ' close';
}
?>

<script>
  var <?=$cartId?> =
  new BitrixSmallCartList;
</script>

<div id="<?= $cartId ?>" class="<?= $cartStyle ?>">
    <?
    $frame = $this->createFrame()->begin();

    require(realpath(dirname(__FILE__)) . '/ajax_template.php');

    $frame->beginStub();
    ?>

    <div class="moved-panel-head animation right-to-left animate">
        <div class="h6">
            Ваша корзина <span class="count">(0)</span>
        </div>

        <span class="close close-moved-pannel"></span>
    </div>
    <? $frame->end(); ?>
</div>

<script>
    <?=$cartId?>.siteId = '<?=SITE_ID?>';
    <?=$cartId?>.cartId = '<?=$cartId?>';
    <?=$cartId?>.ajaxPath = '<?=$componentPath?>/ajax.php';
    <?=$cartId?>.templateName = '<?=$templateName?>';
    <?=$cartId?>.arParams =  <?=CUtil::PhpToJSObject($arParams)?>;
    <?=$cartId?>.closeMessage = '<?=GetMessage('TSB1_COLLAPSE')?>';
    <?=$cartId?>.openMessage = '<?=GetMessage('TSB1_EXPAND')?>';
    <?=$cartId?>.activate();
</script>
