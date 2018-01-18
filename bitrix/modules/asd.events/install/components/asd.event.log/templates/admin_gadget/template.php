<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	if (typeof(ToggleDesc) != 'function')
	{
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
	}
</script>

<?foreach ($arResult['ITEMS'] as $arItem):?>
	<?= $arItem['TIMESTAMP_X']?>: <b><?= $arItem['AUDIT_TYPE_ID']?></b>, <?= GetMessage('ASD_TPL_ENT')?>:
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
		<a style="font-size: 10px;" href="<?= str_replace('#ID#', $arItem['USER_ID'], $arParams['USER_LINK'])?>">[<?= $arResult['USERS'][$arItem['USER_ID']]['ID']?>]</a>
		<?else:?>
		[<?= $arResult['USERS'][$arItem['USER_ID']]['ID']?>]
		<?endif;?>
		(<?= $arResult['USERS'][$arItem['USER_ID']]['LOGIN']?>)
		<?= $arResult['USERS'][$arItem['USER_ID']]['NAME']?> <?= $arResult['USERS'][$arItem['USER_ID']]['LAST_NAME']?>
	</small><br/>
	<?endif;?>


	<a href="#" id="more<?= $arItem['ID']?>" style="font-size: 10px;" onclick="ToggleDesc(<?= $arItem['ID']?>); return false;"><?= GetMessage('ASD_TPL_LINK_MORE')?></a>
	<br/>
	<div style="display: none; width: 500px; overflow: auto;" id="desc<?= $arItem['ID']?>">
		<?= $arItem['DESCRIPTION']?>
	</div>
	<br/>
<?endforeach;?>
