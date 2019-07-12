$(function(){
	
	var countli = $(".caroufredsel_wrapper ul li").size()
	if(countli < 4){
		$(".caroufredsel_wrapper").find(".next").addClass("disable")
	}

	$(".close").live('click', function() {
		CloseModalWindow()
	});
	$(document).keyup(function(event){
		if (event.keyCode == 27) {
			CloseModalWindow()
		}
	});

});

function CentriredModalWindow(ModalName){
	$(ModalName).css({"display":"block","opacity":0});
	var modalH = $(ModalName).height();
	var modalW = $(ModalName).width();
	$(ModalName).css({"margin-left":"-"+(parseInt(modalW)/2)+"px","margin-top":"-"+(parseInt(modalH)/2)+"px"})
}

function OpenModalWindow(ModalName){
	$(ModalName).animate({"opacity":1},300);	
	$("#bgmod").css("display","block");
}

function CloseModalWindow(){
	$("#bgmod").css("display","none");
	$(".modal").css({"opacity":1});
	$(".modal").animate({"opacity":0},300);
	setTimeout(function() { $(".modal").css({"display":"none"}); }, 500)	
}
			
function addToCompare(element, mode, text, deleteUrl) {    
    if (!element && !element.href) return;

    var href = element.href;
    var button = $(element);
			  
	button/*.unbind('click')*/.removeAttr("href");
	titleItem = $(element).parents(".R2D2").find(".item_title").attr('title')
	imgItem = $(element).parents(".R2D2").find(".item_img").attr('src');
	$('#addItemInCompare .item_title').text(titleItem);
	$('#addItemInCompare .item_img img').attr('src', imgItem);
	var ModalName = $('#addItemInCompare');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName);
	
	if (mode == 'list')
	{					

		var removeCompare = '<input type="checkbox" class="addtoCompareCheckbox"/ checked>'+text+'';
		button.html(removeCompare);
		button.attr("href", deleteUrl);
		button.attr("onclick", "return deleteFromCompare(this, \'"+mode+"\', \'"+text+"\', \'"+href+"\');");	
	}
	else if (mode == 'detail' || mode == 'list_price')
	{
		var removeCompare = text;
    	//button.html(removeCompare);
    }

	if (href)
		$.get( href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname),
			$.proxy(
				function(data) {									   
					var compare = $("#compare");
					compare.html(data);
					
	                if (compare.css("display") == "none") {
					    compare.css({ "display": "block"/*, "height": "0"*/ });
					}
				}, button
			)
		);
	return false;
}

function deleteFromCompare(element, mode, text, compareUrl) {
    if (!element || !element.href) return;

    //var href = element.href;
    var button = $(element);
    var href = button.attr("href");    
    
	button.unbind('click').removeAttr("href");
	if (mode == 'list')
	{					

		var removeCompare = ''+text+'';
		button.html(text);
		button.attr("href", compareUrl);
		button.attr("onclick", "return addToCompare(this, \'"+mode+"\', \'"+text+"\', \'"+href+"\');");	
	}

	$.get( href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname) ,
		$.proxy(
			function(data) {									   
				var compare = $("#compare");   
				compare.html(data);
				if (compare.css("display") == "none") {
				    compare.css({ "display": "block", "height": "0" });
				}				
			}, button
		)
	);   
	return false;
}

function disableAddToCompare(element, mode, text, deleteUrl) {
	if (!element ) return;
	                             
    element = $(element);              
	var href = element.attr("href");      
    if (mode == 'list')
    {
		var removeCompare = '<input type="checkbox" class="addtoCompareCheckbox"/ checked>'+text+'';
		element.html(removeCompare);
		element.attr("onclick", "return deleteFromCompare(this, \'"+mode+"\', \'"+text+"\', \'"+href+"\');");
		element.attr("href", deleteUrl);					
	}
	else if (mode == 'detail' || mode == 'list_price')
	{
		var removeCompare = text;
	    element
	        //.html(removeCompare)
	        .unbind('click').removeAttr("href");
	}    
    return false;
}     

function compareTable(counttd) {
	$(".table_compare").find("table").css("width",100+"%")
	$(".table_compare table tr:first").find("td").css("width","auto");
}

