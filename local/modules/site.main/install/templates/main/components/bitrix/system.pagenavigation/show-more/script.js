/**
 * Обработчик шаблона постраничной навигации
 */
$(function() {
	var pageNavigationSelector = '.pagenvaigation-show-more';
	
	$(pageNavigationSelector).each(function() {
		var pageNavigation = $(this);
		var containerSelector = pageNavigation.data('parent-selector');
		var articlesSelector = pageNavigation.data('articles-selector');
		
		var container = pageNavigation.closest(containerSelector);
		
		//Постраничная навигация через кнопку "Показать еще"
		container.on('click', pageNavigationSelector + ' a', function() {
			var loading = new site.ui.loading(pageNavigation);
			
			$.get(
				$(this).attr('href'),
				function(response) {
					loading.hide();
					
					response = $('<div/>').html(response);
					
					response
						.find(containerSelector + ' ' + articlesSelector)
						.appendTo(container);
					
					pageNavigation.remove();
					pageNavigation = response
						.find(pageNavigationSelector)
						.appendTo(container);
				}
			);
			
			return false;
		});
	});
});