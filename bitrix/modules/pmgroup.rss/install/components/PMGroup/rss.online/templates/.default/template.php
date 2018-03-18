<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult as $result) {
echo "<a href='";
echo $result["NEWS_LINK"];
echo "' target='_blank'>".$result["NEWS_TITLE"]."</a>";
echo "<br>";
echo $result["NEWS_DESCRIPTION"]."<br><br>";
}
?>

