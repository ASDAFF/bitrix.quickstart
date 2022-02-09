<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
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
		foreach($arResult["ERROR"] as $v)
			echo ShowError($v);
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
		{
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
		}
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
			<div id="order_form_content">
			<?
		}
		else
		{
			$APPLICATION->RestartBuffer();
		}
		?>
		<?=bitrix_sessid_post()?>

                                        
                                        
                                        
 
<section class="b-detail">  
<div class="b-detail-content">
    
    		<?
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);

			?>
			<script>
				top.BX.scrollToNode(top.BX('ORDER_FORM'));
			</script>
			<?
		}

                ?>
                        
                        
<div class="b-checkout clearfix">
<div class="b-checkout__left">
 <?
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/props.php");
?>
</div>
<div class="b-checkout__right">
    <div class="b-checkout">
        
        <?
        
		if(count($arResult["PERSON_TYPE"]) > 1)
		{
			?>
			<h3 class="b-h3 m-checkout__h3">Тип плательщика</h3>
                        
                        
 
                        
                        
			 
			<?
                        foreach ($arResult["PERSON_TYPE"] as $v) {
                            ?>

                            <div class="b-checkout__label">
                                <label class="b-radio m-radio_gp_1 <? if ($v["CHECKED"] == "Y") { ?> b-checked<? } ?>"><input type="radio"  id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<? if ($v["CHECKED"] == "Y") echo " checked=\"checked\""; ?> onClick="submitForm()"><b><?= $v["NAME"] ?></b></label>
                            </div>                
                        <?
                        }
                       ?>

         
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
					<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">
					<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
					<?
				}
			}
		}

	
        ?>

    </div>
   
   <?
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/delivery.php");
?>  
    <div class="b-checkout m-checkout__last">
        
         <?
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/paysystem.php");
?>
          </div>
</div>
</div>
<div class="b-composition_of_order">


<?
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/summary.php");
?>
</div>
<div class="b-checkout-comment">
<h3 class="b-h3 m-checkout__h3">Ваши комментарии к заказу</h3>
<textarea class="b-text" rows="5" name="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
</div>
<div class="b-checkout-button"><button class="b-button"  onclick="submitForm('Y');" name="submitbutton">Оформить заказ</button></div>
</div>
</section>



                                        
                                        
		<?if($_POST["is_ajax_post"] != "Y")
		{
			?>
				</div>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<input type="hidden" name="profile_change" id="profile_change" value="N">
				<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
				 
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

