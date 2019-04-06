<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div>
	<table class = "mini-sub-table">
	<thead>
		<tr>
			<td colspan="3"><b><?=GetMessage('subscr_type')?></b></td>
		</tr>
	</thead>
		<?if ($arResult["SHOW_POST_SUB"]):?>
		<tr>
			<td style = "text-align:right"><a href = "<?=$arResult["SUBSCRB_EDIT"]?>"><img border="0" src= <?=$templateFolder."/images/mail.gif"?>></a></td>
			<td><a href = "<?=$arResult["SUBSCRB_EDIT_POST"]?>"><b><?=GetMessage('mail')?></a></b></td>
			<td><font color="gray" size="2px"><?=declOfNum($arResult["POST_SUBSCRIBERS"],array(GetMessage("subscriber"),GetMessage("subscribers"),GetMessage("subscriberss")))?></font></td>
		</tr>
		<?endif;?>
		<?if ($arResult["SHOW_RSS_SUB"]):?>
		<tr>
			<td style = "text-align:right"><a href = "<?=$arResult["URL_FOR_RSS"]?>"><img border = "0" src = <?=$templateFolder."/images/rss.gif"?>></a></td>
			<td><a href = "<?=$arResult["URL_FOR_RSS"]?>"><b>RSS</b></a></td>
			<td><font color="gray" size="2px"><?=declOfNum($arResult["RSS_SUBSCRIBERS"], array(GetMessage("reader"), GetMessage("readerss"), GetMessage("readers")))?></font></td>
		</tr>
		<?endif;?> 
		<?if ($arResult["SHOW_SMS_SUB"]):?>
		<tr>
			<td style = "text-align:right"><a href = "<?=$arResult["SUBSCRB_EDIT"]?>"><img border = "0" src = <?=$templateFolder."/images/phone.gif"?>></a></td>
			<td><a href = "<?=$arResult["SUBSCRB_EDIT_SMS"]?>"><b>SMS</b></a></td>
			<td><font color="gray" size="2px"><?=declOfNum($arResult["SMS_SUBSCRIBERS"], array(GetMessage("reader"), GetMessage("readerss"), GetMessage("readers")))?></font></td>
		</tr>
		<?endif;?> 
	</table>
</div>
