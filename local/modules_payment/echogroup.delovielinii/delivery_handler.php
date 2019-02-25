<?php
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("sale");

class CDeliveryDelovieLinii
{
    public function Init(){
        return array(
            "SID" => "DelovieLinii", 
            "NAME" => GetMessage("ECHOGROUP_DELOVIELINII_DELOVYE_LINII"),
            "DESCRIPTION" => "",
            "DESCRIPTION_INNER" =>GetMessage("ECHOGROUP_DELOVIELINII_RASSCET_OBQEMNOGO_VE"),
            "BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),
            "HANDLER" => __FILE__,
            "DBGETSETTINGS" => array("CDeliveryDelovieLinii", "GetSettings"),
            "DBSETSETTINGS" => array("CDeliveryDelovieLinii", "SetSettings"),
            "GETCONFIG" => array("CDeliveryDelovieLinii", "GetConfig"),
            "CALCULATOR" => array("CDeliveryDelovieLinii", "Calculate"),    
            "COMPABILITY" => array("CDeliveryDelovieLinii", "Compability"),
            "PROFILES" => array(
                "simple" => array(
                    "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_DELOVYE_LINII"),
                    "DESCRIPTION" => "",
                    "RESTRICTIONS_WEIGHT" => array(0), 
                    "RESTRICTIONS_SUM" => array(0), 
                )
            )
        );
    }

    public function GetConfig()
    {
        $arConfig = array(
            "CONFIG_GROUPS" => array(
                "all" => GetMessage("ECHOGROUP_DELOVIELINII_NASTROYKI_SLUJBY_DOS"),
            ),
            
            "CONFIG" => array(),
        );
        
        $arConfig["CONFIG"]["arrivalDoor"] = array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_SCITATQ_DOSTAVKU_DO"),
            "GROUP" => "all",
        );
        $arConfig["CONFIG"]["derivalDoor"] = array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_SCITATQ_DOSTAVKU_OT"),
            "GROUP" => "all",
        );
        $arConfig["CONFIG"]["weight_koef"] = array(
            "TYPE" => "STRING",
            "DEFAULT" => "0.005",
            "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_KOEFFICIENT_OTNOSENI"),
            "GROUP" => "all",
        );
        $arConfig["CONFIG"]["value_koef"] = array(
            "TYPE" => "STRING",
            "DEFAULT" => "0.003",
            "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_KOEFFICIENT_OTNOSENI1"),
            "GROUP" => "all",
        );
        $arConfig["CONFIG"]["c_list"] = array(
            "TYPE" => "DROPDOWN",
            "DEFAULT" => "a7700000000000000000000000",
            "TITLE" => GetMessage("ECHOGROUP_DELOVIELINII_GOROD_OTPRAVKI_GRUZA"),
            "VALUES" => CDeliveryDelovieLinii::GetCList(),
            "GROUP" => "all",
        );
        return $arConfig; 
    }
    public function GetCList($revers=false){
	$request=new CHTTP;
        $fstat = stat($_SERVER["DOCUMENT_ROOT"]."/upload/kladr.xml");
        if($fstat["mtime"]+84600<time()){
            $f=$request->Get("http://public.services.dellin.ru/calculatorService2/index.html?request=xmlForm");
	    file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/kladr.xml",$f);
        }

        $xml=simplexml_load_file($_SERVER["DOCUMENT_ROOT"]."/upload/kladr.xml");
        if(!$xml||!$xml->cities->city) return array();
        $arCity=array();
        
        foreach($xml->cities->city as $a)
	if(!defined("BX_UTF") || !BX_UTF){
	if(!$revers)
            $arCity["a".(string)$a->codeKLADR]=iconv("UTF-8","WINDOWS-1251",(string)$a->name) ;
        else{
        	$arCity[iconv("UTF-8","WINDOWS-1251",(string)$a->name)] = (string)$a->codeKLADR;
		$arCity["clear"][iconv("UTF-8","WINDOWS-1251",(string)$a->name)]="a".(string)$a->codeKLADR;
	    }
	}else
	if(!$revers)
            $arCity["a".(string)$a->codeKLADR]=(string)$a->name ;
        else{
        	$arCity[(string)$a->name] = (string)$a->codeKLADR;
		$arCity["clear"][(string)$a->name]="a".(string)$a->codeKLADR;
	    }
        return $arCity;
    }
    public function SetSettings($arSettings)
    {
        return serialize($arSettings);
    }
    public function GetSettings($strSettings)
    {
        return unserialize($strSettings);
    }
    public function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        return array(
            "RESULT" => "OK",
            "VALUE" => self::__GetLocationPrice($arOrder["LOCATION_TO"], $arConfig, $arOrder)
        );
    }

    public function __GetLocationPrice($l,$conf,$arOrder){
	$aTaxonomy=array(GetMessage("ECHOGROUP_DELOVIELINII_ADYGEA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_ADYGEA"), GetMessage("ECHOGROUP_DELOVIELINII_ALTAY_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_RESP_ALTAY"), GetMessage("ECHOGROUP_DELOVIELINII_ALTAYSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_ALTAYSK_KR"), GetMessage("ECHOGROUP_DELOVIELINII_AMURSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_AMUR_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_ARHANGELQSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ARHANG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_ASTRAHANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ASTR_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_BASKORTOSTAN_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_BASK_N"), GetMessage("ECHOGROUP_DELOVIELINII_BELGORODSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_BELG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_BRANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_BRANS_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_BURATIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_BURATIA"), GetMessage("ECHOGROUP_DELOVIELINII_CECENSKAA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_CECENSK_RESP"), GetMessage("ECHOGROUP_DELOVIELINII_CELABINSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_CELAB_OBL"), ""=>"", GetMessage("ECHOGROUP_DELOVIELINII_CUVASSKAA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_CUVAS"), GetMessage("ECHOGROUP_DELOVIELINII_DAGESTAN_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_DAGESTAN"), GetMessage("ECHOGROUP_DELOVIELINII_EVREYSKAA_AOBL")=>GetMessage("ECHOGROUP_DELOVIELINII_EVREYSKAA_AVT_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_INGUSETIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_INGUSETIA"), GetMessage("ECHOGROUP_DELOVIELINII_IRKUTSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_IRKUTSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_IVANOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_IVAN_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KABARDINO_BALKARSKAA")=>GetMessage("ECHOGROUP_DELOVIELINII_KAB_BALK"), GetMessage("ECHOGROUP_DELOVIELINII_KALININGRADSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KALININGR_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KALMYKIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_KALMYKIA"), GetMessage("ECHOGROUP_DELOVIELINII_KALUJSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KALUJ_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KAMCATSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_KAMC_KR"), GetMessage("ECHOGROUP_DELOVIELINII_KARACAEVO_CERKESSKAA")=>GetMessage("ECHOGROUP_DELOVIELINII_KARAC_CERK"), GetMessage("ECHOGROUP_DELOVIELINII_KARELIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_KAREL"), GetMessage("ECHOGROUP_DELOVIELINII_KEMEROVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KEMEROVSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_HABAROVSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_HABAROVSK_KR"), GetMessage("ECHOGROUP_DELOVIELINII_HAKASIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_HAKASIA"), GetMessage("ECHOGROUP_DELOVIELINII_HANTY_MANSIYSKIY_AVT")=>GetMessage("ECHOGROUP_DELOVIELINII_HMAO_UGRA"), GetMessage("ECHOGROUP_DELOVIELINII_KIROVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KIROV_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KOMI_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_KOMI"), GetMessage("ECHOGROUP_DELOVIELINII_KOSTROMSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KOSTR_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KRASNODARSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_KRASN_KR"), GetMessage("ECHOGROUP_DELOVIELINII_KRASNOARSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_KRASNOAR_KR"), GetMessage("ECHOGROUP_DELOVIELINII_KURGANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KURGAN_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_KURSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_KURSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_LENINGRADSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_LEN_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_LIPECKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_LIPEC_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_MAGADANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_MAGADANSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_MARIY_EL_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_MARIY_EL"), GetMessage("ECHOGROUP_DELOVIELINII_MORDOVIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_MORDOV"), GetMessage("ECHOGROUP_DELOVIELINII_MURMANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_MURM_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_NENECKIY_AO")=>GetMessage("ECHOGROUP_DELOVIELINII_NEN_AO"), GetMessage("ECHOGROUP_DELOVIELINII_NIJEGORODSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_NIJEG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_NOVGORODSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_NOVG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_NOVOSIBIRSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_NOVOSIB_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_OMSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_OMSKAA_OBL1"), GetMessage("ECHOGROUP_DELOVIELINII_ORENBURGSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ORENB_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_ORLOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ORLOV_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_PENZENSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_PENZ_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_PERMSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_PERM_KR"), GetMessage("ECHOGROUP_DELOVIELINII_PRIMORSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_PRIM_KR"), GetMessage("ECHOGROUP_DELOVIELINII_PSKOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_PSKOV_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_ROSTOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ROST_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_RAZANSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_RAZ_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_SAHA_AKUTIA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_AKUTIA"), GetMessage("ECHOGROUP_DELOVIELINII_SAHALINSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_SAHALINSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_SAMARSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_SAM_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_SARATOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_SAR_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_SEVERNAA_OSETIA_AL")=>GetMessage("ECHOGROUP_DELOVIELINII_OSETIA"), GetMessage("ECHOGROUP_DELOVIELINII_SMOLENSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_SMOL_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_STAVROPOLQSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_STAVROP"), GetMessage("ECHOGROUP_DELOVIELINII_SVERDLOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_SVERD_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TAMBOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_TAMBOV_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TATARSTAN_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_TATARST"), GetMessage("ECHOGROUP_DELOVIELINII_TOMSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_TOMSK_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TULQSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_TUL_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TVERSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_TVER_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TUMENSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_TUMENS_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_TYVA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_RESP_TYVA"), GetMessage("ECHOGROUP_DELOVIELINII_UDMURTSKAA_RESP")=>GetMessage("ECHOGROUP_DELOVIELINII_UDMURTIA"), GetMessage("ECHOGROUP_DELOVIELINII_ULQANOVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_ULQAN_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_VLADIMIRSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_VLAD_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_VOLGOGRADSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_VOLG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_VOLOGODSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_VOLOG_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_VORONEJSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_VORON_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_AMALO_NENECKIY_AO")=>GetMessage("ECHOGROUP_DELOVIELINII_ANAO"), GetMessage("ECHOGROUP_DELOVIELINII_AROSLAVSKAA_OBL")=>GetMessage("ECHOGROUP_DELOVIELINII_AROSL_OBL"), GetMessage("ECHOGROUP_DELOVIELINII_ZABAYKALQSKIY_KRAY")=>GetMessage("ECHOGROUP_DELOVIELINII_ZABAYKAL_KR"));
        $arKLADR=self::GetCList(true);
        $arCityTo=CSaleLocation::GetByID($l);
	foreach($arKLADR["clear"] as $k=>$v){
		if(preg_match("!(".str_replace(".","\.",$arCityTo["CITY_NAME"]).".*".str_replace(".","\.",$aTaxonomy[$arCityTo["REGION_NAME"]]).")!i",$k))
			$to=str_replace("a","",$v);
	}
	if(!$to) $to=str_replace("a","",$arKLADR["clear"][substr($arCityTo["REGION_NAME"],0,3)][$arCityTo["CITY_NAME"]]);
	if(!$to) $to=str_replace("a","",$arKLADR[$arCityTo["CITY_NAME"]]);
	
	if($arOrder["LOCATION_FROM"]>0){
	        $arCityFrom=CSaleLocation::GetByID($arOrder["LOCATION_FROM"]);
		foreach($arKLADR["clear"] as $k=>$v){
			if(preg_match("!(".str_replace(".","\.",$arCityFrom["CITY_NAME"]).".*".str_replace(".","\.",$aTaxonomy[$arCityFrom["REGION_NAME"]]).")!i",$k))
				$from=str_replace("a","",$v);
		}
		if(!$from) $from=str_replace("a","",$arKLADR["clear"][substr($arCityFrom["REGION_NAME"],0,3)][$arCityFrom["CITY_NAME"]]);
		if(!$from) $from=str_replace("a","",$arKLADR[$arCityFrom["CITY_NAME"]]);
		
	}else{
		$from=str_replace("a","",$conf["c_list"]["VALUE"]);
	}
        $weight=($arOrder["WEIGHT"]?$arOrder["WEIGHT"]/1000:$conf["weight_koef"]["VALUE"]*$arOrder["PRICE"]);
        $value=($arOrder["WEIGHT"]?$arOrder["WEIGHT"]/100000:$conf["value_koef"]["VALUE"]*$arOrder["PRICE"]);
        $arrivalDoor=$conf["arrivalDoor"]["VALUE"]=="Y"?"&arrivalDoor=true":"";
        $derivalDoor=$conf["derivalDoor"]["VALUE"]=="Y"?"&derivalDoor=true":"";
	$request=new CHTTP;

        $xml=simplexml_load_string($request->Get("http://public.services.dellin.ru/calculatorService2/index.html?request=xmlResult&derivalPoint=".$from."&arrivalPoint=".$to."&sizedWeight=".$weight."&sizedVolume=".$value.$arrivalDoor.$derivalDoor));
        if($xml->price)
            return $xml->price*1;
        else 
            return false;
    }
    public function Compability($arOrder, $arConfig)
    {
        $price = self::__GetLocationPrice($arOrder["LOCATION_TO"], $arConfig,$arOrder);
        
        if ($price === false)
            return array();
        else
            return array('simple');
    }
}

AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array("CDeliveryDelovieLinii", "Init")); 
