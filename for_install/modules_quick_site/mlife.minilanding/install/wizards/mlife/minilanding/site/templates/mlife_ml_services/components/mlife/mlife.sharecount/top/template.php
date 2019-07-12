<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


			<script>
			$(function(){
				var unix_timestamp = <?=strtotime($arParams["SHAREDATE"])?>;
				$('#mlfCounter').countdown({
				timestamp: new Date(unix_timestamp*1000),
				showtext: true,
				text_d: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_D")?>',
				text_h: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_H")?>',
				text_m: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_M")?>',
				text_s: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_S")?>'
				});
				
				$('#sharecount').val('<?=$arParams["SHAREDATE"]?>, <?echo date("Y.d.m H:i:s", getmicrotime())?>');
			});
			</script>
			
					<div class="contekstShare">
						<div class="desc"><?=$arParams["IMG_DESC"]?></div>
						<div class="skidka"><?=$arParams["SHARE_DESC"]?></div>
						<div class="counterTitle"><?=GetMessage("MLIFE_SHARECOUNT_FIN")?></div>
						<div id="mlfCounter"></div>
					</div>
			
	<input type="hidden" name="sharecount" id="sharecount" value="<?=$arParams["SHAREDATE"]?>"/>