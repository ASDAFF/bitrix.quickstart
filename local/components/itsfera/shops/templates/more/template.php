<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
    <div class="contacts_page">
      <div class="contacts">
        <h1>адреса магазинов</h1>
        </div>
        <div class="map" id="shops_map"></div>
        <div class="contacts">
        <div class="dealers">

     <script>
     	mht.shops = <?
     		echo WP::js($arResult['SHOPS']);
     	?>;
     	mht.shopRegion = "<?=$arResult['ACTIVE_REGION']?>";
     </script>

<div class="area_block">
	<div class="area_block_title">Город</div>
    <div class="select"><div class="select_arrow"></div><input name="area" class="ui-autocomplete-input"></div>
</div><div class="dealers_block <?=$n==6 ? 'nomarg' : ''?>" id="shops">
	<?
		foreach($arResult['SHOPS'] as $shop){
			?><div class="dealer <?=$shop['isComingSoon'] ? 'coming-soon' : ''?>"  <?=WP::getEditElementID(74, $shop['id'], $this, true)?>>
		    	<a href="<?=$shop['link']?>">
		            <div class="dealer_street"><?=$shop['street']?></div>
		            <div class="dealer_build"><?=$shop['house_html']?></div>
                    <span class="dealer_phones">
                        <?
                            foreach($shop['phones'] as $phone){
                                ?>
                                    <div class="dealer_phone">
                                        <?=$phone?>
                                    </div>
                                <?
                            }
                        ?>
                    </span>
		            <div class="dealer_time_title">время работы</div>
		            <div class="dealer_time"><?=$shop['time']?></div>
		            <div class="dealer_map"><span>на карте</span></div>
		        </a>
		    </div><?
		}
	?>
</div>
      </div>
    </div>
	</div>