function deleteFromCompareTable(element) {
	var counttd = $(".table_compare table tr:first").find("td img").size()
	if(counttd == 1){
		var heightsum = $(".workarea").height()-$(".breadcrumbs").height()
		$(".table_compare").remove();
		$(".filtren.compare").remove();
		$(".deleteAllFromCompareLink").remove();
		$(".emptyListCompare").css("display","block");
		$(".sort").remove();
		$(".nocomapre").css({"height":heightsum+"px","display":"table","width":100+"%"},300);
	
	} else if(counttd < 4){
		compareTable(counttd);  
	}      
	var tdInd = $(element).parent('td').index();
	wtdc = $(element).parents('table').find("tr").find("td:nth("+tdInd+")").width()
	$(element).parents('table').find("tr").find("td:nth("+tdInd+")").css({"width":wtdc+"px","padding":0+"px"}); 
	$(element).parents('table').find("tr").find("td:nth("+tdInd+")").animate({"width":0+"px","padding":0+"px"}, 300); 
	$(element).parents('table').find("tr").find("td:nth("+tdInd+")").text('');
	$(element).parents('table').find("tr").find("td:nth("+tdInd+")").remove();       
	 
	var href = element.href;                 
	$.get( href);     

	return false;
}

function addToCart(element, mode, text, type) {                  	
	if (!element && !element.href)
		return;
	
	var href = element.href;		 
	var button = $(element);
	button.unbind('click').removeAttr("href");
 
	titleItem = button.parents(".R2D2").find(".item_title").attr('title');
	imgItem = button.parents(".R2D2").find(".item_img").attr('src');	
	$('#addItemInCart .item_title').text(titleItem);
	$('#addItemInCart .item_img img').attr('src', imgItem); 
	var ModalName = $('#addItemInCart');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName);

	if (href)
		$.get( href+"&ajax_buy=1", $.proxy(
	  		function(data) {          
				$("#cart_line").html(data);
				/*if (type == "cart")  //picture cart in button
					this.html(text).removeClass("addtoCart").addClass("incart");
				else if (type == "noButton")
					this.html(text);
				else
					this.html(text).removeClass("addtoCart").addClass("incart");	*/
			}, button) 
		);             
	return false;
}

function disableAddToCart(elementId, mode, text) { 
	var	element = $("#"+elementId);
	if (!element)
		return;
    
	$(element).removeAttr("href");               
	/*if (mode == "detail")
		$(element).html(text).removeAttr("href").unbind('click').css("cursor", "default").removeClass("addtoCart").addClass("incart");
	else if (mode == "list")
		$(element).html(text).unbind('click').css("cursor", "default").removeAttr("href").removeClass("addtoCart").addClass("incart");
	else if (mode == "detail_short")
		$(element).html(text).unbind('click').css("cursor", "default").removeAttr("href"); */
}

function DeleteFromCart(element) {
// $(element).parents('tr').remove();
// setTimeout(function(element) { $(element).parents('tr').remove() }, 300)
	$(element).parents('tr').animate({"height":0+"px","opacity":0,"overflow":"hidden","display":"none"}, 300);
	$(element).parents('tr').find("td").animate({"height":0+"px","padding":0+"px"}, 300);
	$(element).parents('tr').find("td").text('').remove();
	var href = element.href;  

	if (href)               
		$.get( href); 
   
	/*$.get(href,
		$.proxy(
			function(data) {alert(data);									   
				$("#basket_main").html(data);	
				return false;				
			}, button
		)
	);*/



	return false;
}

function addToSubscribe(element, text){
	var href = element.href;
	var titleItem = $(element).parents(".R2D2").find(".item_title").attr('title')
	var imgItem = $(element).parents(".R2D2").find(".item_img").attr('src');
	$('#addItemInSubscribe .item_title').text(titleItem);
	$('#addItemInSubscribe .item_img img').attr('src', imgItem);
	var ModalName = $('#addItemInSubscribe');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName)
	
	//$(element).html(text).addClass("incart");
	if (href)
		$.get(href, function() {
		});
	
	return false;
}

function addOfferToSubscribe(element, text, mode){
	var href = element.href;
	if (mode == 'list')
	{
		$('#addItemInCartOptions').css({"display":"none"});
		titleItem = $("#addItemInCartOptions").find(".item_title").attr('title');
		imgItem = $("#addItemInCartOptions").find(".item_img img").attr('src');	
	}
	else if (mode == 'detail')
	{
		titleItem = $(".R2D2").find(".item_title").attr('title');
		imgItem = $(".R2D2").find(".item_img").attr('src');	
	}
	$('#addItemInSubscribe .item_title').text(titleItem);
	$('#addItemInSubscribe .item_img img').attr('src', imgItem);
	var ModalName = $('#addItemInSubscribe');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName)
	
