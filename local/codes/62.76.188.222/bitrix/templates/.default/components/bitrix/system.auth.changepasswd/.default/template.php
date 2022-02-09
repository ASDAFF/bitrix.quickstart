<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
 <section class="b-detail">
    <div class="b-detail-content">
<?
ShowMessage($arParams["~AUTH_RESULT"]);
?>
<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
	<?if (strlen($arResult["BACKURL"]) > 0): ?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<? endif ?>
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="CHANGE_PWD">
	<table class="b-subcribe__table"> 
		<tbody>
			<tr>
				<td><span class="starrequired">*</span><?=GetMessage("AUTH_LOGIN")?></td>
                        </tr><tr>        
				<td><input  class="b-text" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="bx-auth-input" /></td>
			</tr>
			<tr>
				<td><span class="starrequired">*</span><?=GetMessage("AUTH_CHECKWORD")?></td>
                                </tr><tr>   
				<td><input class="b-text"  type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input" /></td>
			</tr>
			<tr>
				<td><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?></td></tr><tr>   
				<td><input class="b-text"  type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" />
 
				</td>
			</tr>
			<tr>
				<td><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?></td>
                                </tr><tr>   
				<td><input  class="b-text" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input"  /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr> 
				<td><input type="submit" class="b-button"  name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" /></td>
			</tr>
		</tfoot>
	</table>

<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
<p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>
<p>
<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
</p>
</form>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
</div>
</section>