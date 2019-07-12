function analogs_count(count)
{
var	element = document.getElementById("an_count");

	if (!element)
		return;
	$(element).html(count);

	return false;
}

function UpdateCompare(IBLOCK_ID)
{
       $.ajax({
                type: "GET",
                url: "/compare.php",
                data: "data="+IBLOCK_ID,
                success: function(html) { 
                        $(".compare_num").empty();
                        $(".compare_num").append(html);
                        num=html*1;
                        if ($(".comp_main").css("display") == "none" && num>0) 
                        {
				$(".comp_main").css({ "display": "block", "height": "0" });
				$(".comp_main").animate({ "height": "22px" }, 300);
			}
			else if($(".comp_main").css("display") != "none" && num==0)
			{
				$(".comp_main").css("display","none");
			}			
                }
        });
}

function disableAddToCart(elementId, mode, text)
{
	var	element = document.getElementById(elementId);
	if (!element)
		return;
	var objForm = element.form;
	
	if (mode == "detail")
		{ 
			$(element).addClass("button_inbasket_card").css("cursor", "default").attr("title", "уже в корзине");
			$(objForm).removeAttr("action");
		}
	else if (mode == "list")
		{
			$(element).addClass("button_inbasket").toggleClass("disabled").css("cursor", "default").attr("title", "уже в корзине");
			$(objForm).removeAttr("action");
		}
	else if (mode == "new"){
			$(element).addClass("icon_add_off").toggleClass("disabled").unbind('click').css("cursor", "default").attr("title", "уже в корзине");
			$(objForm).removeAttr("action");
		}
	else if (mode == "special"){
			$(element).html("Уже в корзине").toggleClass("disabled").unbind('click').css("cursor", "default").attr("title", "уже в корзине");
			$(objForm).removeAttr("action");
		}
}

function checkDisableAddToCart(arPropIds, btmId, productId, mode, action)
{
	var strParams='ID='+productId;
	for(var i=0; i<arPropIds.length; i++) {
		var objSelect=document.getElementById('select_'+productId+'_'+arPropIds[i]);	
		if(objSelect)
		{
			strParams=strParams+"&"+arPropIds[i]+"="+objSelect.options[objSelect.selectedIndex].innerHTML;	
		}
		else
		{
			var objInput=document.getElementById('input_'+productId+'_'+arPropIds[i]);	
			if(objInput)
				strParams=strParams+"&"+arPropIds[i]+"="+objInput.value;
		}
	}	
	var	element = document.getElementById(btmId);
	var objForm = element.form;	
	var butClassDis="";
	if (mode == "detail")	 
		butClassDis="button_inbasket_card";
	else
		butClassDis="button_inbasket";
	
	$.ajax({
                type: "GET",
                url: "/include/checkDisableAddToCart.php",
                data: strParams,
                success: function(data) {   
                        var objData=JSON.parse(data);
                        if(objData.disable=="Y")
                        {                        	
                        	$(element).addClass(butClassDis).addClass("disabled").css("cursor", "default").attr("title", objData.title);
				$(objForm).removeAttr("action");
                        }	
                        else
                        {
                        	$(element).removeClass(butClassDis).removeClass("disabled").css("cursor", "pointer").attr("title", "В корзину");
				$(objForm).attr("action", action);
                        }	
                }
        });
}

function addToCartForm(objForm,  imageToFlyId,  mode, title, objButton)
{	
	var m_action=$(objForm).attr('action');
	if(!m_action)
		return false;
	var m_method=$(objForm).attr('method');		
	var m_data=$(objForm).serialize();		
	var butClassDis="";
	if (mode == "detail")	 
		butClassDis="button_inbasket_card";
	else
		butClassDis="button_inbasket";		
	var imageElement = document.getElementById(imageToFlyId);
	
	$.ajax({
		type: m_method,
		url: m_action,
		data: m_data,
		success: function(result){	
			$(objButton).addClass(butClassDis).addClass("disabled").css("cursor", "default").attr("title", title);			
			$(objForm).removeAttr('action');			
			if (!imageElement)
			{
				$("#cart_line").html(result);				
				return;
			}
			
			var hoverClassName = "";
				var wrapper = null;
			if (mode == "detail")
			{
				hoverClassName = "catalog-detail-hover";
				wrapper =  $(objForm).parent("div.catalog-detail");
			}
			else if (mode == "list")
			{
				hoverClassName = "catalog-item-hover";
				wrapper = $(objForm).parent("div.catalog-item");
			}

			wrapper.unbind("mouseover").unbind("mouseout").removeClass(hoverClassName);

			var imageToFly = $(imageElement);
			var position = imageToFly.position();
			var flyImage = imageToFly.clone().insertBefore(imageToFly);

			flyImage.css({ "position": "absolute", "left": position.left, "top": position.top });
			flyImage.animate({ width: 0, height: 0, left: 948, top: -250 }, 600, 'linear');
			flyImage.data("hoverClassName", hoverClassName);
			flyImage.queue($.proxy(function() {

					flyImage.remove();
					$("#cart_line").html(result);

					if (wrapper.data("adminMode") === true)
					{
						var hoverClassName = "";
						if (mode == "detail")
							hoverClassName = "catalog-detail-hover";
						else if (mode == "list")
							hoverClassName = "catalog-item-hover";

						wrapper.addClass(hoverClassName).bind({
							mouseover: function() { $(this).removeClass(hoverClassName).addClass(hoverClassName); },
							mouseout: function() { $(this).removeClass(hoverClassName); }
						});
					}

			}, {"wrapper" : wrapper, "flyImage" : flyImage, "mode": this.mode}));
		}
	});
	
	return false;	
}

