<? 	
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php"); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.media/prolog.php"); 
?>
<? IncludeModuleLangFile( __FILE__); ?>
<?if (isset($_REQUEST['go']))
{
	
	CModule::IncludeModule("fileman");
	CMedialib::Init();
	$n = microtime(1);
	$ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', "ML_TYPE"=>1)));
	
	$bs = new CIBlockSection;
	$el = new CIBlockElement;
	$MEDIA_IBLOCK_ID = MEDIALIBRARY_IBLOCK_ID;

	foreach ($ar as $col)
	{
	$section_res = CIBlockSection::GetList(
	Array("SORT"=>"ASC"),
	Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$MEDIA_IBLOCK_ID, 'UF_COLLECTION'=>$col["ID"]), false, array("ID", "NAME",'UF_COLLECTION')
	);

	if (!($s = $section_res->GetNext()))
		{
		
		$PARENT_SECTION_ID = false;
		if ($col["PARENT_ID"]>0)
		
			{	$parent_section_res = CIBlockSection::GetList(
				Array("SORT"=>"ASC"),
				Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$MEDIA_IBLOCK_ID, 'UF_COLLECTION'=>$col["PARENT_ID"]), false, array("ID", "NAME",'UF_COLLECTION')
				);
				if ($parent_id = $parent_section_res->GetNext())
					$PARENT_SECTION_ID = $parent_id["ID"];
				
			}

		$arFields = Array(
		  "ACTIVE" => 'Y',
		  "IBLOCK_SECTION_ID" => $PARENT_SECTION_ID,
		  "IBLOCK_ID" => $MEDIA_IBLOCK_ID,
		  "NAME" => $col["NAME"],
		  "SORT" => 100,
			'UF_COLLECTION'=>$col["ID"]
		  );


		  $SECTION_ID = $bs->Add($arFields);
		  $res = ($PARENT_SECTION_ID>0);
		  if(!$res)
		  echo $bs->LAST_ERROR;
	  }
	  else $SECTION_ID = $s["ID"];
		$ar_item = CMedialibItem::GetList(array('arCollections'=>array($col["ID"])));
		foreach ($ar_item as $it)
			{	$found = CIBlockElement::GetList(
												 Array("SORT"=>"ASC"),
												 Array('IBLOCK_ID'=>$MEDIA_IBLOCK_ID,'PROPERTY_MEDIA_ID'=>$it["ID"]),
												 false,
												 false,
												 Array('ID','IBLOCK_ID', 'PROPERTY_MEDIA_ID')
												);
				
				if (!($res_found=$found->GetNext()))
				{
			
					$PROP = array("MEDIA_ID"=>$it["ID"], "REAL_PICTURE"=>CFile::MakeFileArray($it["PATH"]));
						$arLoadProductArray = Array(
					 
					  "IBLOCK_SECTION_ID" =>$SECTION_ID,          
					  "IBLOCK_ID"      => $MEDIA_IBLOCK_ID,
					  "PROPERTY_VALUES"=> $PROP,
					  "NAME"           => $it["NAME"],
					  "ACTIVE"         => "Y",            
					  "PREVIEW_PICTURE" => CFile::MakeFileArray($it["PATH"]),
					  "DETAIL_PICTURE" => CFile::MakeFileArray($it["PATH"])
					  );

					if(!($PRODUCT_ID = $el->Add($arLoadProductArray)))
					  echo "Error: ".$el->LAST_ERROR;
				}	
				
			}  
	  
	} // end foreach step 1
	$arr_del = array();
	$ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', "ML_TYPE"=>1)));
	
	$found = CIBlockElement::GetList( Array("SORT"=>"ASC"),
												 Array('IBLOCK_ID'=>$MEDIA_IBLOCK_ID),
												 false,
												 false,
												 Array('ID','IBLOCK_ID', 'PROPERTY_MEDIA_ID')
												);
	while ($res_found = $found->GetNext())
	{$media_id = $res_found["PROPERTY_MEDIA_ID_VALUE"];
	$ar_item = CMedialibItem::GetList(array("id"=>$media_id));
	if (count($ar_item)==0)
		$arr_del[] = $res_found["ID"];
	}
	$ib = new CIblockElement;
	foreach ($arr_del as $del_id)
		$ib->Delete($del_id);
		
} 
else 
{
?>
<form action="" method='post' name="form1" id="form1">
<input type="submit" name = "go" value="<?echo GetMessage("MM_SYNC")?>" >
</form>
<?
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); 
?>


