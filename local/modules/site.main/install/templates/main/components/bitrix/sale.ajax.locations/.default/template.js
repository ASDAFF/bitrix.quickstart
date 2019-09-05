$(function() {
	var domDocument = $(document);
	if (domDocument.data('sale-ajax-locations-default-ready')) {
		return;
	}
	domDocument.data('sale-ajax-locations-default-ready', true);
	
	$('.sale-ajax-locations-default').each(function() {
		var domElement = $(this);
		var url = domElement.data('ajax-gate');
		var params = domElement.data('params') || {};
		
		domDocument.on('change', '.sale-ajax-locations-default .location-observable', function() {
			var field = $(this);
			var domElement = field.closest('.sale-ajax-locations');
			var loading = new site.ui.loading(domElement);
			
			var extraParams = {
				'LOCATION_VALUE': ''
			};
			domElement.find('.location-observable').each(function() {
				extraParams[this.name] = this.value;
			});
			
			$.post(
				url,
				$.extend(params, extraParams),
				function(response) {
					loading.hide();
					domElement.replaceWith(response);
				}
			);
		});
	});
});