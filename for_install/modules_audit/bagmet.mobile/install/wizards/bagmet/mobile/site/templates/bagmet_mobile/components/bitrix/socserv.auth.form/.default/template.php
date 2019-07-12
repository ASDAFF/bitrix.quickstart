<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
if($arParams["POPUP"]):
	//only one float div per page
	if(defined("BX_SOCSERV_POPUP"))
		return;
	define("BX_SOCSERV_POPUP", true);
?>
<div style="display:none">
<div id="bx_auth_float" class="bx-auth-float">
<?endif?>

<?if($arParams["~CURRENT_SERVICE"] <> ''):?>
<script type="text/javascript">
BX.ready(function(){BxShowAuthService('<?=CUtil::JSEscape($arParams["~CURRENT_SERVICE"])?>', '<?=$arParams["~SUFFIX"]?>')});
</script>
<?endif?>
<?
if($arParams["~FOR_SPLIT"] == 'Y'):?>
<div class="bx-auth-serv-icons">
<?foreach($arParams["~AUTH_SERVICES"] as $service):?>
	<a title="<?=htmlspecialcharsbx($service["NAME"])?>" href="javascript:void(0)" onclick="BxShowAuthService('<?=$service["ID"]?>', '<?=$arParams["SUFFIX"]?>')" id="bx_auth_href_<?=$arParams["SUFFIX"]?><?=$service["ID"]?>"><i class="bx-ss-icon <?=htmlspecialcharsbx($service["ICON"])?>"></i></a>
<?endforeach?>
</div>
<?endif;?>
<div class="auth_form">
		<form method="post" name="bx_auth_services<?=$arParams["SUFFIX"]?>" target="_top" action="<?=$arParams["AUTH_URL"]?>">
			<?if($arParams["~SHOW_TITLES"] != 'N'):?>
			<h4 class="auth_form_title"><?=GetMessage("socserv_as_user")?></h4>
			<p><?=GetMessage("socserv_as_user_note")?></p>
			<?endif;?>
			<?if($arParams["~FOR_SPLIT"] != 'Y'):?>
			<div class="bx-auth-services">
	<?foreach($arParams["~AUTH_SERVICES"] as $service):?>
				<div><a href="javascript:void(0)" onclick="BxShowAuthService('<?=$service["ID"]?>', '<?=$arParams["SUFFIX"]?>')" id="bx_auth_href_<?=$arParams["SUFFIX"]?><?=$service["ID"]?>"><i class="bx-ss-icon <?=htmlspecialcharsbx($service["ICON"])?>"></i><b><?=htmlspecialcharsbx($service["NAME"])?></b></a></div>
	<?endforeach?>
			</div>
			<?endif;?>
			<?if($arParams["~AUTH_LINE"] != 'N'):?>
				<div class="bx-auth-line"></div>
			<?endif;?>
			<div class="auth_form_lj" id="bx_auth_serv<?=$arParams["SUFFIX"]?>" style="display:none">
	<?foreach($arParams["~AUTH_SERVICES"] as $service):?>
				<div id="bx_auth_serv_<?=$arParams["SUFFIX"]?><?=$service["ID"]?>" style="display:none"><?=$service["FORM_HTML"]?></div>
	<?endforeach?>
			</div>
	<?foreach($arParams["~POST"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?endforeach?>
			<input type="hidden" name="auth_service_id" value="" />
		</form>
</div>

<?if($arParams["POPUP"]):?>
</div>
</div>
<?endif?>