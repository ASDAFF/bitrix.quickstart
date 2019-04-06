<?
/////////////////////////////
//INTIS LLC. 2013          //
//Tel.: 8 800-333-12-02    //
//www.sms16.ru             //
//Ruslan Semagin           //
//Skype: pixel365          //
/////////////////////////////

IncludeModuleLangFile(__FILE__);

Class CIntisTwoFactorAuthentificationLite
{
    function GetProtocol ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "PROTOCOL_FIELD", ""));
        return $getResult;
    }

    function GetRegTemplateSelect()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_REGISTER_TEMPLATE_FIELD", ""));
        return $getResult;
    }

    function GetRegTemplateSymbol()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_REGISTER_TEMPLATE_SYMBOL_FIELD", ""));
        return $getResult;
    }

    function GetPassTemplateSelect ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_PASSWORD_TEMPLATE_FIELD", ""));
        return $getResult;
    }

    function GetPassTemplateSymbol ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_FIELD", ""));
        return $getResult;
    }

    function GenPass($lenght)
    {
        $passwordChars = $this->GetRegTemplateSelect();
        $passwordSize = strlen($passwordChars);
        for( $i = 0; $i < $lenght; $i++ ) {
            $passwordTotal .= $passwordChars[ rand( 0, $passwordSize - 1 ) ];
        }
        return $passwordTotal;
    }

    function GenConfirmCode($lenght)
    {
        $passwordChars = $this->GetPassTemplateSelect();
        $passwordSize = strlen($passwordChars);
        for( $i = 0; $i < $lenght; $i++ ) {
            $passwordTotal .= $passwordChars[ rand( 0, $passwordSize - 1 ) ];
        }
        return $passwordTotal;
    }

    function GetUserPhoneField ()
    {
        $getResult = COption::GetOptionString("intis.twofactorauthenticationlite", "SELECT_USER_PHONE_IN_FIELDS_FIELD", "");
        return $getResult;
    }

    function GetTokenField ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "TOKEN_PARAM", ""));
        return $getResult;
    }

    function BindingIpCheck ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "BINDING_TO_IP_CHECK", ""));
        return $getResult;
    }

    function IpBlockCheck ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "IP_BLOCK_CHECK", ""));
        return $getResult;
    }

    function CreateIblockId ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "IBLOCK_WITH_DATA", ""));
        return $getResult;
    }

    function GetAdminPhone ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "ADMIN_PHONE_ID", ""));
        return $getResult;
    }

    function GetGroup ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "USER_GROUPS", ""));
        return $getResult;
    }

    function HelloMessage ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "HELLO", ""));
        return $getResult;
    }

    function GetBalance ()
    {
        $secretKey = $this->GetTokenField();
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <security>
                    <token value="'.$secretKey.'" />
                </security>
            </request>';
        $urltopost = $this->GetProtocol().'xml.sms16.ru/xml/balance.php';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec($ch);

        $parse = $result;
        $p = xml_parser_create();
        xml_parse_into_struct($p,$parse,$vals,$index);
        xml_parser_free($p);

        echo $vals['1']['value'];
        if ($vals['1']['attributes']['CURRENCY']=="RUR")
        {
            echo GetMessage("TWOFACTORAUTHENTIFICATION_RUR");
        }else{
            echo $vals['1']['attributes']['CURRENCY'];
        }

        curl_close($ch);
    }

    function GetCurrentOriginator ()
    {
        $getResult = htmlspecialchars(COption::GetOptionString("intis.twofactorauthenticationlite", "CURRENT_ORIGINATOR_FIELD", ""));
        return $getResult;
    }

    function GetOriginator ($secretKey)
    {
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <security>
                    <token value="'.$secretKey.'" />
                </security>
            </request>';
        $urltopost = $this->GetProtocol().'xml.sms16.ru/xml/originator.php';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec($ch);

        $parse = $result;
        $p = xml_parser_create();
        xml_parse_into_struct($p,$parse,$vals,$index);
        xml_parser_free($p);

        echo "<select name='SELECT_ORIGINATOR'>";
        echo "<option value=''></option>";
        foreach ($vals as $key => $avalue) {
            foreach ($avalue as $value) {
                if (($value=="ORIGINATOR") && ($vals[$key]['attributes']['STATE']=="completed"))
                {
                    if ($vals[$key]['value']==$this->GetCurrentOriginator())
                    {
                        $selected = " selected";
                    }else{
                        $selected = "";
                    }
                    echo "<option value='".$vals[$key]['value']."'".$selected.">".$vals[$key]['value']."</option>";
                    $originator = true;
                }
            }
        }
        echo "</select>";
        echo " ".GetMessage("TWOFACTORAUTHENTIFICATION_GET_ORIGINATOR_NAME");

        curl_close($ch);
    }

    function ValidatePhone ($phone)
    {
        $saveOnlyNumberInPhone =  preg_replace('/[^0-9]/', '', $phone);

        if ((strlen($saveOnlyNumberInPhone)==11) && (substr($saveOnlyNumberInPhone, 0, 1)=="7"))
        {
            $phone = $saveOnlyNumberInPhone;
        }

        if ((strlen($saveOnlyNumberInPhone)==11) && (substr($saveOnlyNumberInPhone, 0, 1)=="8"))
        {
            $phone = "7".substr($saveOnlyNumberInPhone, 1);
        }

        if (strlen($saveOnlyNumberInPhone)==10)
        {
            $phone = "7".$saveOnlyNumberInPhone;
        }
		
		if ((strlen($saveOnlyNumberInPhone)==12) && (substr($saveOnlyNumberInPhone, 0, 2)=="38"))
        {
            $phone = $saveOnlyNumberInPhone;
        }
		
        return $phone;
    }

    function DeleteElementIfNonSecurity ($name, $iblockId)
    {
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=>IntVal($iblockId), "=NAME"=>$name);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            CIBlockElement::Delete($arFields["ID"]);
        }
    }

    function DeleteElement ($name, $iblockId)
    {
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=>IntVal($iblockId), "=NAME"=>$name);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            CIBlockElement::Delete($arFields["ID"]);
        }
    }

    function __GetElement($ip, $login, $iblockId, $adminAlert)
    {
        CModule::IncludeModule("iblock");

        $findElement = false;

        $el = new CIBlockElement;

        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT");
        $arFilter = Array("IBLOCK_ID"=>IntVal($iblockId), "=NAME"=>$ip."---".$login);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            if ($arFields['NAME']==$ip."---".$login)
            {
                $findElement = true;
                $count = $arFields['PREVIEW_TEXT'];
                if ($count<3)
                {
                    $arLoadProductArray = Array(
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $arFields['NAME'],
                        "ACTIVE"         => "Y",
                        "PREVIEW_TEXT"   => $count+1
                    );
                    $el->Update($arFields['ID'], $arLoadProductArray);

                    $getResult = true;
                }elseif ($count==3){
                    if ($adminAlert=="Y")
                    {
                        $smsText = GetMessage("TWOFACTORAUTHENTIFICATION_ADMIN_ALERT");
                        $smsText1 = str_replace("#LOGIN#", $login, $smsText);
                        $smsText2 = str_replace("#IP#", $ip, $smsText1);
                        $this->Send($smsText2, $this->ValidatePhone($this->GetAdminPhone()), $this->GetTokenField(), false);
                    }
                    if(IsModuleInstalled("security"))
                    {
                        CModule::Includemodule("security");

                        $ipp = new CSecurityIPRule;
                        $arFieldsipp = array(
                            "RULE_TYPE" => "A",
                            "ACTIVE" => "Y",
                            "ADMIN_SECTION" => "Y",
                            "SITE_ID" => false,
                            "SORT" => 10,
                            "NAME" => $arFields['ID'],
                            "ACTIVE_FROM" => ConvertTimeStamp(time(), "FULL"),
                            "ACTIVE_TO" => ConvertTimeStamp(time() + 900, "FULL"),
                            "INCL_IPS" => array($ip),
                            "EXCL_IPS" => false,
                            "INCL_MASKS" => array("/*"),
                            "EXCL_MASKS" => false,
                        );
                        $ipp->Add($arFieldsipp);

                        echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_BLOCK_ALERT");

                        $getResult = "HIDE_FORM";
                    }
                }
            }else{
                $getResult = false;
            }
        }

        if ($findElement==false)
        {
            $arLoadProductArray = Array(
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"      => $iblockId,
                "NAME"           => $ip."---".$login,
                "ACTIVE"         => "Y",
                "PREVIEW_TEXT"   => "1",
            );
            $el->Add($arLoadProductArray);
        }

        return $getResult;
    }

    function __GetElementIfNonSecurity($ip, $login, $iblockId)
    {
        CModule::IncludeModule("iblock");

        $findElement = false;

        $el = new CIBlockElement;

        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT");
        $arFilter = Array("IBLOCK_ID"=>IntVal($iblockId), "=NAME"=>$ip."***".$login);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            if ($arFields['NAME']==$ip."***".$login)
            {
                $findElement = true;
                $count = $arFields['PREVIEW_TEXT'];
                if ($count<3)
                {
                    $arLoadProductArray = Array(
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $arFields['NAME'],
                        "ACTIVE"         => "Y",
                        "PREVIEW_TEXT"   => $count+1
                    );
                    $el->Update($arFields['ID'], $arLoadProductArray);

                    $getResult = true;
                }elseif ($count==3){
                    $smsText = GetMessage("TWOFACTORAUTHENTIFICATION_ADMIN_ALERT");
                    $smsText1 = str_replace("#LOGIN#", $login, $smsText);
                    $smsText2 = str_replace("#IP#", $ip, $smsText1);
                    $this->Send($smsText2, $this->ValidatePhone($this->GetAdminPhone()), $this->GetTokenField(), false);

                    $arLoadProductArray = Array(
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $arFields['NAME'],
                        "ACTIVE"         => "Y",
                        "PREVIEW_TEXT"   => $count+1
                    );
                    $el->Update($arFields['ID'], $arLoadProductArray);

                    $getResult = true;
                }elseif ($count>3){
                    $arLoadProductArray = Array(
                        "IBLOCK_SECTION" => false,
                        "NAME"           => $arFields['NAME'],
                        "ACTIVE"         => "Y",
                        "PREVIEW_TEXT"   => $count+1
                    );
                    $el->Update($arFields['ID'], $arLoadProductArray);

                    $getResult = true;
                }
            }else{
                $getResult = false;
            }
        }

        if ($findElement==false)
        {
            $arLoadProductArray = Array(
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"      => $iblockId,
                "NAME"           => $ip."***".$login,
                "ACTIVE"         => "Y",
                "PREVIEW_TEXT"   => "1",
            );
            $el->Add($arLoadProductArray);
        }

        return $getResult;
    }

    function DelayedLocking ()
    {
		if(IsModuleInstalled("security"))
        {
			CModule::Includemodule("security");
			CModule::Includemodule("iblock");

			$rs = CSecurityIPRule::GetList(
				array("ID", "NAME"),
				array(
					"=RULE_TYPE" => "A",
					"<=ACTIVE_TO" => ConvertTimeStamp(time(), "FULL"),
				),
				array("ID"=>"ASC")
			);
			while($ar = $rs->Fetch())
			{
				CSecurityIPRule::Delete($ar["ID"]);
				CIBlockElement::Delete($ar["NAME"]);
			}
		}
        return 'CIntisTwoFactorAuthentificationLite::DelayedLocking();';
    }

    function AddIpToStopList ($ip, $activeFrom, $activeTo, $name, $status)
    {
        if(IsModuleInstalled("security"))
        {
            $ob = new CSecurityIPRule;
            $arFields = array(
                "RULE_TYPE" => "A",
                "ACTIVE" => $status,
                "ADMIN_SECTION" => "Y",
                "SITE_ID" => false,
                "SORT" => 10,
                "NAME" => $name,
                "ACTIVE_FROM" => $activeFrom,
                "ACTIVE_TO" => $activeTo,
                "INCL_IPS" => array($ip),
                "EXCL_IPS" => false,
                "INCL_MASKS" => false,
                "EXCL_MASKS" => false,
            );
            $res = $ob->Add($arFields);
            return $res;
        }else{
            return false;
        }
    }

    function Send ($message, $phone, $secretKey, $oneTimePass)
    {
        if ((LANG_CHARSET)=="UTF-8")
        {
            $msg = $message;
        }elseif ((LANG_CHARSET)=="windows-1251"){
            $msg = iconv("windows-1251", "UTF-8", $message);
        }

        if ($this->GetCurrentOriginator()==true)
        {
            $originator = $this->GetCurrentOriginator();
        }else{
            $originator = "inetsms";
        }

        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <message type="sms">
	                <sender>'.$originator.'</sender>
	                <text>'.$msg.$oneTimePass.'</text>
	                <abonent phone="'.$this->ValidatePhone($phone).'"/>
                </message>
                <security>
                    <token value="'.$secretKey.'" />
                </security>
            </request>';
        $urltopost = $this->GetProtocol().'xml.sms16.ru/xml/';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
?>