//	$(element).html(text).addClass("incart");
	if (href)
		$.get(href, function() {});
	
	return false;
}

function disableAddToSubscribe(elementId, text){                               
	$("#"+elementId)/*.html(text).addClass("incart")*/.unbind('click').removeAttr("href");
}
 //SKU
function addOfferToCart (element, mode, text) {                  	
	if (!element && !element.href)
		return;
			 
	var button = $(element);
		              
 	$('#addItemInCartOptions').css({"display":"none"});
	titleItem = $("#addItemInCartOptions").find(".item_title").attr('title');
	imgItem = $("#addItemInCartOptions").find(".item_img img").attr('src');	
	$('#addItemInCart .item_title').text(titleItem);
	$('#addItemInCart .item_img img').attr('src', imgItem);  
	var ModalName = $('#addItemInCart');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName)   
    
	if (element.href)
		$.get( element.href+"&ajax_buy=1", function(data) {          
				$("#cart_line").html(data);	
			}
		);               
	return false;
}               

function addOfferToCompare(element, mode, text) {
    if (!element || !element.href) return;

    var href = element.href;
    var button = $(element);

    $('#addItemInCartOptions').css({"display":"none"});
	button.unbind('click').removeAttr("href");
	titleItem = $("#addItemInCartOptions").find(".item_title").attr('title');
	imgItem = $("#addItemInCartOptions").find(".item_img img").attr('src');	
	$('#addItemInCompare .item_title').text(titleItem);
	$('#addItemInCompare .item_img img').attr('src', imgItem);
	var ModalName = $('#addItemInCompare');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName)   

	$.get( href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname), function(data) {									   
			var compare = $("#compare");
			compare.html(data);

			if (compare.css("display") == "none") {
			    compare.css({ "display": "block", "height": "0" });
			}
		}
	);  
	return false;
} 

