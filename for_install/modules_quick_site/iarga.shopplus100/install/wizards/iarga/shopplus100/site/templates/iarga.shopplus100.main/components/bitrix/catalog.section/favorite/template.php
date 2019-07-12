<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $templateFolder, $USER;
$user = $USER->GetByID($USER->GetID())->GetNext();?>

<div class="send-mail-favorites">
	<h1><?=GetMessage("FAV_GOODS")?></h1>
	<form action='<?=SITE_DIR?>inc/ajax/sendfav.php' class='uniform'>
		<img src="<?=$templateFolder?>/images/icon-arrow.gif" alt="&rarr;" class="icon-arrow">
		<input type="text" data-alt="<?=GetMessage("YOUR_MAIL")?>" value="<?=$user['EMAIL']?>" class="inp-text repl" name='mail'>
		<a href="#" class="bt_blue send-mail submit"><?=GetMessage("SEND_FAV")?> <img src="<?=$templateFolder?>/images/icon-mail.gif" alt=""></a>
		<p class='error'></p>
	</form>
</div><!--.send-mail-favorites-end-->

<?include($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/iarga.shopplus100.main/components/bitrix/catalog/catalog/bitrix/catalog.section/list/template.php')?>