<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//print_r($arResult)
?>
	<div class="blockLeft">
		<div class="selectServ">
			<div class="title"><?=GetMessage("MLIFE_SHARECOUNT_VOP")?></div>
			<?if(count($arResult['ITEMS'])>0){?>
			<div class="list"><ul>
				<?foreach($arResult['ITEMS'] as $item){?>
					<li><?=$item['NAME']?></li>
				<?}?>
			</ul></div>
			<?}?>
			<div class="button"><?=GetMessage("MLIFE_SHARECOUNT_LOZ")?></div>
		</div>
		<div class="share">
			<script>
			$(function(){
				var unix_timestamp = <?=strtotime($arParams["SHAREDATE"])?>;
				$('#mlfcounter1').countdown({
				timestamp: new Date(unix_timestamp*1000),
				showtext: true,
				text_d: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_D")?>',
				text_h: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_H")?>',
				text_m: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_M")?>',
				text_s: '<?=GetMessage("MLIFE_SHARECOUNT_TEXT_S")?>'
				});
				
				$('#sharetopcount').val('<?=$arParams["SHAREDATE"]?>, <?echo date("Y.d.m H:i:s", getmicrotime())?>');
				$(document).on('click','.selectServ .list li',function(e){
					e.preventDefault();
					if($(this).hasClass('active')) {
						$(this).removeClass('active');
					}else{
						$(this).removeClass('active').addClass('active');
					}
					
					var text = '';
					
					$('.selectServ .list li').each(function() {
						if($(this).attr('class')=="active") {
							text += $(this).html()+',';
						}
					});
					
					$('#sharetopserv').val(text);
					
				});
			});
			</script>
			<div class="image"><img src="<?=$arParams["IMG_DESC"]?>" alt="<?=GetMessage("MLIFE_SHARECOUNT_SHARE")?>"/></div>
			<div class="ingo">
				<div class="title"><?=GetMessage("MLIFE_SHARECOUNT_SHARE")?></div>
				<div class="desc"><?=$arParams["SHARE_DESC"]?></div>
				<div class="counter">
					<div class="cntTitle"><?=GetMessage("MLIFE_SHARECOUNT_FIN")?></div>
					<div class="cntWp" id="mlfcounter1"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="blockRight"><?/*тут форма подгруженная аяксом*/?></div>
	<input type="hidden" name="sharetopcount" id="sharetopcount" value="<?=$arParams["SHAREDATE"]?>"/>
	<input type="hidden" name="sharetopserv" id="sharetopserv" value=""/>