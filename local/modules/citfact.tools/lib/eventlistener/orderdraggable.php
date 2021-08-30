<?php

namespace Citfact\EventListener;

class OrderDraggable
{
    const RESPONSIBLE_COMMENT = 'RESPONSIBLE_COMMENT';
    public static function onInit()
    {
        return array("BLOCKSET" => "\Citfact\EventListener\OrderDraggable",
            "getScripts"  => array("\Citfact\EventListener\OrderDraggable", "mygetScripts"),
            "getBlocksBrief" => array("\Citfact\EventListener\OrderDraggable", "mygetBlocksBrief"),
            "getBlockContent" => array("\Citfact\EventListener\OrderDraggable", "mygetBlockContent"),
        );
    }

    public static function mygetBlocksBrief($args)
    {
        $id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;
        return array(
            'custom1' => array("TITLE" => "Комментарии"),
        );
    }

    public static function mygetScripts($args)
    {
        return "<script> 
            function changeText(){
                $('[data-responsible-comment-text]').show();
                $('[data-responsible-comment]').hide();
            }
            function canselText(){
                $('[data-responsible-comment-text]').hide();
                $('[data-responsible-comment]').show();
            }
            function updateText() {
                canselText();
                var wait = BX.showWait('available_action_wrapper');
                BX.ajax({
                    url: '/local/modules/citfact.tools/lib/ajax/updateCommentRespons.php',
                    method: 'POST',
                    data: { idOrder: $('[data-order-id]').data('order-id'), idProp: $('[data-text-res-comment]').data('text-res-comment'), idPropValue: $('[data-text-res-comment]').val() },
                    dataType: 'text',
                    onsuccess: function(result)
                    {
                        $('[data-comment-text-view]').html(result);
                        BX.closeWait('available_action_wrapper', wait)
                    }
                });
            }
        </script>";
    }

    public static function mygetBlockContent($blockCode, $selectedTab, $args)
    {
        $result = '';
        if ($selectedTab == 'tab_order')
        {
            if ($blockCode == 'custom1'){
                $result = self::generateHTMLComment($args['ORDER']);
            }
        }

        return $result;
    }

    public static function generateHTMLComment($order){
        $html = '';
        if(empty($order)){
            return 'empty($order)';
        }
        $propResComment ='';
        $propertyCollection = $order->getPropertyCollection();
        $arrProperty = $propertyCollection->getArray();
        foreach ($arrProperty['properties'] as $prop){
            if($prop['CODE'] == self::RESPONSIBLE_COMMENT){
                $propResComment = $prop;
                break;
            }
        }

        $html .= '
            <table class="adm-detail-content-table edit-table .lolo" width="100%" cellspacing="0" cellpadding="0" border="0" data-order-id="'.$order->getId().'" data-responsible-comment>
				<tbody>
					<tr>
						<td class="adm-detail-content-cell-l vat" width="40%">'.$propResComment['NAME'].'</td>
						<td class="adm-detail-content-cell-r"><a href="javascript:void(0);" style="text-decoration: none; border-bottom: 1px dashed" " data-change-comment onclick="changeText()">Редактировать комментарий</a>
						<p data-comment-text-view style="color:gray; max-width:800px; overflow:auto;">'.nl2br(htmlspecialcharsbx($propResComment['VALUE'][0])).'</p>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="adm-detail-content-table edit-table" width="100%" cellspacing="0" cellpadding="0" border="0" data-responsible-comment-text style="display: none;">
				<tbody>
					<tr>
                       
                        <td class="adm-detail-content-cell-r" style="padding-left: 25%">
                            <textarea cols="80" rows="8" data-text-res-comment="'.$propResComment['ID'].'">'.$propResComment['VALUE'][0].'</textarea>
                        </td>
					</tr>
					<tr>
                        
                        <td class="adm-detail-content-cell-r" style="padding-left: 40%">
                            <input type="button" value="Сохранить" onclick="updateText()">
                            <input type="button" value="Отмена" onclick="canselText()">
                        </td>
					</tr>
				</tbody>
			</table>
			
        ';

        return $html;
    }
}