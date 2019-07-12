<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="text allw">

<a name="order_fform"></a>
<div id="order_form_div" class="order-checkout">


<NOSCRIPT>
 <div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>

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
			?>
			<script>
			<!--
			window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			//-->
			</script>
			<?
			die();
		}
		else
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
	}
	else
	{
		?>
		<script>
		<!--
		function submitForm(val)
		{
			if(val != 'Y')
				BX('confirmorder').value = 'N';

			var orderForm = BX('ORDER_FORM');

			BX.ajax.submitComponentForm(orderForm, 'order_form_content', true);
			BX.submit(orderForm);

			return true;
		}
		function SetContact(profileId)
		{
			BX("profile_change").value = "Y";
			submitForm();
		}
		//-->
		</script>
		<?if($_POST["is_ajax_post"] != "Y")
		{
			?><form action="" method="POST" name="ORDER_FORM" id="ORDER_FORM">
			<div id="order_form_content" class="myorders">
			<?
		}
		else
		{
			$APPLICATION->RestartBuffer();
		}
		?>
		<?=bitrix_sessid_post()?>
		<?
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			if(!empty($arResult["ERROR"]))
			{
				echo '<div class="errortext"><ul>';
				foreach($arResult["ERROR"] as $v)
					echo "<li>".$v."</li>";
				echo "</ul></div>";
			}
			?>
			<script>
				top.BX.scrollToNode(top.BX('order_form_div'));
			</script>
			<?
		}
		?>
		<?
		if(count($arResult["PERSON_TYPE"]) > 1)
		{
			?>
				<h2><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></h2>

				<table>
					<tbody>
					<?
					foreach($arResult["PERSON_TYPE"] as $v)
					{
						?>
						<tr>
							<td style="vertical-align:top;padding:0 5px;"><input type="radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm()"></td>
							<td><label for="PERSON_TYPE_<?=$v["ID"]?>"><?=$v["NAME"] ?></label></td>
						</tr>
						<?
					}
					?>
					</tbody>
				</table>
				<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
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
		?>

		<h2><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></h2>

		<?$APPLICATION->IncludeComponent("bitrix:eshop.sale.basket.basket", "order", array(
			"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
			"COLUMNS_LIST" => array(
				0 => "NAME",
				1 => "PROPS",
				2 => "PRICE",
				3 => "QUANTITY",
				4 => "DELETE",
			),
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"PATH_TO_ORDER" => "/personal/order.php",
			"HIDE_COUPON" => "N",
			"QUANTITY_FLOAT" => "N",
			"PRICE_VAT_SHOW_VALUE" => "N",
			"SET_TITLE" => "N",
			"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);?>



		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
		?>
		<div class="clearfix"></div>

		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
		?>
		<div class="clearfix"></div>

		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
		?>
		<div class="clearfix"></div>
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
		?>
		<div class="clearfix"></div>
		<br />


		<div style="display: none;">
			<input type="button" name="submitbutton" id="submitbutton" onclick="submitForm('Y');" value="<?=GetMessage("SOA_TEMPL_BUTTON")?>" class="orange-but">
		</div>

		<div id="cart-foot" style="margin-top: 10px;">
			<div id="cf-butts">
				<a class="button orange" onclick="$('#submitbutton').trigger('click');"><span><?= GetMessage("SALE_ORDER_CONTINUE"); ?></span></a>
			</div>
		</div>


		<?if($_POST["is_ajax_post"] != "Y")
		{
			?>
				</div>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<input type="hidden" name="profile_change" id="profile_change" value="N">
				<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
				<br /><br />

			</form>
			<?if($arParams["DELIVERY_NO_AJAX"] == "N"):?>
				<script language="JavaScript" src="/bitrix/js/main/cphttprequest.js"></script>
				<script language="JavaScript" src="/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js"></script>
			<?endif;?>
			<?
		}
		else
		{
			?>
			<script>
				top.BX('confirmorder').value = 'Y';
				top.BX('profile_change').value = 'N';
			</script>
			<?
			die();
		}
	}
}
?>
</div>


</div>