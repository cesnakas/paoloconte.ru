//проверка на избранное CHECK_FAVORITE
function checkFavorite(){
	var products_ids = {};
	var products_favorite = $(document).find(".to-favorite-new");
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
							$(document).find('.to-favorite-new[data-product-id="' + result.products[i].ID + '"]')
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
							$(document).find('.to-favorite-new[data-product-id="' + result.products[i].ID + '"]')
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
		var productId = $(this).data('id');
		var productName = $(this).data('name');
		//console.log(productId+' '+productName);
		$(document).find('#input_id_product_'+productId).val(productId);
		$(document).find('#input_name_product_'+productId).val(productName);
		$('.btn-tobasket').data('product-id', $(this).val());
	});

	//добавление и удаление из избранного ADD_FAVORITE DEL_FAVORITE
	$(document).on("click", ".to-favorite-new", function () {
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

	//добавление в корзину
	$(document).on('click', '.btn-tobasket', function() {
		var product_id = $(this).data('product-id');
		var product_name = $(this).data('product-name');
		var product_price = $(this).data('product-price');
		var url_template = BX.message('BUY_URL');
		if(product_id != '') {
			var buy_url = url_template.replace('#ID#', product_id) + '&ajax_basket=Y';
			var buy_url = buy_url.replace(/&amp;/g, '&');
			overlay.show();
			console.log("add "+product_id+" name "+product_name+" price "+product_price);
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
								"ecommerce": {
										"add": {
												"products": [
														{
																"id": product_id,
																"name": product_name,
																"price": product_price,
																"quantity": 1
														}
												]
										}
								}
						});
			BX.ajax.loadJSON(
				buy_url,
				'',
				function (res) {
					//console.log(res);
					BX.onCustomEvent('OnBasketChange');
					overlay.hide();
				}
			);
		}
		return false;
	});
});