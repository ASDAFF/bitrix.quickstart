var RSGoPro_MenuTO = 0;
var RSGoPro_MenuElemHover = false;

function RSGoPro_menuGetPos($obj){
	return ( parseInt($obj.offset().top)-parseInt($obj.parent().offset().top) );
}

function RSGOPRO_SetHeightMenuMRows(){
	setTimeout(function(){
		// reset
		var $currentLVL2 = $('.catalogmenu2').find('.first.hover').find('.lvl2');
		// in line
		if(!$currentLVL2.hasClass('positioned')){
			$currentLVL2.find('.mrow').css('minHeight','none');
			var position = RSGoPro_menuGetPos( $currentLVL2.find('.mrow:first') ),
				position_tmp = 0,
				last_index = 0,
				max_height = 0;
			$currentLVL2.find('.mrow').each(function(i){
				position_tmp = RSGoPro_menuGetPos( $(this) );
				if( position_tmp!=position ){
					if(last_index>0){
						$currentLVL2.find('.mrow:lt('+(i)+'):gt('+last_index+')').css('minHeight',max_height);
					} else {
						$currentLVL2.find('.mrow:lt('+(i)+')').css('minHeight',max_height);
					}
					last_index = (i-1);
					position = RSGoPro_menuGetPos( $(this) );
					max_height = $(this).outerHeight(true)+2;
				} else {
					if( $(this).outerHeight(true)>max_height )
						max_height = $(this).outerHeight(true)+2;
				}
			});
			if(last_index>0){
				$currentLVL2.find('.mrow:gt('+last_index+')').css('minHeight',max_height);
			}else{
				$currentLVL2.find('.mrow').css('minHeight',max_height);
			}
			$currentLVL2.addClass('positioned');
		}
	},1);
}

$(document).ready(function(){

	$(window).bind('resize', function(){
		$('.catalogmenu2').find('.lvl2').removeClass('positioned');
	});
	
	$('.catalogmenu2').on('mouseenter',function(){
		$(this).addClass('hover');
	}).on('mouseleave',function(){
		$(this).removeClass('hover');
	});
	
	var timeoutHover = {};
	$('.catalogmenu2 a').on('click',function(e){
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
	
	$('.catalogmenu2 li').on('mouseenter',function(){
		var $liObj = $(this);
		$liObj.parent().find('li.hover').removeClass('hover');
		setTimeout(function(){
			$liObj.addClass('hover');
			RSGOPRO_SetHeightMenuMRows();
		},2);
	}).on('mouseleave',function(){
		var $liObj = $(this);
		setTimeout(function(){
			if(!RSGoPro_MenuElemHover){
				$liObj.removeClass('hover')
			}
		},2);
	});
	
	$('.catalogmenu2 .elementinmenu').on('mouseenter',function(){
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