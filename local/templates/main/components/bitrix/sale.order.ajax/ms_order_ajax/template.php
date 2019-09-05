<?use Bitrix\Main\Config\Option;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			$APPLICATION->RestartBuffer();
			?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}

	}
}
CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
?>
<div id="delay_none" class="block_order">
	<NOSCRIPT>
		<div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
	</NOSCRIPT>
	<?
if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
	if(!empty($arResult["ERROR"]))
	{
		foreach($arResult["ERROR"] as $v)
		{
			if($v == \Bitrix\Main\Localization\Loc::getMessage("ERROR_OLD_CONFIDENTIAL"))
			{
				$v = \Bitrix\Main\Localization\Loc::getMessage("ERROR_NEW_CONFIDENTIAL");
			}
			echo ShowError($v);
		}

	}
	elseif(!empty($arResult["OK_MESSAGE"]))
	{
		foreach($arResult["OK_MESSAGE"] as $v)

			echo ShowNote($v);
	}
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
}else
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) == 0)
		{
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
		}
	}
	else
	{
		?>
		<script type="text/javascript">
			var BXFormPosting = false;
			function submitForm(val)
			{
				if (BXFormPosting === true)
					return true;

				BXFormPosting = true;
				if(val != 'Y')
					BX('confirmorder').value = 'N';

				var orderForm = BX('ORDER_FORM');
				BX.showWait();

				BX.ajax.submit(orderForm, ajaxResult);

				return true;
			}

			function ajaxResult(res)
			{
				var orderForm = BX('ORDER_FORM');
				try
				{

					var json = JSON.parse(res);
					BX.closeWait();

					if (json.error)
					{
						BXFormPosting = false;
						return;
					}
					else if (json.redirect)
					{
						window.top.location.href = json.redirect;
					}
				}
				catch (e)
				{
					BXFormPosting = false;
					BX('order_form_content').innerHTML = res;
				}

				BX.closeWait();
				BX.onCustomEvent(orderForm, 'onAjaxSuccess');
			}

			function SetContact(profileId)
			{
				BX("profile_change").value = "Y";
				submitForm();
			}
		</script>
		<?
		if($_POST["is_ajax_post"] != "Y")
		{
		?>
		<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<div id="order_form_content">
		<?
		}else
		{
			$APPLICATION->RestartBuffer();
		}
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			foreach($arResult["ERROR"] as $v)
			{
				if($v == \Bitrix\Main\Localization\Loc::getMessage("ERROR_OLD_CONFIDENTIAL"))
				{
					$v = \Bitrix\Main\Localization\Loc::getMessage("ERROR_NEW_CONFIDENTIAL");
				}
				echo ShowError($v);
			}
			?>
			<script type="text/javascript">
				top.BX.scrollToNode(top.BX('ORDER_FORM'));
			</script>
			<?
		}
		?>
		<div class="col-sm-24 sm-padding-no">
			<h1 class="order_props_title"><?=GetMessage("MS_ORDER_INFO_PAY_DELIVERY")?></h1>
		</div>
		<div class="col-sm-24">

			<div class="wrap_section">
				<div class="row">
					<?
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php");
					?>
				</div>
			</div>

			<div class="wrap_section js_wrap_section">
				<div class="row">
					<?
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
					?>
				</div>
			</div>
			<div class="wrap_section">
				<div class="row">
				<?
				if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
				{

					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
				}
				else
				{
					global $d2p;
					$d2p = true;
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
				}
				?>
				</div>
			</div>
		</div>
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
		if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
				echo $arResult["PREPAY_ADIT_FIELDS"];


		if($_POST["is_ajax_post"] != "Y")
		{
			?>
				</div>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<input type="hidden" name="profile_change" id="profile_change" value="N">
				<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
			</form>
			<?
			if($arParams["DELIVERY_NO_AJAX"] == "N")
			{
				?>
					<div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
				<?
			}
		}
		else
		{
			?>
			<script type="text/javascript">
				top.BX('confirmorder').value = 'Y';
				top.BX('profile_change').value = 'N';
			</script>
			<?
			die();
		}
		?>
		<?
	}
}
?>
</div>
<div id="OrderPropMask" style="display:none"><?php echo Option::get('sotbit.b2bshop', 'TEL_MASK','+7(999)999-99-99');?></div>