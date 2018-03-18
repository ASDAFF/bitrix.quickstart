<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
//Для DepthLevel=1 Вывести один код

//Как распознать секцию

//Для вложенных элементов 


function myGetSections($id)
{ 
 $arrfilter=array("SECTION_ID"=>$id);	
 $cdbres=CIBlockSection::GetList(array("SORT"=>"ASC"),$arrfilter,true);
 
 while($ar_result = $cdbres->GetNext())
 {
	 $myres[]=$ar_result;
 }
 
 return $myres;
}

/*
$arrtest=myGetSections(154);

var_dump($arrtest);
*/

$url=explode("/",$_SERVER['REQUEST_URI']);
$curdept=0;
$myarr=$arResult["SECTIONS"];

/*
echo('<pre>');
var_dump($myarr);
echo('</pre>');
*/
foreach($arResult["SECTIONS"] as $arSection):
?>

 <?
 if($arSection["DEPTH_LEVEL"]==1):
 ?>
 <?
   $secid=$arSection["ID"];
 ?>
    <li><h2 class="f12"><?=$arSection["NAME"]?></h2>
	<ul class="ar">
    
      <?
      $arrdepth2=myGetSections($secid)   
      ?>
      <?
      foreach($arrdepth2 as $item):
	   ?>
       <?if(($item['ID']==$url[2])&& $item["DEPTH_LEVEL"]!=1 && $url[1]='products'):?>
       
       
       <li id="<?=$this->GetEditAreaId($item['ID']);?>">
        <a href="<?=$item["SECTION_PAGE_URL"] ?>" id="selsec">
	      <?=$item["NAME"]?>
        </a>   
        
        <?else:?>
        <li id="<?=$this->GetEditAreaId($item['ID']);?>"><a href="<?=$item["SECTION_PAGE_URL"] ?>"><?=$item["NAME"]?>
</a>   

       <?endif?>
       
       
       
        <?endforeach?>
   

    
      </ul>
 <?
 endif
 ?>

<?endforeach?>