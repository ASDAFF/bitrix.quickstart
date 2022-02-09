<?   
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
   
 
    $APPLICATION->SetTitle("Цифровой мир. Главная страница");
?><?
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_linked","PROPERTY_rating");
$arFilter = Array("IBLOCK_ID"=>"9", "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y","PROPERTY_linked"=> "744");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
$reviews_count=0;
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  pr($arFields);
 $reviews_count++;
}
echo $reviews_count;
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>