<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php
if ($USER->IsAuthorized() || $arParams['ALLOW_AUTO_REGISTER'] == 'Y')
{
	if ($arResult['USER_VALS']['CONFIRM_ORDER'] == 'Y' || $arResult['NEED_REDIRECT'] == 'Y')
	{
		if (strlen($arResult['REDIRECT_URL']) > 0)
		{
			$APPLICATION->RestartBuffer();
			LocalRedirect($arResult['REDIRECT_URL']);
		}
	}
}

$tpl = $_SERVER['DOCUMENT_ROOT'] . $templateFolder;

// @todo: move to template settings
$arParams['ALLOW_NEW_PROFILE'] = 'Y';

CJSCore::Init(array('ajax'));
?>

<div id="order_form_div" class="order-checkout">
	<noscript>
		<div class="errortext"><?php echo GetMessage('SOA_NO_JS') ?></div>
	</noscript>

	<?php
	if (!$USER->IsAuthorized() && $arParams['ALLOW_AUTO_REGISTER'] == 'N')
	{
		// show messages (error/success)
		if (!empty($arResult['ERROR']))
		{
			foreach ($arResult["ERROR"] as $v)
			{
				echo ShowError($v);
			}
		}
		else if (!empty($arResult['OK_MESSAGE']))
		{
			foreach ($arResult['OK_MESSAGE'] as $v)
			{
				echo ShowNote($v);
			}
		}
		include("$tpl/auth.php");
		return;
	}
	?>

	<?php
	if ($arResult['USER_VALS']['CONFIRM_ORDER'] == 'Y' || $arResult['NEED_REDIRECT'] == 'Y')
	{
		if (strlen($arResult['REDIRECT_URL']) == 0)
		{
			include("$tpl/confirm.php");
			return;
		}
	}
	?>
	<?php if ($_POST['is_ajax_post'] != 'Y'): ?>
		<form action="<?php echo $APPLICATION->GetCurPage(); ?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
			<?php echo bitrix_sessid_post() ?>
			<div id="order_form_content">
			<?php else: ?>
				<? $APPLICATION->RestartBuffer(); ?>
			<?php endif; ?>
			<?php
			if (!empty($arResult['ERROR']) && $arResult['USER_VALS']['FINAL_STEP'] == 'Y')
			{
				foreach ($arResult['ERROR'] as $v)
				{
					echo ShowError($v);
				}
			}

			include("$tpl/person_type.php");
			include("$tpl/props.php");
			if ($arParams['DELIVERY_TO_PAYSYSTEM'] == 'p2d')
			{
				include("$tpl/paysystem.php");
				include("$tpl/delivery.php");
			}
			else
			{
				include("$tpl/delivery.php");
				include("$tpl/paysystem.php");
			}

			include("$tpl/summary.php");
			if (strlen($arResult['PREPAY_ADIT_FIELDS']) > 0)
			{
				echo $arResult['PREPAY_ADIT_FIELDS'];
			}
			?>

			<?php if ($_POST['is_ajax_post'] != 'Y'): ?>
			</div>
			<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
			<input type="hidden" name="profile_change" id="profile_change" value="N">
			<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
			<input type="hidden" name="json" value="Y">
			<input type="submit" onclick="submitForm('Y');
					return false;" value="<?php echo GetMessage('SOA_TEMPL_BUTTON') ?>">
		</form>
		<?php if ($arParams['DELIVERY_NO_AJAX'] == 'N'): ?>
			<div style="display:none;"><? $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
		<?php endif; ?>
<?php else: ?>
		<script type="text/javascript">
			top.BX('confirmorder').value = 'Y';
			top.BX('profile_change').value = 'N';
		</script>
<?php endif; ?>
</div>

<script type="text/javascript">
	function submitForm(val)
	{
		if (val != 'Y') {
			BX('confirmorder').value = 'N';
		}

		var orderForm = BX('ORDER_FORM');
		BX.showWait();
		BX.ajax.submit(orderForm, ajaxResult);

		return true;
	}

	function ajaxResult(res)
	{
		try
		{
			var json = JSON.parse(res);
			BX.closeWait();

			if (json.error)
			{
				return;
			}
			else if (json.redirect)
			{
				window.top.location.href = json.redirect;
			}
		}
		catch (e)
		{
			BX('order_form_content').innerHTML = res;
		}
		window.form_element_styling();
		BX.closeWait();
	}

	function SetContact(profileId)
	{
		BX("profile_change").value = "Y";
		submitForm();
	}
</script>