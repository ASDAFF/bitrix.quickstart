<?
$arProperties = $arResult['DISPLAY_PROPERTIES'];
$arProperties = array_slice($arProperties, 0, 6);
?>
<?if (!empty($arProperties)):?>
<div class="startshop-indents-vertical indent-40"></div>
<div class="startshop-row">
    <div class="startshop-properties">
    	<?foreach ($arProperties as $arProperty):?>
			<?if (!is_array($arProperty['VALUE'])):?>
    			<div class="startshop-property"><?=$arProperty['NAME']?> &mdash; <?=$arProperty['VALUE']?>;</div>
			<?else:?>
				<div class="startshop-property"><?=$arProperty['NAME']?> &mdash; <?=implode(', ', $arProperty['VALUE'])?>;</div>
			<?endif;?>
    	<?endforeach;?>
    	<?if (count($arResult['DISPLAY_PROPERTIES']) > 6):?>
    		<a href="#properties" class="startshop-all-properties startshop-link startshop-link-standart"><?=GetMessage('SH_CE_DEFAULT_SHOW_ALL_PROPERTIES')?></a>
			<script type="text/javascript">
    			$(document).ready(function(){
    				$(".startshop-properties .startshop-all-properties").click(function() {
    					var tabs = $('#tabs');
    					tabs.tabs("select", "#properties");
    				})
    			})
    		</script>
		<?endif;?>
    </div>
</div>
<?endif;?>