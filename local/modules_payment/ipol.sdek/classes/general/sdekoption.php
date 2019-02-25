<?
IncludeModuleLangFile(__FILE__);

class sdekOption extends sdekHelper{

    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                Авторизация
    == auth ==  == logoff ==  == authConsolidation ==  == callAccounts ==  == newAccount ==  == checkAuth ==  == optionDeleteAccount ==  == deleteAccount ==  == optionMakeAccDefault ==  == makeAccDefault ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

    function auth($params){
        if(!$params['login'] || !$params['password'])
            die('No auth data');
        if(!class_exists('CDeliverySDEK'))
            die('No main class founded');
        sdekdriver::$MODULE_ID;
        if(!function_exists('curl_init'))
            die(GetMessage("IPOLSDEK_AUTH_NOCURL"));

        $resAuth = self::checkAuth($params['login'],$params['password']);
        if($resAuth['success']){
            sqlSdekLogs::Add(array('ACCOUNT' => $params['login'],'SECURE' => $params['password']));
            $lastCheck = sqlSdekLogs::Check($params['login']);
            COption::SetOptionString(self::$MODULE_ID,'logged',$lastCheck);
            if($lastCheck){
                RegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "sdekdriver", "onEpilog");
                RegisterModuleDependences("main", "OnEndBufferContent", self::$MODULE_ID, "CDeliverySDEK", "onBufferContent");
                RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliverySDEK", "pickupLoader",900);
                RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", self::$MODULE_ID, "CDeliverySDEK", "loadComponent",900);
                RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::$MODULE_ID, "sdekdriver", "orderCreate"); // создание заказа
                RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", self::$MODULE_ID, "CDeliverySDEK", "checkNalD2P"); // проверка платежных систем
                RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliverySDEK", "checkNalP2D"); // проверка платежных систем
                RegisterModuleDependences("sale", "OnSaleComponentOrderShowAjaxAnswer", self::$MODULE_ID, "CDeliverySDEK", "onAjaxAnswer");

                // печать
                RegisterModuleDependences("main", "OnAdminListDisplay", self::$MODULE_ID, "sdekOption", "displayActPrint");
                RegisterModuleDependences("main", "OnBeforeProlog", self::$MODULE_ID, "sdekOption", "OnBeforePrologHandler");

                CAgent::AddAgent("sdekOption::agentUpdateList();", self::$MODULE_ID);//обновление листов
                CAgent::AddAgent("sdekOption::agentOrderStates();",self::$MODULE_ID,"N",1800);//обновление статусов заказов

                $path = COption::GetOptionString('sale','delivery_handles_custom_path','/bitrix/php_interface/include/sale_delivery/');
                if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path."delivery_sdek.php"))
                    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::$MODULE_ID."/install/delivery/", $_SERVER["DOCUMENT_ROOT"].$path, true, true);

                echo "G".GetMessage('IPOLSDEK_AUTH_YES');
            }else
                echo GetMessage('IPOLSDEK_AUTH_NO')." ".GetMessage('IPOLSDEK_AUTH_NO_BD');
        }
        else{
            $retStr=GetMessage('IPOLSDEK_AUTH_NO');
            foreach($resAuth as $erCode => $erText)
                $retStr.=self::zaDEjsonit($erText." (".$erCode."). ");

            echo $retStr;
        }
    }

    function logoff(){
        COption::SetOptionString(self::$MODULE_ID,'logged',false);
        sqlSdekLogs::clear();
        CAgent::RemoveModuleAgents(self::$MODULE_ID);
        UnRegisterModuleDependences("main", "OnEpilog", self::$MODULE_ID, "sdekdriver", "onEpilog");
        UnRegisterModuleDependences("main", "OnEndBufferContent", self::$MODULE_ID, "CDeliverySDEK", "onBufferContent");
        UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliverySDEK", "pickupLoader");
        UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", self::$MODULE_ID, "CDeliverySDEK", "loadComponent");
        UnRegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", self::$MODULE_ID, "CDeliverySDEK", "noPVZNotConverted");
        UnRegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", self::$MODULE_ID, "CDeliverySDEK", "noPVZConverted");

        UnRegisterModuleDependences("main", "OnAdminListDisplay", self::$MODULE_ID, "sdekOption", "displayActPrint");
        UnRegisterModuleDependences("main", "OnBeforeProlog", self::$MODULE_ID, "sdekOption", "OnBeforePrologHandler");

        UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::$MODULE_ID, "sdekdriver", "orderCreate");
        UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", self::$MODULE_ID, "CDeliverySDEK", "checkNalD2P");
        UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, "CDeliverySDEK", "checkNalP2D");
    }

    function authConsolidation(){
        if(!COption::GetOptionString(self::$MODULE_ID,'logged',false))
            return;
        sqlSdekLogs::Add(array('ACCOUNT'=>COption::GetOptionString(self::$MODULE_ID,'logSDEK',false),'SECURE'=>COption::GetOptionString(self::$MODULE_ID,'pasSDEK',false)));
        $id = sqlSdekLogs::Check(COption::GetOptionString(self::$MODULE_ID,'logSDEK',false));
        COption::SetOptionString(self::$MODULE_ID,'logged',$id);
    }

    function callAccounts(){
        $acList = sqlSdekLogs::getAccountsList(true);
        $default = COption::GetOptionString(self::$MODULE_ID,'logged',false);
        foreach($acList as $id => $account){
            $acList[$id] = array(
                'account' => $account,
                'default' => ($id == $default)
            );
        }
        echo json_encode(self::zajsonit($acList));
    }

    function newAccount($params){
        $resAuth = self::checkAuth($params['ACCOUNT'],$params['PASSWORD']);
        if($resAuth['success']){
            $arRequest = array('ACCOUNT' => $params['ACCOUNT'],'SECURE' => $params['PASSWORD'],'ACTIVE'=>'Y','LABEL'=>self::zaDEjsonit($params['LABEL']));
            $arReturn = array('result' => 'ok');
            $id = sqlSdekLogs::Check($params['ACCOUNT']);
            if($id){
                sqlSdekLogs::Update($id,$arRequest);
                $arReturn['text'] = GetMessage('IPOLSDEK_AUTH_UPDATE');
            }else
                sqlSdekLogs::Add($arRequest);
        }else{
            $retStr = GetMessage('IPOLSDEK_AUTH_NO');
            foreach($resAuth as $erCode => $erText)
                $retStr.=self::zaDEjsonit($erText." (".$erCode."). ");
            $arReturn = array(
                'result' => 'error',
                'text'	 => $retStr
            );
        }

        echo json_encode(self::zajsonit($arReturn));
    }

    function checkAuth($account,$password){
        CDeliverySDEK::$sdekCity   = 44;
        CDeliverySDEK::$sdekSender = 44;
        CDeliverySDEK::setOrder(array('GABS'=>array(
            "D_L" => 20,
            "D_W" => 30,
            "D_H" => 20,
            "W"   => COption::GetOptionString(self::$MODULE_ID,'weightD',1000) / 1000
        )));

        CDeliverySDEK::setAuth($account,$password);
        $resAuth = CDeliverySDEK::calculateDost(136);
        if(!$resAuth['success'])
            $resAuth = CDeliverySDEK::calculateDost(10);

        return $resAuth;
    }

    function optionDeleteAccount($params){
        echo json_encode(self::zajsonit(self::deleteAccount($params['ID'])));
    }

    function deleteAccount($id){
        $arReturn = array('result' => 'error', 'text' => '');
        if(!sqlSdekLogs::getById($id))
            $arReturn['text'] = GetMessage('IPOLSDEK_AUTH_NO_EXIST');
        else{
            sqlSdekLogs::setActive($id,'N');
            $curAccs = sqlSdekLogs::getAccountsList();
            if(count($curAccs)){
                if($id == COption::GetOptionString(self::$MODULE_ID,'logged',false)){
                    reset($curAccs);
                    COption::SetOptionString(self::$MODULE_ID,'logged',key($curAccs));
                    $arReturn['result'] = 'collapse';
                }else
                    $arReturn['result'] = 'ok';
            }else{
                COption::SetOptionString(self::$MODULE_ID,'logged',false);
                $arReturn['result'] = 'collapse';
            }
        }
        return $arReturn;
    }

    function optionMakeAccDefault($params){
        echo json_encode(self::zajsonit(self::makeAccDefault($params['ID'])));
    }

    function makeAccDefault($id=false){
        $arReturn = array('result' => 'error', 'text' => '');
        if(!sqlSdekLogs::getById($id))
            $arReturn['text'] = GetMessage('IPOLSDEK_AUTH_NO_EXIST');
        else{
            $arReturn['result'] = 'collapse';
            COption::SetOptionString(self::$MODULE_ID,'logged',$id);
        }
        return $arReturn;
    }

    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    отображение таблицы о заявках
        == tableHandler ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function tableHandler($params){
        cmodule::includeModule('sale');
        $arSelect[0]=($params['by'])?$params['by']:'ID';
        $arSelect[1]=($params['sort'])?$params['sort']:'DESC';

        $arNavStartParams['iNumPage']=($params['page'])?$params['page']:1;
        $arNavStartParams['nPageSize']=($params['pgCnt']!==false)?$params['pgCnt']:1;

        foreach($params as $code => $val)
            if(strpos($code,'F')===0)
                $arFilter[substr($code,1)]=$val;

        $requests   = self::select($arSelect,$arFilter,$arNavStartParams);
        $adServises = sdekdriver::getExtraOptions();
        $strHtml='';
        $tarifs = self::getExtraTarifs();
        $accounts   = sqlSdekLogs::getAccountsList(true);
        $accFullCnt = sqlSdekLogs::getAccountsList(false,true);
        $accountBase = self::getBasicAuth(true);

        $arRules = array(
            'noLabel' => array('courierTimeBeg','courierTimeEnd','packs','currency'),
            'header'  => array(
                'courierDate' => GetMessage('IPOLSDEK_STT_SENDER'),
                'street'      => GetMessage('IPOLSDEK_STT_ADDRESS'),
                'line'        => GetMessage('IPOLSDEK_STT_ADDRESS'),
                'PVZ'         => GetMessage('IPOLSDEK_STT_ADDRESS'),
                'packs'		  => GetMessage('IPOLSDEK_STT_PACKS'),
            ),
        );

        $isConverted = self::isConverted();
        if($isConverted)
            \Bitrix\Main\Loader::includeModule('sale');
        while($request=$requests->Fetch()){
            $reqParams=unserialize($request['PARAMS']);
            $paramsSrt='';
            foreach($reqParams as $parCode => $parVal){
                if(array_key_exists($parCode,$arRules['header']))
                    $paramsSrt .= "<strong>".$arRules['header'][$parCode]."</strong><br>";
                if(!in_array($parCode,$arRules['noLabel']))
                    $paramsSrt.=GetMessage("IPOLSDEK_JS_SOD_$parCode").": ";

                switch($parCode){
                    case 'currency': break;
                    case "AS"      : foreach($parVal as $code => $noThing)
                        if(array_key_exists($code,$adServises))
                            $paramsSrt.= $adServises[$code]['NAME']." (".$code."), ";
                        $paramsSrt = substr($paramsSrt,0,strlen($paramsSrt)-2)."<br>";
                        break;
                    case "GABS"    : $paramsSrt.= $parVal['D_L']."x".$parVal['D_W']."x".$parVal['D_H']." ".GetMessage("IPOLSDEK_cm")." ".$parVal['W']." ".GetMessage('IPOLSDEK_kg')."<br>";break;
                    case "service" : $paramsSrt.=$tarifs[$parVal]['NAME']."<br>"; break;
                    case "courierTimeBeg": $paramsSrt.= GetMessage("IPOLSDEK_JS_SOD_courierTime").": ".$parVal." - ".$reqParams["courierTimeEnd"]."<br>"; break;
                    case "courierTimeEnd": break;
                    case "packs"   :
                        $orderGoods = ($request['SOURCE'] == 1) ? sdekdriver::getGoodsArray(self::oIdByShipment($request['ORDER_ID']),$request['ORDER_ID']) : sdekdriver::getGoodsArray($request['ORDER_ID']);
                        foreach($parVal as $place => $params){
                            $paramsSrt.="<span style='font-style:italic'>".GetMessage('IPOLSDEK_JS_SOD_Pack')." ".$place."</span><br>";
                            $paramsSrt.=GetMessage('IPOLSDEK_dims').": ".$params['gabs']." (".GetMessage('IPOLSDEK_cm').")<br>";
                            $paramsSrt.=GetMessage('IPOLSDEK_weight').": ".$params['weight']." ".GetMessage('IPOLSDEK_kg')."<br>";
                            $paramsSrt.=GetMessage('IPOLSDEK_goods').": ";
                            foreach($params['goods'] as $gId => $cnt )
                                if(array_key_exists($gId,$orderGoods))
                                    $paramsSrt.=$orderGoods[$gId]['NAME']." ({$orderGoods[$gId]['PRODUCT_ID']}): $cnt, ";
                            $paramsSrt = substr($paramsSrt,0,strlen($paramsSrt)-2)."<br>";
                        };
                        break;
                    case 'toPay'   :
                    case 'deliveryP' : $paramsSrt .= sdekExport::formatCurrency(array('TO'=>$reqParams['currency'],'SUM'=>$parVal,'FORMAT'=>true))."<br>"; break;
                    case 'NDSDelivery' :
                    case 'NDSGoods'	   : $paramsSrt.= GetMessage('IPOLSDEK_NDS_'.$parVal)."<br>"; break;
                    case 'departure':
                    case 'location' : $city = sqlSdekCity::getBySId($parVal);
                        $paramsSrt.= $city['NAME']." (".$city['REGION'].")<br>";
                        break;
                    default        : $paramsSrt.=$parVal."<br>"; break;
                }
            }

            $message=unserialize($request['MESSAGE']);
            if($message && count($message))
                $message=implode('<br>',$message);
            else
                $message='';

            $addClass='';
            if($request['STATUS']=='OK')
                $addClass='IPOLSDEK_TblStOk';
            if($request['STATUS']=='ERROR')
                $addClass='IPOLSDEK_TblStErr';
            if($request['STATUS']=='TRANZT')
                $addClass='IPOLSDEK_TblStTzt';
            if($request['STATUS']=='DELETE')
                $addClass='IPOLSDEK_TblStDel';
            if($request['STATUS']=='STORE')
                $addClass='IPOLSDEK_TblStStr';
            if($request['STATUS']=='CORIER')
                $addClass='IPOLSDEK_TblStCor';
            if($request['STATUS']=='PVZ')
                $addClass='IPOLSDEK_TblStPVZ';
            if($request['STATUS']=='OTKAZ')
                $addClass='IPOLSDEK_TblStOtk';
            if($request['STATUS']=='DELIVD')
                $addClass='IPOLSDEK_TblStDvd';

            if($isConverted){
                if($request['SOURCE'] == 1){
                    $oId = self::oIdByShipment($request['ORDER_ID']);
                    $arActions = array(
                        'link'    => '/bitrix/admin/sale_order_shipment_edit.php?order_id='.$oId.'&shipment_id='.$request['ORDER_ID'].'&lang=ru',
                        'delete'  => 'IPOLSDEK_setups.table.delReq('.$request['ORDER_ID'].',\\\'shipment\\\');',
                        'print'   => 'IPOLSDEK_setups.table.print('.$request['ORDER_ID'].',\\\'shipment\\\')',
                        'destroy' => 'IPOLSDEK_setups.table.killReq('.$request['ORDER_ID'].',\\\'shipment\\\')',
                    );
                }else
                    $arActions = array(
                        'link'    => '/bitrix/admin/sale_order_view.php?ID='.$request['ORDER_ID'].'&lang=ru',
                        'delete'  => 'IPOLSDEK_setups.table.delReq('.$request['ORDER_ID'].',\\\'order\\\');',
                        'print'   => 'IPOLSDEK_setups.table.print('.$request['ORDER_ID'].',\\\'order\\\')',
                        'destroy' => 'IPOLSDEK_setups.table.killReq('.$request['ORDER_ID'].',\\\'order\\\')',
                    );
            }else
                $arActions = array(
                    'link'    => 'sale_order_detail.php?ID='.$request['ORDER_ID'].'&lang=ru',
                    'delete'  => 'IPOLSDEK_setups.table.delReq('.$request['ORDER_ID'].',\\\'order\\\');',
                    'print'   => 'IPOLSDEK_setups.table.print('.$request['ORDER_ID'].',\\\'order\\\')',
                    'destroy' => 'IPOLSDEK_setups.table.killReq('.$request['ORDER_ID'].',\\\'order\\\')',
                );

            $contMenu='<td class="adm-list-table-cell adm-list-table-popup-block" onclick="BX.adminList.ShowMenu(this.firstChild,[{\'DEFAULT\':true,\'GLOBAL_ICON\':\'adm-menu-edit\',\'DEFAULT\':true,\'TEXT\':\''.GetMessage('IPOLSDEK_STT_TOORDR').'\',\'ONCLICK\':\'BX.adminPanel.Redirect([],\\\''.$arActions['link'].'\\\', event);\'}';
            if($request['STATUS']=='ERROR' || $request['STATUS']=='NEW' || $request['STATUS']=='DELETE')
                $contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-delete\',\'TEXT\':\''.GetMessage('IPOLSDEK_JSC_SOD_DELETE').'\',\'ONCLICK\':\''.$arActions['delete'].'\'}';
            else
                $contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-view\',\'TEXT\':\''.GetMessage('IPOLSDEK_JSC_SOD_FOLLOW').'\',\'ONCLICK\':\'IPOLSDEK_setups.table.follow('.$request['SDEK_ID'].');\'}';
            if(!in_array($request['STATUS'],array('NEW','ERROR','DELETE','DELIVD','OTKAZ')))
                $contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-move\',\'TEXT\':\''.GetMessage('IPOLSDEK_JSC_SOD_CHECK').'\',\'ONCLICK\':\'IPOLSDEK_setups.table.checkState('.$request['SDEK_ID'].');\'}';
            if($request['STATUS']=='OK'){
                $contMenu.=',{\'TEXT\':\''.GetMessage('IPOLSDEK_JSC_SOD_PRNTSH').'\',\'ONCLICK\':\''.$arActions['print'].'\'}';
                $contMenu.=',{\'GLOBAL_ICON\':\'adm-menu-delete\',\'TEXT\':\''.GetMessage('IPOLSDEK_JSC_SOD_DESTROY').'\',\'ONCLICK\':\''.$arActions['destroy'].'\'}';
            }
            $contMenu.='])"><div class="adm-list-table-popup"></div></td>';
            $strHtml.='<tr class="adm-list-table-row '.$addClass.'">
								'.$contMenu.'
								<td class="adm-list-table-cell"><div>'.$request['ID'].'</div></td>
								<td class="adm-list-table-cell"><div>'.$request['MESS_ID'].'</div></td>
								<td class="adm-list-table-cell"><div><a href="'.$arActions['link'].'" target="_blank">'.$request['ORDER_ID'].'</a></div></td>
								<td class="adm-list-table-cell"><div>'.$request['STATUS'].'</div></td>
								<td class="adm-list-table-cell"><div>'.$request['SDEK_ID'].'</div></td>';
            if($isConverted)
                $strHtml.='<td class="adm-list-table-cell"><div>'.(($request['SOURCE'] == 1)?GetMessage('IPOLSDEK_STT_shipment'):GetMessage('IPOLSDEK_STT_order')).'</div></td>';
            $strHtml.='<td class="adm-list-table-cell"><div><a href="javascript:void(0)" onclick="IPOLSDEK_setups.table.shwPrms($(this).siblings(\'div\'))">'.GetMessage('IPOLSDEK_STT_SHOW').'</a><div style="height:0px; overflow:hidden">'.$paramsSrt.'</div></div></td>
								<td class="adm-list-table-cell"><div>'.$message.'</div></td>
								<td class="adm-list-table-cell"><div>'.date("d.m.y H:i",$request['UPTIME']).'</div></td>';
            if(count($accFullCnt)>1){
                $acc = ($request['ACCOUNT']) ? $request['ACCOUNT'] : $accountBase;
                $strHtml.='<td class="adm-list-table-cell IPOLSDEK_account" title="'.((array_key_exists($acc,$accounts)) ? (($accounts[$acc]['LABEL']) ? $accounts[$acc]['LABEL'] : $accounts[$acc]['ACCOUNT']) : GetMessage("IPOLSDEK_TABLE_ACCINACTIVE")).'">'.$acc.'</td>';
            }
            $strHtml.='</tr>';
        }

        echo json_encode(
            self::zajsonit(
                array(
                    'ttl'  => $requests->NavRecordCount,
                    'mP'   => $requests->NavPageCount,
                    'pC'   => $requests->NavPageSize,
                    'cP'   => $requests->NavPageNomer,
                    'sA'   => $requests->NavShowAll,
                    'html' => $strHtml
                )
            )
        );
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    Функции для печати
        == getOrderInvoice ==  == killOldInvoices == == displayActPrint ==  == OnBeforePrologHandler ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

    function getOrderInvoice($orders){ // получаем квитанцию от сдека
        self::killOldInvoices(); //удаляем старые квитанции
        if(!$orders){
            return array(
                'result' => 'error',
                'error'  => 'No order id'
            );
        }
        if(!is_array($orders))
            $orders = array('order' => $orders);

        $arAccInvoices = array();
        $defAccount = self::getBasicAuth(true);
        $arMade = array();
        foreach($orders as $mode => $IDs){
            $requests = sqlSdekOrders::select(array(),array("ORDER_ID"=>$IDs,"SOURCE"=>($mode == 'order')?0:1));
            while($request=$requests->Fetch()){
                if($request['SDEK_ID']){
                    $accId = ($request['ACCOUNT']) ? $request['ACCOUNT'] : $defAccount;
                    if(!array_key_exists($accId,$arAccInvoices))
                        $arAccInvoices[$accId] = array('XML' => '', 'cnt' => 0);
                    $arAccInvoices[$accId]['XML'] .= '<Order DispatchNumber="'.$request['SDEK_ID'].'"/>';
                    $arAccInvoices[$accId]['cnt']++;
                    $arAccInvoices[$accId]['oid'] = $request['ORDER_ID'];
                    $arMade[$mode][]=$request['ORDER_ID'];
                }
            }
        }
        if(!count($arMade)){
            return array(
                'result' => 'error',
                'error'  => 'No orders founded'
            );
        }
        $copies = (int)COption::GetOptionString(self::$MODULE_ID,"numberOfPrints",2);
        if(!$copies) $copies = 1;

        $arReturn = array(
            'result' => '',
            'error'  => '',
            'files'  => array()
        );

        foreach($arAccInvoices as $accId => $data){
            $headers = self::getXMLHeaders($accId);
            $request = '<?xml version="1.0" encoding="UTF-8" ?>
	<OrdersPrint Date="'.$headers['date'].'" Account="'.$headers['account'].'" Secure="'.$headers['secure'].'"  OrderCount="'.$data['cnt'].'" CopyCount="'.$copies.'">'.$data['XML']."</OrdersPrint>";
            $result = self::sendToSDEK($request,"orders_print");

            if(strpos($result['result'],'<')===0){
                $answer = simplexml_load_string($result['result']);
                $errAnswer = '';
                foreach($answer->OrdersPrint as $print)
                    $errAnswer .= $print['Msg'].". ";
                foreach($answer->Order as $print)
                    $errAnswer .= $print['Msg'].". ";

                $arReturn['error'] .= $errAnswer;
            }else{
                if(!file_exists($_SERVER['DOCUMENT_ROOT']."/upload/".self::$MODULE_ID))
                    mkdir($_SERVER['DOCUMENT_ROOT']."/upload/".self::$MODULE_ID);
                if(!file_exists($_SERVER['DOCUMENT_ROOT']."/upload/sdek_files"))
                    mkdir($_SERVER['DOCUMENT_ROOT']."/upload/sdek_files");
                $fileName = mktime()."_".$accId;
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/upload/".self::$MODULE_ID."/".$fileName.".pdf",$result['result']);
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/upload/sdek_files/order_".$data["oid"].".pdf",$result['result']);

                $arReturn['files'][] = $fileName.".pdf";
            }
        }

        if(count($arReturn['files'])){
            $arReturn['result'] = 'ok';

            $ordersNotFound = '';
            foreach($arMade[$accId] as $mode => $ids){
                $diff = array_diff($orders[$mode],$ids);
                if(count($diff))
                    $ordersNotFound .= implode(', ',$diff).", ";
            }

            if($ordersNotFound){
                if($arReturn['errors'])
                    $arReturn['errors'] .= "; ";
                $arReturn['errors'] .= substr($arReturn['errors'],0,strlen($arReturn['errors'])-2);
            }

            if(!$arReturn['errors'])
                unset($arReturn['errors']);
        }else{
            $arReturn['result'] = 'error';
            unset($arReturn['files']);
        }
        return $arReturn;
    }
    function killOldInvoices(){ // удаляет старые файлы с инвойсами
        $dirPath = $_SERVER['DOCUMENT_ROOT']."/upload/".self::$MODULE_ID."/";
        if(file_exists($dirPath)){
            $dirContain = scandir($dirPath);
            foreach($dirContain as $contain){
                if(strpos($contain,'.pdf')!==false && (mktime() - (int)filemtime($dirPath.$contain)) > 1300)
                    unlink($dirPath.$contain);
            }
        }
    }

    function displayActPrint(&$list){ // действие для печати актов
        if (!empty($list->arActions))
            CJSCore::Init(array('ipolSDEK_printOrderActs'));
        if($GLOBALS['APPLICATION']->GetCurPage() == "/bitrix/admin/sale_order.php")
            $list->arActions['ipolSDEK_printOrderActs'] = GetMessage("IPOLSDEK_SIGN_PRNTSDEK");
    }

    function OnBeforePrologHandler(){ // нажатие на печать актов
        if(!array_key_exists('action', $_REQUEST) || !array_key_exists('ID', $_REQUEST) || $_REQUEST['action'] != 'ipolSDEK_printOrderActs')
            return;
        $ifActs = (COption::GetOptionString(self::$MODULE_ID,'prntActOrdr','O') == 'A')?true:false; // другой способ печати документов, если true, печатаем только акт

        $unFounded  = array(); // не найденные (не отосланные) заказы
        $arRequests = array(); // все заявки вида тип => массив id-шников
        $requests = sqlSdekOrders::select(array(),array("ORDER_ID"=>$_REQUEST["ID"],'SOURCE'=>0));
        while($request=$requests->Fetch()){
            if(!$request['SDEK_ID'])
                $unFounded[$request['ORDER_ID']] = true;
            else
                $arRequests['order'][] = $request['ORDER_ID'];
        }
        foreach($_REQUEST["ID"] as $orderId)
            if(!in_array($orderId,$arRequests['order']))
                $unFounded[$orderId] = true;

        if(count($unFounded) && self::isConverted()){
            \Bitrix\Main\Loader::includeModule('sale');
            $arShipments = array();
            foreach(array_keys($unFounded) as $id){
                $shipments = Bitrix\Sale\Shipment::getList(array('filter'=>array('ORDER_ID' => $id)));
                while($shipment=$shipments->Fetch())
                    $arShipments[$shipment['ID']] = $shipment['ORDER_ID'];
            }
            $requests = sqlSdekOrders::select(array(),array("ORDER_ID"=>array_keys($arShipments),'SOURCE'=>1));
            while($request=$requests->Fetch()){
                if($request['SDEK_ID']){
                    $arRequests['shipment'][] = $request['ORDER_ID'];
                    unset($unFounded[$arShipments[$request['ORDER_ID']]]);
                }
            }
        }
        $badOrders = (count($unFounded)) ? implode(',',array_keys($unFounded)) : false;
        if(!$ifActs){
            $shtrihs = self::getOrderInvoice($arRequests);
            $badOrders .= ($shtrihs['errors']) ? '\n'.$shtrihs['errors'] : ''; // errors - расхождения, error - если коллапс
        }
        ?>
        <script type="text/javascript">
            <?if(count($arRequests) && !$shtrihs['error']){
            if(self::canShipment()){?>
            window.open('/bitrix/js/<?=self::$MODULE_ID?>/printActs.php?orders=<?=implode(":",$arRequests['order'])?>&shipments=<?=implode(":",$arRequests['shipment'])?>','_blank');
            <?}else{?>
            window.open('/bitrix/js/<?=self::$MODULE_ID?>/printActs.php?ORDER_ID=<?=implode(":",$arRequests['order'])?>','_blank');
            <?}
            if(!$ifActs && $shtrihs['files']){
            foreach($shtrihs['files'] as $file){?>
            window.open('/upload/<?=self::$MODULE_ID?>/<?=$file?>','_blank');
            <?}
            }
            if($badOrders){?>
            alert('<?=GetMessage("IPOLSDEK_PRINTERR_BADORDERS").$badOrders?>');
            <?}?>
            <?}else{?>
            alert('<?=GetMessage("IPOLSDEK_PRINTERR_TOTALERROR").'\n'.$shtrihs['error']?> ');
            <?}?>
        </script>
    <?}

    function formActArray(){
        if(!cmodule::includeModule('sale')) return;
        if(self::canShipment())
            $arIds = array('order'=>explode(":",$_REQUEST['orders']),'shipment'=>explode(":",$_REQUEST['shipments']));
        else
            $arIds = array('order'=>explode(":",$_REQUEST['ORDER_ID']));
        $arOrders = array();
        $ttlPay = 0;
        $dWeight = COption::GetOptionString($module_id,'weightD',1000);
        foreach($arIds as $mode => $arId)
            if(count($arId))
                foreach($arId as $id){
                    $req=sqlSdekOrders::select(array(),array('ORDER_ID'=>$id,'SOURCE'=>($mode == 'shipment') ? 1 : 0))->Fetch();
                    if(!$req)
                        continue;
                    $params = unserialize($req['PARAMS']);
                    $baze  = ($mode == 'shipment') ? self::getShipmentById($id) : CSaleOrder::GetById($id);
                    $price = array_key_exists('toPay',$params) ? $params['toPay'] : ((float)($baze['PRICE'] - $baze['PRICE_DELIVERY']));
                    $toPay = (array_key_exists('toPay',$params) && array_key_exists('deliveryP',$params)) ? ($params['toPay'] + $params['deliveryP']) : (($params['isBeznal']=='Y') ? 0 : (float)$baze['PRICE']);
                    $arOrders[] = array(
                        'ID'     => ($baze['ACCOUNT_NUMBER']) ? $baze['ACCOUNT_NUMBER'] : $id,
                        'SDEKID' => $req['SDEK_ID'],
                        'WEIGHT' => ($params['GABS']['W'])?$params['GABS']['W']:($dWeight)/1000,
                        'PRICE'  => $price,
                        'TOPAY'  => $toPay
                    );
                    $ttlPay+=$price;
                }
        return array('arOrders' => $arOrders, 'ttlPay' => $ttlPay);
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                Отображение опций
    == placeFAQ ==  == placeHint ==  == getSDEKCity ==  == printSender ==  == placeStatuses ==  == makeSelect ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function placeFAQ($code){?>
        <a class="ipol_header" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLSDEK_FAQ_'.$code.'_TITLE')?></a>
        <div class="ipol_inst"><?=GetMessage('IPOLSDEK_FAQ_'.$code.'_DESCR')?></div>
    <?}

    function placeHint($code){?>
        <div id="pop-<?=$code?>" class="b-popup" style="display: none; ">
            <div class="pop-text"><?=GetMessage("IPOLSDEK_HELPER_".$code)?></div>
            <div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
        </div>
    <?}

    function getSDEKCity($city){
        $cityId = self::getNormalCity($city);
        $SDEKcity = sqlSdekCity::getByBId($cityId);
        return $SDEKcity;
    }

    function printSender($city){
        $SDEKcity = self::getSDEKCity($city);
        if(!$SDEKcity)
            echo "<tr><td colspan='2'>".GetMessage('IPOLSDEK_LABEL_NOSDEKCITY')."</td><tr>";
        else{
            COption::SetOptionString(self::$MODULE_ID,'departure',$SDEKcity['BITRIX_ID']);
            echo "<tr><td>".GetMessage('IPOLSDEK_OPT_depature')."</td><td>".($SDEKcity['NAME'])."</td><tr>";
        }
    }

    function placeStatuses($option){
        if(self::canShipment()){
            $arStatuses = array();
            $arStShipment = array();
            foreach($option as $key => $val)
                if(strpos($val[0],'status') !== false){
                    unset($option[$key]);
                    $arStatuses[] = $val;
                }elseif(strpos($val[0],'stShipment') !== false){
                    unset($option[$key]);
                    $arStShipment[] = $val;
                }
            ShowParamsHTMLByArray($option);
            ?><tr><td></td><td><div class='IPOLSDEK_sepTable'><?=GetMessage('IPOLSDEK_STT_order')?></div><div class='IPOLSDEK_sepTable'><?=GetMessage('IPOLSDEK_STT_shipment')?></div></td></tr><?
            foreach($arStatuses as $key => $description){?>
                <tr>
                    <td><?=$description[1]?></td>
                    <td>
                        <div class='IPOLSDEK_sepTable'>
                            <?self::makeSelect($description[0],$description[4],COption::GetOptionString(self::$MODULE_ID,$description[0],''));?>
                        </div>
                        <div class='IPOLSDEK_sepTable'>
                            <?
                            $name = str_replace('status','stShipment',$description[0]);
                            self::makeSelect($name,$arStShipment[$key][4],COption::GetOptionString(self::$MODULE_ID,$name,''));?>
                        </div>
                    </td>
                </tr>
            <?}
        }else{
            foreach($option as $key => $descr)
                if(strpos($descr[0],'stShipment') === 0)
                    unset($option[$key]);
            ShowParamsHTMLByArray($option);
        }
    }

    function makeSelect($id,$vals,$def=false,$atrs=''){?>
        <select <?if($id){?>name='<?=$id?>' id='<?=$id?>'<?}?> <?=$atrs?>>
            <?foreach($vals as $val => $sign){?>
                <option value='<?=$val?>' <?=($def == $val)?'selected':''?>><?=$sign?></option>
            <?}?>
        </select>
    <?}

    function getCountryHeaderCities($params = array('country' => 'rus')){
        $allCities = sqlSdekCity::getCitiesByCountry($params['country'],true);
        echo "<table>";
        if(!$allCities->nSelectedCount)
            echo "<tr><td>".GetMessage("IPOLSDEK_NO_CITIES_FOUND")."</td></tr>";
        else{
            $arErrCities = sdekHelper::getErrCities($params['country']);
            echo '<tr class="IPOLSDEK_city_header"><td class="IPOLSDEK_city_header" onclick="IPOLSDEK_setups.cities.callCities(\'success\')">'.GetMessage("IPOLSDEK_HDR_success").' ('.$allCities->nSelectedCount.')</td></tr><tr><td id="IPOLSDEK_city_success"></td></tr>';

            foreach(array('many','notFound') as $type)
                if(count($arErrCities[$type]) > 0)
                    echo '<tr class="IPOLSDEK_city_header"><td class="IPOLSDEK_city_header" onclick="IPOLSDEK_setups.cities.callCities(\''.$type.'\')">'.GetMessage("IPOLSDEK_HDR_$type").' ('.count($arErrCities[$type]).')</td></tr><tr><td id="IPOLSDEK_city_'.$type.'"></td></tr>';
        }
        echo "</table>";
    }

    function getCountryDetailCities($params){
        echo "%".$params['country']."%";
        switch($params['mode']){
            case  'success':
                echo '<table class="adm-list-table">
						<thead>
								<tr class="adm-list-table-header">
									<td class="adm-list-table-cell" style="width: 80px;">'.GetMessage("IPOLSDEK_HDR_BITRIXID").'</td>
									<td class="adm-list-table-cell" style="width: 80px;">'.GetMessage("IPOLSDEK_HDR_SDEKID").'</td>
									<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_REGION").'</td>
									<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_CITY").'</td>
								</tr>
						</thead>
						<tbody>';
                $allCities = sqlSdekCity::getCitiesByCountry($params['country']);
                while($element=$allCities->Fetch())
                    echo '<tr class="adm-list-table-row">
								<td class="adm-list-table-cell">'.$element['BITRIX_ID'].'</td>
								<td class="adm-list-table-cell">'.$element['SDEK_ID'].'</td>
								<td class="adm-list-table-cell">'.$element['REGION'].'</td>
								<td class="adm-list-table-cell">'.$element['NAME'].'</td>
							</tr>';

                echo '</tbody></table>';
                break;
            case 'many':
                $arErrCities = sdekHelper::getErrCities($params['country']);
                if(count($arErrCities['many']) > 0){
                    echo '<table class="adm-list-table">
							<thead>
									<tr class="adm-list-table-header">
										<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_BITRIXNM").'</td>
										<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_SDEKNM").'</td>
										<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_VARIANTS").'</td>
									</tr>
							</thead>
							<tbody>';

                    foreach($arErrCities['many'] as $bitrixId => $arCities){
                        $bitrix = CSaleLocation::GetList(array(),array("ID"=>$bitrixId,"REGION_LID"=>LANGUAGE_ID,"CITY_LID"=>LANGUAGE_ID))->Fetch();
                        if(!$bitrix)
                            $bitrix = CSaleLocation::GetList(array(),array("ID"=>$bitrixId))->Fetch();
                        $location = $bitrix['REGION_NAME'].", ".$bitrix['CITY_NAME']." (".$bitrixId.")";

                        echo '<tr class="adm-list-table-row"><td class="adm-list-table-cell">'.$location.'</td><td class="adm-list-table-cell">'.$arCities['takenLbl'].'</td><td class="adm-list-table-cell">';

                        foreach($arCities['sdekCity'] as $sdekId => $descr)
                            echo $descr['region'].", ".$descr['name']."<br>";

                        echo '</td></tr>';
                    }

                    echo '</tbody></table>';
                }
                break;
            case 'notFound':
                $arErrCities = sdekHelper::getErrCities($params['country']);
                if(count($arErrCities['notFound']) > 0){
                    echo '<table class="adm-list-table">
								<thead>
										<tr class="adm-list-table-header">
											<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_SDEKID").'</td>
											<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_REGION").'</td>
											<td class="adm-list-table-cell">'.GetMessage("IPOLSDEK_HDR_CITY").'</td>
										</tr>
								</thead>
								<tbody>';

                    foreach($arErrCities['notFound'] as $arCity)
                        echo '<tr class="adm-list-table-row">
									<td class="adm-list-table-cell">'.$arCity['sdekId'].'</td>
									<td class="adm-list-table-cell">'.$arCity['region'].'</td>
									<td class="adm-list-table-cell">'.$arCity['name'].'</td>
								</tr>';

                    echo '</tbody></table>';
                }
                break;
        }
    }

    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                Функции для опций
    == killSchet ==  == killUpdt ==  == clearCache ==  == printOrderInvoice ==  == killReqOD ==  == delReqOD ==  == callOrderStates ==  == callUpdateList ==  == goSlaughterCities ==  == senders ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function killSchet(){ // Сбрасываем счетчик заявок в опциях
        if(!self::isAdmin()) return false;
        echo COption::SetOptionString(self::$MODULE_ID,'schet',0);
    }

    function killUpdt($wat){ // Убираем информацию об обновлении
        if(unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/hint.txt"))
            echo 'done';
        else
            echo 'fail';
    }

    function clearCache($noFdb=false){//Очистка кэша
        $obCache = new CPHPCache();
        $obCache->CleanDir('/IPOLSDEK/');
        if(!$noFdb)
            echo "Y";
    }

    function printOrderInvoice($params){ // печать заказа
        if(!array_key_exists('mode',$params))
            $params['mode'] = 'order';
        $resPrint = self::getOrderInvoice(array($params['mode'] => $params['oId']));
        echo json_encode(self::zajsonit($resPrint));
    }

    function killReqOD($params,$mode=false){// удаление заявки из СДЕКа
        if(!self::isAdmin()) return false;
        $oid = (is_array($params)) ? $params['oid'] : $params;
        if(!$mode)
            $mode = (array_key_exists('mode',$params)) ? $params['mode'] : 'order';
        if(sdekdriver::deleteRequest($oid,$mode)){
            if($mode == 'order')
                self::killAutoReq($oid);
            echo "GD:".GetMessage("IPOLSDEK_DRQ_DELETED");
        }else
            echo self::getAnswer();
    }

    function delReqOD($params,$mode=false){// удаление заявки из БД
        if(!self::isAdmin()) return false;
        $oid = (is_array($params)) ? $params['oid'] : $params;
        if(!$mode)
            $mode = (array_key_exists('mode',$params)) ? $params['mode'] : 'order';
        if(self::CheckRecord($oid,$mode)){
            sqlSdekOrders::Delete($oid,$mode);
            if($mode == 'order')
                self::killAutoReq($oid);
        }
        echo GetMessage("IPOLSDEK_DRQ_DELETED");
    }

    function callOrderStates(){ // запрос статусов заказов из опций
        self::getOrderStates();
        $err = self::getErrors();
        echo ($err)?($err):date("d.m.Y H:i:s",COption::GetOptionString(self::$MODULE_ID,'statCync',mktime()));
    }

    function callUpdateList($params){ // запрос на синхронизацию из опций
        $arReturn = false;
        if(array_key_exists('full',$params) && $params['full'] && !array_key_exists('listDone',$params))
            if(!self::updateList())
                $arReturn = array(
                    'result' => 'error',
                    'text'   => GetMessage('IPOLSDEK_UPDT_ERR'),
                );

        if(!$arReturn){
            $us=self::cityUpdater();
            $arReturn = array('result' => $us['result']);
            switch($us['result']){
                case 'error'   : $arReturn['text'] = GetMessage("IPOLSDEK_SYNCTY_ERR_HAPPENING")." ".$us['error']; break;
                case 'end'     : $arReturn['text'] = (array_key_exists('full',$params) && $params['full']) ? GetMessage('IPOLSDEK_UPDT_DONE').date("d.m.Y H:i:s",filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/list.php")) : GetMessage('IPOLSDEK_SYNCTY_LBL_SCOD'); break;
                case 'country' : $arReturn['text'] = GetMessage('IPOLSDEK_SYNCTY_CNTRCTDONE').GetMessage('IPOLSDEK_SYNCTY_'.$us['country']); break;
                default        : $arReturn['text'] = GetMessage('IPOLSDEK_SYNCTY_LBL_PROCESS')." ".$us['done']."/".$us['total']; break;
            }
        }

        echo json_encode(self::zajsonit($arReturn));
    }

    function goSlaughterCities($params){ // переопределение городов
        if(!self::isAdmin()) return false;
        $result = self::slaughterCities();
        if($result == 'done'){
            $us=self::cityUpdater();
            if($us['result']!='error')
                $arResult = array(
                    'text' => ($us['result'] == 'done') ? GetMessage("IPOLSDEK_SYNCTY_LBL_SCOD") : '',
                    'status' => $us['result']
                );
            else
                $arResult = array(
                    'text' => GetMessage("IPOLSDEK_ERRLOG_ERRSUNCCITY")." ".$us['error'],
                    'status' => 'error'
                );
        }else
            $arResult = array(
                'text'   => GetMessage("IPOLSDEK_DELCITYERROR")." ".$result,
                'status' => 'error'
            );

        if($params['mode'] == 'json')
            echo json_encode(self::zajsonit($arResult));
        else
            return $arResult;
    }

    function senders($params = false){
        if(!self::isAdmin('R')) return false;
        $path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::$MODULE_ID.'/senders.txt';
        if($params){
            $dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::$MODULE_ID;
            if(!file_exists($dir))
                mkdir($dir);
            return file_put_contents($path,serialize($params));
        }
        elseif(file_exists($path))
            return unserialize(file_get_contents($path));
        else
            return false;
    }

    function getAccountSelect($params){
        $accounts = array(0=>GetMessage('IPOLSDEK_TC_DEFAULT')) + sqlSdekLogs::getAccountsList();
        $soloAccount = (count($accounts) <= 2);
        if($soloAccount)
            echo $params['country'].'<-%->'.GetMessage('IPOLSDEK_TC_DEFAULT');
        else{
            echo $params['country'].'<-%->';
            sdekOption::makeSelect('countries['.$params['country'].'][acc]',$accounts,$params['default']);
        }
    }

    function ressurect(){
        COption::SetOptionString(self::$MODULE_ID,'sdekDeadServer',false);
    }

    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                Функции для агентов
    == agentUpdateList ==  == agentOrderStates ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function agentUpdateList(){ // вызов обновления списка городов, самовывозов и услуг
        if(!self::updateList())
            self::errorLog(GetMessage('IPOLSDEK_UPDT_ERR'));
        self::cityUpdater();
        return 'sdekOption::agentUpdateList();';
    }

    function agentOrderStates(){ // вызов обновления статусов заказов
        self::getOrderStates();
        self::killOldInvoices(); // удаляем заодно старые печати к заказам
        return 'sdekOption::agentOrderStates();';
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    Синхронизации
        == getOrderStates ==  == updateList == == cityUpdater == == requestCityFile ==  == slaughterCities ==  == getOrderState ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/

    function getOrderStates(){//запрос статусов заказов
        if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLSDEK_ERRLOG_NOSALEOOS"));return false;}//без модуля sale делать нечего

        $dateFirst = date("Y-m-d",COption::GetOptionString(self::$MODULE_ID,'statCync',0));

        $accounts = sqlSdekLogs::getAccountsList();

        foreach($accounts as $id => $acc){
            $headers = self::getXMLHeaders($id);

            $XML = '<?xml version="1.0" encoding="UTF-8" ?>
	<StatusReport Date="'.$headers['date'].'" Account="'.$headers['account'].'" Secure="'.$headers['secure'].'">
		<ChangePeriod DateFirst="'.$dateFirst.'" DateLast="'.date('Y-m-d').'"/>
	</StatusReport>
';

            $result = self::sendToSDEK($XML,'status_report_h');

            if($result['code'] != 200)
                self::errorLog(GetMessage("IPOLSDEK_GOS_UNBLSND").GetMessage("IPOLSDEK_ERRORLOG_BADRESPOND").$result['code']);
            else{
                $xml = simplexml_load_string($result['result']);

                $arOrders = array();
                foreach($xml->Order as $orderMess){
                    $arOrders[]=array(
                        'DispatchNumber' => (string)$orderMess['DispatchNumber'],
                        'State'			 => (int)$orderMess->Status['Code'],
                        'Number'		 => (int)$orderMess['Number'],
                        'Description'    => (string)$orderMess->Status['Description']
                    );
                }
            }

            if(count($arOrders))
                self::setOrderStates($arOrders);
        }

        if(!self::$ERROR_REF)
            COption::SetOptionString(self::$MODULE_ID,'statCync',mktime());
    }

    function getOrderState($params){
        $arOrder = false;

        if(is_array($params)){
            if(array_key_exists('DispatchNumber',$params))
                $dNumber = $params['DispatchNumber'];
            else{
                $arOrder = sqlSdekOrders::select(array(),array('ID' => $params['ID']))->Fetch();
                $dNumber = $arOrder['DispatchNumber'];
            }
        }else
            $dNumber = $params;

        if(!cmodule::includemodule('sale')){self::errorLog(GetMessage("IPOLSDEK_ERRLOG_NOSALEOOS"));return false;}//без модуля sale делать нечего

        if(!$arOrder)
            $arOrder = sqlSdekOrders::select(array(),array('SDEK_ID' => $dNumber))->Fetch();

        $headers = self::getXMLHeaders(array('ID' => self::getOrderAcc($arOrder)));
        $return = false;

        $XML = '<?xml version="1.0" encoding="UTF-8" ?>
			<StatusReport Date="'.$headers['date'].'" Account="'.$headers['account'].'" Secure="'.$headers['secure'].'">
				<Order DispatchNumber="'.$dNumber.'"/>
			</StatusReport>
			';

        $result = self::sendToSDEK($XML,'status_report_h');

        if($result['code'] != 200)
            self::errorLog(GetMessage("IPOLSDEK_GOS_UNBLSND").GetMessage("IPOLSDEK_ERRORLOG_BADRESPOND").$result['code']);
        else{
            $xml = simplexml_load_string($result['result']);
            $arOrder=array(array(
                'DispatchNumber' => (string)$xml->Order['DispatchNumber'],
                'State'			 => (int)$xml->Order->Status['Code'],
                'Number'		 => (int)$xml->Order['Number'],
                'Description'    => (string)$xml->Order->Status['Description']
            ));
            self::setOrderStates($arOrder);
        }
    }

    private function setOrderStates($arOrders){
        $arStateCorr = array(
            1 => "OK",
            2 => "DELETE",
            3 => "STORE",
            4 => "DELIVD",
            5 => "OTKAZ",
            6 => "TRANZT",
            7 => "TRANZT",
            8 => "TRANZT",
            9 => "TRANZT",
            10 => "TRANZT",
            11 => "CORIER",
            12 => "PVZ",
            13 => "TRANZT",
            16 => "TRANZT",
            17 => "TRANZT",
            18 => "TRANZT",
            19 => "TRANZT",
            20 => "TRANZT",
            21 => "TRANZT",
            22 => "TRANZT"
        );

        global $USER;
        if(!is_object($USER))
            $USER = new CUser();

        foreach($arOrders as $orderMess){
            if(array_key_exists($orderMess['State'],$arStateCorr)){// описан ли статус
                $curState = $arStateCorr[$orderMess['State']];
                $arOrder = sqlSdekOrders::select(array(),array('SDEK_ID' => $orderMess['DispatchNumber']))->Fetch();
                if(!$arOrder) // not from API
                    continue;
                $mode = ($arOrder['SOURCE'] == 1 ) ? 'shipment' : 'order';
                if($curState == 'DELETE')
                    sqlSdekOrders::Delete($arOrder['ORDER_ID'],$mode);
                if($arOrder['OK']){
                    if(!sqlSdekOrders::updateStatus(array(
                        "ORDER_ID" => $arOrder['ORDER_ID'],
                        "STATUS"   => $curState,
                        "SOURCE"   => $arOrder['SOURCE']
                    )))
                        self::errorLog(GetMessage('IPOLSDEK_GOS_HASERROR').GetMessage('IPOLSDEK_GOS_CANTUPDATEREQ').$arOrder['ORDER_ID'].". ".GetMessage('IPOLSDEK_GOS_STATUS').$curState.".");
                    else{
                        $newStat = COption::GetOptionString(self::$MODULE_ID,(($arOrder['SOURCE'] == 1)?"stShipment":"status").$curState,false);
                        if($newStat && strlen($newStat) < 3){
                            if($arOrder['SOURCE'] == 1){ // отправление
                                $shipment = self::getShipmentById($arOrder['ORDER_ID']);
                                if($shipment['STATUS_ID'] != $newStat)
                                    if(!self::setShipmentField($arOrder['ORDER_ID'],'STATUS_ID',$newStat))
                                        self::errorLog(GetMessage('IPOLSDEK_GOS_HASERROR').GetMessage('IPOLSDEK_GOS_CANTUPDATESHP').$arOrder['ORDER_ID'].". ".GetMessage('IPOLSDEK_GOS_STATUS').$curState.".");
                            }else{ // заказ
                                $order = CSaleOrder::GetByID($arOrder['ORDER_ID']);
                                if($order['STATUS_ID'] != $newStat){
                                    if(!CSaleOrder::StatusOrder($arOrder['ORDER_ID'],$newStat))
                                        self::errorLog(GetMessage('IPOLSDEK_GOS_HASERROR').GetMessage('IPOLSDEK_GOS_CANTUPDATEORD').$arOrder['ORDER_ID'].". ".GetMessage('IPOLSDEK_GOS_STATUS').$curState.".");
                                }
                            }
                        }
                        // оплаченность
                        if(
                            $orderMess['State'] == 4 &&
                            COption::GetOptionString(self::$MODULE_ID,"markPayed",false) == 'Y' &&
                            $arOrder['SOURCE'] != 1
                        ){
                            $order = CSaleOrder::GetByID($arOrder['ORDER_ID']);
                            if($order['PAYED'] != 'Y'){
                                if(self::isConverted()){
                                    $order = \Bitrix\Sale\Order::load($arOrder['ORDER_ID']);
                                    $paymentCollection = $order->getPaymentCollection();
                                    foreach($paymentCollection as $payment)
                                        if(!$payment->isPaid()){
                                            $payment->setPaid("Y");
                                            $order->save();
                                        }
                                }elseif(!CSaleOrder::PayOrder($arOrder['ORDER_ID'],"Y"))
                                    self::errorLog(GetMessage('IPOLSDEK_GOS_HASERROR').GetMessage('IPOLSDEK_GOS_CANTMARKPAYED').$arOrder['ORDER_ID'].". ");
                            }
                        }
                    }
                }else // попытка оформить неподтвержденный заказ
                    self::errorLog(GetMessage('IPOLSDEK_GOS_HASERROR').GetMessage('IPOLSDEK_GOS_BADREQTOUPDT'.$mode).$arOrder['ORDER_ID'].". ".GetMessage('IPOLSDEK_GOS_STATUS').$curState.".");
            }else
                self::errorLog(GetMessage("IPOLSDEK_GOS_HASERROR").GetMessage("IPOLSDEK_GOS_UNKNOWNSTAT").($orderMess['Number'])." : ".$orderMess['State']." (".$orderMess['Description']."). ".GetMessage("IPOLSDEK_GOS_NOTUPDATED"));
        }
    }

    function updateList(){ // обновление списка пунктов самовывоза
        self::checkAS();
        self::ordersNum();
        $errors = false;
        $request = self::sendToSDEK(false,'pvzlist','type=ALL');
        $arList = array();
        if($request['code'] == 200){
            $xml=simplexml_load_string($request['result']);
            foreach($xml as $key => $val){
                $cityCode = (string)$val['CityCode'];
                if(!sqlSdekCity::getBySId($cityCode))
                    continue;
                $type = (string)$val['Type'];
                $city = (string)$val["City"];
                if(strpos($city,'(') !== false)
                    $city = trim(substr($city,0,strpos($city,'(')));
                if(strpos($city,',') !== false)
                    $city = trim(substr($city,0,strpos($city,',')));
                $code = (string)$val["Code"];
                $arList[$type][$city][$code]=array(
                    'Name'     => (string)$val['Name'],
                    'WorkTime' => (string)$val['WorkTime'],
                    'Address'  => (string)$val['Address'],
                    'Phone'    => (string)$val['Phone'],
                    'Note'     => (string)$val['Note'],
                    'cX'       => (string)$val['coordX'],
                    'cY'       => (string)$val['coordY'],
                );
                if($val->WeightLimit){
                    $arList[$type][$city][$code]['WeightLim'] = array(
                        'MIN' => (float)$val->WeightLimit['WeightMin'],
                        'MAX' => (float)$val->WeightLimit['WeightMax']
                    );
                }
            }
        }
        else{
            $strInfo = GetMessage('IPOLSDEK_FILE_UNBLUPDT').$request['code'].".";
            $errors = true;
        }
        if(count($arList))
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/list.php",json_encode($arList));
        if($strInfo && COption::GetOptionString(self::$MODULE_ID,'logged',false)){
            $file=fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::$MODULE_ID."/hint.txt","a");
            fwrite($file,"<br><br><strong>".date('d.m.Y H:i:s')."</strong><br>".$strInfo);
            fclose($file);
        }

        if(!COption::GetOptionString(self::$MODULE_ID,'logged',false) && $request['code']!=200)
            return array('code' => $request['code']);
        return !$errors;
    }

    function cityUpdater($params=false){
        if(!$params)
            $params = array('timeout'=>false,'mode'=>false);
        cmodule::includeModule('sale');
        $countries = self::getCountryOptions();

        $exportClass = false;
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/tmpExport.txt"))
            $exportClass = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/tmpExport.txt"));
        else{
            foreach($countries as $country => $val)
                if($val['act'] == 'Y'){
                    $exportClass = new cityExport($country,$params['timeout']);
                    break;
                }
        }
        if($exportClass){
            $exportClass->start();
            $result = $exportClass->result;
            if($result['result'] == 'error')
                self::errorLog(GetMessage("IPOLSDEK_ERRLOG_ERRSUNCCITY")." ".$exportClass->result['error']);
            elseif($result['result'] == 'end'){
                $result['country'] = $exportClass->countryMode;
                $nxtCntry = false; // f*ck internal pointer, this crap doesn't work
                foreach($countries as $country => $params)
                    if($params['act'] == 'Y'){
                        if($nxtCntry){
                            $nxtCntry = $country;
                            break;
                        }
                        if($country == $exportClass->countryMode)
                            $nxtCntry = true;
                    }

                if($nxtCntry && $nxtCntry !== true){
                    $exportClass = new cityExport($nxtCntry,$params['timeout']);
                    $exportClass->quickSave();
                    $result['result']  = 'country';
                }
            }
        }else
            $result = array(
                'result' => 'error',
                'error'  => GetMessage("IPOLSDEK_ERRLOG_ERRSUNCCITY")
            );

        if($params['mode'] == 'json')
            echo json_encode(self::zajsonit($result));
        else
            return $result;
    }


    function slaughterCities(){
        if(!self::isAdmin()) return false;
        global $DB;
        if($DB->Query("SELECT 'x' FROM ipol_sdekcities", true)){
            $errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".self::$MODULE_ID."/install/db/mysql/unInstallCities.sql");
            if($errors !== false)
                return "error.".implode("", $errors);
            $errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".self::$MODULE_ID."/install/db/mysql/installCities.sql");
            if($errors !== false)
                return "error.".implode("", $errors);
            return 'done';
        }
    }

    function getCountries($makeArrays=false){ // список стран для синхронизации
        $arCountries = array('rus'=>false,'blr'=>false,'kaz'=>false);
        if($makeArrays){
            foreach($arCountries as $country => $nothing)
                $arCountries[$country] = self::getCountryDescr($country);
        }else
            $arCountries = array_keys($arCountries);
        return $arCountries;
    }

    function getCountryDescr($cntry=false){
        $descr = false;

        switch($cntry){
            case false :
            case 'rus' : $descr = array(
                'FILE'  => 'city.csv',
                'NAME'  => array('Russia','Russian Federation',GetMessage('IPOLSDEK_SYNCTY_rus'),GetMessage('IPOLSDEK_SYNCTY_rus2'),GetMessage('IPOLSDEK_SYNCTY_rus3')),
                'LABEL' => GetMessage('IPOLSDEK_SYNCTY_rus')
            );
                break;
            case 'kaz' : $descr = array(
                'FILE' => 'kaz_city.csv',
                'NAME' => array('Kazakhstan',GetMessage('IPOLSDEK_SYNCTY_kaz')),
                'LABEL' => GetMessage('IPOLSDEK_SYNCTY_kaz')
            );
                break;
            case 'blr' : $descr = array(
                'FILE' => 'bel_city.csv',
                'NAME' => array('Belarus','Belorussia',GetMessage('IPOLSDEK_SYNCTY_blr'),GetMessage('IPOLSDEK_SYNCTY_BELORUSSIA')),
                'LABEL' => GetMessage('IPOLSDEK_SYNCTY_blr')
            );
                break;
        }
        return $descr;
    }

    function requestCityFile($cntr=false){
        $cntrDescr = self::getCountryDescr($cntr);
        if(!$cntrDescr)
            return false;
        $request = self::nativeReq($cntrDescr['FILE']);
        if($request['code'] != '200'){
            self::errorLog(GetMessage('IPOLSDEK_FILEIPL_UNBLUPDT').$request['code']);
            return false;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/".$cntrDescr['FILE'],$request['result']);
        return true;
    }

    protected function ordersNum(){
        cmodule::includeModule('sale');
        // требование СДЭК по сбору статистики, сколько заявок сделано через модуль
        $lastId = COption::GetOptionString(self::$MODULE_ID,'lastSuncId',0);
        $arOrders = array();
        $bdReqs = sqlSdekOrders::select(array("ID","ASC"),array(">ID"=>$lastId,"OK"=>true));
        while($arReq=$bdReqs->Fetch()){
            $year  = date("Y",$arReq['UPTIME']);
            if(!array_key_exists($year,$arOrders))
                $arOrders[$year] = array();

            $month = date("m",$arReq['UPTIME']);
            if(array_key_exists($month,$arOrders[$year]))
                $arOrders[$year][$month]['vis'] += 1;
            else
                $arOrders[$year][$month]['vis'] = 1;
            $arOrders[$year][$month]['id'][] = $arReq['ORDER_ID'];
            if($lastId < $arReq['ID'])
                $lastId = $arReq['ID'];
        }

        foreach($arOrders as $year => $arYear)
            foreach($arYear as $month => $arMonth){
                $ttlPrice = 0;
                $orders = CSaleOrder::GetList(array(),array('ID'=>$arMonth['id']),false,false,array('ID','PRICE'));
                while($order=$orders->Fetch())
                    $ttlPrice += $order['PRICE'];
                $arOrders[$year][$month]['prc'] = round($ttlPrice);
                unset($arOrders[$year][$month]['id']);
            }

        if(count($arOrders)){
            $auth = self::getBasicAuth();
            $request = self::nativeReq('sdekStat.php',array(
                'req' => json_encode(self::zajsonit(array(
                    'reqs' => $arOrders,
                    'acc'  => $auth['ACCOUNT'],
                    'host' => $_SERVER['SERVER_NAME'],
                    'cms'  => 'bitrix'
                )))
            ));
            if(
                $request['code']=='200' &&
                strpos($request['result'],'good') !== false
            )
                COption::SetOptionString(self::$MODULE_ID,'lastSuncId',$lastId);
        }
    }

    protected function checkAS(){
        $req = sdekOption::nativeReq('checkAS.php');
        if($req['code'] == 200){
            if($req['result'] == 'Y')
                COption::SetOptionString(self::$MODULE_ID,'blockSwitch','N');
            elseif($req['result'] == 'N')
                COption::SetOptionString(self::$MODULE_ID,'blockSwitch','Y');
        }
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    Импорт городов
        == setImport ==  == handleImport ==  == getCityTypeId ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function setImport($mode = 'Y'){
        if(is_array($mode))
            $mode = (array_key_exists('mode',$mode)) ? $mode['mode'] : 'Y';
        COption::SetOptionString(self::$MODULE_ID,'importMode',$mode);
    }

    function handleImport($params){
        if(!self::isAdmin()) return false;
        cmodule::includeModule('sale');
        $fname = ($params['fileName']) ? $params['fileName'] : 'tmpImport.txt';
        switch($params['mode']){
            case 'setSync': $sync = self::cityUpdater($_REQUEST['timeOut']);
                if($sync['result'] == 'pause' || $sync['result'] == 'country')
                    $arReturn = array(
                        'text' => GetMessage('IPOLSDEK_IMPORT_PROCESS_SYNC').$sync['done'].GetMessage("IPOLSDEK_IMPORT_PROCESS_FROM").$sync['total'],
                        'step' => 'contSync',
                        'result' => $sync
                    );
                else
                    $arReturn = array(
                        'text' => GetMessage('IPOLSDEK_IMPORT_STATUS_SDONE')."<br><br>",
                        'step' => 'startImport',
                        'result' => $sync
                    );
                break;
            case 'setImport':
                $importClass = new cityExport('rus',$params['timeOut'],$fname);
                $importClass->pauseImport();
                if($importClass->error)
                    $arReturn = array(
                        'text'   => GetMessage('IPOLSDEK_IMPORT_ERROR_lbl').$importClass->error,
                        'step' 	 => false,
                        'result' => 'error',
                    );
                else
                    $arReturn = array(
                        'text'   => GetMessage('IPOLSDEK_IMPORT_STATUS_MDONE'),
                        'step'   => 'init',
                        'result' => $importClass->result,
                    );
                break;
            case 'process' :
                if(!file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/{$fname}"))
                    $arReturn = array(
                        'text'   => GetMessage('IPOLSDEK_IMPORT_ERROR_NOFILES'),
                        'step' 	 => false,
                        'result' => 'error',
                    );
                else{
                    $importClass = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/{$fname}"));
                    $importClass->loadCities();
                    $errors = ($importClass->error) ? GetMessage('IPOLSDEK_IMPORT_ERROR_WHILEIMPORT')."<div class='IPOLSDEK_import_errors'>".$importClass->error."</div>" : '';
                    if($importClass->result['result'] == 'end'){
                        $arReturn = array(
                            'text'   => GetMessage('IPOLSDEK_IMPORT_STATUS_IDONE').$importClass->result['added'].".".$errors ,
                            'step' 	 => 'endImport',
                            'result' => $importClass->result
                        );
                        self::setImport('N');
                    }else
                        $arReturn = array(
                            'text'   => "> ".GetMessage('IPOLSDEK_IMPORT_PROCESS_'.$importClass->result['mode'])." ".GetMessage('IPOLSDEK_IMPORT_PROCESS_WORKING').($importClass->result['done']).GetMessage('IPOLSDEK_IMPORT_PROCESS_FROM').$importClass->result['total']." ".$errors,
                            'step' 	 => 'process',
                            'result' => 'process',
                        );
                }
                break;
        }
        if($params['noJson'])
            return $arReturn;
        else
            echo json_encode(sdekdriver::zajsonit($arReturn));
    }

    function getCityTypeId(){
        if(!sdekdriver::isLocation20()) return;
        $tmp = \Bitrix\Sale\Location\TypeTable::getList(array('select'=>array('*'),'filter'=>array('CODE'=>'CITY')))->Fetch();
        return (is_array($tmp) && array_key_exists('ID',$tmp)) ? $tmp['ID'] : false;
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    Автовыгрузки
        == setAutoloads ==  == autoLoadsHandler ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    function setAutoloads($mode = 'Y'){
        if(is_array($mode))
            $mode = (array_key_exists('mode',$mode)) ? $mode['mode'] : 'Y';
        COption::SetOptionString(self::$MODULE_ID,'autoloads',$mode);
    }

    function autoLoadsHandler($params){
        sdekdriver::$MODULE_ID;
        cmodule::includeModule('sale');
        $arSelect[($params['by'])?$params['by']:'ORDER_ID']=($params['sort'])?$params['sort']:'DESC';

        $arNavStartParams['iNumPage']=($params['page'])?$params['page']:1;
        $arNavStartParams['nPageSize']=($params['pgCnt']!==false)?$params['pgCnt']:1;

        $arFilter = array('CODE'=>'IPOLSDEK_AUTOSEND');
        foreach($params as $code => $val)
            if(strpos($code,'F')===0 && $code != 'FSTATUS')
                $arFilter[substr($code,1)]=$val;
            elseif($code == 'FSTATUS' && $val == 'Y')
                $arFilter['!VALUE']=GetMessage('IPOLSDEK_AUTOLOAD_RESPOND_1');
        $requests = CSaleOrderPropsValue::GetList($arSelect,$arFilter,false,$arNavStartParams);
        $strHtml='';
        $action = (self::isConverted()) ? '/bitrix/admin/sale_order_view.php?ID=' : '/bitrix/admin/sale_order_detail.php?ID=';
        while($request=$requests->Fetch()){
            $addClass = ($request['VALUE'] == GetMessage('IPOLSDEK_AUTOLOAD_RESPOND_1')) ? 'IPOLSDEK_TblStOk' : 'IPOLSDEK_TblStErr';

            $SDEKState = '';
            if($addClass == 'IPOLSDEK_TblStOk'){
                $SDEKState = sqlSdekOrders::GetByOI($request['ORDER_ID']);
                if(!$SDEKState['OK'])
                    $addClass = 'IPOLSDEK_TblStErr';
                $SDEKState = $SDEKState['STATUS'];
            }

            $strHtml.='<tr class="adm-list-table-row '.$addClass.'">
					<td class="adm-list-table-cell"><div><a href="'.$action.$request['ORDER_ID'].'" target="_blank">'.$request['ORDER_ID'].'</a></div></td>
					<td class="adm-list-table-cell"><div>'.$request['MESS_ID'].'</div></td>
					
					<td class="adm-list-table-cell"><div>'.$request['VALUE'].'</div></td>
					<td class="adm-list-table-cell"><div>'.$SDEKState.'</div></td>';
        }

        echo json_encode(
            self::zajsonit(
                array(
                    'ttl' =>$requests->NavRecordCount,
                    'mP'  =>$requests->NavPageCount,
                    'pC'  =>$requests->NavPageSize,
                    'cP'  =>$requests->NavPageNomer,
                    'sA'  =>$requests->NavShowAll,
                    'html'=>$strHtml
                )
            )
        );
    }

    function killAutoReq($orderId=false){
        if(!$orderId || !self::isAdmin())
            return;
        cmodule::includeModule('sale');
        $val = CSaleOrderPropsValue::GetList(array(),array('CODE'=>'IPOLSDEK_AUTOSEND','ORDER_ID'=>$orderId))->Fetch();
        if($val)
            CSaleOrderPropsValue::Delete($val['ID']);
    }


    /*()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()
                                                    Связки и общие
        == select ==  == CheckRecord ==  == nativeReq ==
    ()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()()*/


    public static function select($arOrder=array("ID","DESC"),$arFilter=array(),$arNavStartParams=array()){ // выборка
        if(!self::isAdmin('R'))
            return false;
        return sqlSdekOrders::select($arOrder,$arFilter,$arNavStartParams);
    }
    public static function CheckRecord($orderId,$mode='order'){// проверка наличия заявки для заказа / отгрузки
        if(!self::isAdmin('R'))
            return false;
        return sqlSdekOrders::CheckRecord($orderId,$mode);
    }

    private function nativeReq($where,$what=false){
        if(!$where) return false;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://ipolh.com/webService/sdek/'.$where);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($what){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $what);
        }
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array(
            'result' => $result,
            'code'   => $code
        );
    }

    // LEGACY
    function updateCities($params=array()){
        $exportClass = false;
        cmodule::includeModule('sale');
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/tmpExport.txt"))
            $exportClass = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::$MODULE_ID."/tmpExport.txt"));
        else
            $exportClass = new cityExport('rus',$params['timeout']);

        $exportClass->start();

        if($exportClass->result['result'] == 'error')
            self::errorLog(GetMessage("IPOLSDEK_ERRLOG_ERRSUNCCITY")." ".$exportClass->result['error']);

        if($params['mode'] == 'json')
            echo json_encode($exportClass->result);
        else
            return $exportClass->result;
    }
}
?>