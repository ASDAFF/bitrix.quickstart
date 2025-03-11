<?
 //save catalog product status
            $resElement = CIBlockElement::GetList(
                Array("SORT"=>"ASC"),
                Array('ID'=>$ELEMENT_ID, 'IBLOCK_ID'=>$IBLOCK_ID),
                false,
                false,
                Array('ID', 'IBLOCK_ID', 'PROPERTY_NOT_AVAILABLE_STATUS')
            )->fetch();

            if($resElement['PROPERTY_NOT_AVAILABLE_STATUS_VALUE'] == 'В наличии')
            {
                CIBlockElement::SetPropertyValuesEx(
                    $ELEMENT_ID,
                    $IBLOCK_ID,
                    array(
                        "CATALOG_STATUS" => 1453, //ID заначения из списка
                    )
                );
            }
?>
