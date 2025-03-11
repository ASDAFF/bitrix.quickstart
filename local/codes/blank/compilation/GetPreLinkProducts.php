<?
function GetPreLinkProducts( $iblockId, $elementId, $showCountElBySides )
{
	if(CModule::IncludeModule("iblock"))
    { 
		$resdb = CIBlockElement::GetList(array('ID' => 'DESC'), array(
		               'IBLOCK_ID' => $iblockId,
		               'ACTIVE' => 'Y',
		               'SECTION_GLOBAL_ACTIVE' => 'Y'),
		               false, array('nPageSize' => $showCountElBySides, 'nElementID' => $elementId),
		               array());
		
		$linkProds = [];
		while ( $res = $resdb->fetch() ) 
		{
			if( $res['ID'] !== $elementId )
			{
				$linkProds[] = (int)$res['ID'];	
			}
		}
		
		if( is_array($linkProds) )
			return $linkProds;	
	}
}


 function GetPreLinkProducts2( $IBLOCK_ID_CATALOG, $SECTION_ID, $nElementID, $nPageSize )
    {
        $res = CIBlockElement::GetList(
            array(
                'sort' => 'asc'
            ),
            array(
                'IBLOCK_ID' => $IBLOCK_ID_CATALOG,
                'ACTIVE' => 'Y',
                'SECTION_ID' =>$SECTION_ID
            ),
            false,
            array(
                'nElementID' => $nElementID,
                'nPageSize' => $nPageSize
            )
        );

        $nearElements = [];
        while ($arElem = $res->GetNext())
        {
            if ($arElem['ID'] != $nElementID)
                $nearElements[] = (int)$arElem['ID'];
        }

        if(count($nearElements)==4)
        {
            return $nearElements;
        }
        else
        {
            $res = CIBlockElement::GetList(
                array(
                    'sort' => 'asc'
                ),
                array(
                    'IBLOCK_ID' => $IBLOCK_ID_CATALOG,
                    'ACTIVE' => 'Y',
                    'SECTION_ID' =>$SECTION_ID
                ),
                false,
                array(
                    'nElementID' => $nElementID,
                    'nPageSize' => 10
                )
            );

            $nearElements2 = [];
            while ($arElem = $res->GetNext())
            {
                if ($arElem['ID'] != $nElementID)
                    $nearElements2[] = (int)$arElem['ID'];
            }
            return $nearElements2;
        }
    }




?>
