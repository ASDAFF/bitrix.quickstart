<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
    $perPage = MHT\CatalogPerPageX::getInstance();
    $sorter = MHT\CatalogSort::getInstance();

    $templateData = array(
    	'NAME' => $arResult['NAME'],
    	'CODE' => $arResult['CODE'],
    );

?>

<div class="catalog_page">
    <div class="catalog_block"><!--
        --><div class="catalog wide">
			<?if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID'])){?>
				<?foreach($arResult['SECTIONS_LINKS'] as $k=>$v){?>
					<?if($k == $arParams['SECTION_ID']){?>
						<?$APPLICATION->SetTitle("Черная пятница / ".$v);?>
						<h1>Черная пятница / <?=$v?></h1>
						<?break;?>
					<?}?>
				<?}?>
			<?}else{?>
				<h1><?=$arResult['NAME']?></h1>
			<?}?>
			
		<br/><br/>
		<?
		/********************
		**black friday 2016**
		*******************
		
		\Bitrix\Main\Page\Asset::getInstance()->addJs("/banners/countdown.js");
		\Bitrix\Main\Page\Asset::getInstance()->addCss("/banners/countdown.css");
		
		$seconds = strtotime("2016-11-24 21:00:00") - time();
		$days = str_pad(floor($seconds / 86400), 2, '0', STR_PAD_LEFT);
		$seconds %= 86400;
		$hours = str_pad(floor($seconds / 3600), 2, '0', STR_PAD_LEFT);
		$seconds %= 3600;
		$minutes = str_pad(floor($seconds / 60), 2, '0', STR_PAD_LEFT);
		$seconds %= 60;			
		$seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
		//echo $days.':'.$hours.':'.$minutes.':'.$seconds;
		?>
		
		<script>
		  $(document).ready(function(){
			$(".digits").countdown({
			  image: "/banners/digits.png",
			  format: "dd:hh:mm:ss",
			  startTime: "<?=$days.':'.$hours.':'.$minutes.':'.$seconds?>"
			});
		  });
		</script>
		
		<a href="/o_kompanii/novosti/664791/">
		<div class="banner_friday">
			<div class="wrapper_friday">
			  <div class="cell_friday">
				<div id="holder_friday">
				  <div class="digits"></div>
				</div>
			  </div>
			</div>
		</div>		
		</a>
		
		<?
		*******************
		**black friday 2016**
		********************/
		?>
		
			
            <?
                if(!empty($arResult['ADDITIONAL_LINKS'])){
                    ?>
                        <div class="additionals">
                            <?
                                $s = '';
                                foreach($arResult['ADDITIONAL_LINKS'] as $link){
                                    $s .= '| <a href="'.$link[0].'">'.$link[1].'</a>';
                                }
                                echo substr($s, 2);
                            ?>
                        </div>
                    <?
                }
            ?>
			<?
                
				if (!$_REQUEST["IB_ID"] && !$_REQUEST["SECTION_ID"]) { // выводим список инфоблоков, если пусто в реквесте?>
						<div class="additionals blackfriday-sections">
							<?foreach($arResult['IB_IDS'] as $k=>$v){
								$s .= ' <a href="?IB_ID='.$v.'">'.$arResult['IB_NAMES'][$k].'</a>';
							}
							echo $s;
							?>
						</div>
					
				<?} elseif(!empty($arResult['SECTIONS_LINKS'])){
                    ?>
                        <div class="additionals blackfriday-sections">
                            <?
								
                                $s = '<a href="?">Показать все</a>';
								if ($_REQUEST["SECTION_ID"]) {
									$s .= ' <span>'.$arResult['SECTIONS_LINKS'][$_REQUEST["SECTION_ID"]].'</span> <br/>'; 
								}
								
								//print_r ($arResult['SECTIONS_PARENTS']);
								
                                foreach($arResult['SECTIONS_LINKS'] as $k=>$v){
									
									
									if ($_REQUEST["SECTION_ID"] && $arResult['SECTIONS_DEPTHS'][$_REQUEST["SECTION_ID"]]==1) {
										
										//IB_LINKS\
										
										if($arResult["IB_LINKS"][$_REQUEST["SECTION_ID"]] != $arResult["IB_LINKS"][$k]) continue;
										
										if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID']) && $k == $arParams['SECTION_ID']){
											$s .= ' <span>'.$v.'</span>';
										}else{
											$s .= ' <a href="?SECTION_ID='.$k.'">'.$v.'</a>';
										}
										
										
										
									} elseif ($_REQUEST["SECTION_ID"] && count ($arResult['SECTIONS_CHILDS'][$_REQUEST["SECTION_ID"]]) > 0 ) { // есть родительский раздел, выводим следующий уровень при наличии
										
										
										
										if (!in_array($k, $arResult['SECTIONS_CHILDS'][$_REQUEST["SECTION_ID"]])) continue;
										
										if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID']) && $k == $arParams['SECTION_ID']){
											$s .= ' <span>'.$v.'</span>';
										}else{
											$s .= ' <a href="?SECTION_ID='.$k.'">'.$v.'</a>';
										}
									} elseif ($_REQUEST["SECTION_ID"] && count ($arResult['SECTIONS_CHILDS'][$_REQUEST["SECTION_ID"]]) < 1) { //нету подразделов 
										
										//надо выводить разделы того же уровня, что и секция реквеста, но только с тем же родителем, что и у этой секции
										
										
										
										//echo ('<br/>'.$arResult['SECTIONS_PARENTS'][$k].'!='.$arResult['SECTIONS_PARENTS'][$_REQUEST["SECTION_ID"]]);
										
										if ($arResult['SECTIONS_PARENTS'][$k] != $arResult['SECTIONS_PARENTS'][$_REQUEST["SECTION_ID"]]) {
												continue;
										}
										
										
										if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID']) && $k == $arParams['SECTION_ID']){
											$s .= ' <span>'.$v.'</span>';
										}else{
											$s .= ' <a href="?SECTION_ID='.$k.'">'.$v.'</a>';
										}
										
									} else { // не установлена секция
									
										if (count ($arResult['SECTIONS_CHILDS'][$_REQUEST["SECTION_ID"]]) < 1 && $arResult['SECTIONS_DEPTHS'][$k] > 1) continue; //нет родительских разделов, выводим только топ
										
										if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID']) && $k == $arParams['SECTION_ID']){
											$s .= ' <span>'.$v.'</span>';
										}else{
											$s .= ' <a href="?SECTION_ID='.$k.'">'.$v.'</a>';
										}
																				
									}
									
                                }
                                echo $s;
                            ?>
                        </div>
                    <?
                } else  {
					
					$s = '<a href="?">Показать все</a>';
					echo $s;
				}
            ?>
            <div class="filter_block">
            	<div class="filter_block_top">
                	<div class="sort_block">
                    	<span class="sort_block_title">сортировать по</span><!--
                        --><div class="sort_block_list">
    	                    	<select id="change_sort" data-list-id="<?= $sorter->get('list-id'); ?>">
    	                            <?=$sorter->getOptions()?>
    	                        </select>
                        </div>
                    </div><!--
                    --><div class="group_block js-change-catalog-view"><div class="block_group js-trigger<?if(!empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div><div class="col_group js-trigger<?if(empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div></div><!--
                    --><div class="product_count">
                    	<span class="product_count_title">выводить по</span><!--
                        --><div class="product_count_list">
    	                    	<select id="change_per_page">
                                    <?=$perPage->getOptions()?>
                                </select>
                        </div>
                    </div>
                </div> 
                <?/*
                <div class="filter_block_middle">
                	Расширенный поиск
                </div>
                <div class="filter_block_bottom">

                </div>
                */?>
            </div>
            <div class="products_block js-fit<?if(!empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> block<?}?>" data-list-id="<?= $arParams['TYPE'] ?>">
            	<?
                    $i = 0;

            		foreach($arResult['PRODUCTS'] as $product){
            			echo $product->html('catalog', array(
                            'i' => $i,
                            'line' => 6,
							'HIDE_ACTION_LABEL' => $arParams["HIDE_ACTION_LABEL"],
							'HIDE_NEW_LABEL' => $arParams["HIDE_NEW_LABEL"],
							'HIDE_BLACKFRIDAY_LABEL' => $arParams["HIDE_BLACKFRIDAY_LABEL"]
                        ));
                        $i++;
            		}
            	?>
              </div>
			   <?
				  $postfix = "";
				  if(!empty($arParams['SECTION_ID'])){
					  $postfix .= "&SECTION_ID=".$arParams['SECTION_ID'];
				  }
				  ?>
                <?
                    ob_start();
                    $max = 3;
                    for($i=-3; $i<$max; $i++){
                        $num = $i + $arResult['NAV']['CUR'];
                        if($num <= 0){
                            $max++;
                            continue;
                        }
                        if($num > $arResult['NAV']['LAST']){
                            break;
                        }
                        if($i == 0){
                            ?><li><span><?=$num?></span></li><?
                            continue;
                        }
                        ?><li><a href="?page=<?=$num?><?=$postfix?>"><?=$num?></a></li><?
                    }
                    $lis = ob_get_clean();

                ?>
              <div class="pagination">
                <nav>
                    <? if($arResult['NAV']['CUR'] - 3 > 1){?>
                        <a href="./?<?=$postfix?>" class="pagination_tostart">В начало</a>
                   <? } ?>
                    <? if($arResult['NAV']['CUR'] > 1){ ?>
                        <a href="?page=<?=$arResult['NAV']['CUR'] - 1?><?=$postfix?>" class="pagination_left"></a>
                    <? } ?>
                    <ul class="pagination_center"><?=$lis?></ul>
                    <? if($arResult['NAV']['CUR'] < $arResult['NAV']['LAST']){ ?>
                        <a href="?page=<?=$arResult['NAV']['CUR'] + 1?><?=$postfix?>" class="pagination_right"></a>
                    <? } ?>
                </nav>
            </div>
        </div>
    </div>
</div>