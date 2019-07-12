$(document).ready(function() {
	// tooltips
	$("[rel=tooltip]").tooltip({});	
	
	// всплывающий баллон на недавно просмотренных
	$(".smoll-preview").live('mouseover', function(){ 
		$(this).find(".info-boxover").show();
	});
	
	$(".smoll-preview").live('mouseout', function(){
		$(this).find(".info-boxover").hide();
	});
	
	// ссылка регистрация
	$( "#reg_link" ).bind("click", function(){ 
		$( "#authForm" ).modal('hide');
		$( "#regModal" ).modal('show');
		return false;
	});
		
	// обработчик клика по ссылке с формы регистрации на правила
	$("#agree-link").bind('click', function(){

		$( "#regModal" ).modal('hide');
		$( "#agreeForm" ).modal('show');
		return false;
	});
	
	// обработчик согласия с правилами в форме регистрации(чекбокс)
	$("#chb_agree").live('change', function(){
		document.getElementById('reg_submit').disabled = !this.checked
	});

	// обработчик клика по кнопке согласен на попапе с правилами
	$("#agreeBtn").bind('click', function(){ 
		$( "#agreeForm" ).modal('hide');
		$( "#regModal" ).modal('show');
		return false;
	});
	
	// из формы забыли пароль переходим на авторизацию
	$("#forgot_auth").live('click', function(){
		$( "#forgotPass" ).modal('hide');
		$( "#authForm" ).modal('show');
		return false;
	});
	// из формы регистрации переходим на авторизацию
	$("#register_already").live('click', function(){
		$( "#regModal" ).modal('hide');
		$( "#authForm" ).modal('show');
		return false;
	});
	
	// клик по цвету товара в списке товаров в каталоге
	$(".color-ch button, .color-min").live('click', function() {
        changePhoto($(this));
		return false;
	});

	//назначаем обработчик ajax формы регистрации
	$('#reg').ajaxForm({ 
        
        dataType:  'json',
        beforeSubmit: ValidateForm,
        success: function(json) { 

        	if (json.result == "ERROR") {
						$( "#error_container" ).html(json.message);		
					
			} else if (json.result == "OK") {
						//$( "#"+id_container ).dialog ("option", "height", "auto");
						$( "#register_container" ).animate({ opacity: "hide" }, "slow");	
						$( "#error_container" ).html(json.message);
						document.location.reload();

			} else if (json.result == 'EMAIL_EXISTS') {
							
						$( "#popupForm" ).html( json.form );
						ForgotPasswdDialogPrepare('popupForm', 1, 'regModal');
						$( "#error_container" ).html(json.message);
			}						
        }			
	});
	
	//назначаем обработчик ajax формы обратной связи	
	$('#feedback_form').ajaxForm({ 
	    
	    dataType:  'json',
	    beforeSubmit: ValidateFeedbackForm,
	    success:    function(json) { 
	    	
	    	if (json.result == "ERROR") {
	
				alert(json.message);
				//$( "#feedback_error" ).html(json.message);		
			
			} else if (json.result == "OK") {
				alert(json.message);
				$('#feedBackModal').modal('hide')
				$( "#feedback_form" )[0].reset();
				
			}
	    	
	    } 
	});
});

