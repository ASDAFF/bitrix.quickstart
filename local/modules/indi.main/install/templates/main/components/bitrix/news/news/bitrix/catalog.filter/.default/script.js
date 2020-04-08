$(function() {
	$(document).on('click', '.js-news-period', function(e){
		var form=$(this).closest("form");
		form.find("#arNewsFilter_DATE_ACTIVE_FROM_1").val("01.01."+$(this).text());
		form.find("#arNewsFilter_DATE_ACTIVE_FROM_2").val("31.12."+$(this).text());
		form.submit();
		return false;
	});
	$('.catalog-filter-default :input').change(function() {
		$(this.form).submit();
	})
});