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
			<?}elseif ($arParams['SHOW_NAME']){?>
				<h1><?=$arResult['NAME']?></h1> 
			<?}?>
            <?
                if(!empty($arResult['ADDITIONAL_LINKS']) && empty($arResult['SUBBRANDS'])){
                    ?>
                        <div class="additionals" data-name="ADDITIONAL_LINKS">
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
                if(!empty($arResult['SUBBRANDS'])){?>
                    <div class="additionals">
                        <?
                        $s = '<a href="'.$arParams['SEF_FOLDER'].$arParams['BRANDS_MAIN'].'/">Показать все</a>';
                        foreach($arResult['SUBBRANDS'] as $v){
                            if($arParams['BRAND'] == $v['CODE'])
                                $active = 'class="active"';
                            $s .= ' <a '.$active.' href="'.$arParams['SEF_FOLDER'].$arParams['BRANDS_MAIN'].'/'.$v['CODE'].'/">'.$v['NAME'].'</a>';
                            unset($active);
                        }
                        echo $s;
                        ?>
                    </div>
                <?}
                if(!empty($arResult['SECTIONS_LINKS']) && empty($arResult['SUBBRANDS'])){
                    ?>
                        <div class="additionals">
                            <?
                                $s = '<a href="?">Показать все</a>';
                                foreach($arResult['SECTIONS_LINKS'] as $k=>$v){
									if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID']) && $k == $arParams['SECTION_ID']){
										$s .= ' <span>'.$v.'</span>';
									}else{
										$s .= ' <a href="?SECTION_ID='.$k.'">'.$v.'</a>';
									}
                                }
                                echo $s;
                            ?>
                        </div>
                    <?
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
--><?/*<div class="group_block js-change-catalog-view"><div class="block_group js-trigger<?if(!empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div><div class="col_group js-trigger<?if(empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div></div>*/?><!--
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
            <div class="products_block js-fit row<?if(!empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> block<?}?>" data-list-id="<?= $arParams['TYPE'] ?>">
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