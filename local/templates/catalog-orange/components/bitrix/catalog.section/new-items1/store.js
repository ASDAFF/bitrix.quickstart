function disableAddToCart(elementId, mode, text)
{
	var	element = document.getElementById(elementId);
	if (!element)
		return;

	if (mode == "detail")
		$(element).html("<span>" + text + "</span>").toggleClass("disabled")
			.removeAttr("href").unbind('click').css("cursor", "default");
	else if (mode == "list")
		$(element).html(text).removeClass("catalog-item-buy").addClass("catalog-item-in-the-cart")
			.unbind('click').css("cursor", "default");
}

function addToCart(element, imageToFlyId, mode, text)
{
	if (!element || !element.href)
		return;

	var button = $(element);
	if (mode == "detail")
		button.toggleClass("disabled").unbind('click').css("cursor", "default");
	else if (mode == "list")
		button.removeClass("catalog-item-buy").addClass("catalog-item-in-the-cart").unbind('click').css("cursor", "default");

	$.get(
		element.href + "&ajax_buy=1",
		$.proxy(
			function(data) {

				if (this.mode == "detail")
					this.button.removeAttr("href").html("<span>" + text + "</span>");
				else if (this.mode == "list")
					this.button.removeAttr("href").html(text);

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
				flyImage.animate({ width: 0, height: 0, left: 948, top: -58 }, 600, 'linear');
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

function disableAddToCompare(elementId, text)
{
	var	element = document.getElementById(elementId);
	if (!element)
		return;

	$(element)
		.removeClass("catalog-item-compare").addClass("catalog-item-compared")
		.text(text)
		.unbind('click').removeAttr("href")
		.css("cursor", "default");

	return false;
}

function addToCompare(element, text)
{
	if (!element || !element.href)
		return;

	var href = element.href;
	var button = $(element);

	button.removeClass("catalog-item-compare").addClass("catalog-item-compared").unbind('click').removeAttr("href").css("cursor", "default");

	$.get(
		href + '&ajax_compare=1&backurl=' + decodeURIComponent(window.location.pathname),
		$.proxy(
			function(data) {

				var compare = $("#compare");
				compare.html(data);

				this.text(text);

				if (compare.css("display") == "none") {
					compare.css({ "display": "block", "height": "0" });
					compare.animate({ "height": "22px" }, 300);
				}
			}, button
		)
	);

	return false;
}