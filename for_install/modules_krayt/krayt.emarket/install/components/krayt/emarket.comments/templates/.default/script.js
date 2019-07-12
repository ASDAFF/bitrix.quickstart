function ec_get_stars(rating)
{
	var response = '';
	for(var i=1; i<=10; i++)
	{
		if((i == rating) && (i%2)) 
		{
			response +=  '<div class="star half"></div>';
			i++;
			continue;
		}
		if(!(i%2))
		{
			if(i < rating)
			{
				response += '<div class="star"></div>';
			}
			else if(i == rating)
			{
				response +=  '<div class="star"></div>';
			}
			else if(i > rating)
			{
				response +=  '<div class="star empty"></div>';
			}
		}
	}
	console.log(response);
	return response;
}

//Add comment
function ec_add_commentToList(params) {
	var html = 
	'<div class="ec-comment" id="ec-comment-'+params['ec_this_id']+'" style="display:none">'+
		'<div class="author">'+
			'<b>'+params['UF_NAME']+'</b>'+
			'<time'+BX.message("EMARKET_COMMENTS_NOW")+'</time>'+
			'<span>'+BX.message("EMARKET_COMMENTS_TIME")+': '+params['UF_TERM_OF_USE']+'</span>'+
			'<div class="ec-rating" style="padding: 25px 0 10px;">'+
				ec_get_stars(params['UF_RATING'])+
				'<span>'+parseInt(params['UF_RATING'])/2+'</span>'+
			'</div>'+
		'</div>'+
		'<div class="text">'+
			'<div class="msg">'+
				'<p><b>'+BX.message("EMARKET_COMMENTS_DOST")+':</b><span>'+params['UF_MESS_ADV']+'</span></p>'+
				'<p><b>'+BX.message("EMARKET_COMMENTS_NED")+':</b><span>'+params['UF_MESS_LIM']+'</span></p>'+
				'<p><b>'+BX.message("EMARKET_COMMENTS_COM")+':</b><span>'+params['UF_MESS_COMM']+'</span></p>'+
			'</div>'+
		'</div>'+
	'</div>';
			
	$('.emarket-comments .ec-comments-list').prepend(html);
	$('#ec-comment-'+params['ec_this_id']).slideDown();
}

//Add comment
function ec_add_comment(params) {
	var ajax_loc = $('#ec_this_folder').val();
	
	params['ec_this_id'] = $('#ec_this_id').val();
	params['ec_this_iblock'] = $('#ec_this_iblock').val();
	params['ec_this_hlblock_pc'] = $('#ec_this_hlblock_pc').val();
	params['captcha_code'] = $("#captcha_code").val();
		
	if(index_xhr && index_xhr.readyState != 4)
		index_xhr.abort();

	index_xhr = $.ajax({
		type: "POST",
		url: ajax_loc+'/ajax.php',
		data: params,
		beforeSend: function(){},
		success: function(response){
			switch(response)
			{
				case 'ERROR_CAPTCHA':
					ec_reload_captcha(true);			
				break;
				case 'OK':
					$('.ec-comments-add').slideUp();
					ec_add_commentToList(params);
				break;
				default:
					alert('Error!');
					console.log(response);
				break;
			}
		}
	});
}
//Update comment
function ec_update_comment(params, this_element) {
	var ajax_loc = $('#ec_this_folder').val(),
		count_element;
	
	params['ajax_type'] = 'update';
	params['ec_this_id'] = $('#ec_this_id').val();
	params['ec_this_iblock'] = $('#ec_this_iblock').val();
	params['ec_this_hlblock_pc'] = $('#ec_this_hlblock_pc').val();
	
	console.log(params);
	
	if(index_xhr && index_xhr.readyState != 4)
		index_xhr.abort();

	index_xhr = $.ajax({
		type: "POST",
		url: ajax_loc+'/ajax.php',
		data: params,
		beforeSend: function(){},
		success: function(data){
			switch(data)
			{
				case 'OK':
					if($(this_element).attr('data-name') != 'UF_SOCIAL_COMPLAINT')
					{
						count_element = parseInt($(this_element).text());
						
						if(isNaN(count_element)) 
							count_element = 0;
						
						count_element++;
						$(this_element).text(count_element);
						$(this_element).addClass('active');
						$(this_element).parent().children('.bt-link').addClass('deactive');
					}
					else
					{
						$(this_element).text(BX.message("EMARKET_COMMENTS_JALOBA"));
					}
				break;
				default:
					console.log(data);
				break;
			}
		}
	});
}

//Reload captcha
function ec_reload_captcha(error)
{
	var ajax_loc = $('#ec_this_folder').val();
	
	$.ajax({
		type: "POST",
		url: ajax_loc+'/ajax_getCaptcha.php',
		dataType: "html",
		success: function(capcha){
			$('.emarket-comments .ec-comments-captcha').html(capcha);
		},
		complete: function(){
			setTimeout(function(){
				$('#ec_reload_captcha').addClass('animate');
				if(error)
					$("#captcha_word").addClass('ec-empty');
			}, 100);	
		}
	});
}

