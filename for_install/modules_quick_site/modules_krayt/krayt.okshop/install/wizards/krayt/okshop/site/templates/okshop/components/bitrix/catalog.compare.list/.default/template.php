<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$containerId = "catalog-compare-list".$this->randString();
?>
<div class="emarket-compare-list" id="<?echo $containerId?>">
	<?$frame = $this->createFrame($containerId)->begin('');?>
    <div class="hover-wr">
        <a href="#"
            class="ico <?if(count($arResult) <= 0) echo 'deactivated';?>"
            name="compare_list"
            title="<?if(count($arResult) <= 0) echo GetMessage('CATALOG_COMPARE_EMPTY');?>"
        ></a>
        <span class="cnt_product_compare">
            <?if(count($arResult) > 0) echo count($arResult);?>
        </span>
    </div>
	<?$frame->end();?>
    
    <?
    $arSection = array();
    foreach($arResult as $item)
      {
        $arSection[$item['IBLOCK_SECTION_ID']][] = $item;
      }          
    ?>
    <?if(count($arResult) > 0):?>    
    <div class="list-compare-hide feedback-window arrow_box">
        <div class="title" style="margin-bottom: 0px;"><?=GetMessage("TITLE_COMPARE")?></div>       
        <?foreach($arSection as $key=>$sect):?>
             <ul>
             <?foreach($sect as $item):
                $img = "";
                if($item['PREVIEW_PICTURE'])
                {
                    $img = CFile::ShowImage($item['PREVIEW_PICTURE'], 65, 65, "border=0", "", false);
 
                }
                elseif($item['DETAIL_PICTURE'])
                {                  
                    $img = CFile::ShowImage($item['DETAIL_PICTURE'], 65, 65, "border=0", "", false);
                }                                              
             ?>
                <li>
                    <a href="<?=$item['DETAIL_PAGE_URL']?>">
                    <div>
                        <?=$img?>
                    </div>
                    <div class="compare-prod-name">
                        <?=$item['NAME']?>                    
                    </div>
                    </a>
                </li>
            <?endforeach;?> 
                <?if(count($sect) > 1):?>
                <li class="btn-item">
                    <a class="link-compare em_button" title="<?=GetMessage("CATALOG_COMPARE")?>" href="<?=$arParams['COMPARE_URL']?>?SECTION=<?=$key?>"><?=GetMessage("CATALOG_COMPARE")?></a>
                </li>
                <?else:?>
                <li class="btn-item">
                    <a class="link-compare em_button disabled" title="<?=GetMessage("CATALOG_COMPARE")?>" href="#"><?=GetMessage("CATALOG_COMPARE")?></a>
                </li>
                <?endif;?>       
            </ul>    
        <?endforeach;?> 
        <?
          // echo "<pre>";
//             print_r($arResult);
//             echo "</pre>";
        ?>       
    </div>
    <?endif;?>
</div>
