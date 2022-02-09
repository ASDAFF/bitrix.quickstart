var click_next = false;

function stopSlider(element)
{
	var k = element.data('elementTimerHandle');
	clearInterval(k);
	k = 0;
	element.data('elementTimerHandle', k);
}

function sliderPage(element, tab, cur_page, w)
{
	tab.find('.slider-content-wrap').animate({"left": -(w*cur_page)+"px" }, 500);
	tab.data('page', cur_page+1);

	if (cur_page>0)
	{
		element.find('.m-prev').show();
	}
	else
	{
		element.find('.m-prev').hide();
	}
}

function sliderNext(element, tab)
{
	var w = element.data('elementWidth')*1;
	var cur_page = tab.data('page')*1;

	var pages = tab.data('pages')*1;
	var cnt = tab.data('pages')*1;
	
	console.log(cur_page+' '+pages);

	if (cur_page<pages)
	{
		tab.find('.slider-content-wrap').animate({"left": "-="+(w*1+20)+"px" }, 500);

		var cur_page = tab.data('page')*1; 
		cur_page = cur_page + 1;
		tab.data('page', cur_page);

		tab.find('.b-pager').find('li').removeClass('current');
		tab.find('.b-pager').find('li:eq('+(cur_page-1)+')').addClass('current');

		if (cur_page>1)
		{
			element.find('.m-prev').show();
		}
	}
	else
	{
		// set current tab start position
		tab.data('page', 1);
		tab.data('pages', cnt);

		tab.find('.slider-content-wrap').animate({"left": "0px" });

		tab.find('.b-pager').find('li').removeClass('current');
		tab.find('.b-pager').find('li:first').addClass('current');
		

		// tab pager
		var cur_tab = element.data('activeTab')*1;
		var tab_count = element.data('activeTabCount')*1;
		cur_tab = cur_tab + 1;
		if (cur_tab>tab_count-1)
		{
			cur_tab = 0;
			element.data('activeTab', cur_tab);

		}
		element.data('activeTab', cur_tab);

		
		element.find('.b-tab-head').find('a').removeClass('active');
		element.find('.b-tab-head').find('a:eq('+(cur_tab)+')').addClass('active');

		element.find('.b-tab__body').removeClass('active');
		element.find('.b-tab__body:eq('+(cur_tab-1)+')').addClass('active');

		var _tab = element.find('.b-tab__body:eq('+(cur_tab)+')');
		_tab.find('.b-pager').find('li').removeClass('current');
		_tab.find('.b-pager').find('li:first').addClass('current');

		element.find('.m-prev').hide();
		
	}

	click_next = false;
}



function initSlider(element)
{
	stopSlider(element);

	var w = element.data('elementWidth');

	// get tab sliders
	element.find('.b-tab__body').each(function(){
	
		var tab = $(this);
		var cnt = tab.find('.slider-content-wrap').find('.slider-content').length;

		tab.data('page', 1);
		tab.data('pages', cnt);

		tab.find('.slider-content-wrap').width(w*cnt+40).css('left', '0');
		tab.find('.slider-content').css('float', 'left').css('margin-right', 20);
		
		element.find('.m-prev').hide();


		// pager
		tab.find('.b-pager').find('a').unbind('click').bind('click',function(){
			stopSlider(element);

			element.find('.b-pager').find('li').removeClass('current');
			$(this).parent().addClass('current');
			
			var index_page = $(this).parent().index();
			sliderPage(element, tab, index_page, w);
			
			return false;
		});


		// arrow left
		tab.find('.m-prev').unbind('click').bind('click', function(){
			var cur_page = tab.data('page');
			var pages = tab.data('pages');
		
			if (cur_page>1)
			{
				tab.find('.slider-content-wrap').animate({"left": "+="+(w*1+20)+"px"  }, 500);

				var cur_page = tab.data('page')*1; 
				cur_page = cur_page - 1;
				tab.data('page', cur_page);

				if (cur_page == 1)
				{
					element.find('.m-prev').hide();
				}
				else
				{
					element.find('.m-prev').show();
				}
			}
			else 
			{
				element.find('.m-prev').hide();
			}

			return false;
		});
		
		
		//arrow right
		tab.find('.m-next').unbind('click').bind('click', function(){
			
			if (!click_next)
			{
				click_next = true;

				stopSlider(element)
				sliderNext(element, tab);
			}

			return false;
		});
	})

	element.data('elementTimerHandle', setInterval(function(){

			var cur_tab = element.data('activeTab')*1;
			var tab = element.find('.b-tab__body:eq('+(cur_tab-1)+')')

			sliderNext(element, tab);
		}, element.data('elementTimer')*1 )
	);
}



$(document).ready(function(){
	$('.slider-element').each(function(){
		var element = $(this);

		var timer = element.attr('data-timer')*1;
		
		if (!timer)
		{
			timer = 5000
		}

		var w = element.find('.slider-content-wrap:first').find('.slider-content:first').width();

		element.data('activeTab', 0);
		element.data('activeTabCount', element.find('.b-tab__body').length);
		element.data('elementWidth', w);
		element.data('elementTimer', timer);
		

		// get header links
		element.find('.b-tab-head').find('a').live('click', function(){
			var index = $(this).index();

			element.find('.b-tab-head').find('a').removeClass('active');
			$(this).addClass('active');

			element.find('.b-tab__body').removeClass('active');
			element.find('.b-tab__body').eq(index).addClass('active');

			element.data('activeTab', index);

			stopSlider(element);
			initSlider(element);

			return false;
		});

		initSlider(element);
	})
})