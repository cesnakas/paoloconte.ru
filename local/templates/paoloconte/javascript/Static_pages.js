$(document).ready(function(){
	$('.table-title').each(function() {
		$(this).next().hide();
	});
	$('.show-hide').each(function() {
		$(this).next().hide();
	});
	$('.table-title').click(function() {
		 $(this).next().toggle();
	});
	$('.show-hide').click(function() {
		 $(this).next().toggle();
	});
});