/**
* ajax ресайз картинок
*/
function ajaxImgLoad()
{
	var arSource = {};
	var arWidth = {};
	var arHeight= {};
	var arElmId = {};
	var imgId = 0;
	
	$('.ajaximgload').each(function(index, element) {
		imgId = $(element).data('microdata').imgid;
		arSource[ imgId ] = $(element).data('microdata').imgsrc;
		arWidth [ imgId ] = $(element).data('attribute').width;
		arHeight[ imgId ] = $(element).data('attribute').height;
		arElmId [ imgId ] = $(element).data('microdata').elmid;
		
		return false;
	});

	if(imgId)
	{
		var obj			= {};
		var imgTag		= "";

		$.ajax({
			type	: "POST",
			url		: "/include/ajax/ajaximgload.php",
			dataType: "json",
			data	: {
				'arSource'	: arSource,
				'arWidth'	: arWidth,
				'arHeight'	: arHeight,
				'arElmId'	: arElmId
			},
			success: function(data){
				objTools.forEach(data, function(key, val){
                    obj = $('.ajaximgload-imgid-' + key +'.ajaximgload'+ arWidth[key] + 'x' + arHeight[key]);
					imgTag = '<img src="'+ val +'"';
					objTools.forEach(obj.data('attribute'), function(subkey, subval){
						if(subval) imgTag += ' '+ subkey + '=' + subval;
					});
					objTools.forEach(obj.data('microdata'), function(subkey, subval){
						if(subval) imgTag += ' data-' + subkey + '=' + subval;
					});
					imgTag += ' />';

					obj.replaceWith(imgTag);
				});
				
				ajaxImgLoad();
			}
		});
	}else{

		var changeColorFlag = false;
        if( $('#ajaximgload-thumbs img').length )
		{
			objTools.forEach(product.currentSet, function(key, val){
				objTools.forEach(val.curPhotosSmall, function(subkey, subval){
                    $('#ajaximgload-thumbs img').each(function(){
                        if($(this).attr("data-imgsrc")==product.currentSet[key].curPhotosBig[subkey])
                        {
                            product.currentSet[key].curPhotosSmall[subkey] = $(this).attr("src");
                        }
                    });
					//product.currentSet[key].curPhotosSmall[subkey] = $('#ajaximgload-thumbs img[data-imgsrc='+product.currentSet[key].curPhotosBig[subkey]+']').eq(0).attr('src');
					//$('#ajaximgload-thumbs img[data-elmid='+key+']').eq(0).remove();
				});
			});

            changeColorFlag = true;
		}
        if( $('#ajaximgload-medium img').length )
        {
            objTools.forEach(product.currentSet, function(key, val){
                objTools.forEach(val.curPhotosMiddle, function(subkey, subval){
                    $('#ajaximgload-medium img').each(function(){
                        if($(this).attr("data-imgsrc")==product.currentSet[key].curPhotosBig[subkey])
                        {
                            product.currentSet[key].curPhotosMiddle[subkey] = $(this).attr("src");
                        }
                    });
                    //product.currentSet[key].curPhotosMiddle[subkey] = $('#ajaximgload-medium img[data-imgsrc='+product.currentSet[key].curPhotosBig[subkey]+']').eq(0).attr('src');
                    //$('#ajaximgload-medium img[data-elmid='+key+']').eq(0).remove();
                });
            });
            changeColorFlag = true;

        }
        if (changeColorFlag == true) {

            product.changeColor(product.currentColorId);
        }
	}
}

/**
 * функция скрывает или показывает стрелки в зависимости от текущей картинки в карусели
 * @param {int} current
 * @param {int} current
 */
function showHideArrows(curImageIndex, total) {
	if (curImageIndex==1) {
		$("#left-arr").hide();
	} else {
		$("#left-arr").show();
	}
	
	if (curImageIndex==total) {
		$("#right-arr").hide();
	} else {
		$("#right-arr").show();
	}
}

/**
 * ставит ширину и др. параметры для мод. окон на основе заданных ИД
 */
function setWidthCustom(carousel, myModal) {

    var curImageWidth = $("#"+carousel+" .active img").width()+30;
    var curImageHeight = $("#"+myModal).height();
    var marginLeft = (curImageWidth/2);
    var marginTop = (curImageHeight/2);

    var curActiveWidth = curImageWidth;

    if (curImageHeight > $(window).height()) {
        marginTop = 0;
        $('#'+myModal).css({'top':'auto'});
    } 	else {
        $('#'+myModal).css({'top':'50%'});
    }
    $('#'+myModal).css({'margin-left':'-'+marginLeft+'px', 'margin-top':'-'+marginTop+'px','width':curActiveWidth+'px'});

}
/**
 * ставит ширину и др. параметры для мод. окон в дет. карточке
 */
