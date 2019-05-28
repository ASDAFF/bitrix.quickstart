<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ( !isset($arParams['ITEMS_IDS'][0])  && Bitrix\Main\Loader::includeModule("iblock")){
    echo 'No results';
    return;
}



$arResult['PRODUCTS'] = WP::cache(
    'c_seearch_detectum_result'.var_export($arParams,true),
    WP::time(10, 's'),
    function() use ($arParams){

        /*$ids = array();
        MHT::eachCatalogIBlock(function($iblock) use (&$ids){
            $ids[] = $iblock['ID'];
        });*/


        $products = array();

        /*

        echo '<pre>';
        print_r( $ids );
        echo '</pre>';

        foreach ($ids as $iIblockId) {


            $iblockProducts = array();
            $arSel = MHT\Product::getSelect();

            $arSelect = $arSel['f'] ;
            $arFilter = Array("ID" => $arParams['ITEMS_IDS'],"IBLOCK_ID"=>$iIblockId);
            echo '<pre>';
            print_r($arFilter);
            echo '</pre>';

            $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 100), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();

                echo '<pre>';
                print_r($arFields);
                echo '</pre>';

                $arFields['PROPERTIES'] = $ob->GetProperties();


                $iblockProducts[$arFields['ID']] = new MHT\Product($arFields, $arFields['PROPERTIES']);
            }

            $products = array_merge($products,$iblockProducts);
        }*/



        //foreach($ids as $id){
            $iblockProducts = WP::bit(array(
                'of' => 'elements',
                'f' => array(
                    //'IBLOCK_ID' => $id,
                    //'ACTIVE' => 'Y',
                    'ID' => $arParams['ITEMS_IDS']
                ),
                'p' => array(
                ),
                'debug' => false, //true, //$_GET['dbg'],
                'sel' => MHT\Product::getSelect(),
                'sort' => array(
                    'ID' => 'ASC'
                ),
                'map' => function($d, $f, $p){
                    return new MHT\Product($f, $p);
                }
            ));


            $products = array_merge($products, $iblockProducts);


            $arProductsById = array();
            foreach ($products as $arOneProduct){

                $arProductsById[ $arOneProduct->fields['ID']  ] = $arOneProduct;

            }

            return $arProductsById;

        //}

        /*shuffle($products);
        $products = array_slice($products, 0, $arParams['ELEMENTS_COUNT']);*/

        return $products;
    }
);

/*echo '<pre>';
print_r($arResult['PRODUCTS']);
echo '</pre>';*/

$this->IncludeComponentTemplate();