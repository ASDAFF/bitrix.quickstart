<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");

/**
 * Create index.php, .rubric.menu_ext.php, .section.php
 * 
 * @param <string> typeIBCode
 * @param <string> typeIBName
 * @param <array>  meta
 */
function createFiles4TypeIB($typeIBCode, $typeIBName, $meta)
{	
	$dir = str_replace( "catalog-", "", str_replace("_", "-", $typeIBCode) );
	$typeDir = $_SERVER["DOCUMENT_ROOT"]."/".$dir.'/';
	
	if(!file_exists($typeDir.'index.php'))
    {
        $root_index = '<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
        $APPLICATION->SetPageProperty("title", "'.$typeIBName.'");?>
        <?$APPLICATION->IncludeComponent("bitrix:menu", "bitronic_rubric", array(
	        "ROOT_MENU_TYPE" => "rubric",
	        "MENU_CACHE_TYPE" => "N",
	        "MENU_CACHE_TIME" => "3600",
	        "MENU_CACHE_USE_GROUPS" => "Y",
	        "MENU_CACHE_GET_VARS" => array(
	        ),
	        "MAX_LEVEL" => "2",
	        "CHILD_MENU_TYPE" => "left",
	        "USE_EXT" => "Y",
	        "DELAY" => "N",
	        "ALLOW_MULTI_SELECT" => "N",
	        ),
	        false
        );?>
        <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>';
        
        $filename = $typeDir."index.php";
        $fh = fopen($filename, "w+");
        fwrite($fh, $root_index);
        fclose($fh);
    }
	
	if(!file_exists($typeDir.'.rubric.menu_ext.php'))
    {
        $rubric_menu_ext = '<?global $APPLICATION;	
            $aMenuLinksExt=$APPLICATION->IncludeComponent("yenisite:menu.ext", "", array(
	        "ID" => $_REQUEST["ID"],
	        "IBLOCK_TYPE" => array(
		        // 0 => "catalog_'.$typeIBCode.'",
		        0 => "catalog_".str_replace(array("/", "-"),array("", "_"), $APPLICATION->GetCurDir())
	        ),
	        "IBLOCK_ID" => array(
	        ),
	        "DEPTH_LEVEL_START" => "2",
	        "DEPTH_LEVEL_FINISH" => "3",
	        "IBLOCK_TYPE_URL" => "/#IBLOCK_TYPE#/",
	        "IBLOCK_TYPE_URL_REPLACE" => "",
	        "SECTION_ELEMENT_CNT" => "Y",
	        "CACHE_TYPE" => "A",
	        "CACHE_TIME" => "3600"
	        ),
	        false
        );
            $aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
        ?>';

        $filename = $typeDir.".rubric.menu_ext.php";
        $fh = fopen($filename, "w+");
        fwrite($fh, $rubric_menu_ext);
        fclose($fh);
    }
	
	if(!file_exists($typeDir.'.section.php'))
    {
        $root_section = '<?
        $sSectionName="'.$typeIBName.'";
		$arDirProperties = Array(
			"title"			=> "'.$meta[0].'",
			"keywords"		=> "'.$meta[1].'",
			"description"	=> "'.$meta[2].'",
			"h1"			=> "'.$meta[3].'"
		);
        ?>';
        $filename = $typeDir.".section.php";
        $fh = fopen($filename, "w+");
        fwrite($fh, $root_section);
        fclose($fh);
    }
}

$obBlocktype = new CIBlockType;

//Count of types IB
$query 	= "SELECT COUNT(*) AS CNT FROM `".$arResult["prefix"]."hierarchy` WHERE `domain_id`=1 AND `rel`=0 AND `type_id`=5 AND `tpl_id`=1";
$count 	= mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

if($left > $count["CNT"])
{
	$left = 0;
	$right = 5;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
	$this->content .= $this->ShowHiddenField("left", $left);
	$this->content .= $this->ShowHiddenField("right", $right);
}
else
{
	global $USER;
	global $APPLICATION;

	$query 	= "SELECT obj_id AS ID, alt_name AS CODE FROM `".$arResult["prefix"]."hierarchy` WHERE `domain_id`=1 AND `rel`=0 AND `type_id`=5 AND `tpl_id`=1 LIMIT {$left}, 10";
	$res 	= mysql_query($query, $link);
	
	while($arItem = mysql_fetch_assoc($res))
	{
		$meta = array(); // Meta information for .section.php
		
		$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arItem['ID']} AND `field_id`=25";
		$tmpres = mysql_query($query, $link);
		$tmpres = mysql_fetch_assoc($tmpres);
		$description = $tmpres['varchar_val'];
		
		$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arItem['ID']} AND `field_id`=24";
		$tmpres = mysql_query($query, $link);
		$tmpres = mysql_fetch_assoc($tmpres);
		$keywords = $tmpres['varchar_val'];
		
		$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arItem['ID']} AND `field_id`=23";
		$tmpres = mysql_query($query, $link);
		$tmpres = mysql_fetch_assoc($tmpres);
		$h1 = $tmpres['varchar_val'];
		
		$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arItem['ID']} AND `field_id`=22";
		$tmpres = mysql_query($query, $link);
		$tmpres = mysql_fetch_assoc($tmpres);
		$title = $tmpres['varchar_val'];
		
		array_push($meta, $title, $keywords, $description, $h1);		
		
		$query = "SELECT name AS NAME FROM `".$arResult['prefix']."objects` WHERE `id`={$arItem['ID']}";
		$resQ = mysql_query($query, $link);
		$arr = mysql_fetch_assoc($resQ);
		
		$arFields = Array(
		'ID'=>'catalog_'.$arItem['CODE'],
		'SECTIONS'=>'Y',
		'SORT'=>100,
		'LANG'=>Array(
			'en'=>Array(
				'NAME'=>$arItem['CODE'],
				'SECTION_NAME'=>'Sections',
				'ELEMENT_NAME'=>'Elements',
				),
			'ru'=>Array(
				'NAME'=>$arr['NAME'],
				'SECTION_NAME'=>GetMessage('SECTIONS'), // Not work
				'ELEMENT_NAME'=>GetMessage('ELEMENTS'), // Not work
				)
			)
		);
		
		$arTIB = CIBlockType::GetList(array(), array('ID'=>'catalog_'.$arItem['CODE']))->Fetch();
		
		if (empty($arTIB))
		{
			$result = $obBlocktype->Add($arFields);
			
			$dir = str_replace("catalog-", "", str_replace("_", "-", $arItem['CODE']));
			$type_dir = $_SERVER["DOCUMENT_ROOT"]."/".$dir;
			
			mkdir($type_dir, 0777);
			
			createFiles4TypeIB($arItem['CODE'], $arr['NAME'], $meta);
		}
	}
	
	// Увеличиваем левую и правую границу
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>