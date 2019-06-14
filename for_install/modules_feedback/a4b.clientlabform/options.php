<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = "a4b.clientlabform";

Loader::includeModule($module_id);


	$OPTIONS = array(
		"iblock_id"=>"",
		"RE_SITE_KEY"=>"",
		"RE_SEC_KEY"=>""
	);

	$sids = array();
	$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
	while ($res = $arQuery->Fetch())
	{
		array_push($sids, $res["ID"]);
	}

	//var_dump($sids);

	foreach ($OPTIONS as $o => $opt) {

		$option = COption::GetOptionString("a4b.clientlabform", $o);
		$OPTIONS[$o] = $option;
		if ($_REQUEST[$o]!='') {


			COption::SetOptionString(
				"a4b.clientlabform",
				$o,
				$_REQUEST[$o],
				false,
				false
			);

			$option = $_REQUEST[$o];
			$OPTIONS[$o] = $option;
		}
	}


$tabControl = new CAdminTabControl(
	"tabControl",
	null
);


$tabControl->Begin();
?>

<form class="clform-options" action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">
	<div class="row">
		<h2><?php echo GetMessage('CLIENTLAB_FORM_OPTIONS_TITLE'); ?></h2>
			<label>
				<?php echo GetMessage('CLIENTLAB_FORM_IBLOCK_ID_TITLE'); ?>
				<input name="iblock_id" type="number" value="<?php echo htmlspecialcharsbx($OPTIONS['iblock_id']); ?>" />
			</label>
	</div><!-- /.row -->
	<div class="row">
		<h2><?php echo GetMessage('CLIENTLAB_FORM_RE_TITLE'); ?></h2>
			<a target="_blank" href="https://www.google.com/recaptcha/admin/create#list"><?php echo GetMessage('CLIENTLAB_FORM_RE_REG_LINK'); ?></a>
			<label>
				<?php echo GetMessage('CLIENTLAB_FORM_RE_SITE_KEY'); ?>
				<input name="RE_SITE_KEY" type="text" value="<?php echo htmlspecialcharsbx($OPTIONS['RE_SITE_KEY']); ?>" />
			</label>
			<label>
				<?php echo GetMessage('CLIENTLAB_FORM_RE_SEC_KEY'); ?>
				<input name="RE_SEC_KEY" type="text" value="<?php echo htmlspecialcharsbx($OPTIONS['RE_SEC_KEY']); ?>" />
			</label>
	</div><!-- /.row -->
	<?
	$tabControl->Buttons();
	?>
	<input type="submit" name="apply" value="<? echo(Loc::GetMessage("CLIENTLAB_FORM_BTN_APPLY")); ?>" class="adm-btn-save" />
</form>
<br />


<?php $tabControl->End(); ?>

<style>
	.help-block {
		margin: 30px 0;
		padding: 15px;
	}
</style>
<div class="help-block adm-detail-content-wrap">
	<?php echo GetMessage('CLIENTLAB_FORM_EVENTS_DESCRIPTION'); ?>
</div><!-- /.help-block -->
