jQuery(function(){
	var setTimeoutConst;
	if ((Modernizr.mq('only all and (min-width: 768px)') && !Modernizr.touch) || $("html.ie8").length>0) {
		$('.main-navigation .navbar-nav>li.dropdown, .main-navigation li.dropdown>ul>li.dropdown').hover(
		function(){
			var $this = $(this);
			setTimeoutConst = setTimeout(function(){
				$this.addClass('open').slideDown();
			}, 0);

		},	function(){ 
			clearTimeout(setTimeoutConst );
			$(this).removeClass('open');
		});
	}
	$(window).resize(function(){
		var setTimeoutConst;
		if ((Modernizr.mq('only all and (min-width: 768px)') && !Modernizr.touch) || $("html.ie8").length>0) {
			$('.main-navigation .navbar-nav>li.dropdown, .main-navigation li.dropdown>ul>li.dropdown').hover(
			function(){
				var $this = $(this);
				setTimeoutConst = setTimeout(function(){
					$this.addClass('open').slideDown();
				}, 0);

			},	function(){ 
				clearTimeout(setTimeoutConst );
				$(this).removeClass('open');
			});
		}
	});
});