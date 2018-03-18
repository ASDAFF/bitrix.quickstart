<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$dir = $APPLICATION->GetCurPage(true);
if($_GET[GALLERY_ID]==''){
if($arParams[SET_P_TITLE]==Y){
$APPLICATION->SetTitle($arParams[MAIN_TITLE]);
}
?>
<div id="object1"> 
  <ul class="galthumbnails"> 
<?foreach($arResult[COLLECTIONS] as $colkey => $colval){ 
if($colval[ICON]!=""){
?>

    <li class="galspan2"> <a href="<?=$dir?>?GALLERY_ID=<?=$colkey?>" class="thumbnail" > <div class="galimg"><img id="col<?=$colkey?>" title="<?=$colval[NAME]?>" alt="<?=$colval[DESCR]?>" src="<?=$colval[ICON]?>"/> </div>
        <div class="caption"> 
          <h3 class="blue"><?=$colval[NAME]?></h3>
         </div>
       </a> </li>
<?}}?>
   </ul>
 </div>
<?} else{

if($arParams[SET_P_TITLE]==Y){
$APPLICATION->SetTitle($arResult[COLLECTIONS][$_GET[GALLERY_ID]][NAME]);
}
?> 
<ul class="galthumbnails"> 
<?
foreach ($arResult[COLLECTIONS][$_GET[GALLERY_ID]][ITEMS] as $key => $item) { 
?> 
  <li class="galspan2"> <a href="<?=$item['PATH']?>" class="thumbnail fancygal" title="<?=$item['NAME']?>"  rel="gal1"> <div class="galimg"><img id="tm<?=$key?>" src="<?=$item['THUMB_PATH']?>" alt="<?=$item['NAME']?>" title="<?=$item['NAME']?>"/> </div>
      <div class="ntc">      
         <div class="caption" style="position: relative; padding: 0px;">
         <?$str_name=substr($item['NAME'],0,30);
         echo $str_name;
         if (strlen($str_name)==30){
         echo "...";
         }?>
         </div>
      </div>
     </a> </li>
 <? } ?> </ul>
<?if($arParams[SHOW_B_LINK]==Y){?>

<a href="<?=$dir?>" class="galblink"><?=$arParams[SHOW_B_LINK_VALUE]?></a>

<? }?>
<script type="text/javascript">
  $(document).ready(function() {
    $('.fancygal').fancybox({
nextClick : true
});
  });
</script>
<?}?>