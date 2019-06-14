<?
IncludeModuleLangFile(__FILE__);
Class CShsProductview
{
    function GetPeople($number)
    {
        $module_id = 'shs.productview';
        $module_status = CModule::IncludeModuleEx($module_id);
        //$module_status = "3";
        if($module_status == '1') {
            $include_class = true;
        }
        elseif($module_status == '2') {
            $include_class = true;
            //echo GetMessage("DEMO");
        }
        elseif($module_status == '3'){
            $include_class = false;
            echo GetMessage("SHS_DEMOEND_PROD");
            return GetMessage("SHS_DEMOEND_PROD");
        }
        if($number==0) return "";
        $od = false;
        $string = "";
        $number = (string)$number;
        if(strlen($number)>=2)
        {
            $n = substr($number, -2);
            $n = (int)$n;
        }
        if(strlen($number)==1 || ($n<=10 || $n>=20))
        {
            if(strlen($number)>=2) $number = substr($number, -1);
            $number = (int)$number;
            switch($number)
            {
                case 1: $string = GetMessage("SHS_PEOPLE_STR_0");break;
                case 2: $string = GetMessage("SHS_PEOPLE_STR_1");break;
                case 3: $string = GetMessage("SHS_PEOPLE_STR_1");break;
                case 4: $string = GetMessage("SHS_PEOPLE_STR_1");break;
                case 5: $string = GetMessage("SHS_PEOPLE_STR_2");break;
                case 6: $string = GetMessage("SHS_PEOPLE_STR_2");break;
                case 7: $string = GetMessage("SHS_PEOPLE_STR_2");break;
                case 8: $string = GetMessage("SHS_PEOPLE_STR_2");break;
                case 9: $string = GetMessage("SHS_PEOPLE_STR_2");break;
                case 0: $string = GetMessage("SHS_PEOPLE_STR_2");break;
            }
        }elseif($n>10 || $n<20){  //print "TEST";
            $string = GetMessage("SHS_PEOPLE_STR_2");
        }
        return $string;
    }
}
?>
