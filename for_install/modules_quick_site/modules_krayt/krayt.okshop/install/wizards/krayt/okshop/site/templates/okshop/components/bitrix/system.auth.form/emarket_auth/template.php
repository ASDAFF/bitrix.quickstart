<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//CJSCore::Init(array("popup"));
?>
<div id="eMarket-auth">
	<?if ($arResult["FORM_TYPE"] == "login") {?>
		<?//echo '<pre>'; print_r($_SERVER['DOCUMENT_ROOT']);echo '</pre>';?>
		<input type="hidden" name="eMarket_auth_cur_page" value="<?=$this->GetFolder()?>">
		<input type="hidden" name="eMarket_auth_backurl" value="<?=$arResult["BACKURL"]?>">
		<input type="hidden" name="eMarket_auth_forgotPassUrl" value="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>">
		<input type="hidden" name="eMarket_auth_site_id" value="<?=SITE_ID?>">
		
		
		<span class="ico login-ico"></span>
		<a class="link" href="<?=$arResult["AUTH_URL"]?>" id="eMarket-login"><?=GetMessage("AUTH_LOGIN")?></a>
		<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
			<a class="link" href="<?=SITE_DIR?>auth/registration.php" ><?=GetMessage("AUTH_REGISTER")?></a>
		<?endif;?>
	<?} else {
		$name = trim($USER->GetFullName());
		if(strlen($name) <= 0) 
			$name = $USER->GetLogin();
		?>
		<a class="link" href="<?=$arResult['PROFILE_URL']?>"><?=htmlspecialcharsEx($name);?></a>
		<a class="link" href="<?=$APPLICATION->GetCurPageParam("logout=yes", Array("logout"))?>"><?=GetMessage("AUTH_LOGOUT")?></a>
	<?}?>
</div>

<script>
	/*function openAuthorizePopup() {
		var authPopup = BX.PopupWindowManager.create("AuthorizePopup", null, {
			autoHide: true,
			//	zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			overlay : true,
			draggable: {restrict:true},
			closeByEsc: true,
			closeIcon: { right : "12px", top : "10px"},
			content: '<div style="width:400px;height:400px; text-align: center;"><span style="position:absolute;left:50%; top:50%"><img src="<?=$this->GetFolder()?>/images/wait.gif"/></span></div>',
			events: {
				onAfterPopupShow: function() {
					BX.ajax.post (
							'<?=$this->GetFolder()?>/ajax.php',
							{
								backurl: '<?=CUtil::JSEscape($arResult["BACKURL"])?>',
								forgotPassUrl: '<?=CUtil::JSEscape($arResult["AUTH_FORGOT_PASSWORD_URL"])?>',
								site_id: '<?=SITE_ID?>'
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
							},
							this)
					);
				}
			}
		});

		authPopup.show();
	}*/
</script>