function setWidth()
{
    return setWidthCustom('carousel-inner', 'myModal');
}

function showAjaxLoader() {
	var height = $(".page-container").height();
			
	var waitHtml = '<div class="centerbg1" id="preloaderbg" style="display: block;height: '+height+'px"> \
      <div class="centerbg2"> \
        <div id="preloader"></div> \
      </div> \
    </div>';
    $(waitHtml).prependTo('body');
}

function hideAjaxLoader() {
	//if (!!bWait) {
	$("#preloaderbg").remove();
	//}
}

/**
 * функция подготавливает диалоговое окно Восстановление пароля
 * @param {String} id_container идентификатор контейнера для окна
 * @param {Integer} prepare_ajax_form_flag нужно ли готовоить ajax форму (0/1)
 * @param {String} hide_div мод. окно которое нужно скрыть
 */
function ForgotPasswdDialogPrepare(id_container, prepare_ajax_form_flag, hide_div)
{   
	if (hide_div) $( "#"+hide_div ).modal('hide'); 
	$( "#forgotPass" ).modal('show');
	
	if (prepare_ajax_form_flag == 1) {
		$('#forgotForm').ajaxForm({
			dataType: 'json',
			success: function(json) {
				if (json.message) $( "#error_forgot_container" ).html(json.message);	
	        }
		});		
	}	
}

function CheckEmail(email)
{
	var reg_e = /^[0-9\.a-z_\-]+@[0-9a-z_\-^\.]+\.[a-z]{2,6}$/i;
	if (email != '') {
		if(!reg_e.test(email) ) return false;
	}
	return true;
}

/**
 * функция парсит строку запроса в адресной строке и возвращает массив $_GET
 * @param {object} obj_window - объект window
 * @return {array} массив $_GET
 */
function parseGetParams(obj_window)
{ 
   var $_GET = {}; 

   var __GET = obj_window.location.search.substring(1).split("&"); 
   for(var i=0; i<__GET.length; i++) { 
      var getVar = __GET[i].split("="); 
      $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
   } 
   return $_GET; 
}

/**
 * 
 * @param {} formData
 * @param {} jqForm
 * @param {} options
 * @return {}
 */
function ValidateForm(formData, jqForm, options) {

	var form_id = jqForm[0].form_id.value; 
	
	if (form_id == 'reg') {
		return ValidateRegisterForm();
	} /*else if (form_id == 'feedback') {
		return ValidateFeedbackForm();
	} else if (form_id == 'say_brand') {
		return ValidateSayBrand();
	} else if (form_id == 'as_stylist') {
		return ValidateAsStylistForm();
	} else if (form_id == 'first_reg') {
		return ValidateRegistrationFin();
	}*/	

}

/**
 * Функция валидации формы регистрации
 * @param {Boolean} auth_page_flag (true) если форма находится на странице авторизации 
 * @return {Boolean} 
 */
function ValidateRegisterForm(auth_page_flag)
{

	var error = new Array();
	if (auth_page_flag===true) {
		var name = document.getElementById('REGISTER2_NAME');
		var email = document.getElementById('REGISTER2_EMAIL');
		var pass = document.getElementById('REGISTER2_PASSWORD');
		var confirm_pass = document.getElementById('REGISTER2_CONFIRM_PASSWORD');
		if (!$('#chb_agree2').is(":checked")){ 
			error.push('Не отмечен флажок согласия с правилами');
		}
	} else {
		var name = document.getElementById('REGISTER_NAME');
		var email = document.getElementById('REGISTER_EMAIL');
		var pass = document.getElementById('REGISTER_PASSWORD');
		var confirm_pass = document.getElementById('REGISTER_CONFIRM_PASSWORD');
		if (!$('#chb_agree').is(":checked")){ 
			error.push('Не отмечен флажок согласия с правилами');
		}
	}
	
	if (name.value == '') error.push('Не заполнено поле Имя');	
	if (email.value == '') error.push('Не заполнено поле Email');	
	if (pass.value == '') error.push('Не заполнено поле Пароль');	
	if (confirm_pass.value == '') error.push('Не заполнено поле Подтверждение пароля');

	if (email.value != '') {
		if(!CheckEmail(email.value) ){
			error.push('Поле Email заполнено неверно');
		}	
	}
	
	if (pass.value != '' && confirm_pass.value !='') {
		if (pass.value != confirm_pass.value) {
			error.push('поле Пароль не совпадает с полем Подтверждение пароля');
		}
	}

	var str_error = error.join("\n");
	
	if (str_error != '') {
		alert(str_error);
		return false;	
	}
	return true;
}

