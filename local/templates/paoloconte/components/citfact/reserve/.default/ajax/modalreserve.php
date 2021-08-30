<?php
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");?>

<div class="modal fade cityModal" id="cityModalInReserve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "list_cities_modal_inreserve",
                Array(
                    'CUR_PAGE' => $APPLICATION->GetCurPageParam(),
                    "IBLOCK_ID" => 20,
                    "PROPERTY_CODES" => array('OBLAST', 'MAIN'),
                    "CURRENT_CITY_ID" => $_SESSION["CITY_ID"],
                    "SHOW_WINDOW" => false,
                )
            ); ?>
        </div>
    </div>
</div>