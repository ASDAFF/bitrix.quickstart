<?
function GetDeliveries()
{
//  старой ядро не правильно делает выборку
//	$db_dtype = CSaleDelivery::GetList(
//	    array(
//	            "SORT" => "ASC",
//	            "NAME" => "ASC"
//	        ),
//	    array(
//	            "ACTIVE" => "Y"
//	        ),
//	    false,
//	    false,
//	    array()
//	);
//	$arrDeliveries = [];
//	while ($ar_dtype = $db_dtype->Fetch())
//	{
//		$arrDeliveries[ $ar_dtype['ID'] ] = $ar_dtype;
//	}

    $dbResultList = \Bitrix\Sale\Delivery\Services\Table::GetList( array(
            'order' => array("SORT" => "ASC"),
            'filter' => array('ACTIVE'=>'Y', 'PARENT_ID'=>0),
            'select' => array('*')
        )
    );
    
    $arrDeliveries = [];
    while ($arResult = $dbResultList->fetch())
        $arrDeliveries[$arResult['ID']] = $arResult;

	if( is_array($arrDeliveries) )
		return $arrDeliveries;		
}
?>
