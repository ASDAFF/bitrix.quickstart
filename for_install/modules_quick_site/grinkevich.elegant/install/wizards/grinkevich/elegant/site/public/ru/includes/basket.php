
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>


<style>
	#fancybox-outer {
		background: none;
	}

	#fancybox-close {
		background: none;
		background-position: 0px 0px;
		z-index: 0;
	}

</style>

<script>
$(document).ready(function(){
    $("#basketOrderButton2").live("click", function(){
	    ///basket/order/

	    $.fancybox.showActivity();

	    var postdatas = $("#basket_form").formSerialize();

	    $.ajax({
		    url: "<?=SITE_DIR?>includes/basket_full.php",
		    type: "POST",
		    data: postdatas + "&BasketOrder=1",
		    cache	: false,
		    success: function(data){
			    location.replace("<?=SITE_DIR?>basket/order/");
			    $.fancybox.hideActivity();
		    }
	    });


	    return false;
    });

    $("#basket_form").live("submit", function(){
	    $.fancybox.showActivity();

	    var postdatas = $("#basket_form").formSerialize();

	    $.ajax({
		    url: "<?=SITE_DIR?>includes/basket_full.php",
		    type: "POST",
		    data: postdatas + "&BasketRefresh=1",
		    cache	: false,
		    success: function(data){
			    $("#basketDiv").html(data);
			    $.fancybox.hideActivity();
		    }
	    });
	    return false;
    });
});
</script>


<div class="pop_white">
	<div class="pop_white_top">
		<div></div>
	</div>
	<div class="pop_white_text">
		<div class="close">
			<a href="#" id="fn-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/pop_close.gif" alt="" /></a>
		</div>
		<div>
			<h2>Корзина</h2>
			<div id="basketDiv" style="width: 800px;">
			    <?include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."includes/basket_full.php");?>
			</div>
		</div>
	</div>
	<div class="pop_white_bottom">
		<div></div>
	</div>
</div>


