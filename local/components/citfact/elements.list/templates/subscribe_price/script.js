$(document).ready(function (data) {

	//изменение id товара в зависимости от выбраного размера
	$(document).on('click', 'a.btn-tobasket', function(e) {
		if ($(this).attr("href") === "#")
		{
			$('#chooseSizeModal').modal('show');
			return;
		}
		else if($(this).data('is-basket') == true && $(this).attr("href") !== "#")
		{
			$('#offerInbasketModal').modal('show');
			return;
		}
		location.reload();
	});
	$(document).on('change', 'input.radio-offer', function() {
		var productId = $(this).data('id');
		var productFId = $(this).data('product-id');
		var productName = $(this).data('name');

		if (document.getElementById(productId) !== null)
		{
			document.getElementById("S" + productFId).textContent="Уже в корзине";
			$("a.btn-tobasket[data-product-id-marker = '"+ productFId + "']").attr('data-is-basket', 'true');

		}
		else
		{
			document.getElementById("S" + productFId).textContent="В корзину";
			$("a.btn-tobasket[data-product-id-marker = '"+ productFId + "']").attr('data-is-basket', 'false');
		}

		$(this).closest('.basket-item').find('input.radio-offer').not($(this)).prop('checked', false);
		$(this).closest('.basket-item').find('label.active').not($(this)).removeClass("active");
		$(this).closest('label').addClass('active');
		var elems = document.getElementsByTagName('label');
		$(document).find('#input_id_product_'+productId).val(productId);
		$(document).find('#input_name_product_'+productId).val(productName);
		$("a.btn-tobasket[data-product-id-marker = '"+ productFId + "']").attr('data-product-id', productId);
		$("a.btn-tobasket[data-product-id-marker = '"+ productFId + "']").attr("href", "/catalog/?id=" + productId + "&action=ADD2BASKET&ajax_basket=Y")
	});

});