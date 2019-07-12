<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$APPLICATION->SetPageProperty("description",$arResult["SECTION"]["NAME"]);
$APPLICATION->SetPageProperty("keywords",$arResult["SECTION"]["NAME"]);
?> 


<?
function crop_str($string, $limit)
{
 
 $substring_limited = substr($string,0, $limit);        
 return substr($substring_limited, 0, strrpos($substring_limited, ' ' ));   
}

?>



<?

//echo "<pre>";print_r($arResult);echo "</pre>";
//echo $arResult["SECTION"]["DEPTH_LEVEL"]
?>



<?if($arResult["SECTION"]["DEPTH_LEVEL"]==1):?>


<h2 class="secname1"><?=$arResult["SECTION"]["NAME"]?></h2>
		<div class="mybord"></div>

<?
foreach($arResult["SECTIONS"] as $cell=>$arElement):
?>
<?
$cutdesc=crop_str($arElement["DESCRIPTION"], 100);
?>
					
		
<div class="c33">
	
<div class="c333">
              <img src="<?=$arElement["PICTURE"]["SRC"]?>" width="98" height="87px" border="0">

</div>
						<div class="c3333">
                        
                        <a href="<?=$arElement["SECTION_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
                        <br>
                        <p class="itemdesc"><?=$cutdesc?>
                         </p>
						
			 
						</div>
</div> 

<?
endforeach
?>

<div id="navchain">
	<?=$arResult["NAV_STRING"]?>
</div> 
 <?endif?>
