<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");

$arFields = Array(
	'ID'=>'joomla',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'joomla',
			)
		)
	);

$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);


$TEMPLATE = array(
	"[table]sections" => array(
		"[type]" => "I",
		"NAME" => "title",
		"CODE" => "alias",
		"DESCRIPTION" => "description",
		"SORT" => "ordering",
		"[table]categories" =>	array(
				"[type]" => "S",
				"[parent]" => "section",
				"IBLOCK_ID" => "#ID#",
				"NAME" => "title",
				"CODE" => "alias",
				"DESCRIPTION" => "description",
				"SORT" => "ordering",
				"[table]content" => array(
						"[type]" => "E",
						"[parent]" => "catid",
						"IBLOCK_ID" => "#IBLOCK_ID#",
						"IBLOCK_SECTION_ID" => "#IBLOCK_SECTION_ID#",
						"NAME" => "title",
						"PREVIEW_TEXT" => "introtext",
						"DETAIL_TEXT" => "fulltext",
						"CODE" => "alias",
						"SORT" => "ordering",
						"DATE_ACTIVE_FROM" => "publish_up",
						"DATE_CREATE" => "created",
					)
			
			)
	)

);


function iblock_migrate($tmp, $parent, $b_parent, $prefix = "", $link, $tab = "&nbsp;")
{
	foreach($tmp as $key=>$tm)
	{
		if(substr_count($key, "[table]"))
		{
			$where = "";
			$table = $prefix.str_replace("[table]", "", $key);
			if(strlen($tmp[$key]["[parent]"]) > 0)
			{
				$where = " WHERE ".$tmp[$key]["[parent]"]." = '".$parent."'";
			}
				$query = "SELECT * FROM ".$table.$where;

			$result = mysql_query($query, $link);
			$arItem = array();
			while ($arItem = mysql_fetch_assoc($result))
			{
				switch($tm["[type]"]){
					case "I":
						$arResult = array(
							"SITE_ID" => "s1",
							"ACTIVE" => "Y",
							"IBLOCK_TYPE_ID" => "joomla",
							"NAME" => $arItem[$tm["NAME"]],
							"CODE" => $arItem[$tm["CODE"]],
							"DESCRIPTION" => $arItem[$tm["DESCRIPTION"]],
							"SORT" => $arItem[$tm["SORT"]],
							);
	
						$iblock = new CIBlock;
						$res = CIBlock::GetList(array(), array("CODE" => $arItem[$tm["CODE"]]))->GetNext();
						if($res)
							$id = $res["ID"];
						else
						{
							$id = $iblock->Add($arResult);

							$ibp = new CIBlockProperty;
							$arFields = Array( "NAME" => "keywords", "ACTIVE" => "Y", "SORT" => "100", "CODE" => "keywords", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id );
							$PropID = $ibp->Add($arFields);
							$arFields = Array( "NAME" => "description", "ACTIVE" => "Y", "SORT" => "100", "CODE" => "description", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id );
							$PropID = $ibp->Add($arFields);
						}



						iblock_migrate($tmp[$key], $arItem["id"], $id, $prefix, $link, $tab.$tab);
						break;
					case "S":
						$arResult = array(
							"IBLOCK_ID" => $b_parent,
							"NAME" => $arItem[$tm["NAME"]],
							"CODE" => $arItem[$tm["CODE"]],
							"DESCRIPTION" => $arItem[$tm["DESCRIPTION"]],
							"SORT" => $arItem[$tm["SORT"]],
							);
						$res = CIBlockSection::GetList(array(), array("IBLOCK_ID"=> $b_parent,"CODE" => $arItem[$tm["CODE"]]))->GetNext();
						$sec = new CIBlockSection;
						if($res)
							$id = $res["ID"];
						else
							$id = $sec->Add($arResult);

						iblock_migrate($tmp[$key], $arItem["id"], $id, $prefix, $link, $tab.$tab);
						break;
					case "E":
						$res1 = CIBlockSection::GetByID($b_parent)->GetNext();
						$arResult = array(
							"IBLOCK_ID" => $res1["IBLOCK_ID"],
							"IBLOCK_SECTION_ID" => $b_parent,
							"NAME" => $arItem[$tm["NAME"]],
							"CODE" => $arItem[$tm["CODE"]],
							"DESCRIPTION" => $arItem[$tm["DESCRIPTION"]],
							"SORT" => $arItem[$tm["SORT"]],
							"PREVIEW_TEXT_TYPE" => "html",
							"DETAIL_TEXT_TYPE" => "html",
							"PREVIEW_TEXT" => $arItem[$tm["PREVIEW_TEXT"]],
							"DETAIL_TEXT" => $arItem[$tm["DETAIL_TEXT"]],
							"PROPERTY_VALUES" => array("keywords" => $arItem["metakey"], "description" => $arItem["metadesc"]),
							"ACTIVE_FROM" => date("d.m.Y h:i:s", strtotime($arItem[$tm["DATE_ACTIVE_FROM"]])),
							"DATE_CREATE" => date("Y-m-d h:i:s", strtotime($arItem[$tm["DATE_CREATE"]])),
							"XML_ID" => $arItem['id'],
							);
						$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=> $res1["IBLOCK_ID"],"CODE" => $arItem[$tm["CODE"]]))->GetNext();
						$sec = new CIBlockElement;
						
						if(!$res)
							$id = $sec->Add($arResult);			
						break;
					default:
						break;
				}

			}

			
		}
	}
}
iblock_migrate($TEMPLATE, 0, 0, $arResult["prefix"], $link);

	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
?>
