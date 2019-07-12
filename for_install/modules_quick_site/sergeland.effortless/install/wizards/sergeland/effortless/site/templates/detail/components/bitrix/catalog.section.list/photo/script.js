jQuery(document).ready(function($) {
	$(window).load(function() {
		// filter items on button click
		$('.filters').on( 'click', 'ul.nav li a', function() {
			var filterValue = $(this).attr('data-filter');
			$(".filters").find("li.active").removeClass("active");
			$(this).parent().addClass("active");
			$('.isotope-container').isotope({ filter: filterValue });
			return false;
		});
	});
});