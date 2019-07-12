
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$arResult["REQUEST_URI"] = $_SERVER["HTTP_REFERER"];?>

<style>
	#fancybox-outer {
		background: #fff;
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
				<a href="#" id="fn-close"><img src="<?=SITE_TEMPLATE_PATH?>/images/pop_close.gif" alt="[x]" /></a>
			</div>
		<h2>



		</h2>
		<div>
			    <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "popup", array(
				"REGISTER_URL" => SITE_DIR."register/",
				"FORGOT_PASSWORD_URL" => SITE_DIR."personal/profile/?forgot_password=yes",
				"PROFILE_URL" => SITE_DIR."personal/profile/",
				"SHOW_ERRORS" => "Y"
				),
				false
			);?>
		</div>
	</div>
	<div class="pop_white_bottom">
		<div></div>
	</div>
</div>


