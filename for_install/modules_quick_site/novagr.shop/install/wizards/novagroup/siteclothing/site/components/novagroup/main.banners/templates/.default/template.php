<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();$this->setFrameMode(true);

if (!empty($arResult["PREVIEW_TEXT"])) {
    ?>
    <div class="text-block top">
    <?=$arResult["PREVIEW_TEXT"]?>
    </div>
<?
}

if ($arResult["PROPERTY_VIEW_VALUE"] == "slider") {
?>

<div class="carusel-my">
	<div class="carousel slide" id="myCarousel">
		<div class="carousel-inner mycarousel-inner">
			<?php
			$i = 1;
			for ($j=1; $j<13; $j++) {
				
				if (!empty($arResult["PROPERTY_BANNER" . $j . "_VALUE"])) {
					if ($i==1) {
						$addClass = " active";
					} else {
						$addClass = "";
					}
					?>
					<div class="item<?=$addClass?>">
	                <a href="<?=$arResult["PROPERTY_LINK_BANNER" . $j . "_VALUE"]?>"><img alt="<?=$arResult['SITE']['SITE_NAME']?>" title="<?=$arResult['SITE']['SITE_NAME']?>" src="<?=CFile::GetPath($arResult["PROPERTY_BANNER" . $j . "_VALUE"])?>"></a>
	                
	              </div>
					
					<?php
					$i++;
				} 
			}
			?>
		</div>
		<a data-slide="prev" href="#myCarousel" class="left carousel-control">&nbsp;</a>
		<a data-slide="next" href="#myCarousel" class="right carousel-control">&nbsp;</a>
	</div>
</div>
<div class="clear"></div>
<script>
$(document).ready(function(){
	$('#myCarousel').carousel({
		  interval: 5000
	})
});
</script>
<?php 
} elseif ($arResult["PROPERTY_VIEW_VALUE"] == "slider_setka") {
	?>
	<div class="carusel-my02">
		<div class="carousel slide" id="myCarousel">
            <div class="carousel-inner mycarousel-inner">
            <?php
			$i = 1;
			for ($j=5; $j<13; $j++) {
				
				if (!empty($arResult["PROPERTY_BANNER" . $j . "_VALUE"])) {
					if ($i==1) {
						$addClass = " active";
					} else {
						$addClass = "";
					}
					
					?>
					<div class="item<?=$addClass?>">
	                <a href="<?=$arResult["PROPERTY_LINK_BANNER" . $j . "_VALUE"]?>"><img alt="<?=$arResult['SITE']['SITE_NAME']?>" title="<?=$arResult['SITE']['SITE_NAME']?>" src="<?=CFile::GetPath($arResult["PROPERTY_BANNER" . $j . "_VALUE"])?>"></a>
	                
	              </div>
					
					<?php
					$i++;
				} 
			}
			?>
                     
            </div>
            <a data-slide="prev" href="#myCarousel" class="left carousel-control">&nbsp;</a>
            <a data-slide="next" href="#myCarousel" class="right carousel-control">&nbsp;</a>
          </div>
	</div>
	<div class="home-block">
		<?php
		for ($i=1; $i<5; $i++) {
			
			if (!empty($arResult["PROPERTY_BANNER" . $i . "_VALUE"])) {
				?>
				<div><a href="<?=$arResult["PROPERTY_LINK_BANNER" . $i. "_VALUE"]?>"><img width="247" height="247" src="<?=CFile::GetPath($arResult["PROPERTY_BANNER" . $i. "_VALUE"])?>" alt="<?=$arResult['SITE']['SITE_NAME']?>" title="<?=$arResult['SITE']['SITE_NAME']?>"></a></div>
				<?php 
			}
			
		}
		?>
	</div>
	<div class="clear"></div>
	<script>
	$(document).ready(function(){
		$('#myCarousel').carousel({
			  interval: 5000
		})
	});
	</script>
	<?
} else {
	?>
	<div class="home-left">
<?php
		for ($i=1; $i<7; $i++) {
			
			if (!empty($arResult["PROPERTY_BANNER" . $i. "_VALUE"])) {
				?>
				<div><a href="<?=$arResult["PROPERTY_LINK_BANNER" . $i. "_VALUE"]?>"><img width="247" height="247" alt="<?=$arResult['SITE']['SITE_NAME']?>" title="<?=$arResult['SITE']['SITE_NAME']?>" src="<?=CFile::GetPath($arResult["PROPERTY_BANNER" . $i. "_VALUE"])?>"></a></div>
				<?php 
			}
			
		}
		?>
	</div>
	<div class="home-right">
		<?php 
		
		for ($i=7; $i<13; $i++) {
			
			if (!empty($arResult["PROPERTY_BANNER" . $i. "_VALUE"])) {
				?>
				<div><a href="<?=$arResult["PROPERTY_LINK_BANNER" . $i. "_VALUE"]?>"><img width="247" height="247" alt="<?=$arResult['SITE']['SITE_NAME']?>" title="<?=$arResult['SITE']['SITE_NAME']?>" src="<?=CFile::GetPath($arResult["PROPERTY_BANNER" . $i. "_VALUE"])?>"></a></div>
				<?php 
			}
		}
		?>
	</div>
	<div class="clear"></div>
	<?php 
}

if (!empty($arResult["DETAIL_TEXT"])) {
    ?>
    <div class="text-block bottom-block">
    <?=$arResult["DETAIL_TEXT"]?>
    </div>
    <?
}
?><??>