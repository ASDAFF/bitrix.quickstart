<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/sberbank_new_softeffect.php"));
?>
<html>
<head>
	<title><?=GetMessage('SE_SBER_KVITANCIA');?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
	<style type="text/css">
		table.sbb td {
			color: #000000;
			font-size: 12px;
			font-family: Times New Roman, Arial, Tahoma;
			padding: 0;
			margin: 0;
		}
		@media print {
			input {display: none; }
		}
		
	</style>
</head>
<body bgColor="#ffffff">
<table class="sbb" border="0" cellpadding="0" cellspacing="0" width="680">
	<tbody>
		<tr valign="top">
			<td style="border-right: 1px none; border-width: 1px; border-style: solid none none solid; border-color: #000; width: 235px;" align="center"><strong><?=GetMessage('SE_SBER_IZVESHHENIE');?></strong><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><strong><?=GetMessage('SE_SBER_KASSIR');?></strong></td>
			<td style="border-right: 1px solid #000; border-width: 1px 1px 1px; border-style: solid solid none; border-color: #000 #000 #000; padding: 5px;" align="center">
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td align="right"><small><em><?=GetMessage('SE_SBER_FORM_PD');?></em></small></td>
	                    </tr>
	                    <tr>
	                        <td style="border-bottom: 1px solid #000; font-size:10px;" align="center"><p><strong><?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?></strong></p></td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NAME_POL');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td style="width: 37mm; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("INN"))?></td>
	                        <td style="width: 9mm;">&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("KPP"))?></td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_INN_POL');?>)</small></td>
	                        <td><small>&nbsp;</small></td>
	
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NUM_POL');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
						<tr>
							<td style="width: 15px;"><?=GetMessage('SE_SBER_IN');?>&nbsp;</td>
							<td style="width: 325px; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("BANK_NAME"))?></td>
							<td style="width: 45px;" align="right"><?=GetMessage('SE_SBER_BIK');?>&nbsp;&nbsp;</td>
							<td style="width: 70px; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("BANK_BIC"))?></td>
						</tr>
	                    <tr>
	                        <td>&nbsp;</td>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_BANK_POL');?>)</small></td>
	                        <td>&nbsp;</td>
	                        <td>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td nowrap="nowrap" style="width: 57mm;"><?=GetMessage('SE_SBER_KOR_POL');?>&nbsp;&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;"><?=(CSalePaySystemAction::GetParamValue("BANK_COR_ACCOUNT"))?></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td style="width: 70mm; border-bottom: 1px solid #000;" align="center"><?=GetMessage('SE_SBER_ORDER_ID');?><?=(CSalePaySystemAction::GetParamValue("ORDER_ID"))?> <?=GetMessage('SE_SBER_ORDER_ID_FROM');?> <?=(CSalePaySystemAction::GetParamValue("DATE_INSERT"))?></td>
	                        <td style="width: 2mm;">&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;">&nbsp;</td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NAME_PLAT');?>)</small></td>
	                        <td><small>&nbsp;</small></td>
	
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_LS_PLAT');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td nowrap="nowrap" width="1%"><?=GetMessage('SE_SBER_FIO_PLAT');?>&nbsp;&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;" width="100%"><?=(CSalePaySystemAction::GetParamValue("PAYER_CONTACT_PERSON"))?>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td nowrap="nowrap" width="1%"><?=GetMessage('SE_SBER_ADRESS_PLAT');?>&nbsp;&nbsp;</td>
	
	                        <td style="border-bottom: 1px solid #000;" width="100%"><?
								//собираем фактический
								$sAddrFact = "";
								(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"))>0)
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"))>0)
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_CITY"))>0) 
								{
									$g = substr(CSalePaySystemAction::GetParamValue("PAYER_CITY"), 0, 2);
									$sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>GetMessage('SE_SBER_GOD') && $g<>GetMessage('SE_SBER_GOD_B')? GetMessage('SE_SBER_GOD')." ":"").(CSalePaySystemAction::GetParamValue("PAYER_CITY"));
								}
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"))>0) 
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"));
								echo $sAddrFact;
							?>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                    	<? if(strpos(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), ".")!==false)
								$a = explode(".", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));
							else
								$a = explode(",", (CSalePaySystemAction::GetParamValue("SHOULD_PAY"))); ?>
	                        <td><?=GetMessage('SE_SBER_SUMM');?>&nbsp;<font>&nbsp;<?=$a[0]?>&nbsp;</font>&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;<?=($a[1]) ? $a[1] : '00'?> <?=GetMessage('SE_SBER_KOP');?></td>
	                        <td align="right">&nbsp;&nbsp;<?=GetMessage('SE_SBER_SUMM_SERVICE');?>&nbsp;&nbsp;_____&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;____&nbsp;<?=GetMessage('SE_SBER_KOP');?></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td><?=GetMessage('');?>&nbsp;&nbsp;_______&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;____&nbsp;<?=GetMessage('SE_SBER_KOP');?></td>
	                        <td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 20___ <?=GetMessage('SE_SBER_GOD');?></td>
	                    </tr>
	                </tbody>
	
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td><small><?=GetMessage('SE_SBER_USLOVIA');?></small></td>
	                    </tr>
	                </tbody>
	            </table>
	
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td align="right"><strong><?=GetMessage('SE_SBER_PODPIS');?> _____________________</strong></td>
	                    </tr>
	                </tbody>
	            </table>
            </td>
        </tr>
        <tr valign="top">
            <td style="border-right: 1px none; border-width: 1px; border-style: solid none solid solid; border-color: #000; width: 235px;" align="center"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><strong><?=GetMessage('SE_SBER_KVITANCIA');?></strong><br /><font style="FONT-SIZE: 8px">&nbsp;<br /></font><strong><?=GetMessage('SE_SBER_KASSIR');?></strong></td>
			<td style="border-right: 1px solid #000; border-width: 1px; border-style: solid; border-color: #000; padding: 5px;" align="center">
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td align="right"><small><em><?=GetMessage('SE_SBER_FORM_PD');?></em></small></td>
	                    </tr>
	                    <tr>
	                        <td style="border-bottom: 1px solid #000; font-size:10px;" align="center"><p><strong><?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?></strong></p></td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NAME_POL');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td style="width: 37mm; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("INN"))?></td>
	                        <td style="width: 9mm;">&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("KPP"))?></td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_INN_POL');?>)</small></td>
	                        <td><small>&nbsp;</small></td>
	
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NUM_POL');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
						<tr>
							<td style="width: 15px;"><?=GetMessage('SE_SBER_IN');?>&nbsp;</td>
							<td style="width: 325px; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("BANK_NAME"))?></td>
							<td style="width: 45px;" align="right"><?=GetMessage('SE_SBER_BIK');?>&nbsp;&nbsp;</td>
							<td style="width: 70px; border-bottom: 1px solid #000;" align="center"><?=(CSalePaySystemAction::GetParamValue("BANK_BIC"))?></td>
						</tr>
	                    <tr>
	                        <td>&nbsp;</td>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_BANK_POL');?>)</small></td>
	                        <td>&nbsp;</td>
	                        <td>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td nowrap="nowrap" style="width: 57mm;"><?=GetMessage('SE_SBER_KOR_POL');?>&nbsp;&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;"><?=(CSalePaySystemAction::GetParamValue("BANK_COR_ACCOUNT"))?></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td style="width: 70mm; border-bottom: 1px solid #000;" align="center"><?=GetMessage('SE_SBER_ORDER_ID');?><?=(CSalePaySystemAction::GetParamValue("ORDER_ID"))?> <?=GetMessage('SE_SBER_ORDER_ID_FROM');?> <?=(CSalePaySystemAction::GetParamValue("DATE_INSERT"))?></td>
	                        <td style="width: 2mm;">&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;">&nbsp;</td>
	                    </tr>
	                    <tr>
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_NAME_PLAT');?>)</small></td>
	                        <td><small>&nbsp;</small></td>
	
	                        <td align="center"><small>(<?=GetMessage('SE_SBER_LS_PLAT');?>)</small></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td nowrap="nowrap" width="1%"><?=GetMessage('SE_SBER_FIO_PLAT');?>&nbsp;&nbsp;</td>
	                        <td style="border-bottom: 1px solid #000;" width="100%"><?=(CSalePaySystemAction::GetParamValue("PAYER_CONTACT_PERSON"))?>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td nowrap="nowrap" width="1%"><?=GetMessage('SE_SBER_ADRESS_PLAT');?>&nbsp;&nbsp;</td>
	
	                        <td style="border-bottom: 1px solid #000;" width="100%"><?
								//собираем фактический
								$sAddrFact = "";
								(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"))>0)
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"))>0)
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"));
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_CITY"))>0) 
								{
									$g = substr(CSalePaySystemAction::GetParamValue("PAYER_CITY"), 0, 2);
									$sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>GetMessage('SE_SBER_GOD') && $g<>GetMessage('SE_SBER_GOD_B')? GetMessage('SE_SBER_GOD')." ":"").(CSalePaySystemAction::GetParamValue("PAYER_CITY"));
								}
								if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"))>0) 
									$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"));
								echo $sAddrFact;
							?>&nbsp;</td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                    	<? if(strpos(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), ".")!==false)
								$a = explode(".", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));
							else
								$a = explode(",", (CSalePaySystemAction::GetParamValue("SHOULD_PAY"))); ?>
	                        <td><?=GetMessage('SE_SBER_SUMM');?>&nbsp;<font>&nbsp;<?=$a[0]?>&nbsp;</font>&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;<?=($a[1]) ? $a[1] : '00'?> <?=GetMessage('SE_SBER_KOP');?></td>
	                        <td align="right">&nbsp;&nbsp;<?=GetMessage('SE_SBER_SUMM_SERVICE');?>&nbsp;&nbsp;_____&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;____&nbsp;<?=GetMessage('SE_SBER_KOP');?></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	
	                    <tr>
	                        <td><?=GetMessage('SE_SBER_ITOG');?>&nbsp;&nbsp;_______&nbsp;<?=GetMessage('SE_SBER_RUB');?>&nbsp;____&nbsp;<?=GetMessage('SE_SBER_KOP');?></td>
	                        <td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 20___ <?=GetMessage('SE_SBER_GOD');?></td>
	                    </tr>
	                </tbody>
	
	            </table>
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td><small><?=GetMessage('SE_SBER_USLOVIA');?></small></td>
	                    </tr>
	                </tbody>
	            </table>
	
	            <table style="margin-top: 3px; width: 122mm;" border="0" cellpadding="0" cellspacing="0">
	                <tbody>
	                    <tr>
	                        <td align="right"><strong><?=GetMessage('SE_SBER_PODPIS');?> _____________________</strong></td>
	                    </tr>
	                </tbody>
	            </table>
            </td>
        </tr>
    </tbody>
</table>
<br />
<small>
<h1><?=GetMessage('SE_SBER_VNIMANIE');?></h1>
<!-- Условия поставки -->
<h1><b><?=GetMessage('SE_SBER_METOD_OPLATI');?>:</b></h1>
<ol>
	<li><?=GetMessage('SE_SBER_METOD_OPLATI_1');?></li>
	<li><?=GetMessage('SE_SBER_METOD_OPLATI_2');?></li>
	<li><?=GetMessage('SE_SBER_METOD_OPLATI_3');?></li>
	<li><?=GetMessage('SE_SBER_METOD_OPLATI_4');?></li>
</ol>
<h1><b><?=GetMessage('SE_SBER_USLOVIA_POSTAVKI');?>:</b> </h1>
<ul>
	<li><?=GetMessage('SE_SBER_USLOVIA_POSTAVKI_1');?></li>
	<li><?=GetMessage('SE_SBER_USLOVIA_POSTAVKI_2');?></li>
</ul>
<p><b><?=GetMessage('SE_SBER_PRIM');?>:</b> <?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?> <?=GetMessage('SE_SBER_PRIM_EX');?></p>
</small>
</body>
</html>