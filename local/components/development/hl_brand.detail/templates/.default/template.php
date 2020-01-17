<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (!empty($arResult['ERROR']))
{
	ShowError($arResult['ERROR']);
	return false;
}

global $USER_FIELD_MANAGER;

$listUrl = str_replace('#BLOCK_ID#', intval($arParams['BLOCK_ID']),	$arParams['LIST_URL']);
?>
<a href="<?=htmlspecialcharsbx($listUrl)?>"><?=GetMessage('HLBLOCK_ROW_VIEW_BACK_TO_LIST')?></a><br><br>

<div class="reports-result-list-wrap">

			<? foreach($arResult['row']['DISPLAY'] as $val):?>
                <?if($val['VALUE'] != '' && $val['VALUE'] != NULL){?>
				<div>
                    <?if($val['TYPE']=='file'){?>
                        <?if($val['MULTIPLE']==Y){
                            foreach ($val['VALUE'] as $vals){?>
                             <div class="img_div"><img src="<?=CFile::GetPath($vals)?>"></div>
                            <?}
                        }else{?>
                            <div class="img_div"><img src="<?=CFile::GetPath($val['VALUE'])?>"></div>
                            <?}?>
                    <?}else{?>
                        <?if($val['MULTIPLE']==Y){
                            foreach ($val['VALUE'] as $vals){?>
                                <div class="reports-last-column"><?=$vals?></div>
                            <?}
                        }else{?>
					            <div class="reports-last-column"><?=$val['VALUE']?></div>
                            <?}?>
                    <?}?>
				</div>
                <?}?>
			<? endforeach; ?>
</div>