//Number formatting
function ec_floorN(x, n)
{
	var mult = Math.pow(10, n);
	return Math.floor(x*mult)/mult;
}

//Calculate star
function ec_calc_star()
{
	var html_star = '',
		rating_criteria = {},
		rating = [],
		rating_sum = 0,
		rating_sum_int = 0;
	
	$('.ec-rating .ec-criteria').each(function(indx_parent, element_parent){
		var _parent = $(element_parent).children('.ec-criteria-rating');

		rating[indx_parent] = [];
		rating[indx_parent]['criteria_id'] = $(element_parent).attr('data-id');
		rating[indx_parent]['criteria_code'] = $(element_parent).attr('data-code');
		rating[indx_parent]['rating'] = 0;
		
		_parent.children('a').each(function(indx, element){
			if($(element).hasClass('active'))
			{
				rating[indx_parent]['rating'] = indx+1;
			}
		});
	});
	
	for(var i=0; i < rating.length; i++)
	{
		rating_sum += rating[i]['rating'];
		rating_criteria[rating[i]['criteria_code']] = rating[i]['rating'];
	}
	
	//Получм значение рейтинга
	rating_sum = rating_sum / (rating.length / 2);
	rating_sum_int = parseInt(rating_sum);
	
	for(var i=1; i<=10; i++)
	{
		if((i == rating_sum_int) && (i%2)) 
		{
			html_star += '<div class="star half"></div>';
			i++;
			continue;
		}
		if(!(i%2))
		{
			if(i < rating_sum_int)
			{
				html_star += '<div class="star"></div>';
			}
			else if(i == rating_sum_int)
			{
				html_star += '<div class="star"></div>';
			}
			else if(i > rating_sum_int)
			{
				html_star += '<div class="star empty"></div>';
			}
		}
	}
	
	$('.ec-criteria-full .ec-criteria-rating').html(ec_get_stars(rating_sum_int));
	$('.ec-criteria-full .ec-criteria-val').text(ec_floorN(rating_sum/2, 2));
	
	$('input[name="UF_RATING"]').val(ec_floorN(rating_sum, 2));
	$('input[name="UF_RATING_LIST"]').val(JSON.stringify(rating_criteria));
}

$(document).on('click', '#ec_reload_captcha', function(event){
	event.preventDefault();
	ec_reload_captcha();
});

$(document).on('click', '#ec_comment_show', function(event){
	event.preventDefault();
	$('.ec-comments-add').slideToggle();
});

$(document).on('click', '#ec_comment_cancel', function(event){
	event.preventDefault();
	$('.ec-comments-add').slideUp();
});

$(document).on('click', '.ec-rating-list-show', function(event){
	event.preventDefault();
	$(this).parent().children('.ec-rating-list').slideToggle();
	$(this).toggleClass('arrow-top');
});

$(document).on('click', '#ec_comment_add', function(event){
	event.preventDefault();

	var bool = true,
		params = new Object(),
		required = [
			'captcha_word',
			'UF_NAME',
			'UF_MESS_ADV',
			'UF_MESS_LIM',
			'UF_MESS_COMM'];
		
	$('.ec-input-param').each(function(indx, element){
		if( $(element).val() == '' &&
			($.inArray($(element).attr('name'), required) >= 0))
		{
			bool = false
			$(element).addClass('ec-empty');
		}
		else
		{
			$(element).removeClass('ec-empty');
			params[$(element).attr('name')] = $(element).val();
		}
	});
	
	if(bool)
		ec_add_comment(params);
});


$(document).on('click', '.ec-comment .control a', function(event){
	event.preventDefault();
	var this_is = $(this),
		params = new Object();
		
	params['ID'] = this_is.attr('data-id');
	params[this_is.attr('data-name')] = 1;
	
	if(!this_is.hasClass('deactive'))
		ec_update_comment(params, this_is);
});


//Ready doom
$(document).ready(function(){

	$('.ec-rating .ec-criteria').each(function(indx_parent, element_parent){
		var _parent = $(element_parent).children('.ec-criteria-rating');
		
		_parent.children('a').on('mouseleave', function(){
			_parent.children('a').addClass('empty');
		});

		_parent.children('a').each(function(indx, element){
			$(element).on('mouseenter', function(){
				for(var i=0; i <= indx; i++)
				{
					_parent.children('a').eq(i).removeClass('empty');
				}
			});
			
			$(element).on('click', function(){
				_parent.children('a').removeClass('active');
				$(element_parent).children('.ec-criteria-val').text(indx+1);
				
				for(var i=0; i <= indx; i++)
				{
					_parent.children('a').eq(i).addClass('active');
				}
				ec_calc_star();
			});
		});
	});

});