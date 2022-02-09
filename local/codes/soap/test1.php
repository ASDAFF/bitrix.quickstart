<?   
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    



CModule::IncludeModule('iblock');

$file = "Name_prod;Cost;skld13;skld89;skld99\n";

 
 
$arSelect = Array("ID", "NAME", "IBLOCK_ID");
$arFilter = Array("IBLOCK_ID"=>1, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  
  $file .= '"' . $arFields['NAME'] . '";"0";"0";"0";"0"' . "\n";  
}


file_put_contents('data.csv', $file); 