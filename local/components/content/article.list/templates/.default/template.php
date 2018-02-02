<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult)){

      foreach($arResult as $key => $arSection)
      	{	if (is_array($arSection['ITEMS'])) { // если в разделе есть элементы
    
           echo "<h2>".$arSection["NAME"]."(".$arSection['COUNT'].")</h2>"; // название и количество
           if(count($arSection["ITEMS"]) > 0)
         {
           echo "<ul style=\"margin-bottom:10px;\">";
              foreach ($arSection["ITEMS"] as $arItem)
                       {
            echo "<li>";
              if($arItem["DETAIL_TEXT_SIZE"] > 0) // если есть детальный текст статьи
                {
                 echo "<a href=\"".$arItem["DETAIL_PAGE_URL"]."\" style=\"font-weight:bold;\" >".$arItem["NAME"]."</a> <br />"; // выводим ссылку на детальную страницу
                }
               else
                {
                 echo "<span style=\"font-weight:bold;\">".$arItem["NAME"]."</span><br />"; // если нет то просто заголовок
                }
    if(strlen($arItem["PREVIEW_TEXT"]) > 0){ //если есть текст для анонса
       echo "<span>".$arItem["PREVIEW_TEXT"]."</span>";  // выодим его
     }
        echo "</li>";             
       }        
     echo "</ul>";
    }
   } 
     	}
}
?>	