function ValidateFeedbackForm()
{
	var feedback_name = document.getElementById('feedback_name');
	var feedback_message = document.getElementById('feedback_message');
	var feedback_email = document.getElementById('feedback_email');
	
	var error_str = '';
	var error = Array();
	if (feedback_name.value == '') error.push('Не заполнено поле Имя');
	if (feedback_message.value == '') error.push('Не заполнено поле Ваш вопрос');
	if (feedback_email.value == '') {
		error.push('Не заполнено поле Email');	
	} else if (!CheckEmail(feedback_email.value)){
		error.push('Поле Email заполнено неверно');
	}	
	
	var str_error = error.join("\n");	
	if (str_error != '') {
		alert(str_error);
		return false;	
	}

}

/**
 * функция возвращает высоту клиентской области браузера
 * 
 * @return int
 */
function getClientHeight()
{
	return document.compatMode=='CSS1Compat' && !window.opera ? document.documentElement.clientHeight : document.body.clientHeight;
}

/**
 * функция определяет насколько проскроллена страница по высоте
 *
 * @return int
 */
function getBodyScrollTop()
{
    return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
}

function dump(obj, obj_name) {
	var result = '';
	for (var i in obj) result += obj_name + '.' + i + ' = ' + obj[i] + '\n';
	return result;
}

/**
 * функция срабатывает при клике на цвет товара в списке товаров - ф-я меняет фото товара в нужном цвете
 * @param {obj} obj - объект дом по которому прошел клик
 * @param {string} photoPath - путь к фото
 * @param {int} elemId - id фото из бд
 */
function changePhoto(obj) {
	var photoPath = obj.data('pic');
    var elemId = obj.data('color-id');
    var colorId = obj.data('color-code');
    
    $(".catalog-preview-color-element-"+elemId).hide();
    var hrefObj = $(".catalog-preview-color-element-"+elemId+"-"+colorId);
	
    var nameHrefObj = $(".catalog-preview-color-element-"+elemId+"-"+colorId + " a");
    hrefObj.css('display','block');
    //$(".over .link-popover-card").hide();
    // highlight active color
    var name = obj.attr('data-name');
	
    $('span[data-name='+name+']').parent().find('span.color-min').each(function(i, val) {
        $(this).removeClass('active-color');
    });
    $('span[data-name='+name+']').addClass('active-color');
    // make detail link
    var link = hrefObj.attr("href");
    if($.trim(link)=='') return false;
    var posLastSlash = link.lastIndexOf("/");
    var detailLink = link.substring(0, posLastSlash)+"/";
    // part of link after last slash
    var linkSuffix = link.substring(posLastSlash+2); // dismiss /?
    //linkSuffix = 'cs=&#color-11-583';
    var paramsArr = linkSuffix.split('&');
    linkSuffix = paramsArr[0]; // dismiss part of color ( like "&#color-11-583")
    if (linkSuffix.length == 0) {
        var newLink = detailLink + '?cs=&#color-'+colorId+'-'+elemId;
    } else {
        // there is a size in link
        var newLink = detailLink + '?'+linkSuffix+'&#color-'+colorId+'-'+elemId;
    }
    hrefObj.attr("href", newLink);
    nameHrefObj.attr("href", newLink);
	$(".labelLink").attr("href", newLink);
}

