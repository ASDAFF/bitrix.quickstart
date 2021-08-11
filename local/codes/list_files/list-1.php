<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("sect_id", "13");
$APPLICATION->SetTitle("Используем Bitrix Framework");

function MakeElementsTree(){
		global $APPLICATION;
		if(!CModule::IncludeModule("iblock")){
			echo "не подключается модуль инфоблоки";
		}
		
		// Идентификатор раздела 
		$eltype = 27;
		
		if (!empty($eltype))
		 {
			
			//ID инфоблока
			$res = CIBlockSection::GetByID($eltype);
				if($ar_res = $res->GetNext()) $parentIBlockID = $ar_res['IBLOCK_ID'];
					
			$arFilter=array(
				"IBLOCK_ID" => $parentIBlockID,
				"SECTION_ID" => $eltype,
			);

			$ar_result=Array();

			$arProj = CIBlockSection::GetList(array("SORT"=>"ASC"),$arFilter,false);

			  while($projRes = $arProj->GetNextElement())
				{
					$arFields = $projRes->GetFields();
							
					$ar_result[$arFields["ID"]]["NAME"] = $arFields["NAME"];
				}	
				
				foreach($ar_result as $arrkey => $arrvalue){	
				   $arProjElem = CIBlockElement::GetList(array(),array("SECTION_ID"=>$arrkey),false);
					while($projResElem = $arProjElem->GetNextElement())
					{
						$arElemFields = $projResElem->GetFields();
						
						$arSelFlds["NAME"] = $arElemFields["NAME"];
						$arSelFlds["PREVIEW_TEXT"] = $arElemFields["PREVIEW_TEXT"];
						$arSelFlds["DETAIL_PAGE_URL"] = $arElemFields["DETAIL_PAGE_URL"];
						$arSelFlds["DETAIL_TEXT_SIZE"] = strlen($arElemFields["DETAIL_TEXT"]);
						
						
						$ar_result[$arrkey]["ITEMS"][] = $arSelFlds;
					}
				}
			  foreach($ar_result as $key => $arrValues)
			   {
					echo "<h4>".$arrValues["NAME"]."</h4>";
					 if(is_array($arrValues["ITEMS"]) && count($arrValues["ITEMS"]) > 0)
					 {
						echo "<ul style=\"margin-bottom:10px;\">";
						 
						 foreach ($arrValues["ITEMS"] as $arrItem)
						  {
							echo "<li class=\"gvert\">";
							
							if($arrItem["DETAIL_TEXT_SIZE"] > 0)
								{
								  echo "<a href=\"".$arrItem["DETAIL_PAGE_URL"]."\" style=\"font-weight:bold;\" >".$arrItem["NAME"]."</a><br />";
								}
							else
								{
								  echo "<span style=\"font-weight:bold;\">".$arrItem["NAME"]."</span><br />";
								}

							if(strlen($arrItem["PREVIEW_TEXT"]) > 0){
							   echo "<span>".$arrItem["PREVIEW_TEXT"]."</span>"; 
							 }
						echo "</li>";				
					  }
						
						echo "</ul>";	
						
					 }
			 
			   }	
		 }
		 else{
			showError("В свойствах страницы не указан ID раздела с элементами");
		 }
		 
   } //end MakeElementsTree
?> 

<?MakeElementsTree();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>