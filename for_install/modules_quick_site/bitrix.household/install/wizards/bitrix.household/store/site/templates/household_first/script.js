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
				$(".comp_main .compare").css({ "display": "block"});
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
	if (mode == "detail")
		{ 
			$(element).html(text).removeClass("catalog-item-buy").addClass("catalog-item-in-the-cart")		
				.unbind('click').css("cursor", "default");
			$(element).html("<img src='/images/inbasket.png' width='79px' height='19px' alt='уже в корзине'/>").toggleClass("disabled")
				.removeAttr("href").unbind('click').css("cursor", "default");
			
		}
	else if (mode == "list")
		{
		$(element).html(text).removeClass("catalog-item-buy").addClass("catalog-item-in-the-cart")
			.unbind('click').css("cursor", "default");
		$(element).html("<img src='/images/inbasket.png' width='79px' height='19px' alt='уже в корзине'/>").toggleClass("disabled")
			.removeAttr("href").unbind('click').css("cursor", "default");
		}	
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

	$.get(
		element.href + "&ajax_buy=1",
		$.proxy(
			function(data) {
				if (this.mode == "detail")
					this.button.removeAttr("href").html("<img src='/images/inbasket.png' width='79px' height='19px alt='уже в корзине' />");
				else if (this.mode == "list")
					this.button.removeAttr("href").html("<img src='/images/inbasket.png' width='79px' height='19px alt='уже в корзине' />");

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
				else if (this.mode == "list")
				{
					hoverClassName = "catalog-item-hover";
					wrapper = this.button.parents("div.catalog-item");
				}

				wrapper.unbind("mouseover").unbind("mouseout").removeClass(hoverClassName);

				var imageToFly = $(imageElement);
				var position = imageToFly.position();
				var flyImage = imageToFly.clone().insertBefore(imageToFly);

				flyImage.css({ "position": "absolute", "left": position.left, "top": position.top });
				flyImage.animate({ width: 0, height: 0, left: 900, top: -58 }, 600, 'linear');
				flyImage.data("hoverClassName", hoverClassName);
				flyImage.queue($.proxy(function() {
					this.flyImage.remove();
					$("#cart_line").html(data);
					if (this.wrapper.data("adminMode") === true)
					{
						var hoverClassName = "";
						if (this.mode == "detail")
							hoverClassName = "catalog-detail-hover";
						else if (this.mode == "list")
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

function disableAddToCompare(elementId, textCompared, id, COMPARE_NAME, IBLOCK_ID)
{ 
	var objLink = document.getElementById(elementId);
	if (!objLink)
		return;
	var href = $(objLink).attr('href');	
	$(objLink).removeClass("catalog-item-compare");
	$(objLink).addClass("catalog-item-compared");
	$(objLink).children("input:checkbox").attr('checked', true);	
	$(objLink).children("label").text(textCompared);	
	href=href.replace("ADD_TO_COMPARE_LIST","DELETE_FROM_COMPARE_LIST");
	$(objLink).attr('href', href);
	$(objLink).css("cursor", "pointer");
	
	return false;
}

function addToCompare(objLink, textCompare, textCompared, id, COMPARE_NAME, IBLOCK_ID)
{
	if (!objLink || !objLink.href) 
		return;
		
	var elId=objLink.id;
	var href = $(objLink).attr('href');	
	$.get(href, function(data){
	  	if($(objLink).hasClass("catalog-item-compare"))
		{
			$(objLink).removeClass("catalog-item-compare");
			$(objLink).addClass("catalog-item-compared");
			$(objLink).children("input:checkbox").attr('checked', true);	
			$(objLink).children("label").text(textCompared);	
			href=href.replace("ADD_TO_COMPARE_LIST","DELETE_FROM_COMPARE_LIST");
			$(objLink).attr('href', href);
		}	
		else
		{
			$(objLink).removeClass("catalog-item-compared");
			$(objLink).addClass("catalog-item-compare");
			$(objLink).children("input:checkbox").attr('checked', false);
			$(objLink).children("label").text(textCompare);		
			href=href.replace("DELETE_FROM_COMPARE_LIST", "ADD_TO_COMPARE_LIST");
			$(objLink).attr('href', href);
		}	
		UpdateCompare(IBLOCK_ID);	
	});
	
	return false;
}