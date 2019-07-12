<table cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td>
				<label for="keyword"><?=GetMessage("SEARCH_TITLE");?></label>
			</td>
			<td>
				<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
				<?
				$INPUT_ID = trim($arParams["~INPUT_ID"]);
				if(strlen($INPUT_ID) <= 0)
					$INPUT_ID = "title-search-input";
				$INPUT_ID = CUtil::JSEscape($INPUT_ID);
				
				$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
				if(strlen($CONTAINER_ID) <= 0)
					$CONTAINER_ID = "title-search";
				$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);
				
				
				
				if($arParams["SHOW_INPUT"] !== "N"):?>
					<div id="<?echo $CONTAINER_ID?>">
					<form action="<?echo $arResult["FORM_ACTION"]?>">
						<input id="<?echo $INPUT_ID?>" type="text" name="q" value="" size="30" maxlength="150" autocomplete="off" />&nbsp; <input type="submit" name="s" class="btn btn-grey do wd-70 hg-21 pt-0 pb-2 pl-10 pr-10" style="top: -1px;" value="<?=GetMessage("CT_BST_SEARCH_BUTTON");?>" />
						
					</form>
					</div>
				
				<?endif?>
				
				<script type="text/javascript">
					var jsControl = new JCTitleSearch({
						//'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
						'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
						'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
						'INPUT_ID': '<?echo $INPUT_ID?>',
						'MIN_QUERY_LEN': 2
					});
				</script>
			</td>
		</tr>
	</tbody>
</table>