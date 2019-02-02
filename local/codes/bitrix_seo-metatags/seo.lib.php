<?
$metaON = array();
include $_SERVER["DOCUMENT_ROOT"]."/includes/meta.php";
$urlget0 = $APPLICATION->GetCurUri();

if ($metaON[$urlget0])
{	
	$custom_title = $metaON[$urlget0]["title"];
	$custom_keywords = $metaON[$urlget0]["keywords"];
	$custom_description = $metaON[$urlget0]["description"];
}
	
//SEO
$APPLICATION->ShowMeta("robots", false, true);

if ( !empty($custom_keywords) and isset($custom_keywords) )
{
	echo '<meta name="keywords" content="'.$custom_keywords.'" />'."\n";
}
else
{
	$APPLICATION->ShowMeta("keywords", false, true);
}
	
if ( !empty($custom_description) and isset($custom_description) )
{
	echo '<meta name="description" content="'.$custom_description.'" />'."\n";
}	
else
{
	$APPLICATION->ShowMeta("description", false, true);
}

if ( !empty($custom_title) and isset($custom_title) )
{
	echo '<title>'.$custom_title.'</title>'."\n";
}
else
{
	echo "<title>", $APPLICATION->ShowTitle(), "</title>\n";
}
?>