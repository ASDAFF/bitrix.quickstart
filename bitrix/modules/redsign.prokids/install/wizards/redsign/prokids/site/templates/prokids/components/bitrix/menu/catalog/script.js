var RSGoPro_MenuTO = 0;
var RSGoPro_MenuElemHover = false;

function RSGoPro_ElementInMenuPos( $liObj )
{
	var l = 250;
	var $searchObj;
	if( !$liObj.hasClass('first') )
	{
		$searchObj = $liObj.parents('li.first');
	} else {
		$searchObj = $liObj;
	}
	if( $searchObj.find('.elementinmenu').length>0 )
	{
		var c = $('.catalogmenu').find('li.hover').length - 2;
		if( $('.catalogmenu').find('li.hover:last').parent().find('ul').length<1 )
		{
			c = c - 1;
		}
		if( $('.centering:first').width() < 1260 )
		{
			c = c + 5;
		}
		if(c>2)
		{
			$('.catalogmenu').find('li.first.hover').parent().find('.elementinmenu').css({'display':'none'});
		} else {
			$('.catalogmenu').find('li.first.hover').parent().find('.elementinmenu').css({'display':'block','left':(c*l-2)+'px'});
		}
	}
}

$(document).ready(function(){
	
	$('.catalogmenu').on('mouseenter',function(){
		$(this).addClass('hover');
	}).on('mouseleave',function(){
		$(this).removeClass('hover');
	});
	
	var timeoutHover = {};
	$('.catalogmenu a').on('click',function(e){
		var $link = $(this);
		if(!$link.hasClass('hover')){
			e.preventDefault();
			$link.addClass('hover');
		}
	}).on('mouseenter',function(){
		var $link = $(this);
		$link.parent().parent().find('a.hover').removeClass('hover');
		timeoutHover[$link.index()] = setTimeout(function(){
			$link.addClass('hover');
		},150);
	}).on('mouseleave',function(){
		var $link = $(this);
		clearTimeout(timeoutHover[$link.index()]);
		timeoutHover[$link.index()] = setTimeout(function(){
			$link.removeClass('hover');
		},2);
	});
	
	$('.catalogmenu li').on('mouseenter',function(){
		var $liObj = $(this);
		$liObj.parent().find('li.hover').removeClass('hover');
		setTimeout(function(){
			$liObj.addClass('hover');
			RSGoPro_ElementInMenuPos( $liObj );
		},2);
	}).on('mouseleave',function(){
		var $liObj = $(this);
		setTimeout(function(){
			if(!RSGoPro_MenuElemHover)
			{
				$liObj.removeClass('hover')
			}
			RSGoPro_ElementInMenuPos( $liObj );
		},2);
	});
	
	$('.catalogmenu .elementinmenu').on('mouseenter',function(){
		RSGoPro_MenuElemHover = true;
	}).on('mouseleave',function(){
		RSGoPro_MenuElemHover = false;
	});
	
	if(RSDevFunc_PHONETABLET)
	{
		$('.catalogmenusmall a.parent').on('click',function(){
			if($(this).parent().find('ul').hasClass('noned'))
			{
				$(this).parent().find('ul').removeClass('noned');
				return false;
			}
		});
		$(document).on('click',function(){
			var $obj = $(this);
			if(!$('.catalogmenusmall ul.first').hasClass('noned'))
			{
				$('.catalogmenusmall ul.first').addClass('noned');
			}
		});
	} else {
		$('.catalogmenusmall li.parent').on('mouseenter',function(){
			$(this).find('ul').removeClass('noned');
		}).on('mouseleave',function(){
			$(this).find('ul').addClass('noned');
		});
	}
});