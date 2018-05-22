<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><div class="authinhead" id="inheadauthform"><?
	$frame = $this->createFrame('inheadauthform',false)->begin();
	$frame->setBrowserStorage(true);
		if($arResult["FORM_TYPE"]=="login")
		{
			?><div class="authinheadinner logged"><?
				?><i class="icon pngicons"></i><a href="<?=SITE_DIR?>auth/"><?=GetMessage('RSGOPRO_AUTH')?></a> | <a href="<?=SITE_DIR?>auth/?register=yes"><?=GetMessage('RSGOPRO_REGISTRATION')?></a><?
			?></div><?
		} else {
			?><div class="authinheadinner guest"><?
				?><a class="auth_top_panel-item" href="<?=SITE_DIR?>personal/"><?=GetMessage('RSGOPRO_PERSONAL_PAGE')?></a><i class="icon pngicons"></i><a class="auth_top_panel-item" href="?logout=yes"><?=GetMessage('RSGOPRO_EXIT')?></a><?
			?></div><?
		}
	$frame->beginStub();
		?><div class="authinheadinner logged"><?
			?><i class="icon pngicons"></i><a href="<?=SITE_DIR?>auth/"><?=GetMessage('RSGOPRO_AUTH')?></a> | <a href="<?=SITE_DIR?>auth/?register=yes"><?=GetMessage('RSGOPRO_REGISTRATION')?></a><?
		?></div><?
	$frame->end();
?></div>