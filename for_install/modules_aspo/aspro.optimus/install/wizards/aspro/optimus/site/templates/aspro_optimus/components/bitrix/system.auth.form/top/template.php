<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form id="auth_params" action="<?=SITE_DIR?>ajax/show_personal_block.php">
	<input type="hidden" name="REGISTER_URL" value="<?=$arParams["REGISTER_URL"]?>" />
	<input type="hidden" name="FORGOT_PASSWORD_URL" value="<?=$arParams["FORGOT_PASSWORD_URL"]?>" />
	<input type="hidden" name="PROFILE_URL" value="<?=$arParams["PROFILE_URL"]?>" />
	<input type="hidden" name="SHOW_ERRORS" value="<?=$arParams["SHOW_ERRORS"]?>" />
</form>
<?
$frame = $this->createFrame()->begin('');
$frame->setBrowserStorage(true);
?>
<?if(!$USER->IsAuthorized()):?>
	<div class="module-enter no-have-user">
		<!--noindex-->
			<a class="avtorization-call icon" rel="nofollow" href="<?=SITE_DIR;?>auth/"><span><?=GetMessage("AUTH_LOGIN_ENTER");?></span></a>
			<a class="register" rel="nofollow" href="<?=$arParams["REGISTER_URL"];?>"><span><?=GetMessage("AUTH_LOGIN_REGISTER");?></span></a>
		<!--/noindex-->
	</div>
<?else:?>
	<div class="module-enter have-user">
		<?
		global $USER;
		$arPhoto=array();
		$arUser=COptimusCache::CUser_GetList(array("SORT"=>"ASC", "CACHE" => array("MULTI" => "N", "TAG"=>COptimusCache::GetUserCacheTag($USER->GetID()))), array("ID"=>$USER->GetID()), array("FIELDS"=>array("ID", "PERSONAL_PHOTO")));
		if($arUser["PERSONAL_PHOTO"]){
			$arPhoto=CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"], array("width"=>21, "height"=>21), BX_RESIZE_IMAGE_EXACT, true);
		}
		?>
		<!--noindex-->
			<a href="<?=$arResult["PROFILE_URL"]?>" class="reg icon <?=($arPhoto ? "has-img" : "");?>" rel="nofollow" >
				<?if($arPhoto){?>
					<span class="bg_user" style='background:url("<?=$arPhoto['src'];?>") center center no-repeat;'></span>
				<?}?>
				<span><?=GetMessage("AUTH_LOGIN_BUTTON");?></span>
			</a>
			<a href="<?=SITE_DIR?>?logout=yes" class="exit_link" rel="nofollow"><span><?=GetMessage("AUTH_LOGOUT_BUTTON");?></span></a>
		<!--/noindex-->
	</div>	
<?endif;?>
<?$frame->end();?>
