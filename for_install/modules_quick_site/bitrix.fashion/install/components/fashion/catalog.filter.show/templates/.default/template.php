<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$arDelete = array("login",  "logout", "register", "forgot_password", "change_password");?>

<?if(isset($arResult['DATA_SYS_FILTER']['item_color'])&&count($arResult['DATA_SYS_FILTER']['item_color']['VALUES'])):?>
<li class="block color">
	<h4><?=$arResult['DATA_SYS_FILTER']['item_color']['NAME']?></h4>
	<ul>
	<?foreach($arResult['DATA_SYS_FILTER']['item_color']['VALUES'] as $key => $color):?>
		<li<?=(isset($arResult['arrCurrent']['item_color'])&&$arResult['arrCurrent']['item_color']==$key?' class="active"':'')?>><a href="<?=$APPLICATION->GetCurPageParam('oitem_color='.$key, array_merge(array('oitem_color'), $arDelete))?>" class="color-1" title="<?=$color['NAME']?>">
		<?if(is_array($color['PICTURE']))
			echo '<img src="'.$color['PICTURE']['src'].'" title="'.$color['NAME'].'" alt="'.$color['NAME'].'" width="'.$color['PICTURE']['width'].'" height="'.$color['PICTURE']['height'].'" />';
		else
			echo '<span style="background-color:#'.$color['CODE'].'"></span>';?>
		</a></li>
	<?endforeach;?>
	</ul>
</li>
<?endif;?>

<?if(isset($arResult['DATA_SYS_FILTER']['item_size'])&&count($arResult['DATA_SYS_FILTER']['item_size']['VALUES'])):?>
<?natsort($arResult['DATA_SYS_FILTER']['item_size']['VALUES']);?>
<li class="block sizes">
	<h4><?=$arResult['DATA_SYS_FILTER']['item_size']['NAME']?></h4>
	<ul>
	<?foreach($arResult['DATA_SYS_FILTER']['item_size']['VALUES'] as $key => $size):?>
		<li<?=(isset($arResult['arrCurrent']['item_size'])&&$arResult['arrCurrent']['item_size']==$key?' class="active"':'')?>><a href="<?=$APPLICATION->GetCurPageParam('oitem_size='.$key, array_merge(array('oitem_size'), $arDelete))?>"><?=$size?></a></li>
	<?endforeach;?>
	</ul>
</li>
<?endif;?>

<?if($arResult['PRICE'][0]>0&&$arResult['PRICE'][1]>0):?>
<?
$price_field = array('price_from:'.$arResult['PRICE']['ID'], 'price_to:'.$arResult['PRICE']['ID']);
?>
<li class="block price">
	<h4><?=GetMessage("PRICE")?></h4>
	<form action="<?=$APPLICATION->GetCurDir()?>" method="get" id="frm-price">
	<?
	foreach($_GET as $key => $value){
		if(!in_array($key, array_merge(array('o'.$price_field[0], 'o'.$price_field[1]), $arDelete)))
			echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
	}
	?>
	<p>
		<label for="price-min"><?=GetMessage("FROM")?></label> <input type="text" name="o<?=$price_field[0]?>" id="price-min" value="<?=$arResult['PRICE'][0]?>" />
		<label for="price-max"><?=GetMessage("TO")?></label> <input type="text" name="o<?=$price_field[1]?>" id="price-max" value="<?=$arResult['PRICE'][1]?>" />
	</p>
	</form>
	<span class="min"><?=$arResult['PRICE'][0]?></span>
	<span class="max"><?=$arResult['PRICE'][1]?></span>
	<div class="price-range"></div>
</li>
<?
$min = $arResult['PRICE'][0];
$max = $arResult['PRICE'][1];

if(isset($arResult['arrCurrent'][$price_field[0]])&&$arResult['arrCurrent'][$price_field[0]]>=$arResult['PRICE'][0]&&$arResult['arrCurrent'][$price_field[0]]<$arResult['PRICE'][1])
	$min = $arResult['arrCurrent'][$price_field[0]];

if(isset($arResult['arrCurrent'][$price_field[1]])&&$arResult['arrCurrent'][$price_field[1]]<=$arResult['PRICE'][1]&&$arResult['arrCurrent'][$price_field[1]]>$arResult['PRICE'][0])
	$max = $arResult['arrCurrent'][$price_field[1]];
?>
<script>
$(".price-range").slider({
		range: true,
		min: <?=$arResult['PRICE'][0]?>,
		max: <?=$arResult['PRICE'][1]?>,
		values: [ <?=$min?>, <?=$max?> ],
		slide: function( event, ui ) {
			$("#price-min").val(ui.values[0]);
			$("#price-max").val(ui.values[1]);
		},
		change: function(event, ui) {
			$('#frm-price').submit();
		}
	});
</script>
<?endif;?>

<?if(!empty($arResult['DATA_FILTER'])||!empty($arResult['DATA_OFFERS_FILTER'])):?>
	<?foreach(($arResult['DATA_FILTER'] + $arResult['DATA_OFFERS_FILTER']) as $id => $arProp):?>
		<?natsort($arProp['VALUES']);?>
		<li class="block brands">
			<h4><?=$arProp['NAME']?></h4>
			<ul>
				<?foreach($arProp['VALUES'] as $val_id => $val):?>
				<?$l = array_key_exists($id, $arResult['DATA_FILTER'])?'m':'o';?>
				<li<?=(isset($arResult['arrCurrent'][$id])&&$arResult['arrCurrent'][$id]==$val_id?' class="active"':'')?>><a href="<?=$APPLICATION->GetCurPageParam($l.$id.'='.$val_id, array_merge(array($l.$id), $arDelete))?>"><?=$val?></a></li>
				<?endforeach;?>
			</ul>
		</li>
	<?endforeach;?>
<?endif;?>