function addHtml(lastPropCode, arSKU, mode, type) {               
	if (mode == "list")
	{
		if (type == "clear_cart" || type == "clear_compare")
		{
		    BX("listItemPrice").innerHTML = item_price;
		    if ($("#listItemPrice").hasClass('discount-price'))
		    	$("#listItemPrice").removeClass('discount-price');
		    BX("listItemOldPrice").innerHTML = "";
			if (type == "clear_cart")  
		    	BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart" >'+BX.message('addToCart')+'</a>';
  			else if (type == "clear_compare")
  				BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart">'+BX.message('addCompare')+'</a>';
		    return;
		}
		var selectedSkuId = BX(lastPropCode).value;
		for (var i = 0; i < arSKU.length; i++)
		{
			if (arSKU[i]["ID"] == selectedSkuId)
			{
				if (arSKU[i]["DISCOUNT_PRICE"] != "")
				{
					BX("listItemPrice").innerHTML = arSKU[i]["DISCOUNT_PRICE"];
					BX("listItemOldPrice").innerHTML = arSKU[i]["PRICE"];
					$("#listItemPrice").addClass('discount-price');					
				}
				else
				{
					BX("listItemPrice").innerHTML = arSKU[i]["PRICE"];
					BX("listItemOldPrice").innerHTML = "";
				}
				if (type == "cart")
				{
		            if (arSKU[i]["CAN_BUY"])
					{
						if (arSKU[i]["CART"] == "")
							BX("element_buy_button").innerHTML = '<a href="'+arSKU[i]["ADD_URL"]+'" rel="nofollow" class="bt3 addtoCart" onclick=" return addOfferToCart(this, \'list\', \''+BX.message('inCart')+'\');" id="catalog_add2cart_link_'+arSKU[i]["ID"]+'"><span></span>'+BX.message('addToCart')+'</a>';
					    else
							BX("element_buy_button").innerHTML = '<a rel="nofollow" class="bt3 addtoCart" onclick=" return addOfferToCart(this, \'list\', \''+BX.message('inCart')+'\');" id="catalog_add2cart_link_'+arSKU[i]["ID"]+'"><span></span>'+BX.message('addToCart')+'</a>';
					/*	else if (arSKU[i]["CART"] == "inCart")
							BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart"  id="catalog_add2cart_link_'+arSKU[i]["ID"]+'">'+BX.message('inCart')+'</a>';//.setAttribute("href",arSKU[i]["ADD_URL"]).innerHTML = '<span class="cartbuy"></span> ';
						else if (arSKU[i]["CART"] == "delay")
							BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart"  id="catalog_add2cart_link_'+arSKU[i]["ID"]+'">'+BX.message('delayCart')+'</a>';*/
					}
					else
					{
						if (arSKU[i]["CART"] == "inSubscribe")
							BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart">'+BX.message('inSubscribe')+'</a>';									
						else if (BX.message["USER_ID"] > 0 && arSKU[i]["SUBSCRIBE_URL"] != "")  
							BX("element_buy_button").innerHTML = '<a href="'+arSKU[i]["SUBSCRIBE_URL"]+'" rel="nofollow" class="bt3" onclick="return addOfferToSubscribe(this, \''+BX.message('inSubscribe')+'\', \'list\');" id="catalog_add2cart_link_"'+arSKU[i]["ID"]+'">'+BX.message('subscribe')+'</a>';
						else
							BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3" onclick="showAuthForSubscribe(this, \''+arSKU[i]["ID"]+'\', \''+arSKU[i]["SUBSCRIBE_URL"]+'\')">'+BX.message('subscribe')+'</a>';
					}    
				}
				else if (type == "compare" && BX("element_buy_button"))
				{         
					if (arSKU[i]["COMPARE"] == "inCompare")
	                    BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3">'+BX.message('inCompare')+'</a>';
					else
	                	BX("element_buy_button").innerHTML = '<a href="'+arSKU[i]["COMPARE_URL"]+'" rel="nofollow" class="bt3 addtoCompare" onclick="return addOfferToCompare(this, \'list\', \''+BX.message('inCompare')+'\');" id="catalog_add2compare_link_'+arSKU[i]["ID"]+'">'+BX.message('addCompare')+'</a>';
				
				}        
				break;
			}
		}
	}
	else if (mode == "detail")
	{
		if (type == "clear_cart")
		{
			BX("minOfferPrice").style.display = "block";
			BX("currentOfferPrice").innerHTML = "";
			if ($("#currentOfferPrice").hasClass('discount-price'))
				$("#currentOfferPrice").removeClass('discount-price');   
			BX("currentOfferOldPrice").innerHTML = "";
			BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart">'+BX.message('addToCart')+'</a><br/><br/><br/>';
			if (BX("element_compare_button"))
				BX("element_compare_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart">'+BX.message('addCompare')+'</a>';
			return;
		}
		var selectedSkuId = BX(lastPropCode).value;
		for (var i = 0; i < arSKU.length; i++)
		{
			if (arSKU[i]["ID"] == selectedSkuId)
			{
                BX("minOfferPrice").style.display = "none";
				if (arSKU[i]["DISCOUNT_PRICE"] != "")
				{
					BX("currentOfferPrice").innerHTML = arSKU[i]["DISCOUNT_PRICE"];
					BX("currentOfferOldPrice").innerHTML = arSKU[i]["PRICE"]; 
					$("#currentOfferPrice").addClass('discount-price');
					if ($("#currentOfferPrice").hasClass('price'))
						$("#currentOfferPrice").removeClass('price'); 
				}
				else
				{
					BX("currentOfferPrice").innerHTML = arSKU[i]["PRICE"];
					BX("currentOfferOldPrice").innerHTML = "";
				}
                if (arSKU[i]["CAN_BUY"])
				{
					if (arSKU[i]["CART"] == "")
						BX("element_buy_button").innerHTML = '<a href="'+arSKU[i]["ADD_URL"]+'" rel="nofollow" class="bt3 addtoCart" onclick="arSKU['+i+'][\'CART\']= \'inCart\'; return addToCart(this, \'detail\', \''+BX.message('inCart')+'\', \'cart\'); " id="catalog_add2cart_link_'+arSKU[i]["ID"]+'"><span></span>'+BX.message('addToCart')+'</a><br/><br/><br/>';
					else
						BX("element_buy_button").innerHTML = '<a rel="nofollow" class="bt3 addtoCart" onclick="arSKU['+i+'][\'CART\']= \'inCart\'; return addToCart(this, \'detail\', \''+BX.message('inCart')+'\', \'cart\'); " id="catalog_add2cart_link_'+arSKU[i]["ID"]+'"><span></span>'+BX.message('addToCart')+'</a><br/><br/><br/>';
				/*	else if (arSKU[i]["CART"] == "inCart")
						BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart"  id="catalog_add2cart_link_'+arSKU[i]["ID"]+'">'+BX.message('inCart')+'</a><br/><br/><br/>';
					else if (arSKU[i]["CART"] == "delay")
						BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt3 incart"  id="catalog_add2cart_link_'+arSKU[i]["ID"]+'">'+BX.message('delayCart')+'</a><br/><br/><br/>';*/
				}
				else
				{
					if (arSKU[i]["CART"] == "inSubscribe")
						BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt2 incart">'+BX.message('inSubscribe')+'</a><br/><br/><br/>';						
					else if (BX.message["USER_ID"] > 0 && arSKU[i]["SUBSCRIBE_URL"] != "")   
						BX("element_buy_button").innerHTML = '<a href="'+arSKU[i]["SUBSCRIBE_URL"]+'" rel="nofollow" onclick="return addOfferToSubscribe(this, \''+BX.message('inSubscribe')+'\', \'detail\');" class="bt2" id="catalog_add2cart_link_"'+arSKU[i]["ID"]+'">'+BX.message('subscribe')+'</a><br/><br/><br/>';
					else
						BX("element_buy_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt2" onclick="showAuthForSubscribe(this, \''+arSKU[i]["ID"]+'\', \''+arSKU[i]["SUBSCRIBE_URL"]+'\')">'+BX.message('subscribe')+'</a><br/><br/><br/>';

				}
				if (BX("element_compare_button"))
					if (arSKU[i]["COMPARE"] == "inCompare")
						BX("element_compare_button").innerHTML = '<a href="javascript:void(0)" rel="nofollow" class="bt2">'+BX.message('inCompare')+'</a>';
					else
						BX("element_compare_button").innerHTML = '<a href="'+arSKU[i]["COMPARE_URL"]+'" rel="nofollow" class="bt2 addtoCompare" onclick="arSKU['+i+'][\'COMPARE\']= \'inCompare\'; return addToCompare(this, \'detail\', \''+BX.message('inCompare')+'\');" id="catalog_add2compare_link_'+arSKU[i]["ID"]+'">'+BX.message('addCompare')+'</a>';
				break;
			}
		}
	}
}
	
function checkSKU(form_name, SKU, prop_num, arProperties) {             
    for (var i = 0; i < prop_num; i++)
    {                               
        if (SKU[i] != document.forms[form_name][arProperties[i].CODE].value)
            return false;
    }
    return true;
}

function buildSelect(form_name, cont_name, prop_num, arSKU, arProperties, mode, type) {   	                      
	var properties_num = arProperties.length;  
	var lastPropCode = arProperties[properties_num-1].CODE;    
	
    for (var i = prop_num; i < properties_num; i++)
    {
        var q = BX('prop_' + i);
        if (q)
            q.parentNode.removeChild(q);
    }
      
    var select = BX.create('SELECT', {
        props: {
            name: arProperties[prop_num].CODE,
			id :  arProperties[prop_num].CODE
        },
        events: {
            change: (prop_num < properties_num-1)
				? function() {
					buildSelect(form_name, cont_name, prop_num + 1, arSKU, arProperties, mode, type);
					if (this.value != "null") BX(arProperties[prop_num+1].CODE).disabled = false;
					addHtml(lastPropCode, arSKU, mode, "clear_"+type);
				}
				: function() {
					if (this.value != "null")
						addHtml(lastPropCode, arSKU, mode, type);
					else
						addHtml(lastPropCode, arSKU, mode, "clear_"+type);
            	}
        }
    });
	if (prop_num != 0) select.disabled = true;

	var ar = [];
	select.add(new Option('--'+BX.message("chooseProp")+' '+arProperties[prop_num].NAME+'--', 'null'));
	for (var i = 0; i < arSKU.length; i++)
	{
		if (checkSKU(form_name, arSKU[i], prop_num, arProperties) && !BX.util.in_array(arSKU[i][prop_num], ar))
		{
			select.add(new Option(
					arSKU[i][prop_num],     //text
					prop_num < properties_num-1 ? arSKU[i][prop_num] : arSKU[i]["ID"]// value

			));
			ar.push(arSKU[i][prop_num]);
		}
	}
 
    var cont = BX.create('tr', {
        props: {id: 'prop_' + prop_num},
        children:[
            BX.create('td', {html: arProperties[prop_num].NAME + ': '}),
            BX.create('td', { children:[
                select
            ]}),
        ]
    });

    var tmp = BX.findChild(BX(cont_name), {tagName:'tbody'}, false, false);
	tmp.appendChild(cont);
 
	if (prop_num < properties_num-1)
		buildSelect(form_name, cont_name, prop_num + 1, arSKU, arProperties, mode, type);   
}

function showOfferPopup(element, mode, text, arSKU, arProperties, mess, type) {                  	
	if (!element || !element.href)
		return;      
	BX.message(mess);			 
	var button = $(element);
	if (mode == "detail")
		button.unbind('click');
	else if (mode == "list")
		button.unbind('click');
  
	var titleItem = button.parents(".R2D2").find(".item_title").attr('title')
	var imgItem = button.parents(".R2D2").find(".item_img").attr('src');	
	var item_price = button.parents(".R2D2").find(".item_price").text();	
	var item_old_price = button.parents(".R2D2").find(".item_old_price").text();
	$('#addItemInCartOptions .item_title').text(titleItem);
	$('#addItemInCartOptions .item_title').attr('title', titleItem);
	$('#addItemInCartOptions .item_price').text(item_price);
	$('#addItemInCartOptions .item_old_price').text(item_old_price);
	$('#addItemInCartOptions .item_img img').attr('src', imgItem);
	$("#addItemInCartOptions .item_count").attr('value', 1);
	var ModalName = $('#addItemInCartOptions');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName)   
	window.item_price = item_price; 
	       
	buildSelect("buy_form_list", "sku_selectors_list", 0, arSKU, arProperties, mode, type);
	var properties_num = arProperties.length;
	var lastPropCode = arProperties[properties_num-1].CODE;
	addHtml(lastPropCode, arSKU, mode, "clear_"+type);
		            
	return false;
}
		
function setEqualHeight(columns){
	var tallestcolumn = 0;
	columns.each(function(){
		currentHeight = $(this).height();
		if(currentHeight > tallestcolumn){
			tallestcolumn = currentHeight;
		}
	});
	columns.height(tallestcolumn);
}	

function setEqualHeight2(columns){
	var tallestcolumn = 0;
	columns.each(function(){
		currentHeight = $(this).height();
		if(currentHeight > tallestcolumn){
			tallestcolumn = currentHeight;
		}
	});
	columns.height(tallestcolumn);
}

function setEqualHeight3(columns){
	var tallestcolumn = 0;
	columns.each(function(){
		currentHeight = $(this).height();
		if(currentHeight > tallestcolumn){
			tallestcolumn = currentHeight;
		}
	});
	columns.height(tallestcolumn);
}

$(".horizontalfilter > li > span").live('click', function() {
	var ind = $(this).parent("li").index();
	ind++;
	if ($(this).parent("li").hasClass("active")){} else
	{	
		$(this).parents('.filtren.compare').find('.active').removeClass('active')
		$(this).parent("li").addClass('active');
		$(".filtren.compare .cntf").find(".cnt:nth-child("+ind+")").addClass('active');
	}
	return false;
}); 
/*TABS*/
/* */
$(".tabsblock > .tabs > a").live('click', function() {
	var ind = $(this).index();
	ind++;
	if ($(this).hasClass("active")){} else
	{	
		$(this).parents('.tabsblock').find('.active').removeClass('active')
		$(this).addClass('active');
		$(".tabsblock").find(".cnt:nth-child("+ind+")").addClass('active');
	}
	return false;
});

$("#notify_auth_form > .social > form > ul > li > a").live('click', function() {
	setTimeout(function() {
		var modalH = $("#popupFormSubscribe").height();
		var modalW = $("#popupFormSubscribe").width();
		$("#popupFormSubscribe").animate({"margin-left":"-"+(parseInt(modalW)/2)+"px","margin-top":"-"+(parseInt(modalH)/2)+"px"},300)
	}, 100)	
});
$("#login > .social ul li a").live('click', function() {
	setTimeout(function() {
		var modalH = $("#login").height();
		var modalW = $("#login").width();
		$("#login").animate({"margin-left":"-"+(parseInt(modalW)/2)+"px","margin-top":"-"+(parseInt(modalH)/2)+"px"},300)

	}, 100)	
});
 
$(document).ready(function() {
	$(".tlistitem_horizontal_shadow").css({"width":$(".tlistitem_horizontal_shadow").parent(".R2D2").width()+"px"})
	setEqualHeight($(".listitem li > h4"));
	setEqualHeight2($("#foo_newproduct > li > h4"));
	setEqualHeight3($("#foo_saleleader > li > h4"));
	setEqualHeight($(".listitem li > .buy"));
	setEqualHeight2($("#foo_newproduct > li > .buy"));
	setEqualHeight3($("#foo_saleleader > li > .buy")); 
	$(".caroufredsel_wrapper").css({"margin":"0 auto"}); 
	$(".slideViewer").css({"float":"none"});
	
	var counttd = $(".table_compare table tr:first").find("td img").size()
	if(counttd < 4){
		compareTable(counttd);
	}  
	$(".table_compare table").find("tr td:nth(0)").css("width","160px")
	$(".table_compare table").find("tr:nth(0) td").css("height", 120+"px")
	$(".table_compare table").find("tr:nth(1) td").css("height", $(".table_compare table").find("tr:nth(1) td").height()+"px")
	$(".table_compare").css("height", $(".table_compare").find("table").height()+30+"px")
	var countli = $(".caroufredsel_wrapper ul li").size()
	if(countli < 4){
		$(".caroufredsel_wrapper").find(".next").addClass("disable")
	}  
});     

function addProductToSubscribe(element, url, id){
	BX.showWait();
	if ($('#url_notify_'+id))
		$('#url_notify_'+id).html('').addClass("incart");

	var titleItem = $(element).parents(".R2D2").find(".item_title").attr('title');
	if (titleItem)
	{
		var imgItem = $(element).parents(".R2D2").find(".item_img").attr('src');
	}
	else
	{
		titleItem = $("#addItemInCartOptions").find(".item_title").attr('title');
		imgItem = $("#addItemInCartOptions").find(".item_img img").attr('src');
	}
			
	BX.ajax.post(url, '', function(res) {
		BX.closeWait();
		document.body.innerHTML = res;		
		$('#addItemInSubscribe .item_title').text(titleItem);
		$('#addItemInSubscribe .item_img img').attr('src', imgItem);
		var modalH = $('#addItemInSubscribe').height();
		$("#bgmod").css("display","block");
		$('#addItemInSubscribe').css({"display":"block","margin-top":"-"+(parseInt(modalH)/2)+"px" });
	});
}

function showAuthForSubscribe(element, id, notify_url){
	/* $("#popupFormSubscribe").css({display: "block"}); */
	var ModalName = $('#popupFormSubscribe');
	CentriredModalWindow(ModalName);
	OpenModalWindow(ModalName);
	window.button = $(element);
	window.subId = id;
	BX('popup_notify_url').value = notify_url;
	BX('popup_user_email').focus();
}

function showAuthForm(){
	if (BX('notify_auth_form').style.display == "none")
	{
		BX('notify_auth_form').style["display"] = "block";
		BX('notify_user_auth').value = 'Y';
		BX('notify_user_email').style["display"] = "none";
		BX('subscribeBackButton').style["display"] = "inline";
		BX('subscribeCancelButton').style["display"] = "none";
		$('#popup_n_error').css('display', 'none');
		
		var ModalName = $('#popupFormSubscribe');
		$(ModalName).css({'width':'auto'})
/*	 	var modalW = $(ModalName).width();
		$(ModalName).css({"margin-left":"-"+(parseInt(modalW)/2)+"px","opacity":0}) */
		CentriredModalWindow(ModalName);
		OpenModalWindow(ModalName);
	}
}    

function showUserEmail(){
	if (BX('notify_user_email').style.display == "none")
	{
		BX('notify_user_email').style["display"] = "block";
		BX('notify_user_auth').value = 'N';
		BX('notify_auth_form').style["display"] = "none";
		BX('subscribeBackButton').style["display"] = "none";
		BX('subscribeCancelButton').style["display"] = "inline";
		$('#popup_n_error').css('display', 'none');
		
		var ModalName = $('#popupFormSubscribe');
		$(ModalName).css({'width':'auto'})
/* 		var modalW = $(ModalName).width();
		$(ModalName).css({"margin-left":"-"+(parseInt(modalW)/2)+"px","opacity":0}) */
		CentriredModalWindow(ModalName);
		OpenModalWindow(ModalName);
	}
}