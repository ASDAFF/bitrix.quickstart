var RSGoPro_JSPParentSelector = '.scrollp';
var RSGoPro_JSPScrollSelector = '.scroll';
var RSGoPro_JSPAllChildrensSelector = '.scrollinner';
var RSGoPro_JSPOneChildrenSelector = '.scrollitem';
var RSGoPro_JSPButtonsSelector = '.scrollbtn';
var RSGoPro_SimpleScrollScrollSpeed = 500;

(function($){
    $.fn.hasScrollBarY = function(){
        var divnode = this.get(0);
        if(divnode.scrollHeight>divnode.clientHeight)
            return true;
    }
	 $.fn.hasScrollBarX = function(){
        var divnode = this.get(0);
        if(divnode.scrollWidth>divnode.clientWidth)
            return true;
    }
})(jQuery);

function RSGoPro_JSPInit(selector)
{
	var $scroll = $(selector);
	if( $scroll.length>0 )
	{
		$scroll.parents(RSGoPro_JSPParentSelector).addClass('jsp');
		$scroll.jScrollPane({animateScroll:true,mouseWheelSpeed:30,verticalGutter:0});
		$scroll.each(function(i){
			if( $(this).hasClass('jspScrollable') )
			{
				$(this).parents(RSGoPro_JSPParentSelector).addClass('jspHasScroll');
			}
		});
	}
}
function RSGoPro_JSPReinit(selector,needDestroy)
{
	var $scroll = $(selector);
	if( $scroll.length>0 )
	{
		if( $scroll.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') )
		{
			var count=0,elemWidth=0;
			$scroll.each(function(i){
				count = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).length;
				elemWidth = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).filter(':first').outerWidth(true);
				$(this).find(RSGoPro_JSPAllChildrensSelector).css({width:(count*elemWidth)+'px'});
			});
		}
		if( needDestroy )
		{
			$scroll.data('jsp').destroy();
			RSGoPro_JSPInit(selector);
		} else {
			var pane2api;
			setTimeout(function(){ // fix for slow shit
				$scroll.parents(RSGoPro_JSPParentSelector).removeClass('jspHasScroll');
				$scroll.each(function(i){
					pane2api = $(this).data('jsp');
					pane2api.reinitialise();
					if( $(this).hasClass('jspScrollable') )
					{
						$(this).parents(RSGoPro_JSPParentSelector).addClass('jspHasScroll');
					}
				});
			},50);
		}
	}
}

function RSGoPro_SimpleScrollInit(selector)
{
	var $scroll = $(selector);
	if( $scroll.length>0 )
	{
		var count=0,elemWidth=0;
		if( $scroll.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') )
		{
			$scroll.each(function(i){
				count = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).length;
				elemSize = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).filter(':first').outerWidth(true);
				$(this).css({overflowX:'auto',overflowY:'hidden'}).find(RSGoPro_JSPAllChildrensSelector).css({width:(count*elemSize)+'px'});
				if( $(this).hasScrollBarX() )
				{
					$(this).parents(RSGoPro_JSPParentSelector).addClass('jspHasScroll');
				}
			});
		} else {
			$scroll.each(function(i){
				count = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).length;
				elemSize = $(this).find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).filter(':first').outerHeight(true);
				//$(this).css({overflowX:'hidden',overflowY:'auto'}).find(RSGoPro_JSPAllChildrensSelector).css({width:(count*elemSize)+'px'});
				if( $(this).hasScrollBarY() )
				{
					$(this).parents(RSGoPro_JSPParentSelector).addClass('jspHasScroll');
				}
			});
		}
		$scroll.parents(RSGoPro_JSPParentSelector).addClass('simple');
	}
}
function RSGoPro_SimpleScrollReinit(selector)
{
	$(selector).each(function(i){
		if( $(this).hasScrollBarY() )
		{
			$(this).parents(RSGoPro_JSPParentSelector).addClass('jspHasScroll');
		}
	});
}

function RSGoPro_ScrollInit(selector)
{
	if(RSDevFunc_PHONETABLET) // this is tablet or phone
	{
		// init
		RSGoPro_SimpleScrollInit(selector);
	} else { // this is PC
		// init
		RSGoPro_JSPInit(selector);
	}
}
function RSGoPro_ScrollReinit(selector,needDestroy)
{
	if(RSDevFunc_PHONETABLET) // this is tablet or phone
	{
		// reinit
		RSGoPro_SimpleScrollReinit(selector);
	} else {
		// reinit
		RSGoPro_JSPReinit(selector,needDestroy);
	}
}
function RSGoPro_ScrollPressButton($btn)
{
	var $scroll = $btn.parents(RSGoPro_JSPParentSelector).find(RSGoPro_JSPScrollSelector);
	if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') )
	{
		var elemSize = $scroll.find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).filter(':first').outerWidth(true);
	} else {
		var elemSize = $scroll.find(RSGoPro_JSPAllChildrensSelector).find(RSGoPro_JSPOneChildrenSelector).filter(':first').outerHeight(true);
	}
	if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') && $btn.hasClass('prev') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('jsp') )
	{
		$scroll.data('jsp').scrollByX( -(elemSize) );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') && $btn.hasClass('prev') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('simple') )
	{
		$scroll.stop().scrollTo( {top:'+=0px',left:'-='+(elemSize)}, RSGoPro_SimpleScrollScrollSpeed );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') && $btn.hasClass('next') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('jsp') )
	{
		$scroll.data('jsp').scrollByX( (elemSize) );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('horizontal') && $btn.hasClass('next') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('simple') )
	{
		$scroll.stop().scrollTo( {top:'+=0px',left:'+='+(elemSize)}, RSGoPro_SimpleScrollScrollSpeed );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('vertical') && $btn.hasClass('prev') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('jsp') )
	{
		$scroll.data('jsp').scrollByY( -(elemSize) );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('vertical') && $btn.hasClass('prev') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('simple') )
	{
		$scroll.stop().scrollTo( {top:'-='+(elemSize),left:'+=0px'}, RSGoPro_SimpleScrollScrollSpeed );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('vertical') && $btn.hasClass('next') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('jsp') )
	{
		$scroll.data('jsp').scrollByY( (elemSize) );
	} else if( $btn.parents(RSGoPro_JSPParentSelector).hasClass('vertical') && $btn.hasClass('next') && $btn.parents(RSGoPro_JSPParentSelector).hasClass('simple') )
	{
		$scroll.stop().scrollTo( {top:'+='+(elemSize),left:'+=0px'}, RSGoPro_SimpleScrollScrollSpeed );
	}
}
function RSGoPro_ScrollGoToElement($element)
{
	var $scroll = $element.parents(RSGoPro_JSPParentSelector).find(RSGoPro_JSPScrollSelector);
	if( $element.parents(RSGoPro_JSPParentSelector).hasClass('jsp') )
	{
		$scroll.data('jsp').scrollToElement( $element, false );
	} else if( $element.parents(RSGoPro_JSPParentSelector).hasClass('simple') ) {
		$scroll.stop().scrollTo( $element, RSGoPro_SimpleScrollScrollSpeed );
	}
}


$(document).ready(function(){
	
	// press button
	$(document).on('click',RSGoPro_JSPParentSelector+' '+RSGoPro_JSPButtonsSelector,function(){
		var $btn = $(this);
		if( $btn.parents(RSGoPro_JSPParentSelector).length>0 )
		{
			RSGoPro_ScrollPressButton($btn);
		}
		return false;
	});
	
});