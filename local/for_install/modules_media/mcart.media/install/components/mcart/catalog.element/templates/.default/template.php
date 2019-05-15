<?#print "<pre>"; print_r($arResult["sortament_options"]); print "</pre>";?>


<?
$SAMPLE_WIDTH = 448;
$SAMPLE_HEIGHT = 200;
?>

            	<h1 class="entry-title"><?=$arResult["NAME"]?></h1>
                
            <div style="position:relative;top:10px;display:table-cell;width:50%">
<div class="entry-body" style="display:table-row">
<div class="asd" style="padding:0px;display:table-cell;"><div class="entry">
	           	 	<div class="slider-wrap">
						<div id="main-photo-slider" class="csw">
							<div class="panelContainer">
							<?
							$i = 0;
							foreach ($arResult["PHOTOS_SECTION"] as $pic){
							$i = $i+1;
							?>
								<div class="panel" title="Panel<?echo " ".$i?>">
									<div class="wrapper">
									<?#echo $pic['big']["HEIGHT"];?>
										<img src="<?=$pic['big']["SRC"]?>" width="<?=$pic['big']["WIDTH"]?>" height="<?=$pic['big']["HEIGHT"]?>" alt="" title=""  />
									</div>
								</div>
							<?}?>	
							</div><!-- .panelContainer-->
					</div><!-- #main-photo-slider-->
					<div id="movers-row">
					<?
							$i = 0;
							foreach ($arResult["PHOTOS_SECTION"] as $pic){
							$i = $i+1;
							?>
			        	<div><a href="#<?=$i?>" class="cross-link active-thumb"><img src="<?=$pic['small']["SRC"]?>" width="<?=$pic['small']["WIDTH"]?>" height="<?=$pic['small']["HEIGHT"]?>" class="nav-thumb" alt="temp-thumb" /></a></div>
						
					<?}?>	
					</div><!-- #movers-row-->
					</div><!-- .slider-wrap-->

                    <div class="description">
                    	<span class="heading">Описание</span>
                        <div class="descr-body">
                        	<?=$arResult["DETAIL_TEXT"]?></div>
               		</div>
                    
                   
                </div>
<div class="2c" style="display:table-cell;"><div class="details">
					<div class="uptop">
                	<div class="price"> 
                    	<span class="metr">За 1м<sup>2</sup></span>
                        <?=$arResult["sortament_options"]["price"]?> <span class="wmr">руб</span>
                    </div>
                    <div class="sort">
                    	<a href="#dialog" name="modal">зависит от сорта</a>
                    </div>
                    <div class="zakaz">
                    	<a href="#dialog2" name="modal">Заказать</a>
                    </div>
                    </div>
                    <div class="sizes">
                    	<ul>
                    		<li>Толщина <span><?=$arResult['sortament_options']['thickness']?></span></li>
                    		<li>Ширина <span><?=$arResult['sortament_options']['width']?></span></li>
                    		<li>Длина <span><?=$arResult['sortament_options']['length']?></span></li>
                    		<li>Производитель <span><?=$arResult["DISPLAY_PROPERTIES"]["SORT"]["DISPLAY_VALUE"]?></span></li>
                    	</ul>	
                    </div>
                </div>
                </div>
				</div>
				</div>
</div>
