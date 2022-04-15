<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
	<?foreach($arResult["ITEMS"] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem["INPUT"];
		endif;
	endforeach;?>
        <input type="hidden" name="set_filter" value="Y" />
         
   <h2 class="b-sidebar-filter__title">Выбор по параметрам:</h2>
   
   
        <div class="b-sidebar-filter-container">
            <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Розничная цена</span>
            </div>
            <div class="clearfix">
                <div class="b-sidebar-filter__left">
                    <input type="text" class="b-text" id="SLIDER_MIN" />
                </div>
                <span class="b-sidebar-filter__mdash">—</span>
                <div class="b-sidebar-filter__right">
                    <input type="text" class="b-text" id="SLIDER_MAX" />
                </div>
                <div class="b-sidebar-filter-slider">
                    <div id="b-slider"></div>
                </div>
            </div>
        </div>
 
 	<?foreach($arResult["arrProp"] as $k => $arItem):?>
		 
        <div class="b-sidebar-filter-container">
            <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text"><?=$arItem['NAME'];?></span>
            </div>
            
         <?if($arItem["VALUE_LIST"]){?>
            <table>
                <?foreach($arItem["VALUE_LIST"] as $id => $val){?>
                <tr>
                    <td><label class="b-checkbox"><input type="checkbox" name="arrFilter_pf[<?=$arItem['CODE']?>]" value="<?=$id;?>" /><?=$val;?></label></td>
                </tr> 
               	<?}?>							
            </table>
            <?}?>
        </div>	 
			 
		<?endforeach;?>
        <div><button class="b-button">Показать</button></div>
   </form>


<?prent($arResult);?>