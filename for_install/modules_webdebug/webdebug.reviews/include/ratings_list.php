<?
IncludeModuleLangFile(__FILE__);

if (IntVal($WD_Reviews2_InterfaceID)<=0) {
	return;
}

if (webdebug_reviews_demo_expired()) {
	webdebug_pageprops_show_demo();
	die();
}


$bRatingExists = false;
$bRatingParticipateExists = false;

// Ratings (from DB)
$arRatings = array();
$resRating = CWD_Reviews2_Ratings::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
while ($arRating = $resRating->GetNext()) {
	$arRatings[] = $arRating;
	$bRatingExists = true;
	if ($arRating['PARTICIPATES']=='Y') {
		$bRatingParticipateExists = true;
	}
}

$strWarning = false;
if (!$bRatingExists) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_RATINGS');
} elseif (!$bRatingParticipateExists) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_RATINGS_PARTICIPATES');
}
?>

<div class="adm-list-table-wrap" id="wd_reviews2_interface_ratings" style="position:relative">
	<div class="adm-list-table-top">
		<input type="button" class="adm-btn adm-btn-add" value="<?=GetMessage('WD_REVIEWS2_BTN_ADD');?>" onclick="WD_Reviews2_Rating_ShowPopup(true, <?=$WD_Reviews2_InterfaceID;?>);" />
		<input type="button" class="adm-btn" value="<?=GetMessage('WD_REVIEWS2_BTN_REFRESH');?>" id="wd_reviews2_ratings_list_refresh" onclick="WD_Reviews2_Ratings_Refresh(<?=$WD_Reviews2_InterfaceID;?>);" />
	</div>
	<table class="adm-list-table">
		<tbody>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner">ID</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_COL_NAME');?></div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_COL_PARTICIPATES');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_COL_SORT');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"></div></td>
			</tr>
			<?foreach($arRatings as $arRating):?>
				<tr class="adm-list-table-row">
					<td class="adm-list-table-cell"><?=$arRating['ID'];?></td>
					<td class="adm-list-table-cell"><b><?=$arRating['NAME'];?></b></td>
					<td class="adm-list-table-cell align-right"><?=($arRating['PARTICIPATES']=='Y'?GetMessage('WD_REVIEWS2_Y'):GetMessage('WD_REVIEWS2_N'));?></td>
					<td class="adm-list-table-cell align-right"><?=$arRating['SORT'];?></td>
					<td class="adm-list-table-cell align-right" style="padding:4px 2px;">
						<input type="button" value="<?=GetMessage('WD_REVIEWS2_RATINGS_EDIT');?>" onclick="WD_Reviews2_Ratings_Edit(this, <?=$WD_Reviews2_InterfaceID;?>, <?=$arRating['ID'];?>);" class="btn-edit" />
					</td>
					<td class="adm-list-table-cell align-right" style="padding:4px 2px;">
						<input type="button" value="<?=GetMessage('WD_REVIEWS2_RATINGS_DELETE');?>" onclick="WD_Reviews2_Ratings_Delete(this, <?=$WD_Reviews2_InterfaceID;?>, <?=$arRating['ID'];?>);" class="btn-delete" />
					</td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
	<div class="adm-list-table-footer">
		<span class="adm-table-counter adm-table-counter-visible"><?if($strWarning!==false):?><span class="wd_reviews2_warning_16" title="<?=$strWarning;?>"></span><?endif?><?=GetMessage('WD_REVIEWS2_RATINGS_COUNT');?>: <span><?=count($arRatings);?></span></span>
	</div>
</div>
<script>
var WD_Reviews2_Popup_Rating_Rating;
function WD_Reviews2_Rating_ShowPopup(CreateNew, InterfaceID, RatingID) {
	BX.showWait();
	WD_Reviews2_Popup_Rating = new BX.CDialog({
		title: (CreateNew?'<?=GetMessage('WD_REVIEWS2_RATINGS_POPUP_ADD');?>':'<?=GetMessage('WD_REVIEWS2_RATINGS_POPUP_EDIT');?>'),
		content: '',
		width: 900,
		height: 400,
		resizable: true,
		draggable: true
	});
	BX.addCustomEvent(WD_Reviews2_Popup_Rating, 'onWindowClose', function(){
		$(this.DIV).remove();
	});
	WD_Reviews2_Popup_Rating.SetButtons([{
			title: '<?=GetMessage('WD_REVIEWS2_RATINGS_POPUP_SAVE');?>',
			id: 'wd_reviews2_save_form_button',
			name: 'action_send',
			className: 'adm-btn-save',
			action: function(){
				BX.showWait();
				BX.ajax.submit(BX('wd_reviews2_rating_edit_form'));
			}
		}, {
			title: '<?=GetMessage('WD_REVIEWS2_RATINGS_POPUP_CANCEL');?>',
			id: 'wd_reviews2_save_form_cancel',
			name: 'cancel',
			action: function(){
				WD_Reviews2_Popup_Rating.Close();
			}
		}
	]);
	WD_Reviews2_Popup_Rating.SetContent('');
	if (InterfaceID==undefined) {InterfaceID='';}
	if (RatingID==undefined) {RatingID='';}
	jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_ratings.php?&action=edit&new='+(CreateNew?'Y':'N')+'&interface='+InterfaceID+'&rating='+RatingID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
		WD_Reviews2_Popup_Rating.SetContent(HTML);
		WD_Reviews2_Popup_Rating.Show();
		BX.closeWait();
	});
}
function WD_Reviews2_OnRatingSave(HTML) {
	if (HTML.indexOf('#'+'WD_REVIEWS2_RATING_SAVE_ERROR'+'#')>-1) {
		alert('<?=GetMessage('WD_REVIEWS2_ERROR_SAVE');?>');
	} else if (HTML.indexOf('#'+'WD_REVIEWS2_RATING_SAVE_SUCCESS'+'#')>-1){
		WD_Reviews2_Popup_Rating.Close();
		$('#wd_reviews2_interface_ratings').replaceWith(HTML);
	}
	BX.closeWait();
}
function WD_Reviews2_Ratings_Edit(Sender, InterfaceID, RatingID) {
	WD_Reviews2_Rating_ShowPopup(Sender==false, InterfaceID, RatingID);
}
function WD_Reviews2_Ratings_Delete(Sender, InterfaceID, RatingID) {
	BX.showWait();
	if (confirm('<?=GetMessage('WD_REVIEWS2_RATINGS_DELETE_CONFIRM');?>')) {
		jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_ratings.php?&action=delete&interface='+InterfaceID+'&rating='+RatingID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
			$('#wd_reviews2_interface_ratings').replaceWith(HTML);
			BX.closeWait();
		});
	} else {
		BX.closeWait();
	}
}
function WD_Reviews2_Ratings_Refresh(InterfaceID) {
	BX.showWait();
	jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_ratings.php?&action=list&interface='+InterfaceID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
		$('#wd_reviews2_interface_ratings').replaceWith(HTML);
		BX.closeWait();
	});
}
if (window.jQuery) {
	$('#wd_reviews2_interface_ratings .adm-list-table-row').dblclick(function(){
		$(this).find('.btn-edit').click();
	});
}

</script>