<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	function ToggleDesc(id)
	{
		if (document.getElementById('desc'+id).style.display == 'none')
		{
			document.getElementById('desc'+id).style.display = 'block';
			document.getElementById('more'+id).innerHTML = '<?= CUtil::jsEscape(GetMessage('ASD_TPL_LINK_LESS'))?>';
		}
		else
		{
			document.getElementById('desc'+id).style.display = 'none';
			document.getElementById('more'+id).innerHTML = '<?= CUtil::jsEscape(GetMessage('ASD_TPL_LINK_MORE'))?>';
		}
	}
</script>

<p><?= GetMessage('ASD_TPL_LT')?> <b><?= $arResult['LT']?> <?= GetMessage('ASD_TPL_LT_DAYS')?></b></p>

<form method="get" action="<?= POST_FORM_ACTION_URI?>">
	<table class="data-table">
		<tr valign="top">
			<td><?= GetMessage('ASD_TPL_F_TYPE')?>:</td>
			<td>
				<select name="type[]" multiple="multiple" size="3">
					<option value=""><?= GetMessage('ASD_TPL_F_TYPE_ALL')?></option>
					<?foreach($arResult['TYPES'] as $type => $name): if (!in_array($type, $arResult['AVAILABLE_TYPES'])) continue;?>
					<option value="<?= $type?>"<?if (in_array($type, $arResult['REQUEST_TYPE'])){?> selected="selected"<?}?>><?= $name?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<?if ($arParams['FILTER_USER'] == 'Y'):?>
		<tr valign="top">
			<td><?= GetMessage('ASD_TPL_F_USER')?>:</td>
			<td>
				<select name="user">
					<option value=""></option>
					<?foreach($arResult['FILTER_USERS'] as $arUser):?>
					<option value="<?= $arUser['ID']?>"<?if ($arUser['ID'] == $arResult['REQUEST_USER']){?> selected="selected"<?}?>>
						[<?= $arUser['ID']?>] (<?= $arUser['LOGIN']?>) <?= $arUser['NAME']?> <?= $arUser['LAST_NAME']?>
					</option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<?endif;?>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="<?= GetMessage('ASD_TPL_F_SUBMIT')?>" />
				<?if (!empty($arResult['REQUEST_TYPE']) || $arResult['REQUEST_USER']>0):?>
				<input type="submit" name="reset" value="<?= GetMessage('ASD_TPL_F_RESET')?>" />
				<?endif;?>
			</td>
		</tr>
	</table>
</form>
<br/>

<table class="data-table" width="100%">
	<tr>
		<th width="120"><?= GetMessage('ASD_TPL_TIME')?></th>
		<th><?= GetMessage('ASD_TPL_EVENT')?></th>
	</tr>
<?foreach ($arResult['ITEMS'] as $arItem):?>
	<tr valign="top">
		<td style="white-space: nowrap;"><?= $arItem['TIMESTAMP_X']?></td>
		<td>
			<b><?= $arItem['AUDIT_TYPE_ID']?></b>, <?= GetMessage('ASD_TPL_ENT')?>:
			<?
			if ($arItem['ENTITY'] == 'USER')
			{
				?>
				<?if ($arParams['USER_LINK'] != ''):?>
				<a href="<?= str_replace('#ID#', $arItem['ITEM_ID'], $arParams['USER_LINK'])?>">[<?= $arResult['USERS'][$arItem['ITEM_ID']]['ID']?>]</a>
				<?else:?>
				[<?= $arResult['USERS'][$arItem['USER_ID']]['ID']?>]
				<?endif;?>
				(<?= $arResult['USERS'][$arItem['ITEM_ID']]['LOGIN']?>)
				<?= $arResult['USERS'][$arItem['ITEM_ID']]['NAME']?> <?= $arResult['USERS'][$arItem['ITEM_ID']]['LAST_NAME']?>
				<?
			}
			elseif ($arItem['ENTITY'] == 'USER_GROUP')
			{
				echo $arResult['GROUPS'][$arItem['ITEM_ID']]['NAME'];
			}
			elseif ($arItem['ENTITY'] == 'TASK')
			{
				echo $arResult['TASKS'][$arItem['ITEM_ID']]['NAME'];
			}
			elseif ($arItem['ENTITY']=='F_MESS' && $arResult['F_MESS'][$arItem['ITEM_ID']]['PATH']!='')
			{
				?><a href="<?= $arResult['F_MESS'][$arItem['ITEM_ID']]['PATH']?>"><?= $arItem['ITEM_ID']?></a><?
			}
			elseif ($arItem['ENTITY']=='F_TOPIC' && $arResult['F_TOPICS'][$arItem['ITEM_ID']]['LAST_MESSAGE_ID']>0)
			{
				$idMess = $arResult['F_TOPICS'][$arItem['ITEM_ID']]['LAST_MESSAGE_ID'];
				?><a href="<?= $arResult['F_MESS'][$idMess]['PATH']?>"><?= $arItem['ITEM_ID']?></a><?
			}
			elseif ($arItem['ENTITY']=='SECTION' && isset($arResult['SECTIONS'][$arItem['ITEM_ID']]))
			{
				?><a href="<?= $arResult['SECTIONS'][$arItem['ITEM_ID']]['URL']?>"><?= $arResult['SECTIONS'][$arItem['ITEM_ID']]['NAME']?></a><?
			}
			elseif ($arItem['ENTITY']=='ELEMENT' && isset($arResult['ELEMENTS'][$arItem['ITEM_ID']]))
			{
				?><a href="<?= $arResult['ELEMENTS'][$arItem['ITEM_ID']]['URL']?>"><?= $arResult['ELEMENTS'][$arItem['ITEM_ID']]['NAME']?></a><?
			}
			else
			{
				echo $arItem['ITEM_ID'];
			}
			?>
			<br/>
			<?if ($arParams['NOT_SHOW_USER']!='Y' && $arItem['USER_ID']>0 && isset($arResult['USERS'][$arItem['USER_ID']])):?>
			<small><?= GetMessage('ASD_TPL_INFO')?>:
				<?if ($arParams['USER_LINK'] != ''):?>
				<a href="<?= str_replace('#ID#', $arItem['USER_ID'], $arParams['USER_LINK'])?>">[<?= $arResult['USERS'][$arItem['USER_ID']]['ID']?>]</a>
				<?else:?>
				[<?= $arResult['USERS'][$arItem['USER_ID']]['ID']?>]
				<?endif;?>
				(<?= $arResult['USERS'][$arItem['USER_ID']]['LOGIN']?>)
				<?= $arResult['USERS'][$arItem['USER_ID']]['NAME']?> <?= $arResult['USERS'][$arItem['USER_ID']]['LAST_NAME']?>
			</small><br/>
			<?endif;?>


			<small><a href="#" id="more<?= $arItem['ID']?>" onclick="ToggleDesc(<?= $arItem['ID']?>); return false;"><?= GetMessage('ASD_TPL_LINK_MORE')?></a></small>
			<br/>
			<div style="display: none;" id="desc<?= $arItem['ID']?>">
				<?= $arItem['DESCRIPTION']?>
			</div>
		</td>
	</tr>
<?endforeach;?>
</table>
<br/>
<?= $arResult['NAV_STRING']?>
