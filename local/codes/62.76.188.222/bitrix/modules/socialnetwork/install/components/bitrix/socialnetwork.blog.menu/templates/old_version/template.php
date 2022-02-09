<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
			<?
			if (strlen($arResult["urlToNewPost"])>0)
			{
				?>
				<td><a href="<?=$arResult["urlToNewPost"]?>" title="<?=GetMessage("BLOG_MENU_ADD_MESSAGE_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_ADD_MESSAGE")?></a></td>
				<?
			}
			
			if(strlen($arResult["urlToDraft"])>0)
			{
				?>
				<td><div class="blogtoolseparator"></div></td>
				<td><a href="<?=$arResult["urlToDraft"]?>" title="<?=GetMessage("BLOG_MENU_DRAFT_MESSAGES_TITLE")?>" class="blogtoolbutton"><?=GetMessage("BLOG_MENU_DRAFT_MESSAGES")?></a></td>
				<?
			}
			?>
		</tr>
	</table>
<br />