function parse_url (str, component) {
  // http://kevin.vanzonneveld.net
  // +      original by: Steven Levithan (http://blog.stevenlevithan.com)
  // + reimplemented by: Brett Zamir (http://brett-zamir.me)
  // + input by: Lorenzo Pisani
  // + input by: Tony
  // + improved by: Brett Zamir (http://brett-zamir.me)
  // %          note: Based on http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
  // %          note: blog post at http://blog.stevenlevithan.com/archives/parseuri
  // %          note: demo at http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
  // %          note: Does not replace invalid characters with '_' as in PHP, nor does it return false with
  // %          note: a seriously malformed URL.
  // %          note: Besides function name, is essentially the same as parseUri as well as our allowing
  // %          note: an extra slash after the scheme/protocol (to allow file:/// as in PHP)
  // *     example 1: parse_url('http://username:password@hostname/path?arg=value#anchor');
  // *     returns 1: {scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}
  var query, key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port',
            'relative', 'path', 'directory', 'file', 'query', 'fragment'],
    ini = (this.php_js && this.php_js.ini) || {},
    mode = (ini['phpjs.parse_url.mode'] &&
      ini['phpjs.parse_url.mode'].local_value) || 'php',
    parser = {
      php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
    };

  var m = parser[mode].exec(str),
    uri = {},
    i = 14;
  while (i--) {
    if (m[i]) {
      uri[key[i]] = m[i];
    }
  }

  if (component) {
    return uri[component.replace('PHP_URL_', '').toLowerCase()];
  }
  if (mode !== 'php') {
    var name = (ini['phpjs.parse_url.queryKey'] &&
        ini['phpjs.parse_url.queryKey'].local_value) || 'queryKey';
    parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
    uri[name] = {};
    query = uri[key[12]] || '';
    query.replace(parser, function ($0, $1, $2) {
      if ($1) {uri[name][$1] = $2;}
    });
  }
  delete uri.source;
  return uri;
}

function loadPreviewElementModalWindow(path,sizeIds,SET_FIRST_PHOTO)
{
	showAjaxLoader();
	product.CHANGE_URL = 0;
	var productUrlComponents = parse_url(path);

    if(typeof productUrlComponents.path == "undefined" || typeof productUrlComponents.path == undefined )
    {
        return true;
    }
    else
    {
        productUrlPath = productUrlComponents.path;
    }
    if(typeof productUrlComponents.query == "undefined" || typeof productUrlComponents.path == undefined )
    {
        productUrlQuery = "";
    }
    else
    {
        productUrlQuery = productUrlComponents.query;
    }
    if(typeof productUrlComponents.fragment == "undefined" || typeof productUrlComponents.path == undefined )
    {
        productUrlFragment = "null-color";
    }
    else
    {
        productUrlFragment = productUrlComponents.fragment;
    }

    $.ajax({
        type		: "POST",
        url			: productUrlPath+'?CAJAX=1&'+productUrlQuery,
        data		: {},
        success:function(html) {

            $('#quickView01ModalBody').html(html);
            $("#quickView").modal({width:"950"}).on('hide.bs.modal', function (e) {

                if (e.target.id == "quickView") {

                    if ($('.zoomContainer').length > 0) {

                        $('.zoomContainer').remove();
                        $("#detailImg1").removeData('elevateZoom');
                    }
                }
            });
            product.productUrl = productUrlPath;
            SET_PRODUCT_FIRST_PHOTO = (SET_FIRST_PHOTO===true) ? true : false;
            $('#'+productUrlFragment+'-set-by-hash').click();
            if($.isArray(sizeIds))
            {
                for(var i=0;i<sizeIds.length;i++)
                {
                    if(product.checkSize(sizeIds[i]))
                    {
                        product.changeSize(sizeIds[i]);
                        break;
                    }
                }
            }
            hideAjaxLoader();
        }
    });

    return false;
}