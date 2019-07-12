<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="order_fform"></a>
<div id="order_form_div" class="order-checkout">
<NOSCRIPT>
<div class="alert alert-error"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>
<?
if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
	if(!empty($arResult["ERROR"]))
	{
		echo '<div class="alert alert-error"><ul>';
		foreach($arResult["ERROR"] as $v)
			echo "<li>".$v."</li>";
		echo "</ul></div>";
	}
	elseif(!empty($arResult["OK_MESSAGE"]))
	{
		echo '<div class="alert alert-success">';
		foreach($arResult["OK_MESSAGE"] as $v)
			echo ShowNote($v);
		echo '</div>';
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
		BX.ajax.submitComponentFormCustom = function(obForm, container, bWait)
		{
			if (!obForm.target)
			{
				if (null == obForm.BXFormTarget)
				{
					var frame_name = 'formTarget_' + Math.random();
					obForm.BXFormTarget = document.body.appendChild(BX.create('IFRAME', {
						props: {
							name: frame_name,
							id: frame_name,
							src: 'javascript:void(0)'
						},
						style: {
							display: 'none'
						}
					}));
				}

				obForm.target = obForm.BXFormTarget.name;
			}

			if (!!bWait) {
				showAjaxLoader();
			}
			
			obForm.BXFormCallback = function(d) {
				if (!!bWait) {
					hideAjaxLoader();
					//BX.closeWait(w);
				}	

				BX(container).innerHTML = d;
				if (window.bxcompajaxframeonload){
					setTimeout("window.bxcompajaxframeonload();window.bxcompajaxframeonload=null;", 10)
				};
				BX.onCustomEvent('onAjaxSuccess', []);
			};

			BX.bind(obForm.BXFormTarget, 'load', BX.proxy(BX.ajax._submit_callback, obForm));

			return true;
		}
		function submitForm(val)
		{
			if(val != 'Y') 
				BX('confirmorder').value = 'N';
			
			var orderForm = BX('ORDER_FORM');
			
			BX.ajax.submitComponentFormCustom(orderForm, 'order_form_content', true);
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
		<div class="left-iy">
		<?=bitrix_sessid_post()?>
		<?
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			if(!empty($arResult["ERROR"]))
			{
				echo '<p>'.GetMessage('CHECK_THESE_ERRORS').'<p><div class="alert alert-error"><ul>';
				foreach($arResult["ERROR"] as $v)
					echo "<li>".$v."</li>";
				echo "</ul></div>";
			}
			?>
			<script>
				top.BX.scrollToNode(top.BX('ORDER_FORM'));
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
							<td ><input type="radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm()"></td>
							<td><label for="PERSON_TYPE_<?=$v["ID"]?>"><?=$v["NAME"] ?></label></td>
						</tr>
						<?
					}
					?>
					</tbody>
				</table>
				<p><input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>"></p>
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
					</div>						
					<div class="right-iy">	
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
		?>
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
		?>			
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
		?>
		</div>
		<?if($_POST["is_ajax_post"] != "Y")
		{
			?>
				</div>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<input type="hidden" name="profile_change" id="profile_change" value="N">
				<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">

				
					
				
			</form>
			<?if($arParams["DELIVERY_NO_AJAX"] == "N"):?>
<?
$APPLICATION->AddHeadScript("/bitrix/js/main/cphttprequest.js?".NovaGroupGetVersionModule());
$APPLICATION->AddHeadScript("/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js?".NovaGroupGetVersionModule());
/*
				<script language="JavaScript" src="/bitrix/js/main/cphttprequest.js?<?=NovaGroupGetVersionModule()?>"></script>
				<script language="JavaScript" src="/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js?<?=NovaGroupGetVersionModule()?>"></script>
*/
?>
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
 