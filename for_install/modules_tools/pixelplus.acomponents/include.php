<? 
$arClasses = array(
	"CPixelPlusFormat" => "classes/general/pixelplusfomat.php",
	"CPixelPlusFormatUF" => "classes/general/pixelplusformatuf.php",
	"CPixelPlusFormatSF" => "classes/general/pixelplusformatsf.php",
	"CPixelPlusFormatParamsC" => "classes/general/pixelplusformatparamsc.php",
	"CPixelplusAcomponents"=> "classes/general/pixelplusacomponents.php",
);
CModule::AddAutoloadClasses("pixelplus.acomponents", $arClasses);
?>
