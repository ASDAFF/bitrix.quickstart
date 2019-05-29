<?
$module_id = 'step2use.redhelper';

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/options.php');

$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($MODULE_RIGHT >= 'R')
{
	$arAllOptions = array(
		array('CLIENT_ID', GetMessage('S2U_CLIENT_ID'), array('text', 50))
	);

	if($REQUEST_METHOD == 'POST' && isset($_REQUEST['update']) && check_bitrix_sessid())
	{
		foreach($arAllOptions as $ar)
		{
			$val = ${$ar[0]};
			if($ar[2][0] == 'checkbox' && $val != 'Y')
				$val = 'N';
			COption::SetOptionString($module_id, $ar[0], $val);
		}
	}

	$aTabs = array(
		array(
			'DIV' => 'edit1',
			'TAB' => GetMessage('MAIN_TAB_SET'),
			'ICON' => 'easyseo_settings',
			'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')
		),
	);

    ?>

    <script type="text/javascript" src="http://data.redhelper.ru/js/swfobject.js"></script>
    <script type="text/javascript">swfobject.embedSWF("http://data.redhelper.ru/swf/redhelp_728x90.swf", "rhlp_b728x90","728", "90", "9.0.0", false, {link1: "http://redhelper.ru?p=2000466"}, {wmode:"opaque"}, {});</script>
    <div id="rhlp_b728x90"></div>
    <style>
        .rh-wr {
            width: 400px;
            float: left;
        }
        .rh-title {
            text-transform: uppercase;
            font-size: 15px;
            line-height: 15px;
            font-family: Verdana;
            display: block;
            margin: 10px 0px 3px 0;
            color: #4C4C4C;
        }
        .rh-title:first-letter {
            color: #B82525;
        }
        .rh-p {
            width: 390px;
            font-size: 12px;
            color: #4C4C4C;
            line-height: 19px;
            font-family: "Tahoma";
        }
        #content_container_ver {
            position: relative;
        }
        #step2use-logo {
            position: absolute;
            right: 10px;
            bottom: -10px;
            font-size: 10px;
        }
        .clear {
            clear: both; 
            width: 0; 
            height: 0;
        }
    </style>
    <div class="rh-wr">
    <div class="rh-title"><?php echo GetMessage('S2U_PROMO_1') ?></div>
    <div class="rh-p"><?php echo GetMessage('S2U_PROMO_1_1') ?></div>
    </div>

    <div class="rh-wr">
<div class="rh-title"><?php echo GetMessage('S2U_PROMO_2') ?></div>
<div class="rh-p"><?php echo GetMessage('S2U_PROMO_2_1') ?></div>
</div>

<div class="rh-wr">
<div class="rh-title"><?php echo GetMessage('S2U_PROMO_3') ?></div>
<div class="rh-p"><?php echo GetMessage('S2U_PROMO_3_1') ?></div>
</div>

<div class="rh-wr">
<div class="rh-title"><?php echo GetMessage('S2U_PROMO_4') ?></div>
<div class="rh-p"><?php echo GetMessage('S2U_PROMO_4_1') ?></div>
</div>

<div class="rh-wr">
<div class="rh-title"><?php echo GetMessage('S2U_PROMO_5') ?></div>
<div class="rh-p"><?php echo GetMessage('S2U_PROMO_5_1') ?></div>
</div>

<div class="rh-wr">
<div class="rh-title"><?php echo GetMessage('S2U_PROMO_6') ?></div>
<div class="rh-p"><?php echo GetMessage('S2U_PROMO_6_1') ?></div>
</div>
    
    <div class="clear">&nbsp;</div>
    <a href="http://step2use.com" target="_blank" id="step2use-logo"><?php echo GetMessage('S2U_OUR_LINK'); ?><img src="http://step2use.com/footer_step2use.png"/></a>
    
    <?
    
    if(COption::GetOptionString($module_id, 'CLIENT_ID'))
    echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "OK",
		"MESSAGE" =>  GetMessage("S2U_INST_OK"),
        'DETAILS' => GetMessage("S2U_INST_OK_DESC"),
		"HTML" => true,
	));
    
	$tabControl = new CAdminTabControl('tabControl', $aTabs);

	$tabControl->Begin();
	
	echo 	'<form method="POST" action="'.$APPLICATION->GetCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID.'">
				'.bitrix_sessid_post();
	
	$tabControl->BeginNextTab();

	if(is_array($arAllOptions))
		foreach($arAllOptions as $Option)
		{
			$val = COption::GetOptionString($module_id, $Option[0]);
			$type = $Option[2];

			if($type[0] == 'checkbox')
				$label = '<label for="'.htmlspecialchars($Option[0]).'">'.$Option[1].'</label>';
			else
				$label = $Option[1];
			
			if($type[0] == 'checkbox')
				$input = '<input type="checkbox" name="'.htmlspecialchars($Option[0]).'" id="'.htmlspecialchars($Option[0]).'" value="Y"'.($val == 'Y' ? ' checked' : '').'>';
			elseif($type[0] == 'text')
				$input = '<input type="text" size="'.$type[1].'" maxlength="255" value="'.htmlspecialchars($val).'" name="'.htmlspecialchars($Option[0]).'">';
			elseif($type[0] == 'textarea')
				$input = '<textarea rows="'.$type[1].'" cols="'.$type[2].'" name="'.htmlspecialchars($Option[0]).'">'.htmlspecialchars($val).'</textarea>';

			echo 	'<tr>
						<td valign="top">
							'.$label.'
						</td>
						<td valign="top" nowrap>
							'.$input.'
						</td>
					</tr>';
            if($Option[0]=='CLIENT_ID' && $val) echo '<tr><td colspan="2"><a href="http://redhelper.ru/my/settings" target="_blank">'.GetMessage("S2U_SETTINGS").'</a></td></tr>';
		}

	$tabControl->Buttons();
	
	echo 	'<input '.($MODULE_RIGHT < 'W' ? 'disabled' : '').' type="submit" name="update" value="'.GetMessage("S2U_SAVE").'">
			<input type="reset" name="reset" value="'.GetMessage("S2U_RESET").'">
			</form>';
	$tabControl->End();
}
?>
<form action="http://redhelper.ru/" target="_blank" method="get">
        <input name="p" type="hidden" value="2000466">
        <input style="font-size: 18px; padding: 8px;" type="submit" value="<?php echo GetMessage('S2U_GET_LOGIN'); ?>">
    </form>