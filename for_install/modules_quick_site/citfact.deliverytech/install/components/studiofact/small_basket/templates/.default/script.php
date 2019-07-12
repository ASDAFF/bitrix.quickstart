<script type="text/javascript">
	$(document).on("click", "a#small_basket", function () {
		if (!$(".small_basket_hover_block").hasClass("active")) {
			$(".small_basket_hover_block").slideDown("fast", function () {
				$(this).addClass("active");
			});
		} else {
			$(".small_basket_hover_block").slideUp("fast", function () {
				$(this).removeClass("active");
			});
		}

		return false;
	});
	$(document).on("click", "a.small_basket_hover_delete_action", function () {
		$.ajax({
			data: "update_small_basket=Y&SMALL_BASKET_DELETE="+$(this).attr("data-id")+"&SMALL_BASKET_OPEN=Y",
			url: $("#small_basket_box").attr("data-path"),
			async: true,
			cache: false,
			success: function (html) {
				$("#small_basket_box").html(html);
				$("#small_basket").addClass("update");
				setTimeout(function() { $("#small_basket").removeClass("update") }, 1000);
				$("#SMALL_BASKET_ORDER_PHONE").inputmask("+7 (999) 999 9999");
			}
		});

		return false;
	});
	$(document).on("change", ".small_basket_hover_quantity input", function () {
		$.ajax({
			data: "update_small_basket=Y&SMALL_BASKET_QUANTITY="+$(this).val()+"&SMALL_BASKET_ID="+$(this).attr("id").replace("QUANTITY_", "")+"&SMALL_BASKET_OPEN=Y",
			url: $("#small_basket_box").attr("data-path"),
			async: true,
			cache: false,
			success: function (html) {
				$("#small_basket_box").html(html);
				$("#small_basket").addClass("update");
				setTimeout(function() { $("#small_basket").removeClass("update") }, 1000);
				$("#SMALL_BASKET_ORDER_PHONE").inputmask("+7 (999) 999 9999");
			}
		});
	});
	$(document).on("click", "a.small_basket_hover_buy", function () {
		if (!$(".order_by_click").hasClass("active")) {
			$(".order_by_click").slideDown("fast", function () {
				$(this).addClass("active");
			});
		} else {
			$(".order_by_click").slideUp("fast", function () {
				$(this).removeClass("active");
			});
		}

		return false;
	});
	$(document).on("click", "a.small_basket_hover_buy_go", function () {
		var phone = $("#SMALL_BASKET_ORDER_PHONE").val().replace(/[^0-9]/g, '');
		if (phone.length != 11) {
			$("#SMALL_BASKET_ORDER_PHONE").addClass("red_border");
			setTimeout(function() { $("#SMALL_BASKET_ORDER_PHONE").removeClass("red_border") }, 1000);
		} else {
			$.ajax({
				data: "update_small_basket=Y&SMALL_BASKET_FAST_ORDER=Y&SMALL_BASKET_ORDER_PHONE="+$("#SMALL_BASKET_ORDER_PHONE").val(),
				url: $("#small_basket_box").attr("data-path"),
				async: true,
				cache: false,
				success: function (html) {
					$("#small_basket_box").html(html);
					var html = '<div class="success_fast_order"><?=GetMessage("SUCCESS_FAST_ORDER");?></div>';
					$.fancybox(html, {
						autoSize : false,
						autoResize : true,
						autoCenter : true,
						openEffect : "fade",
						closeEffect : "fade",
						width: 250,
						height: 75,
						helpers: {
							overlay: {
								locked: false
							}
						}
					});
				}
			});
		}

		return false;
	});
</script>