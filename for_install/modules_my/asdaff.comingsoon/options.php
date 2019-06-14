<?
//CJSCore::Init(array("jquery"));
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="/bitrix/js/asdaff.comingsoon/farbtastic.js"></script>
<link rel="stylesheet" href="/bitrix/themes/asdaff.comingsoon/farbtastic.css" type="text/css" />

<?
$date_today = date("d.m.Y");
$sites_list = array();
$sites_arr = CSite::GetList($by="sort", $order="asc");
while ($site = $sites_arr->Fetch())
{
    $sites_list[] = array($site["LID"] => $site["NAME"]);
}
?>

<script type="text/javascript">

    if(!Date.prototype.toLocaleFormat){
    	Date.prototype.toLocaleFormat = function(format) {
    		var f = {
    			Y : this.getFullYear(),
    			y : this.getFullYear()-(this.getFullYear()>=2e3?2e3:1900),
    			m : this.getMonth() + 1,
    			d : this.getDate(),
    			H : this.getHours(),
    			M : this.getMinutes(),
    			S : this.getSeconds()
    		}, k;
    		for(k in f)
    			format = format.replace('%' + k, f[k] < 10 ? "0" + f[k] : f[k]);
    		return format;
    	}
    }


    $(function(){
        var current_tab = $;
        var bg_inp;
        $('div[id^=sets]').mouseenter(function(){
            if(current_tab.attr('id') != $(this).attr('id')){
                $('#colorpicker').farbtastic('#color');
                bg_inp = $(this).find($('input[name^=CS_bg_]'));
                bg_inp.parent().append($('#colorpicker'));
                $('#color').attr('value', bg_inp.attr('value'));
                $('#color').attr('style', bg_inp.attr('style'));
                $('#colorpicker').fadeIn(0);
                current_tab = $(this);
            }
        });
        $('#colorpicker').mouseup(function(){
            bg_inp.attr('value',$('#color').attr('value'));
            bg_inp.attr('style',$('#color').attr('style'));
        });

        <?for($i=0;$i<count($sites_list);$i++):?>
            <?$keys = array_keys($sites_list[$i]);?>
            $('input[name="CS_date_<?=$keys[0]?>"]').parent().append($('#calendar_<?=$keys[0]?>'));
        <?endfor;?>

        $checker = 0;
        $('.edit-table tr td').children().focus(function(){
            $(this).parent().parent().find($('.mess')).fadeOut(200);
            $checker = 1;
        });
        $('.edit-table tr td').children().blur(function(){
            $(this).parent().parent().find($('.mess')).fadeIn(200);
            $checker = 0;
        });
        $('#colorpicker').click(function(){
            $('.farbtastic').fadeIn(200);
        });
        $('.edit-table tr td').click(function(){
            if($checker){
                $('.farbtastic').fadeOut(200);
            }
        });
        $('div[id^=sets]').mouseleave(function(){
            $('.farbtastic').fadeOut(200);
        });

        $('.paste_date').click(function(){

            time = $(this).attr('data-val');
            min = 1000 * 60;
            set_time = min * time;
            var min15 = new Date(new Date().getTime()+ set_time );
            $('input[name="'+$(this).attr('data-date-field')+'"]').val(min15.toLocaleFormat('%d.%m.%Y %H:%M:%S'));

            return false;

        });

    });
</script>

<?



global $MESS;
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.comingsoon/lang/", "/install/options.php"));
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/asdaff.comingsoon/options.php');
$module_id = "asdaff.comingsoon";
CModule::IncludeModule($module_id);
$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($MOD_RIGHT>="R"):


    $activeGroups = array();
    $rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array ('ACTIVE' => 'Y'));
    while($Group = $rsGroups->Fetch()){
        if($Group['ID'] < 3)
            continue;


//        echo '<pre>'; print_r($Group); echo '</pre>';
        $activeGroups[$Group['ID']] = $Group['NAME'];
    }

