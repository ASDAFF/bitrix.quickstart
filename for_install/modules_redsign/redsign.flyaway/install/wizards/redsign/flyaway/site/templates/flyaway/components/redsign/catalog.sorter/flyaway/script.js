function RSFlyAwaySorterGo(ajaxpagesid, $obj, url, isBigdata) {
	if ($obj) {
		var catalogSelector = '#'+ajaxpagesid;

		rsFlyaway.darken($(catalogSelector));

		if (isBigdata != 'Y' && url && url != '') {
            console.log( 'url = ' + url );
			$.getJSON(url, {}, function(json){
				RSFlyAwayPutJSon(json);
			}).fail(function(json){
				console.warn( 'sorter - change template -> error responsed' );
			}).always(function() {
				rsFlyaway.darken($(catalogSelector));
				initCompare();
				rsFlyaway_SetInFavorite();
				initTimer();
				initViews();
				initSelect();
			});
		}
	}
}

$(document).ready(function(){
	// ajax sorter -> change (click link)
	$(document).on('click','.js-sorterajax-switcher, .js-sorter-switcher',function(e){
		var $link = $(this);
		if ($link.parents('.catalogsorter').length > 0) {
			var ajaxpagesid = $link.parents('.catalogsorter').data('ajaxpagesid');
			if (ajaxpagesid && ajaxpagesid != '') {
				if ($link.parents('.js-bigdata').length > 0) { // big data
					console.log( 'sorter - bigdata' );
					RSFlyAwaySorterGo(ajaxpagesid, $link, '', 'Y');

					var $jsBigdata = $link.parents('.js-bigdata');

					BX.ajax({
						url: $jsBigdata.data('url'),
						method: 'POST',
						data: {'parameters':$jsBigdata.data('parameters'), 'template':$jsBigdata.data('template'), 'rcm':'yes', 'view':$link.data('fvalue')},
						dataType: 'html',
						processData: false,
						start: true,
						onsuccess: function (html) {
							var ob = BX.processHTML(html);
							// inject
							BX($jsBigdata.data('injectId')).innerHTML = ob.HTML;
							BX.ajax.processScripts(ob.SCRIPT);
							rsFlyaway.darken($(catalog_selector));
						}
					});

				} else { // normal components
					var url = $link.attr('href') + '&isAjax=Y';
					RSFlyAwaySorterGo(ajaxpagesid, $link, url, 'N');
				}
			}
		}
		e.preventDefault();
	});

	$('.js-sorter').each(function() {
		var $item = $(this).find('.js-sorter-item'),
            $btn = $(this).find('.js-sorter-btn');

		$item.on('click', function(e) {
			var text = $(this).find('.js-sorter-text').text();

			$btn.html(text);
			$item.removeClass('views-item_current');
			$(this).addClass('views-item_current');
			e.preventDefault();
		});
	});
});
