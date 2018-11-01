<?php
$res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array(
                '=ID' => $arEntity["UF"]['UF_REG_PLACE']['VALUE'],
                '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ),
            'select' => array(
                'I_ID' => 'PARENTS.ID',
                'I_NAME_RU' => 'PARENTS.NAME.NAME',
                'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
                'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
            ),
            'order' => array(
                'PARENTS.DEPTH_LEVEL' => 'asc'
            )
        ));
        $arrAdressData=[];
        while($item = $res->fetch())
        {
            $arrAdressData[]=$item;
        }

