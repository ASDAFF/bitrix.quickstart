<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<script language="JavaScript" type="text/JavaScript">
   function set(arg, f){
   document.getElementById('rating_'+f).value = arg;
   document.getElementById('form_'+f).submit();
   return true;
   }
   
   function Background(i, id, count){
	for(var j=0; j<=i; j++){
		 document.getElementById('img_'+j+'_'+id).className="star";
		}
	for(var j=i+1; j<count; j++){
		 document.getElementById('img_'+j+'_'+id).className="star2";
		}
   return true;
   }
   
	function Star(r, id, count){
	for(var j=0; j<r; j++){
		 document.getElementById('img_'+j+'_'+id).className="star";
		}
	for(var j=r; j<count; j++){
		 document.getElementById('img_'+j+'_'+id).className="star2";
		}
   return true;
   }
</script>

<?$back="";?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="form_<?=$arResult["ID"]?>" id="form_<?=$arResult["ID"]?>">
		<div style="float:left;">

			<table class="star"><tr>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):
				$back.="'img_".$i."_".$arResult["ID"]."',";?>
				<td><div onmouseout="Star(<?=(int)($arResult["PROPERTIES"]["rating"]["VALUE"])?>, <?=$arResult["ID"]?>, <?=count($arResult["VOTE_NAMES"])?>);" onmousemove="Background(<?=$i?>, <?=$arResult["ID"]?>, <?=count($arResult["VOTE_NAMES"])?>)" class="star<?if ($i+1>=$arResult["PROPERTIES"]["rating"]["VALUE"]) {?>2<?}?> " id="img_<?=$i?>_<?=$arResult["ID"]?>" onClick="set('<?=$i?>',<?=$arResult["ID"]?>)"></div></td>
			<?endforeach?>
			</tr></table>
		</div>
			
			<input type="hidden" name="rating" id="rating_<?=$arResult["ID"]?>">
		
		
		<?echo bitrix_sessid_post();?>
		<input type="hidden" name="back_page" value="<?=$arResult["BACK_PAGE_URL"]?>"/>
		<input type="hidden" name="vote_id" value="<?=$arResult["ID"]?>" />
		<input type="hidden" name="vote" value="<?=GetMessage("T_IBLOCK_VOTE_BUTTON")?>" />
	</form>



<?/*?>
<div class="iblock-vote">
	<form method="get" action="<?=POST_FORM_ACTION_URI?>">
		<select name="rating">
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<option value="<?=$i?>"><?=$name?></option>
			<?endforeach?>
		</select>
		<?echo bitrix_sessid_post();?>
		<input type="hidden" name="back_page" value="<?=$arResult["BACK_PAGE_URL"]?>" />
		<input type="hidden" name="vote_id" value="<?=$arResult["ID"]?>" />
		<input type="submit" name="vote" value="<?=GetMessage("T_IBLOCK_VOTE_BUTTON")?>" />
	</form>
</div>

<?*/?>