//    echo '<pre>'; print_r($activeGroups); echo '</pre>';

    $arAllOptions = array();
    for($i=0;$i<count($sites_list);$i++){
        $keys = array_keys($sites_list[$i]);
        $arAllOptions[$i] = array(
            Array("CS_checkbox_".$keys[0], GetMessage("CS_CHECKBOX"), "N", Array("checkbox", "Y")),
            Array("CS_header_".$keys[0], GetMessage("CS_HEADER"), GetMessage("CS_HEADER_EX"), Array("text", "")),
            Array("CS_bg_".$keys[0], GetMessage("CS_BG"), "#ffffff", Array("text", "")),
            Array("CS_logo_".$keys[0], GetMessage("CS_LOGO"), "/bitrix/images/asdaff.comingsoon/logo.png", Array("text", "")),
            Array("CS_text_".$keys[0], GetMessage("CS_TEXT"), GetMessage("CS_TEXT_EX"), Array("textarea", "3","20")),
            Array("CS_date_".$keys[0], GetMessage("CS_DATE"), $date_today, Array("text", "")),
            Array("CS_allow_user_".$keys[0], GetMessage("allow_groups"), false, Array("multiselectbox", $activeGroups)),
            Array("CS_auto_".$keys[0], GetMessage("auto_open"), "N", Array("checkbox", "Y")),

        );
    }

    if($MOD_RIGHT>="W"):
        if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0)
        {
            COption::RemoveOption($module_id);
            reset($arGROUPS);
            while(list(,$value)=each($arGROUPS))
                $APPLICATION->DelGroupRight($module_id, array($value["ID"]));
        }
        if($REQUEST_METHOD=="POST" && strlen($Update)>0)
        {
//            echo '<pre> $arAllOptions'; print_r($arAllOptions); echo '</pre>';
//            echo '<pre>'; print_r($_REQUEST); echo '</pre>';

            foreach($arAllOptions as $key=>$option){
                $keys = array_keys($sites_list[$key]);
                $path = $_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/'.$keys[0].'/';
                $string = '<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/'.$keys[0].'/site_closed.php");die();?>';
                $text = file_get_contents($path.'init.php');
                for($i=0; $i<count($option); $i++) {
                    $name=$option[$i][0];
                    $val=$$name;

                    if(is_array($val))
                        $val = implode(',',$val);
//                    echo '<pre>'; print_r($$name); echo '</pre>';

                    if($option[$i][3][0]=="checkbox"){
                        if($val!="Y"){
                            $val="N";
                            $file = fopen($path.'init.php', 'w');
                            fwrite($file, str_replace($string, '', $text));
                            fclose($file);
                        }
                        elseif(strpos($string, $text) === false){
                            $file = fopen($path.'init.php', 'a');
                            fwrite($file, $string);
                            fclose($file);
                        }
                    }
                    COption::SetOptionString($module_id, $name, $val, $option[$i][1]);
                }
            }
        }
    endif; //if($MOD_RIGHT>="W"):

    $aTabs = array();
    foreach($sites_list as $site_arr){
        foreach($site_arr as $site_id=>$site_name){
            $aTabs[] = array('DIV' => 'set'.$site_id, 'TAB' => $site_name, 'ICON' => 'CS_settings', 'TITLE' => GetMessage('CS_MAIN_TITLE').' '.$site_name);
        }
    }

    $tabControl = new CAdminTabControl('tabControl', $aTabs);
    $tabControl->Begin();
    ?>

<link rel="stylesheet" href="/bitrix/themes/asdaff.comingsoon/style.css" type="text/css" charset="utf-8" />
<div id="colorpicker" style="display:none;"></div>
<input type="hidden" id="color" value="#ffffff">
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="curform">

    <?
//echo '<pre>'; print_r($arAllOptions); '</pre>';
//    echo '<pre>'; print_r($arAllOptions); echo '</pre>';
    ?>


    <?for($i=0;$i<count($aTabs);$i++):?>



        <?$keys = array_keys($sites_list[$i]);?>
        <?$tabControl->BeginNextTab();?>
        <?

        unset( $arAllOptions[$i][5])
        ?>

        <?__AdmSettingsDrawList('asdaff.comingsoon', $arAllOptions[$i]);?>
<!--        <span id="calendar_--><?//=$keys[0]?><!--">--><?//=Calendar("CS_date_".$keys[0], "curform")?><!--</span>-->
        <tr>
            <td><?=GetMessage("CS_DATE")?></td>
            <td><?echo CAdminCalendar::CalendarDate("CS_date_".$keys[0], COption::GetOptionString($module_id, "CS_date_".$keys[0], $date_today), 19, true)?>
                <br>
                <a href="#" class="paste_date" data-date-field="CS_date_<?=$keys[0]?>" data-val="15"><?=GetMessage('15min')?></a>
                <a href="#" class="paste_date" data-date-field="CS_date_<?=$keys[0]?>" data-val="30"><?=GetMessage('30min')?></a>
                <a href="#" class="paste_date" data-date-field="CS_date_<?=$keys[0]?>" data-val="60"><?=GetMessage('1hour')?></a>
                <a href="#" class="paste_date" data-date-field="CS_date_<?=$keys[0]?>" data-val="1440"><?=GetMessage('1day')?></a>
                <a href="#" class="paste_date" data-date-field="CS_date_<?=$keys[0]?>" data-val="10080"><?=GetMessage('week')?></a>
            </td>
        </tr>
    <?endfor;?>

    <?$tabControl->Buttons();?>
    <script type="text/javascript">
        function RestoreDefaults()
        {
            if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>";
        }
    </script>
    <input type="submit" name="Update" <?if ($MOD_RIGHT<'W') echo "disabled" ?> value="<?echo GetMessage('MAIN_SAVE')?>">
    <input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
    <input type="hidden" name="Update" value="Y">
    <?=bitrix_sessid_post();?>
    <input type="button" <?if ($MOD_RIGHT<'W') echo "disabled" ?> title="<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>" OnClick="RestoreDefaults();" value="<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>">

    <?$tabControl->End();?>

</form>
<?endif;
?>