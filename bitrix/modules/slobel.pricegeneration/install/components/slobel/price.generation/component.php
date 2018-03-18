<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 0;

if($this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	if($_POST['gen']=='Y'){
	
		$res = CIBlockSection::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "NAME"), false);
		while($ob = $res->GetNextElement()){ $arSection[] = $ob->GetFields(); }
	
		$arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"]);

		$arSelect = array("ID", "NAME", "IBLOCK_SECTION_ID", "CATALOG_GROUP_".$arParams['PRICE_CODE']);
		$arProperties=array();
		if($arParams["FIELD_CODE"]){
			foreach ($arParams["FIELD_CODE"] as $data){
				$arProperties[] = $data;
			}
		}
	
		if($arParams["PROPERTY_CODE"]){
			foreach ($arParams["PROPERTY_CODE"] as $data){
				$arSelect[] = "PROPERTY_".$data;
				$arProperties[] = "PROPERTY_".$data."_VALUE";
			}
		}
		
		$arFieldsName=array();
		$arParams["FIELD_CODE_NAME"]=array('ID'=>'ID', 'NAME'=>GetMessage("NAME"));
		foreach($arParams["FIELD_CODE"] as $data){
			if(array_key_exists($data, $arParams["FIELD_CODE_NAME"])) $arFieldsName[]=$arParams["FIELD_CODE_NAME"][$data];
		}
	
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
		while ($prop_fields = $properties->GetNext())
		{
			if(in_array($prop_fields['CODE'], $arParams["PROPERTY_CODE"])) $arFieldsName[]=$prop_fields['NAME'];
		}
		if(CModule::IncludeModule("catalog")){
			$arFieldsName[]=GetMessage("PRICE");
			$arFieldsName[]=GetMessage("CURRENCY");
		}
		$addcolumns=0;
		if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] != "Y"){
			$addcolumns+=2;
			$arFieldsName[]=GetMessage("SECTION_ID");
			$arFieldsName[]=GetMessage("SECTION_NAME");
		}
		
		if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] == "Y"){
			$max=0;
			$db_list = CIBlockSection::GetList(Array(), array('IBLOCK_ID'=>$arParams["IBLOCK_ID"]), true, array('ID', 'DEPTH_LEVEL'));
			while($ar_result = $db_list->GetNext())
				$max=($ar_result['DEPTH_LEVEL']>$max)?$ar_result['DEPTH_LEVEL']:$max;
		
			for($i=1; $i<=$max; $i++){
				$addcolumns+=2;
				$arFieldsName[]=GetMessage("SECTION_ID")." #".$i;
				$arFieldsName[]=GetMessage("SECTION_NAME")." #".$i;
			}
		}
	
		if($arParams["CHECK_STOCK"] == "Y")	$arFilter[">CATALOG_QUANTITY"] = 0;
		if($arParams["CHECK_DATES"] == "Y") $arFilter["ACTIVE"] = "Y";
	
		$pricetrue=0;
	
		$multiSeparator=$arParams['MULTI_SEPARATOR'];
		if(!$arParams['MULTI_SEPARATOR'])$multiSeparator=', ';
		
		$countElement=0;
		// select all elements of the information unit
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement()){
			$arFields = $ob->GetFields();
			foreach($arSection as $keySection => $valueSection){
				if($valueSection['ID']==$arFields['IBLOCK_SECTION_ID']){
					$arDEPTH='';
					if($arParams["CHECK_PARENT"] == "Y" && $arParams["CHECK_SECTION"] == "Y"){
						$countDEPTH=0;
						$nav = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $valueSection['ID'], array('ID','IBLOCK_SECTION_ID','NAME','DEPTH_LEVEL'));
						while ($arNav=$nav->GetNext()){
							if($arParams["COLS_SECTION"] == "Y"){
								$countDEPTH++;
								$arDEPTH[$countDEPTH]['ID']=$arNav['ID'];
								$arDEPTH[$countDEPTH]['NAME']=$arNav['NAME'];
							}
							else
								(!empty($arDEPTH)) ? $arDEPTH.=' > '.$arNav['NAME'] : $arDEPTH.=$arNav['NAME'];
						}
						
					}
					else $arDEPTH=$valueSection['NAME'];
					
					$iProperty = -1;
					
					if($arParams["CHECK_SECTION"] != "Y") $valueSection['ID']=0;
					else $arResult["PROPERTIES"][$valueSection['ID']]['NAME']=$arDEPTH;
					
					if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] != "Y"){
						$sectionID=$valueSection['ID'];
						$sectionName=$arResult["PROPERTIES"][$valueSection['ID']]['NAME'];
						unset($arResult["PROPERTIES"][$valueSection['ID']]['NAME']);
						$valueSection['ID']=0;
					}
					if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] == "Y"){
						unset($arResult["PROPERTIES"][$valueSection['ID']]['NAME']);
						$valueSection['ID']=0;
					}
					
					foreach ($arProperties as $Property){
						$iProperty++;
						if($arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] && $arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty]!=$arFields[$Property]){
							$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] .= $multiSeparator.$arFields[$Property];
							$countElement--;
						}else
							$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $arFields[$Property];
					}
					if($arFields['CATALOG_PRICE_'.$arParams['PRICE_CODE']])$pricetrue=2;
					if(CModule::IncludeModule("catalog")){
						$iProperty++;
						$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $arFields['CATALOG_PRICE_'.$arParams['PRICE_CODE']];
						$iProperty++;
						if($arParams['CURRENCY']!='iblock') $arFields['CATALOG_CURRENCY_'.$arParams['PRICE_CODE']]=$arParams['CURRENCY'];
						if($arParams['CURRENCY']=='main') $arFields['CATALOG_CURRENCY_'.$arParams['PRICE_CODE']]=$arParams['MAIN_CURRENCY'];
						$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $arFields['CATALOG_CURRENCY_'.$arParams['PRICE_CODE']];
					}
					if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] != "Y"){
						$iProperty++;
						$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $sectionID;
						$iProperty++;
						$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $sectionName;
					}
					if($arParams["COLS_SECTION"] == "Y" && $arParams["CHECK_PARENT"] == "Y"){
						foreach($arDEPTH as $key => $val){
							$iProperty++;
							$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $val['ID'];
							$iProperty++;
							$arResult["PROPERTIES"][$valueSection['ID']][$arFields['ID']][$iProperty] = $val['NAME'];
						}
					}
					break;
				}
			}
			$arResult["COUNT_ELEMENT"]=++$countElement;
		}
		
		foreach($arResult["PROPERTIES"] as $key => $val){
			if(empty($val))
				unset($arResult["PROPERTIES"][$key]);
		}
		// line that begins displayed price itself
		$startCatalog=4;
		$startNameCatalog=$startCatalog-1;
		
	if($arParams['COLS_SECTION']=='Y' && $arParams['FORMATED_FILE']!='csv' && $arParams['HEADER']=='Y')
			$startCatalog--;
	
	if($arParams['NAME_COLS']!='Y')
		$startCatalog--;
		

		$arResult["COUNT_ROW"] = $arResult["COUNT_ELEMENT"] + count($arResult["PROPERTIES"]) + $startCatalog-1;
		$arResult["COUNT_COLUMN"] = count($arProperties)+$pricetrue+$addcolumns;
		
		$this->SetResultCacheKeys(array(
				"PROPERTIES",
				"COUNT_ELEMENT", // the number of columns in the table
				"COUNT_COLUMN", // number of columns
				"COUNT_ROW"
		));
	
		(!empty($arParams['COLOR']))?$color=substr($arParams['COLOR'], 1):$color='c0c0c0';
		(!empty($arParams['FONT']))?$font=$arParams['FONT']:$font='Arial';
		(!empty($arParams['FONT_SIZE']))?$fontsize=$arParams['FONT_SIZE']:$fontsize='10';
		(!empty($arParams['FONT_COLOR']))?$fontcolor=substr($arParams['FONT_COLOR'], 1):$fontcolor='00000';
		
		(!empty($arParams['FORMATED_FILE_DIR']))?$arResult['PATH']=$arParams['FORMATED_FILE_DIR']:$arResult['PATH']='/upload/';
		(!empty($arParams['FORMATED_FILE_NAME']))?$arResult['FILE_NAME']=$arParams['FORMATED_FILE_NAME']:$arResult['FILE_NAME']='price';
		(!empty($arParams['FORMATED_FILE']))?$arResult['FILE_EXPANSION']=$arParams['FORMATED_FILE']:$arResult['FILE_EXPANSION']='xlsx';
		(!empty($arParams['CSV_SEPARATOR']))?$csvsep=$arParams['CSV_SEPARATOR']:$csvsep=';';

		
		$sectionSort=array();
		$sectionSortParams=($arParams['SECTION_SORT']=='ASC')?SORT_ASC:SORT_DESC;
		
		if($arParams['SECTION_SORT_BY']=='NAME'){
			
			foreach ($arResult["PROPERTIES"] as $key => $row){
				$arResult["PROPERTIES"]["\"".$key."\""]  = $arResult["PROPERTIES"][$key];
				unset($arResult["PROPERTIES"][$key]);
			}
			
			foreach ($arResult["PROPERTIES"] as $key => $row)
				$sectionSort[$key]  = $row['NAME'];

			array_multisort($sectionSort, $sectionSortParams, $arResult["PROPERTIES"]);
		}
		elseif($arParams['SECTION_SORT']=='ASC'){
			ksort($arResult["PROPERTIES"], SORT_NUMERIC);
		}
		elseif($arParams['SECTION_SORT']=='DESC'){
			krsort($arResult["PROPERTIES"], SORT_NUMERIC);
		}
		
		
		$elementSortParams=($arParams['ELEMENT_SORT']=='ASC')?SORT_ASC:SORT_DESC;
		foreach ($arResult["PROPERTIES"] as $key =>$val){
			$elementSort=array();
			foreach($val as $keytwo => $valtwo){
				if($keytwo!='NAME'){
					$arResult["PROPERTIES"][$key]["\"".$keytwo."\""]=$arResult["PROPERTIES"][$key][$keytwo];
					unset($arResult["PROPERTIES"][$key][$keytwo]);
				}
			}
			foreach($val as $keytwo => $valtwo){
				if($keytwo!='NAME'){
					$elementSort["\"".$keytwo."\""]=$valtwo[$arParams['ELEMENT_SORT_BY']];
				}
			}

			$name=$arResult["PROPERTIES"][$key]['NAME'];
			unset($arResult["PROPERTIES"][$key]['NAME']);
			array_multisort($elementSort, $elementSortParams, $arResult["PROPERTIES"][$key]);
			if(!empty($name))$arResult["PROPERTIES"][$key]['NAME']=$name;
		}
		
		require_once('classes/PHPExcel.php');
		
		switch ($arResult['FILE_EXPANSION']) {
			case 'xls':
				require_once('classes/PHPExcel/Writer/Excel5.php');
				break;
			case 'xlsx':
				require_once('classes/PHPExcel/Writer/Excel2007.php');
				break;
			case 'htm':
				require_once('classes/PHPExcel/Writer/HTML.php');
				break;
			case 'csv':
				require_once('classes/PHPExcel/Writer/CSV.php');
				break;
/*  			case 'pdf':
				$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
				$rendererLibrary = 'dompdf';
				$rendererLibraryPath = $_SERVER["DOCUMENT_ROOT"].$componentPath.'/classes/PHPExcel/Writer/'.$rendererLibrary;
				break; */
		}
		
		$priceTitle=GetMessage("PRICE_TITLE").date("j.n.Y");
		
		if(LANG_CHARSET=="windows-1251")
			$priceTitle=$APPLICATION->ConvertCharset($priceTitle, LANG_CHARSET, "UTF-8");

		// create a class for working with Excel, select the first sheet
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
	
		// configure caching and execution time
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize ' => '256MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	 
		set_time_limit(0);
		ini_set("max_execution_time", "0");
		
		// set the title sheet
		$sheet->setTitle($priceTitle);
	
		// Detection alphabetic character column by its index
		function cellsToMergeByColsRow($start = NULL, $end = NULL, $row = NULL){
			$start = PHPExcel_Cell::stringFromColumnIndex($start);
			$end = PHPExcel_Cell::stringFromColumnIndex($end);
			$merge = "$start{$row}:$end{$row}";
			return $merge;
		}
	
		// preliminary cell formatting
		$styleArray = array(
				'font'  => array(
						'bold'  => false,
						'color' => array('rgb' => $fontcolor),
						'size'  => $fontsize,
						'name'  => $font
				));
		$sheet->getDefaultStyle()->applyFromArray($styleArray);
		/* $sheet->getDefaultStyle()->getFont()->setName($font)
										->setSize($fontsize); */
		$sheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
 		// formatting of the cell header and set the value
		
		if($arParams['FORMATED_FILE']!='csv' && $arParams['HEADER']=='Y'){
			$sheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setRGB($color);
			$sheet->getStyle('A1')->getFont()->setBold(true);
			$sheet->mergeCells(cellsToMergeByColsRow(0, $arResult["COUNT_COLUMN"]-1, 1));
			$sheet->getRowDimension(1)->setRowHeight(16);
			$sheet->setCellValue("A1", $priceTitle);
		}
		else {
			$startCatalog-=3;
			$startNameCatalog-=2;
		}
		
		if($arParams['NAME_COLS']=='Y'){
		// formatting and filling the column names
		$sheet->getStyle(cellsToMergeByColsRow(0, $arResult["COUNT_COLUMN"]-1, $startNameCatalog))->getFont()->setBold(true);
		$elementProperty=0;
	 	foreach(range(PHPExcel_Cell::stringFromColumnIndex(0), PHPExcel_Cell::stringFromColumnIndex($arResult["COUNT_COLUMN"]-1)) as $charColumn){
	 		if($arFieldsName[$elementProperty]==GetMessage("NAME")){
	 			$sheet->getStyle($charColumn.$startNameCatalog.':'.$charColumn.$arResult["COUNT_ROW"])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	 		}
	 		if(LANG_CHARSET=="windows-1251")
	 			$arFieldsName[$elementProperty]=$APPLICATION->ConvertCharset($arFieldsName[$elementProperty], LANG_CHARSET, "UTF-8");
			$sheet->setCellValue($charColumn.$startNameCatalog, htmlspecialchars_decode($arFieldsName[$elementProperty]));
			$elementProperty++;
		} 
		};
	
		// fill the table information block elements
		$arElement=array();
		if($arResult["PROPERTIES"]){
		foreach($arResult["PROPERTIES"] as $keyResult => $valueResult){
			if($arParams['CHECK_SECTION']=='Y' && $arParams['COLS_SECTION']!='Y' && $arParams['FORMATED_FILE']!='csv'){
				$sheet->mergeCells(cellsToMergeByColsRow(0, $arResult["COUNT_COLUMN"]-1, $startCatalog));
				$sheet->duplicateStyle($sheet->getStyle('A1'), 'A'.$startCatalog);
				if(LANG_CHARSET=="windows-1251")
					$valueResult['NAME']=$APPLICATION->ConvertCharset($valueResult['NAME'], LANG_CHARSET, "UTF-8");
				$sheet->setCellValue("A".$startCatalog, htmlspecialchars_decode($valueResult['NAME']));
			}
			unset($arResult["PROPERTIES"][$keyResult]['NAME']);
			foreach($arResult["PROPERTIES"][$keyResult] as $keyResultElemet => $valueResultElemet){
				$startCatalog++;
				$elementProperty=0;
				foreach(range(PHPExcel_Cell::stringFromColumnIndex(0), PHPExcel_Cell::stringFromColumnIndex($arResult["COUNT_COLUMN"]-1)) as $charColumn){
					if(!$valueResultElemet[$elementProperty])$valueResultElemet[$elementProperty]=$arParams['NULL'];
					if(LANG_CHARSET=="windows-1251")
						$valueResultElemet[$elementProperty]=$APPLICATION->ConvertCharset($valueResultElemet[$elementProperty], LANG_CHARSET, "UTF-8");
					$sheet->setCellValue($charColumn.$startCatalog, htmlspecialchars_decode($valueResultElemet[$elementProperty]));
					$elementProperty++;
				}
			}
			if($arParams['CHECK_SECTION']=='Y' && $arParams['COLS_SECTION']!='Y' && $arParams['FORMATED_FILE']!='csv')$startCatalog++;
		}
		}
	
		// install automatic width for columns
		foreach(range(PHPExcel_Cell::stringFromColumnIndex(0), PHPExcel_Cell::stringFromColumnIndex($arResult["COUNT_COLUMN"]-1)) as $charColumn){
			$sheet->getColumnDimension($charColumn)->setAutoSize(true);
		} 
	
		// write to a file
		switch ($arResult['FILE_EXPANSION']) {
			case 'xls':
				$objWriter = new PHPExcel_Writer_Excel5($xls);
				break;
			case 'xlsx':
				$objWriter = new PHPExcel_Writer_Excel2007($xls);
				break;
			case 'htm':
				$objWriter = new PHPExcel_Writer_HTML($xls);
				break;
			case 'csv':
				$objWriter = new PHPExcel_Writer_CSV($xls);
				$objWriter->setDelimiter($csvsep);
				break;
/* 			case 'pdf':
				if(!PHPExcel_Settings::setPdfRenderer($rendererName,$rendererLibraryPath)){
					die(
						'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
						'<br />' .
						'at the top of this script as appropriate for your directory structure'
						);
					}
				$objWriter = new PHPExcel_Writer_PDF($xls);	
				break; */
		}

		$objWriter->save($_SERVER['DOCUMENT_ROOT'].$arResult['PATH'].$arResult['FILE_NAME'].'.'.$arResult['FILE_EXPANSION']);  
	
		$arResult['RESULT']='Y';
	}
	
 	$this->IncludeComponentTemplate();
}?>