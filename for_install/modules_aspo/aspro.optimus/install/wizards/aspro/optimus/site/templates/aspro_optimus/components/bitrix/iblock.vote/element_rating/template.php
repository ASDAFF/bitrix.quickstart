<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?
//Let's determine what value to display: rating or average ?
if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$DISPLAY_VALUE = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$DISPLAY_VALUE = 0;
}
else
	$DISPLAY_VALUE = $arResult["PROPERTIES"]["rating"]["VALUE"];
?>

<??>
<div class="iblock-vote" id="vote_<?echo $arResult["ID"]?>">

<script type="text/javascript">
if(!window.voteScript) window.voteScript =
{
	trace_vote: function(div, flag)
	{
		var my_div;
		var r = div.id.match(/^vote_(\d+)_(\d+)$/);		
		var i = 0;
		do{
			star_div = document.getElementById('vote_'+r[1]+'_'+i);
			if(star_div){
				if(flag){
					star_div.savedClass = star_div.className;
					if(i<=r[2]){
						star_div.className = 'star-active star-over';
					}
					else{
						star_div.className = 'star-active star-empty';
					}
				}
				else{
					star_div.className = star_div.savedClass;
				}
			}
			++i;
		}
		while(star_div);
	},
	<?
	//16*
	//Интерфейсный JavaScript
	//хороший кандидат на "генерализацию"
	?>
	do_vote: function(div, parent_id, arParams)
	{
		var r = div.id.match(/^vote_(\d+)_(\d+)$/);

		var vote_id = r[1];
		var vote_value = r[2];

		function __handler(data)
		{
			var obContainer = document.getElementById(parent_id);
			if (obContainer)
			{
				var obResult = document.createElement("DIV");
				obResult.innerHTML = data;
				obContainer.parentNode.replaceChild(obResult, obContainer);
			}
		}

		// PShowWaitMessage('wait_' + parent_id, true);

		<?
		//17*
		//Запрос будет отослан напрямую компоненту.
		?>
		var url = '/bitrix/components/bitrix/iblock.vote/component.php'

		<?
		//18*
		//Добиваем параметры поста выбором пользователя
		?>
		arParams['vote'] = 'Y';
		arParams['vote_id'] = vote_id;
		arParams['rating'] = vote_value;

		var TID = CPHttpRequest.InitThread();
		CPHttpRequest.SetAction(TID, __handler);
		<?
		//19*
		//Стандартная библиотека была чуть-чуть поправлена
		//чтобы могла отсылать параметры - массивы и массивы массивов и ...
		?>
		CPHttpRequest.Post(TID, url, arParams);
		<?
		//20*
		//Продолжение экскурсии в файле component.php (начало)
		?>
	}
}
</script>
<?
//10*
//Обратите внимание на id этого div'а
//Именого его (div'а) содержимое и будет заменяться
//результатом запроса
?>

<table>
	<tr>
	<?if($arResult["VOTED"] || $arParams["READ_ONLY"]==="Y"):?>
		<?if($DISPLAY_VALUE):?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<?if(round($DISPLAY_VALUE) > $i):?>
					<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star-voted" title="<?echo $name?>"></div></td>
				<?else:?>
					<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star-empty" title="<?echo $name?>"></div></td>
				<?endif?>
			<?endforeach?>
		<?else:?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star" title="<?echo $name?>"></div></td>
			<?endforeach?>
		<?endif?>
	<?else:
		$onclick = "voteScript.do_vote(this, 'vote_".$arResult["ID"]."', ".$arResult["AJAX_PARAMS"].")";
		?>
		<?if($DISPLAY_VALUE):?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<?if(round($DISPLAY_VALUE) > $i):?>
					<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star-active star-voted" title="<?echo $name?>" onmouseover="voteScript.trace_vote(this, true);" onmouseout="voteScript.trace_vote(this, false)" onclick="<?echo htmlspecialcharsbx($onclick);
//11*
//Вызов функции, которая сформирует, отошлет и обработает запрос
//Первый параметр - понадобится для определения величины голоса
//Второй - это id контейнера для "замены" ответом
//Третий - содержит ключ к параметрам
?>"></div></td>
				<?else:?>
					<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star-active star-empty" title="<?echo $name?>" onmouseover="voteScript.trace_vote(this, true);" onmouseout="voteScript.trace_vote(this, false)" onclick="<?echo htmlspecialcharsbx($onclick)?>"></div></td>
				<?endif?>
			<?endforeach?>
		<?else:?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<td><div id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="star-active star-empty" title="<?echo $name?>" onmouseover="voteScript.trace_vote(this, true);" onmouseout="voteScript.trace_vote(this, false)" onclick="<?echo htmlspecialcharsbx($onclick)?>"></div></td>
			<?endforeach?>
		<?endif?>
	<?endif?>
	</tr>
</table>
</div><?
//12*
//Продолжение экскурсии в файле component.php (конец)
?>