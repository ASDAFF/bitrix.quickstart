<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?if($arParams["~CURRENT_SERVICE"] <> ''):?>
<script type="text/javascript">
BX.ready(function(){BxShowAuthService('<?=CUtil::JSEscape($arParams["~CURRENT_SERVICE"])?>', '<?=$arParams["~SUFFIX"]?>')});
</script>
<?endif?>
<div class="social">
	<form method="post" name="bx_auth_services<?=$arParams["SUFFIX"]?>" target="_top" action="<?=$arParams["AUTH_URL"]?>">
		<ul class="lsnn">
			<?
			$countServices = count($arParams["~AUTH_SERVICES"]);
			$curCountServices = 0;
			?>
			<?foreach($arParams["~AUTH_SERVICES"] as $service):?>
				<?if ($curCountServices == 6 && $countServices > 6):?>
					<li class="<?if ($countServices-$curCountServices < 7) echo "not"?>full all"><a href="javascript:void(0)" class=""><span></span></a>
						<ul class="lsnn">
				<?endif?>
				<li>
					<a href="javascript:void(0)"  onclick="BxShowAuthService('<?=$service["ID"]?>', '<?=$arParams["SUFFIX"]?>')" id="bx_auth_href_<?=$arParams["SUFFIX"]?><?=$service["ID"]?>"><span class="<?=htmlspecialcharsbx($service["ICON"])?>"></span></a>
				</li>
				<?
				$curCountServices++;
				?>
			<?endforeach?>
			<?if ($curCountServices > 6 ):?>
					</li>
				</ul>
			<?endif?>
		</ul>
		<div class="bx-auth-line"></div>
		<div class="bx-auth-service-form" id="bx_auth_serv<?=$arParams["SUFFIX"]?>" style="display:none">
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