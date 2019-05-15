<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?=$arResult['AUTO_LOADING_1'];?>

<?$UniqID = $this->randString();?>
<div class="wdr2_list" id="wdr2_list_<?=$UniqID;?>">
	<?if(!empty($arResult['ITEMS'])):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?>
			<div class="wdr2_pager wdr2_pager_top"><?=$arResult["NAV_STRING"]?></div>
		<?endif;?>
		<div class="wdr2_items">
			<?foreach($arResult['ITEMS'] as $arItem):?>
				<?$arFieldName = false;?>
				<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $arItem['EDIT_NAME']);?>
				<?$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $arItem['DELETE_NAME'], array('CONFIRM'=>GetMessage('WD_REVIEWS2_DELETE_TITLE')));?>
				<div id="wdr2_item_<?=$arItem['ID'];?>">
					<div class="wdr2_item<?if($arResult['SHOW_UNMODERATED']):?> wdr2_item_moderated_<?=$arItem['MODERATED'];?><?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						<a name="<?=$arItem['ID'];?>"></a>
						<div class="wdr2_baloon_body">
							<?if(is_array($arItem['RATINGS']) && $arItem['RATING_RESULT_REAL']>0):?>
								<div class="wdr2_item_rating">
									<?=CWD_Reviews2::ShowRating($arItem['RATING_RESULT_REAL'],array('INTERFACE_ID'=>$arResult['INTERFACE_ID'],'READ_ONLY'=>'Y'));?>
									<div class="wdr2_item_rating_detail">
										<?foreach($arItem['RATINGS'] as $arRating):?>
											<div class="wdr2_one_rating">
												<?=$arRating['NAME']?><br/>
												<?=CWD_Reviews2::ShowRating($arRating['VALUE'],array('INTERFACE_ID'=>$arResult['INTERFACE_ID'],'READ_ONLY'=>'Y'));?>
											</div>
										<?endforeach?>
									</div>
								</div>
							<?endif?>
							<?if(is_array($arItem['FIELDS'])):?>
								<div class="wdr2_fields">
									<?foreach($arItem['FIELDS'] as $arField):?>
										<?$DisplayBlock = stripos($arField['DISPLAY_VALUE'],'<div')!==false || stripos($arField['DISPLAY_VALUE'],'<br')!==false || count(explode("\n",$arField['DISPLAY_VALUE']))>1;?>
										<?if($arField['TYPE']=='TEXT' && $arField['PARAMS']['is_name']=='Y'){$arFieldName=$arField; continue;}?>
										<?if($arField['TYPE']=='TEXT' && $arField['PARAMS']['is_email']=='Y'){continue;}?>
										<?if($arField['DISPLAY_VALUE']==''){continue;}?>
										<div class="wdr2_field<?if($DisplayBlock):?> wdr2_field_block<?endif?>"><div class="wdr2_field_title"><?=$arField['NAME'];?>:</div> <div class="wdr2_field_value"><?=$arField['DISPLAY_VALUE'];?></div></div>
									<?endforeach?>
								</div>
							<?endif?>
						</div>
						<div class="wdr2_meta">
							<?if($arParams["SHOW_AVATARS"] && $arItem['USER_ID']>0 && is_array($arResult['USERS'][$arItem['USER_ID']]['PHOTO'])):?>
								<div class="wdr2_photo">
									<?$arResizedPhoto = CFile::ResizeImageGet($arResult['USERS'][$arItem['USER_ID']]['PHOTO'], array('width'=>24, 'height'=>24), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
									<table><tr><td><img src="<?=$arResizedPhoto['src']?>" width="<?=$arResizedPhoto['width']?>" height="<?=$arResizedPhoto['height']?>" alt="" /></td></tr></table>
								</div>
							<?endif?>
							<?if(is_array($arFieldName)):?>
								<div class="wdr2_name"><?=$arFieldName['DISPLAY_VALUE'];?></div>
							<?endif?>
							<div class="wdr2_date"><?=$arItem['DISPLAY_DATE'];?></div>
							<?if($arParams['ALLOW_VOTE']):?>
								<!--noindex-->
									<div class="wdr2_vote">
										<span class="wdr2_vote_title"><?=GetMessage('WD_REVIEWS2_IS_REVIEW_HELPFUL');?></span>
										<a href="#" class="wdr2_vote_send wdr2_vote_send_y" rel="nofollow" onclick="return wdr2_send_vote_<?=$UniqID;?>(<?=$arParams['INTERFACE_ID'];?>, '<?=$arParams['TARGET'];?>', <?=$arItem['ID']?>, '+1');"><?=GetMessage('WD_REVIEWS2_Y');?></a>
										<span class="wdr2_vote_count wdr2_vote_count_y"><?=$arItem['VOTES_Y'];?></span>
										<a href="#" class="wdr2_vote_send wdr2_vote_send_n" rel="nofollow" onclick="return wdr2_send_vote_<?=$UniqID;?>(<?=$arParams['INTERFACE_ID'];?>, '<?=$arParams['TARGET'];?>', <?=$arItem['ID']?>, '-1');"><?=GetMessage('WD_REVIEWS2_N');?></a>
										<span class="wdr2_vote_count wdr2_vote_count_n"><?=$arItem['VOTES_N'];?></span>
									</div>
								<!--/noindex-->
							<?endif?>
							<div class="wdr2_clear"></div>
						</div>
						<?if($arParams["SHOW_ANSWERS"] && strlen($arItem['~ANSWER'])):?>
							<div class="wdr2_baloon_body wdr2_baloon_answer">
								<?=$arItem['~ANSWER'];?>
							</div>
							<?if($arItem['ANSWER_USER_ID']>0):?>
								<div class="wdr2_meta wdr2_meta_answer">
									<?if($arParams["SHOW_ANSWER_AVATAR"] && is_array($arResult['USERS'][$arItem['ANSWER_USER_ID']]['PHOTO'])):?>
										<div class="wdr2_photo">
											<?$arResizedPhoto = CFile::ResizeImageGet($arResult['USERS'][$arItem['ANSWER_USER_ID']]['PHOTO'], array('width'=>24, 'height'=>24), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
											<table><tr><td><img src="<?=$arResizedPhoto['src']?>" width="<?=$arResizedPhoto['width']?>" height="<?=$arResizedPhoto['height']?>" alt="" /></td></tr></table>
										</div>
									<?endif?>
									<div class="wdr2_name"><?=$arResult['USERS'][$arItem['ANSWER_USER_ID']]['ANSWER_DISPLAY_NAME'];?></div>
									<?if($arParams["SHOW_ANSWER_DATE"]):?>
										<div class="wdr2_date"><?=$arFieldName['DISPLAY_DATE_ANSWER'];?></div>
									<?endif?>
									<div class="wdr2_clear"></div>
								</div>
							<?endif?>
						<?endif?>
					</div>
				</div>
			<?endforeach?>
		</div>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<div class="wdr2_pager wdr2_pager_bottom"><?=$arResult["NAV_STRING"]?></div>
		<?endif;?>
	<?else:?>
		<p><?=GetMessage('WD_REVIEWS2_NO_REVIEWS');?></p>
	<?endif?>
</div>
<script>
//<![CDATA[
var wdr2_errors = {
	'AUTH_ERROR': '<?=GetMessage('WDR2_ERROR_AUTH_ERROR');?>',
	'YOU_CANNOT_VOTE': '<?=GetMessage('WDR2_ERROR_YOU_CANNOT_VOTE');?>',
	'VOTE_ERROR': '<?=GetMessage('WDR2_ERROR_VOTE_ERROR');?>'
};
function wdr2_send_vote_<?=$UniqID;?>(InterfaceID, Target, ReviewID, Amount) {
	$.ajax({
		url: '/bitrix/tools/wd_reviews2.php',
		type: 'GET',
		datatype: 'json',
		data: 'action=vote&interface='+InterfaceID+'&target='+Target+'&review='+ReviewID+'&amount='+Amount,
		success: function(JSON) {
			if (JSON.success) {
				$('#wdr2_item_'+JSON.review+' .wdr2_vote_count_'+JSON.flag).text(JSON.value);
			} else {
				alert(wdr2_errors[JSON.error_message]);
			}
		}
	});
	return false;
}
//]]>
</script>

<?=$arResult['AUTO_LOADING_2'];?>