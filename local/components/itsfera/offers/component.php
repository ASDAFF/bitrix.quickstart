<?
	//в компоненте заменен  MHT\Product на MHT\MobileCatalog
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$page = intval($_GET['page']);
	if(!$page){
		$page = 1;
	}

	$CATSORT = MHT\CatalogSort::getInstance();

	$CATSORT->setListId($arParams['TYPE']);
	if($CATSORT->isDefault()){
		$CATSORT->set(3); // По названию
	}

    $sort_f = !empty($arParams['SORT_BY'])?$arParams['SORT_BY']:$CATSORT->get('field');
    $sort_o = !empty($arParams['SORT_ORDER'])?$arParams['SORT_ORDER']:$CATSORT->get('order');
	$per = MHT\CatalogPerPageX::getInstance()->get();
	
	if(isset($arParams['TYPE'])){
		$type = $arParams['TYPE'].'=Y';
	}
	elseif(isset($arParams['BRAND'])){
		$type = 'CML2_MANUFACTURER_VALUE='.$arParams['BRAND'];
	}
	else{
		LocalRedirect('/brands/', '404 File Not Found');
	}

	$ids = array();
	$idnames = array();
	$idcodes = array();
	
	$additionalLinks = array();

	MHT::eachCatalogIBlock(function($iblock) use (&$ids, &$idsbf, &$idnames, &$idcodes, &$arParams, &$additionalLinks){
		$id = $iblock['ID'];
		$ibname = $iblock["NAME"];
		$ibcode = $iblock["CODE"];


        // проверка есть ли товары в текущем инфоблоке для вывода ссылок на ИБ
        /*
        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID" => $id, "PROPERTY_CML2_MANUFACTURER_VALUE" => $arParams['BRAND'], "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($res->SelectedRowsCount() > 0)
        {
            $idsbf[] = $id;
            $idnames[] = $ibname;
            $idcodes[] = $ibcode;
            return;
        }

        /*
		$properties = CIBlockProperty::GetList(
            Array("sort"=>"asc", "name"=>"asc"),
            Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$id, "CODE"=>"SAYT_BLACK_FRIDAY_TOVAR")
        );
		if ($prop_fields = $properties->GetNext())
		{
		  
			$idsbf[] = $id;
			$idnames[] = $ibname;
			$idcodes[] = $ibcode;
			return;
		
		}
        */
	
		if(empty($arParams['BRAND'])){
			$ids[] = $id;
			return;
		}
		
		$p = WP::bit(array(
			'of' => 'list-values',
			'f' => array(
				'iblock' => $id,
				'CODE' => 'CML2_MANUFACTURER',
				'VALUE' => $arParams['BRAND']
			),
			'one' => 'f'
		));
		if(!empty($p)){
			$ids[] = $id;
						
			$additionalLinks[] = array(
				WP::getSmartFilterName(array(
					'id' => $p['ID'],
					'property' => $p['PROPERTY_ID'],
					'full' => $iblock['LIST_PAGE_URL'].'?'
				)),
				$iblock['NAME']
			);
		}
	});
		
	$arResult = WP::cache(
		array(
			'c_offers',
			$page,
			$per,
			$sort_f,
			$sort_o,
			$type,
			$arParams['SECTION_ID'],
			$_REQUEST['IB_ID'],			
			$arParams['SHOW_SECTIONS'],
			$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
			WP::lastUpdate($ids)
		),
		WP::time(3, 'm'),
		function() use ( &$page, &$per, &$sort_f, &$sort_o, &$type, &$arParams, &$ids, &$idsbf, &$idnames, &$idcodes, &$additionalLinks){
			$products = array();
			
			if(in_array($arParams['TYPE'],array("SAYT_NOVINKA","SAYT_NA_GLAVNUYU","SAYT_AKTSIONNYY_TOVAR","SAYT_BLACK_FRIDAY_TOVAR"))){
				$p = array(
					$arParams['TYPE']."_VALUE" => "Да"
				);
			}else{
				$p = empty($arParams['TYPE'])
					? array(
						'CML2_MANUFACTURER_VALUE' => html_entity_decode($arParams['BRAND'], ENT_QUOTES, 'UTF-8'),
					)
					: array(
						$arParams['TYPE'] => 'Y'
					);
			}
			
			$f = array(
				'IBLOCK_ID' => $_REQUEST["IB_ID"] ? $_REQUEST["IB_ID"] : $idsbf,
				'ACTIVE' => 'Y'
				
			);
			
			if($arParams['SHOW_SECTIONS'] == "Y" && !empty($arParams['SECTION_ID'])){
				$f['SECTION_ID'] = $arParams['SECTION_ID'];
				$f['INCLUDE_SUBSECTIONS'] = 'Y';
			}
			
			$products = WP::bit(array(
				'of' => 'elements',
				'f' => $f,
				'p' => $p,
				'nav' => array(
					'iNumPage' => $page,
					'nPageSize' => $per,
				),
				'sort' => array(
					$sort_f => $sort_o
				),
				'map' => function($d, $f, $p){
					return new MobileCatalog($f, $p);
				}
			));
			
			
			$list = WP::bit('list');

			if(isset($arParams['BRAND']) && !empty($products[0])){
				$arParams['NAME'] = $products[0]->get('brand');
			}

			$sections = array();
			if($arParams['SHOW_SECTIONS'] == "Y" && $_REQUEST["IB_ID"]){
				$rsSections = CIBlockSection::GetList(
					array('left_margin'=>'asc','sort'=>'asc'),
					array(
						//'IBLOCK_ID'=> 455,
						'IBLOCK_ID'=> $_REQUEST["IB_ID"],
						//'SECTION_ID' => $_REQUEST["SECTION_ID"] ? $_REQUEST["SECTION_ID"] : false,
						'GLOBAL_ACTIVE'=>'Y',
						'PROPERTY'=>array('!SAYT_BLACK_FRIDAY_TOVAR'=>false),
						//'CNT_ACTIVE'=>'Y',
						//'ELEMENT_SUBSECTIONS'=>'Y'
					),
					true
				);
				while($arSection = $rsSections->Fetch()){
					$sections[$arSection["ID"]] = $arSection["NAME"];
					$depths[$arSection["ID"]] = $arSection["DEPTH_LEVEL"];
					if ($arSection["IBLOCK_SECTION_ID"]) {
						$childs[$arSection["IBLOCK_SECTION_ID"]][] = $arSection["ID"];
						$parents[$arSection["ID"]] = $arSection["IBLOCK_SECTION_ID"];
					} elseif ($arSection["DEPTH_LEVEL"]==1) {
						$iblinks[$arSection["ID"]] = $arSection["IBLOCK_ID"];
					}
				}
			}
			
			return array(
				'PRODUCTS' => $products,
				'CODE' => $arParams['BRAND'],
				'NAME' => $arParams['NAME'],
				'IB_IDS' => $idsbf,
				'IB_NAMES' => $idnames,
				'IB_CODES' => $idcodes,
				'ADDITIONAL_LINKS' => $additionalLinks,
				'SECTIONS_LINKS' => $sections,
				'IB_LINKS' => $iblinks,				
				'SECTIONS_DEPTHS' => $depths,
				'SECTIONS_CHILDS' => $childs,
				'SECTIONS_PARENTS' => $parents,
				'NAV' => array(
					'CUR' => $list->NavPageNomer,
					'LAST' => $list->NavPageCount,
				)
			);
		}
	);

	$this->IncludeComponentTemplate();
?>