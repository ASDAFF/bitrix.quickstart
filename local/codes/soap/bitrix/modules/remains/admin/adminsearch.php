<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if(!$USER->isAdmin()) die();
 
if($_REQUEST['action'] == 'compare'){
    
  
    $v1 = $_REQUEST["v1"];
    $v2 = $_REQUEST["v2"];
    CModule::IncludeModule('remains');
    $remains = new remainsHelper();
    $remains->compare($v2, $v1);
}
else {  
    CModule::IncludeModule('iblock');

    //$arSelect = Array("ID", "NAME", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "SECTION_PAGE_URL");
    $arFilter = Array("IBLOCK_ID"=>1, "NAME"=> '%'. $_REQUEST['name'] . '%');
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, false);
    
    
    function getSectLink($id){ 
        
        static $cache = array();
        
        if($cache[$id])
            return $cache[$id];
        
          $res2 = CIBlockSection::GetByID($id);
          $ar_res2 = $res2->GetNext(); 
         
          
          $cache[$id] = $ar_res2["SECTION_PAGE_URL"] ;
        
           return $cache[$id];
    }
    
    
    ?>
<table>
    <?
    while($ob = $res->GetNextElement())
    {
      $arFields = $ob->GetFields();
      if($arFields['PREVIEW_PICTURE'])
          $arFields['PREVIEW_PICTURE'] = CFile::GetFileArray($arFields['PREVIEW_PICTURE']);
      ?>
          <tr id="v1_<?=$arFields['ID'];?>" data-id="<?=$arFields['ID'];?>"> 
              <td>  
      <input type="radio" name="item" value="<?=$arFields['ID'];?>">
</td><td><span class="str"><?=$arFields['NAME'];?></span><? 

   
         $link = getSectLink($arFields["IBLOCK_SECTION_ID"]);
 ?> 
<br><a href="<?=$link?>" target="_blank"><?=$link?></a>
</td> 
<td><?if($arFields['PREVIEW_PICTURE']){?>
    <img src="<?=$arFields['PREVIEW_PICTURE']['SRC']?>" width="100">
    <?}?></td>
              
          </tr>  

               <?
    }
    ?>
</table>    
    <?
}