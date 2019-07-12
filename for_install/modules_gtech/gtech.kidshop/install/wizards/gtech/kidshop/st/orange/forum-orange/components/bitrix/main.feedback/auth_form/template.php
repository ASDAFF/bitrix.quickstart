<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div class="profile-menu1 profile-menu-group1">

	<div class="profile-menu-inner1">
		<a class="profile-menu-avatar1" id="profile-menu-avatar1" onclick="return OpenProfileMenuPopup1(this);">
		<img src="<?=$templateFolder?>/images/key.png" border="0" width="18px" align="left">&nbsp;
		<span style="border-bottom: dashed 1px #000;"><?=GetMessage("PERSONAL_TITLE")?></span>
		</a>
	</div>

</div>

<div class="profile-menu-popup1 profile-menu-popup-group1" id="profile-menu-popup1">

<div class="profile-menu-popup-items1">
    <div class="feedback-wrap1">
                <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "main_auth", array(
	"REGISTER_URL" => "/personal/reg.php",
	"FORGOT_PASSWORD_URL" => "/personal/reg.php",
	"PROFILE_URL" => "/personal/",
	"SHOW_ERRORS" => "Y"
	),
	false
);?>
    </div>
</div>


<script type="text/javascript">
function OpenProfileMenuPopup1(source)
{
	var offsetTop = -20;
	var offsetLeft = -130;

	var ie7 = false;

	if (ie7 || (document.documentMode && document.documentMode <= 7))
	{
		offsetTop = -54;
	    offsetLeft = -12;
	}

	var popup = BX.PopupWindowManager.create("profile-menu1", BX("profile-menu-avatar1"), {
		offsetTop : offsetTop,
		offsetLeft : offsetLeft,
		autoHide : true,
		closeIcon : true,
		content : BX("profile-menu-popup1")
	});

	popup.show();


	BX.bind(popup.popupContainer, "mouseover", BX.proxy(function() {
		if (this.params._timeoutId)
		{
			clearTimeout(this.params._timeoutId);
			this.params._timeoutId = undefined;
		}

		this.show();
	}, popup));

	return false;
}

function CloseProfileMenuPopup(event)
{
	if (!this.params._timeoutId)
		this.params._timeoutId = setTimeout(BX.proxy(function() { this.close()}, this), 300);
}
</script>
