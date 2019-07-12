<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$networkUrl = CBitrix24NetOAuthInterface::NET_URL.CBitrix24NetOAuthInterface::PASSPORT_URL."?user_lang=".LANGUAGE_ID;
?>
<div class="network-note">
	<?=GetMessage('SAL_N_NOTE')?><br><br>

	<div class="network-link">
		<a class="webform-small-button" href="<?=$networkUrl?>" target="_blank"><span class="webform-small-button-left"></span><span class="webform-small-button-text"><?=GetMessage('SAL_N_PASSPORT')?></span><span class="webform-small-button-right"></span></a>
	</div>
</div>

