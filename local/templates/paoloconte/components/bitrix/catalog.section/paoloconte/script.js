//проверка на избранное CHECK_FAVORITE
function checkFavorite(){
    var products_ids = {};
    var products_favorite = $(document).find(".to-favorite");
    products_favorite.each(function(i){
        var prod_id = $(this).data('product-id');
        if(prod_id !='' && prod_id !=undefined)
            products_ids[i]={'ID':prod_id};
    });
    if(Object.keys(products_ids).length >0){
        var obj = {
            TYPE: "CHECK_FAVORITE",
            products_id: products_ids
        };
        $.post("/include/ajax_handler.php", obj)
            .done(function(outData){
                var result = JSON.parse(outData);
                if (result.status) {
                    for (i in result.products) {
                        if(result.products[i].FAVORITE == 'Y') {
                            $(document).find('.to-favorite[data-product-id="' + result.products[i].ID + '"]')
                                .attr('data-favorite-id', result.products[i].BASKET_ID)
                                .addClass("add-in-favorite")
                                .find("i").each(function () {
                                    if ($(this).hasClass("fa-heart-o")) {
                                        $(this).addClass('active').fadeOut(300);
                                    }
                                    else {
                                        $(this).removeClass('active').fadeIn(300);
                                    }
                                });
                        }
                        else{
                            $(document).find('.to-favorite[data-product-id="' + result.products[i].ID + '"]')
                                .attr('data-favorite-id', result.products[i].BASKET_ID)
                                .removeClass("add-in-favorite")
                                .find("i").each(function () {
                                    if ($(this).hasClass("fa-heart")) {
                                        $(this).addClass('active').fadeOut(300);
                                    }
                                    else {
                                        $(this).removeClass('active').fadeIn(300);
                                    }
                                });
                        }
                    }
                }
            });
    }
}

$(document).ready(function () {
    //изменение id товара в зависимости от выбраного размера
    $(document).on('change', 'input.radio-offer', function() {
        var item_id = $(this).data('item-id');
        $('.btn-tobasket[data-item-id="'+item_id+'"]').data('product-id', $(this).val());
        $('.tester.fast-order').data('product-id', $(this).val());
        //console.log($('.tester.fast-order'));
        $('.fast-order').data('product-id', $(this).val());
    });

    //добавление в избранное ADD_FAVORITE
    $(".to-favorite").on("click", function () {
        var that = this;
        var image = $(this).data('image');
        if($(this).hasClass('add-in-favorite')){
            var product_id = $(this).attr('data-favorite-id');
            var obj = {
                TYPE: "DEL_FAVORITE",
                product_id: product_id
            };
        }
        else{
            var product_id = $(this).data('product-id');
            var obj = {
                TYPE: "ADD_FAVORITE",
                product_id: product_id
            };
        }
        if (product_id != '') {
            overlay.show();
            $.post("/include/ajax_handler.php", obj)
                .done(function (outData) {
                    var result = JSON.parse(outData);
                    if (result.status) {
                        BX.onCustomEvent('OnBasketChange');
                        checkFavorite();
                        if (!$(that).hasClass("add-in-favorite")) {
                            $('#toFavoriteModal').find('img').attr('src', image);
                            $('#toFavoriteModal').modal('show');
                        }
                    }
                    else {
                        console.log("ОШИБКА! - " + result.error);
                    }
                })
                .complete(function () {
                    overlay.hide();
                });
        }
    });

    //проверка на избранное CHECK_FAVORITE
    checkFavorite();

    /*выбор кол-ва товаров на странице*/
    $(document).on('change', '.pageElementCount', function(){
        document.location.href = $(this).val();
    });
});