<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	WP::loadScript('/js_/catalog.js');
    $perPage = MHT\CatalogPerPageX::getInstance();
    $sorter = MHT\CatalogSort::getInstance();

    if($arParams['ADD_CHAIN'] == 'Y'){
		$APPLICATION->SetTitle("Продукты бренда ".$arResult['NAME']);
        $APPLICATION->AddChainItem($arResult['NAME'], '/brand/'.$arResult['CODE'].'/');
    }
	
?>
<section class="padding">
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
				<div class="headingline inner">
                    <p class="heading"><?=$arResult['NAME']?></p>
                </div>
			<?}?>
			
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
                }
            ?>
         
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
                            ?><a class="current_page"><?=$num?></a><?
                            continue;
                        }

                        /*if ($i == $arResult['NAV']['CUR']-1){
                            ?><a class="current_page"><?=$num?></a><?
                            continue;
                        }*/
                        ?><a href="?page=<?=$num?><?=$postfix?>"><?=$num?></a><?
                    }
                    $lis = ob_get_clean();

                ?>
              <div class="pagination">

                    <? if($arResult['NAV']['CUR'] - 3 > 1){?>
                        <a href="./?<?=$postfix?>" class="pagination_tostart">В начало</a>
                   <? } ?>
                    <? if($arResult['NAV']['CUR'] > 1){ ?>
                        <a href="?page=<?=$arResult['NAV']['CUR'] - 1?><?=$postfix?>" class="pagination_prev"></a>
                    <? } ?>
                    <?=$lis?>
                    <? if($arResult['NAV']['CUR'] < $arResult['NAV']['LAST']){ ?>
                        <a href="?page=<?=$arResult['NAV']['CUR'] + 1?><?=$postfix?>" class="pagination_next"></a>
                    <? } ?>
            </div>
        </div>
    </div>
</div>
</section>