function addToCart(element, imageToFlyId, mode, text)
{

	if (!element || !element.href)
		return;

	var button = $(element);
	if (mode == "detail")
		{button.toggleClass("disabled").unbind('click').css("cursor", "default");}
	else if (mode == "list")
		{button.removeClass("catalog-item-buy").addClass("catalog-item-in-the-cart").unbind('click').css("cursor", "default");}
	else /*if (mode == "new" || mode == "speсial")*/
		{button.unbind('click').css("cursor", "default");}

	$.get(
		element.href + "&ajax_buy=1",
		$.proxy(
			function(data) {				
				if (this.mode == "detail")
					this.button.removeAttr("href").addClass("button_inbasket_card").attr("title", "уже в корзине");
				else if (this.mode == "list")
					this.button.removeAttr("href").addClass("button_inbasket").attr("title", "уже в корзине");
				else if (this.mode == "new")
					this.button.removeAttr("href").addClass("icon_add_off").attr("title", "уже в корзине");
				else /*if (this.mode == "speсial")*/
					this.button.removeAttr("href").html("Уже в корзине").attr("title", "уже в корзине");

				var imageElement = document.getElementById(this.imageToFlyId);
				if (!imageElement)
				{
					$("#cart_line").html(data);
					return;
				}

				var hoverClassName = "";
				var wrapper = null;
				if (this.mode == "detail")
				{
					hoverClassName = "catalog-detail-hover";
					wrapper = this.button.parents("div.catalog-detail");
				}
				else /*if (this.mode == "list")*/
				{
					hoverClassName = "catalog-item-hover";
					wrapper = this.button.parents("div.catalog-item");
				}

				wrapper.unbind("mouseover").unbind("mouseout").removeClass(hoverClassName);

				var imageToFly = $(imageElement);
				var position = imageToFly.position();
				var flyImage = imageToFly.clone().insertBefore(imageToFly);

				flyImage.css({ "position": "absolute", "left": position.left, "top": position.top });
				flyImage.animate({ width: 0, height: 0, left: 1000, top: -58 }, 600, 'linear');
				flyImage.data("hoverClassName", hoverClassName);
				flyImage.queue($.proxy(function() {
					this.flyImage.remove();
					$("#cart_line").html(data);
					if (this.wrapper.data("adminMode") === true)
					{
						var hoverClassName = "";
						if (this.mode == "detail")
							hoverClassName = "catalog-detail-hover";
						else /*if (this.mode == "list")*/
							hoverClassName = "catalog-item-hover";
						this.wrapper.addClass(hoverClassName).bind({
							mouseover: function() { $(this).removeClass(hoverClassName).addClass(hoverClassName); },
							mouseout: function() { $(this).removeClass(hoverClassName); }
						});
					}

				}, {"wrapper" : wrapper, "flyImage" : flyImage, "mode": this.mode}));

			}, { "button": button, "mode": mode, "imageToFlyId" : imageToFlyId }
		)
	);

	return false;
}

function disableAddToCompare(elementId, text)
{
	var	element = document.getElementById(elementId);
	if (!element)
		return;

	var href = $(element).attr('href');
	href=href.replace("ADD_TO_COMPARE_LIST","DELETE_FROM_COMPARE_LIST");
	
	$(element)
		.removeClass("catalog-item-compare").addClass("catalog-item-compared")
		.attr('href', href)
		.attr('onclick', 'return deleteFromCompare(this, "Удалить из списка стравнения");')
		.css({"background-position": "0 -24px"});

	return false;
}

function addToCompare(element, text)
{
	if (!element || !element.href) 
		return;

	var href = element.href;
	var new_href=href.replace("ADD_TO_COMPARE_LIST","DELETE_FROM_COMPARE_LIST");
	var button = $(element);

	button.removeClass("catalog-item-compare").addClass("catalog-item-compared")
		  .attr('href', new_href)
		  .attr('onclick', 'return deleteFromCompare(this, "Удалить из списка стравнения");')
	      .css({"background-position": "0 -24px"});

	$.get(
		href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname),
		$.proxy(
			function(data) {

				var compare = $("#compare");
				compare.html(data);
				//this.text(text);
				if (compare.css("display") == "none") {
					compare.css({ "display": "block", "height": "0" });
					compare.animate({ "height": "22px" }, 300);
				}
			}, button
		)
		
	);
	
	return false;
}

function deleteFromCompare(element, text)
{
    if (!element || !element.href) 
		return;
	
	var href = element.href;
	var new_href=href.replace("DELETE_FROM_COMPARE_LIST","ADD_TO_COMPARE_LIST");
	var button = $(element);

	button.removeClass("catalog-item-compare").addClass("catalog-item-compared")
	      //.unbind('click').removeAttr("href")
		  .attr('href', new_href)
		  .attr('onclick', 'return addToCompare(this, "Добавить в список стравнения");')
	      .css({"background-position": "0 0"});
		  
	$.get(
		href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname),
		$.proxy(
			function(data) {

				var compare = $("#compare");
				compare.html(data);
				//this.text(text);
				if (compare.css("display") == "none") {
					compare.css({ "display": "block", "height": "0" });
					compare.animate({ "height": "22px" }, 300);
				}
			}, button
		)
	);

return false;
}

