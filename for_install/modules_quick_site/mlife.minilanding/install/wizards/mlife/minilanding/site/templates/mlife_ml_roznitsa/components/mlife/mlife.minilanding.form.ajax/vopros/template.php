<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
if($arResult['SHOW_FORM']==1 and $arResult['CHECK_SPAM']!=1 and !$arResult['SENDFORM']) {

	$APPLICATION->RestartBuffer();
	?>
	
	<div class="bg1"><div class="bg2">
	<div class="closewrap">
		<span class="close"></span>
	</div>
		<div class="wrapform">
		<div class="title"><?=$arParams['F_NAME']?></div>
        <div class="desc"><?=$arParams['F_DESC']?></div>
	<div class="wrapfield">
	<?
	if(is_array($arParams['FIELD_SHOW']) && count($arParams['FIELD_SHOW'])>0){
		foreach($arParams['FIELD_SHOW'] as $value){
		?>
		<?if($value=='mess') {?>
		</div><div class="wrapmess">
		<?}?>
		<div class="<?=$value?>"><?
			if($value=='mess') {
			?><textarea class="mlftarea" id="f<?=$arParams['FORMID']?><?=$value?>" name="<?=$value?>" placeholder="<?=GetMessage("MLIFE_CAT_BK_FIELD_REQ_".strtoupper($value))?><?if($arResult['SEND_REQ'][$value]==1) { echo '*';}?>"><?=$arResult['SEND'][$value]?></textarea><?
			}else{
			?><input class="mlfinp" id="f<?=$arParams['FORMID']?><?=$value?>" type="text" name="<?=$value?>" value="<?=$arResult['SEND'][$value]?>" placeholder="<?=GetMessage("MLIFE_CAT_BK_FIELD_REQ_".strtoupper($value))?><?if($arResult['SEND_REQ'][$value]==1) {echo '*';}?>"/><?
			}
			if(isset($arResult['ERROR'][$value]) && strlen($arResult['ERROR'][$value])>0) {
				?>
				<div class="fielderror"><?=$arResult['ERROR'][$value]?></div>
				<?
			}
		?></div><?
		}
		?>
		</div>
		<?
	}
	if(is_array($arParams['FIELD_SHOW_HIDDEN']) && count($arParams['FIELD_SHOW_HIDDEN'])>0){
		foreach($arParams['FIELD_SHOW_HIDDEN'] as $value){
			?>
			<input type="hidden" id="f<?=$arParams['FORMID']?><?=$value?>" name="<?=$value?>" value="<?=$arResult['SEND'][$value]?>"/>
			<?
		}
	}
	echo bitrix_sessid_post('bistrclick_sessid');
	?>
		<div class="button">
          <a href="#" id="submitformf<?=$arParams['FORMID']?>"><?=GetMessage("MLIFE_CAT_BK_SENDMESS")?></a>
        </div>
		</div>
	</div></div>
	<?
	die();
}
elseif($arResult['SHOW_FORM']==1 and $arResult['CHECK_SPAM']!=1 and $arResult['SENDFORM']) {
	?>
	<?$APPLICATION->RestartBuffer();?>
	<div class="bg1"><div class="bg2">
	<div class="closewrap">
		<span class="close"></span>
	</div>
		<div class="wrapform">
		<div class="title"><?=$arParams['F_NAME']?></div>
        <div class="desc"><?=$arParams['MESS_OK']?></div>
		<br/><br/>
		</div>
	</div></div>
	<?
	die();
}
else if($arResult['SHOW_FORM']==0 && $arResult['CHECK_SPAM']!=1){
	$key = md5(strtotime(date("M-d-Y H:00:00")).$arParams['KEY'].$arResult['REF_START']);
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("<?=$arParams["CLASS_LINK"]?>").each(function() {
				$(this).attr('href','<?=$arResult['REF_START']?>?formclick=1&referer=<?=$key?>&mlife_formid=<?=$arParams['FORMID']?>');
			});
		});
		$(document).ready(function(){
			$(document).on('click','<?=$arParams["CLASS_LINK"]?>',function(e){
				e.preventDefault();
					$.ajax({
						 url: $(this).attr('href'),
						 data: {},
						 dataType : "html",
						 success: function (data, textStatus) {
							$('#mlfSite').append('<div class="modalform" id="modalform<?=$arParams['FORMID']?>"><div class="wrap">'+data+'</div></div>');
							$('input, textarea').placeholder();
							var ots = ($(window).height()-$('.modalform .wrap').height())/2;
								$('.modalform .wrap').css({'margin-top': ots+'px'});
							$("html").css({'overflow-y':'hidden'});
						}
					});
			});
			$(document).on('click','#modalform<?=$arParams['FORMID']?> .wrap .close',function(e){
				$('.modalform').remove();
				$("html").css({'overflow-y':'auto'});
			});
			$(document).on('click','.modalform .wrap #submitformf<?=$arParams['FORMID']?>',function(e){
				e.preventDefault();
				<?
				$field = false;
				if(is_array($arParams['FIELD_SHOW']) && count($arParams['FIELD_SHOW'])>0){
				$field = true;
					foreach($arParams['FIELD_SHOW'] as $value){
						?>
						var form_<?=$value?> = $('#f<?=$arParams['FORMID']?><?=$value?>').val();
						<?
					}
				}
				if(is_array($arParams['FIELD_SHOW_HIDDEN']) && count($arParams['FIELD_SHOW_HIDDEN'])>0){
					foreach($arParams['FIELD_SHOW_HIDDEN'] as $value){
						?>
						var form_<?=$value?> = $('#f<?=$arParams['FORMID']?><?=$value?>').val();
						<?
					}
				}
				
				?>
				var bistrclick_sessid = $("#bistrclick_sessid").val();
				$('.modalform .wrap .bg1').append('<div class="preload"><div class="load"></div></div>');
				<?if($field){?>
				setTimeout(function(){
				$.ajax({
					url: '<?=$arResult['REF_START']?>',
					data: {
							bistrclick_sessid: bistrclick_sessid, 
							formclick: '1', 
							name_bk: '1', 
							referer: '<?=$key?>', 
							mlife_formid: '<?=$arParams['FORMID']?>',
							<?if(in_array('name',$arParams['FIELD_SHOW'])) {?> name: form_name,<?}?>
							<?if(in_array('mess',$arParams['FIELD_SHOW'])) {?> mess: form_mess,<?}?>
							<?if(in_array('phone',$arParams['FIELD_SHOW'])) {?> phone: form_phone,<?}?>
							<?if(in_array('email',$arParams['FIELD_SHOW'])) {?> email: form_email,<?}?>
							<?if(in_array('addfield1',$arParams['FIELD_SHOW_HIDDEN'])) {?> addfield1: form_addfield1,<?}?>
							<?if(in_array('addfield2',$arParams['FIELD_SHOW_HIDDEN'])) {?> addfield2: form_addfield2,<?}?>
							<?if(in_array('addfield3',$arParams['FIELD_SHOW_HIDDEN'])) {?> addfield3: form_addfield3,<?}?>
							<?if(in_array('addfield4',$arParams['FIELD_SHOW_HIDDEN'])) {?> addfield4: form_addfield4,<?}?>
							},
					dataType : "html",
					success: function (data, textStatus) {
						$('.modalform .wrap').html(data);
						$('input, textarea').placeholder();
					}
				});
				},500);
				<?}?>
			});
		});
	</script>
	<?
}
elseif($arResult['CHECK_SPAM']==1 && $arResult['SHOW_FORM']==1) {
	?>
	<?$APPLICATION->RestartBuffer();?>
	<div class="bg1"><div class="bg2">
	<div class="closewrap">
		<span class="close"></span>
	</div>
		<div class="wrapform">
		<div class="title"><?=$arParams['F_NAME']?></div>
        <div class="desc"><?=GetMessage('MLIFE_CAT_BK_ERROR_KEY')?></div>
		</div>
	</div></div>
	<?
	die();
}