<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="order_form_div">
<NOSCRIPT>
 <div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>
<div class="order-checkout" id="order_form">
<?
if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
	if(!empty($arResult["ERROR"]))
	{
		echo '<div class="errortext"><ul>';
		foreach($arResult["ERROR"] as $v)
			echo "<li>".$v."</li>";
		echo "</ul></div>";
	}
	elseif(!empty($arResult["OK_MESSAGE"]))
	{
		foreach($arResult["OK_MESSAGE"] as $v)
			echo ShowNote($v);
	}

	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
}
else
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			LocalRedirect($arResult["REDIRECT_URL"]);
			die();
		}
		else
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
	}
	else
	{
		$FORM_NAME = 'ORDERFORM_'.RandString(5);
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			if(!empty($arResult["ERROR"]))
			{
				echo '<div class="errortext"><ul>';
				foreach($arResult["ERROR"] as $v)
					echo "<li>".$v."</li>";
				echo "</ul></div>";
			}
		}
		?>
		
		<script>
		<!--
		function submitForm(val)
		{
			if(val != 'Y') 
				document.getElementById('confirmorder').value = 'N';
			var orderForm = document.getElementById('orderform');
			//jsAjaxUtil.InsertFormDataToNode(orderForm, 'order_form_div', false);
			orderForm.submit();
			return true;
		}
		//-->
		</script>
		<form name="orderform" id="orderform" method="POST" action="<?=$APPLICATION->GetCurPage()?>">
			<div id="order_form_id">
			&nbsp;
				<?
				if(count($arResult["PERSON_TYPE"]) > 1)
				{
					?>
					<div class="order-item">
						<div class="order-title">
							<div class="order-title-inner">
								<span><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></span>
							</div>
						</div>
						<div class="order-info">
							<table width="100%" cellpadding="0" cellspacing="6">
								<tbody>
								<?
								foreach($arResult["PERSON_TYPE"] as $v)
								{
									?>
									<tr>
										<td valign="top" width="0%"><input type="radio" id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?>></td>
										<td valign="top" width="100%"><label for="PERSON_TYPE_<?= $v["ID"] ?>"><?= $v["NAME"] ?></label></td>
									</tr>
									<script>
									   $('#PERSON_TYPE_<?= $v["ID"] ?>').live("change",function() {
											submitForm();
										});
									</script>
									<?
								}
								?>
							</tbody></table>
							<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
						</div>
					</div>
					<?
				}
				else
				{
					if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0)
					{
						?>
						<input type="hidden" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
						<input type="hidden" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
						<?
					}
					else
					{
						foreach($arResult["PERSON_TYPE"] as $v)
						{
							?>
							<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">11
							<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
							<?
						}
					}
				}

				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
				?>			
				<?
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
				?>
				<?
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
				?>			
				<?
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
				?>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<div class="order-buttons">
				<input type="button" name="submitbutton" onclick="submitForm('Y');" value="<?=GetMessage("SOA_TEMPL_BUTTON")?>" data-ajax="false">
				</div>
			</div>
		</form>
		
		<?
	}
}
?>
</div>
</div>