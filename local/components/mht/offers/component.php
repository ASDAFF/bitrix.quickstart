<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$page = intval($_GET['page']);
	if(!$page){
		$page = 1;
	}

	 // Разный кэш для страниц
	if(!$arParams['CACHE_TIME'])
		$arParams['CACHE_TIME'] = WP::time(30, 's');

	$arParams['PAGE'] = $page;
	$arParams['CACHE_PARAM_IB_ID'] = $_REQUEST['IB_ID'];
	$arParams['CACHE_PARAM_SECTION_ID'] = $_REQUEST['SECTION_ID'];

//if ($this->StartResultCache())
//{

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
        $arBrand = Itsfera::getBrandByCode($arParams['BRAND']);
        if($arBrand['IS_SECTION'] == 'Y'){
            $arParams['BRAND'] = array();
            $arSelect = Array("ID", "NAME", "CODE");
            $arFilter = Array("IBLOCK_ID"=>getIBlockIdByCode('brands'), "SECTION_ID"=>$arBrand['ID'], "ACTIVE"=>"Y");
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $arParams['BRAND'][] = toLower($arFields['NAME']);
            }
        }
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

        if($arParams['SHOW_SECTIONS'] == 'Y') {
            $properties = CIBlockProperty::GetList(
                Array("sort" => "asc", "name" => "asc"),
                Array("ACTIVE" => "Y", "IBLOCK_ID" => $id, "CODE" => $arParams['TYPE'])
            );
            if ($prop_fields = $properties->GetNext()) {
                $idsbf[] = $id;
                $idnames[] = $ibname;
                $idcodes[] = $ibcode;
                return;
            }
        }
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
			GLOBAL $USER;
            /*if($USER->IsAuthorized()){
                echo'<pre>';print_r($p);echo"</pre>";
            }*/

			$tar = $additionalLinks[] = array(
				WP::getSmartFilterName(array(
					'id' => $p['ID'],
					'property' => $p['PROPERTY_ID'],
					'full' => $iblock['LIST_PAGE_URL'].'?'
				)),
				$iblock['NAME']
			);
            /*if($USER->IsAuthorized()){
                echo'<pre>';print_r($tar);echo"</pre>";
            }*/
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
			'some_parameter',
			$arParams['SHOW_SECTIONS'],
			$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
			WP::lastUpdate($ids)
		),
		$arParams['CACHE_TIME'],
		function() use ( &$page, &$per, &$sort_f, &$sort_o, &$type, &$arParams, &$ids, &$idsbf, &$idnames, &$idcodes, &$additionalLinks){
			$products = array();

			if(in_array($arParams['TYPE'],array("SAYT_NOVINKA","SAYT_NA_GLAVNUYU","SAYT_AKTSIONNYY_TOVAR","SAYT_BLACK_FRIDAY_TOVAR"))){
				$p = array(
					$arParams['TYPE']."_VALUE" => "Да"
				);
			}else{
                if(!is_array($arParams['BRAND']))
                    $arParams['BRAND'] = html_entity_decode($arParams['BRAND'], ENT_QUOTES, 'UTF-8');
				$p = empty($arParams['TYPE'])
					? array(
						'CML2_MANUFACTURER_VALUE' => $arParams['BRAND'],
						//'CML2_MANUFACTURER_VALUE' => html_entity_decode($arParams['BRAND'], ENT_QUOTES, 'UTF-8'),
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
					return new MHT\Product($f, $p);
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
            GLOBAL $USER;
            if($arParams['SHOW_SUB_BRANDS']){
                $arSelect = Array("ID", "NAME", "CODE");
                $arFilter = Array("IBLOCK_ID"=>getIBlockIdByCode("brands"), "ACTIVE"=>"Y", "SECTION_ID"=>$arParams['BRANDS_ID']);
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while($ob = $res->GetNextElement())
                {
                    $arFields = $ob->GetFields();
                    if($arParams['BRANDS_MAIN'] != $arFields['CODE'])
                        $subBrands[] = array(
                            'NAME'=>$arFields['NAME'],
                            'CODE'=>$arFields['CODE']
                        );
                }
            }


            if ($arParams['TYPE'] == 'SAYT_NOVINKA') {

                //чистим инфоблоки
                $arIBlockIDs = Itsfera::getIBlockIDByElemProp('SAYT_NOVINKA', 'Да');
                foreach ($idsbf as $key => $val) {
                    if ( ! $arIBlockIDs[$val]) {
                        unset($idsbf[$key]);
                    }
                }

                //чистим разделы Новинки
                $arSectionNew = Itsfera::getSectionIDByElemProp('SAYT_NOVINKA', 'Да');
                foreach ($sections as $key => $val) {
                    if ( ! $arSectionNew[$key]) {
                        unset($sections[$key]);
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
				'SUBBRANDS' => $subBrands,
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
//}
?>
