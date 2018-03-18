<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);

if (!class_exists("oldbrowser_class"))
{

    class oldbrowser_class

    {
        function oldbrowser_addScript(){
            $module_id = 'epir.oldbrowser';
            if(COption::GetOptionString('epir.oldbrowser', 'include_jquery') == "Y"){
                $GLOBALS["APPLICATION"]->AddHeadString('<script type="text/javascript" src="/bitrix/tools/oldbrowser/jquery_1.7.1.js"></script>',true);
            }
            $GLOBALS["APPLICATION"]->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/tools/oldbrowser/css/jquery.reject.css"/>',true);
            $GLOBALS["APPLICATION"]->AddHeadString('<script type="text/javascript" src="/bitrix/tools/oldbrowser/jquery.reject.js"></script>',true);

            if(!COption::GetOptionString($module_id, 'string_1_oldbrowser')){
                $value_1 = GetMessage('OBM_TEXT_VAL_1');
            }else{
                $value_1 = COption::GetOptionString($module_id, 'string_1_oldbrowser');
            }

            if(!COption::GetOptionString($module_id, 'string_2_oldbrowser')){
                $value_2 = GetMessage('OBM_TEXT_VAL_2');
            }else{
                $value_2 = COption::GetOptionString($module_id, 'string_2_oldbrowser');
            }

            if(!COption::GetOptionString($module_id, 'string_3_oldbrowser')){
                $value_3 = GetMessage('OBM_TEXT_VAL_3');
            }else{
                $value_3 = COption::GetOptionString($module_id, 'string_3_oldbrowser');
            }

            if(COption::GetOptionString($module_id, 'ie7') == "Y"){
                $ie7_value = "msie7: true";
            }else{
                $ie7_value = "";
            }
            if(COption::GetOptionString($module_id, 'ie8') == "Y"){
                $ie8_value = "msie8: true";
            }else{
                $ie8_value = "";
            }
            if($ie7_value != ""){
                $zap = ",";
            }else{
                $zap = "";
            }
            ?>

            <?$GLOBALS["APPLICATION"]->AddHeadString("
        <script type='text/javascript'>
            $(function(){
                $.reject({
                    reject: {
                            ".$ie7_value.$zap."
                            ".$ie8_value."
                         //msie7: true,
                         //msie8: true,
                         //msie9: true
                         //firefox: true
                    },
                    imagePath:'/bitrix/tools/oldbrowser/images/',
                    header: '".GetMessage('OBM_WINDOW_TITLE')."',
                    paragraph1: '".$value_1."',
                    paragraph2: '".$value_2."',
                    closeMessage: '".$value_3."',
                    closeLink: '".GetMessage('OBM_WINDOW_CLOSE')."'
                });
            });
        </script>",true);
        }
    }
} // class exists

?>
