<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/include.php");

IncludeModuleLangFile(__FILE__);

$module_id = "rarus.sms4b";
$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($SMS_RIGHT < "R")
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

global $SMS4B;
global $APPLICATION;

$arTime = localtime(time(), true);
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.js');
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/sms4b_sendsms.js');
$APPLICATION->SetAdditionalCSS('/bitrix/js/'.$module_id.'/css/sms4b_sendsms.css');

$arResult["RESULT_MESSAGE"]["TYPE"] = '';


$arsGmt = array(4 => "(+4) ".GetMessage('MOSCOW'),
				3 => "(+3) ".GetMessage('KALININGRAD'),
				6 => "(+6) ".GetMessage('EKATA'),
				7 => "(+7) ".GetMessage('OMSK'),
				8 => "(+8) ".GetMessage('KEMEROVO'),
				9 => "(+9) ".GetMessage('IRKYTSK'),
				10 => "(+10) ".GetMessage('CHITA'),
				11 => "(+11) ".GetMessage('VLADIVOSTOK'),
				12 => "(+12) ".GetMessage('MAGA'),
 );
$arResult["GMT_CONTROL"] .= '<select size="1" name="gmt" id="gmtControl">';
foreach ($arsGmt as $gmtkey => $gmtval)
{
	$arResult["GMT_CONTROL"] .= '<option value="'.$gmtkey.'" '.((COption::GetOptionString('rarus.sms4b', 'gmt') == $gmtkey) ? ' selected ' : '').'>'.$gmtval.'</option>';
}
$arResult["GMT_CONTROL"] .= '</select>';

if($SMS4B->LastError == '' && $SMS4B->GetSOAP("AccountParams",array("SessionID" => $SMS4B->GetSID())) === true )
{

	if($SMS4B->arBalance["Rest"] < 0.1)
	{
		$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
		$arResult["RESULT_MESSAGE"]["MESSAGE"] = GetMessage("NO_MESSAGES").'<br>';
		$arResult["CAN_SEND"] = "N";
	}
	else
	{
		$arResult["BALANCE"] = $SMS4B->arBalance["Rest"];
		$arResult["ADRESSES"] = $SMS4B->arBalance["Addresses"];

		if (strlen($_REQUEST['apply']) > 0)
		{
			//take data entered by user
			$sender = htmlspecialchars($_POST["sender_number"]);
			$message = $_POST["message"];

			//need message about sending?
			$request = ($_POST["reply"] == "on")? 0 : 1;

			//checking and setting new def address
			if ($_REQUEST["def_sender"] == "Y")
			{
				if (in_array($sender,$arResult["ADRESSES"]) && $SMS_RIGHT >= "W")
				{
					COption::SetOptionString("rarus.sms4b","defsender",$sender);
					$arResult["RESULT_MESSAGE"]["TYPE_DEF"] = "CHANGING_DEF_SENDER_NUMBER";
					$arResult["RESULT_MESSAGE"]["MESSAGE_DEF"] = GetMessage("S_NAME")."\"".$sender."\"".GetMessage("NEW_DEF_NUM");
				}
				else
				{
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"] = GetMessage("NOT_IN_LIST");
				}
			}

			//
			$noneProcessedNumbers = str_replace(array("," ,"\n"), ";" , $_POST["destination_number"]);
			$noneProcessedNumbersArray = explode(';', $noneProcessedNumbers);
			$noneProcessedNumbersCount = count($noneProcessedNumbersArray);

			//  ,
			$destination = $SMS4B->parse_numbers($_POST["destination_number"]);
			$numbersForSendCount = count($destination);
			//
			$arResult["DOUBLED_NUMBERS"] = $SMS4B->doubled_numbers;

			$dataFieldError = false;

			//   SMS   100   *SMS-Test*
			if (in_array("SMS-TEST", $arResult["ADRESSES"]) && $arResult["BALANCE"] > 100)
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('BLOCK_SMS')." <a href = \"/office/symbol_name_request.php\" target=\"_blank\">".GetMessage('ORDER_SMS_NAME')."</a>";
			}

			if ($sender == "" || !in_array($sender, $arResult["ADRESSES"]))
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_NOT_SET_SENDER_NUMBER');
			}
			//
			if ($numbersForSendCount == 0)
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_NOT_SET_DEST_NUMBERS');
			}
			//
			if ($message == "")
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_NOT_SET_TEXT');
			}
			elseif (isset($SMS4B->sms_sym_count) && $SMS4B->sms_sym_count != '' && strlen($message) > $SMS4B->sms_sym_count)
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage("ERROR_BIG_TEXT");
			}


			if (!is_numeric($_REQUEST["gmt"]))
			{
				$dataFieldError = true;
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_GMT');
			}
			else
			{
				$arTime = localtime(time(), true);

				$gmt = htmlspecialchars($_REQUEST["gmt"]);

				if ($arTime["tm_isdst"] > 0)
					$gmt +=1;
			}

			// greenwheech timestamp
			$greenWeechTimeStamp = mktime(gmdate('H'), gmdate('i'), gmdate('s'), gmdate('n'), gmdate('j'), gmdate('Y'));

			//
			if (!isset($_REQUEST["BEGIN_SEND_AT"]) || $_REQUEST["BEGIN_SEND_AT"] == '')
			{
				$startUp = "";
			}
			else
			{
				//..    ,      59
				if (strlen($_REQUEST["BEGIN_SEND_AT"]) == 16)
				{
					$send_at = $_REQUEST["BEGIN_SEND_AT"].":30";
				}
				else
				{
					$send_at = $_REQUEST["BEGIN_SEND_AT"];
				}

				$startUp = $SMS4B->GetFormatDateForSmsForm($send_at, $gmt);

				//
				if ($startUp == -1)
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_BEGIN_SEND');
				}

				$timestampStartUp = MakeTimeStamp($send_at);
				$currTimeStamp = $greenWeechTimeStamp + ($gmt*3600);

				//
				/*if ($timestampStartUp < $currTimeStamp)
				{
					$timestampStartUp = $currTimeStamp;

				}*/
				$startUp = date("Ymd H:i:s", $timestampStartUp+1);

				//     10
				$timeX = $timestampStartUp-(86400*10);
				if ($timeX > $currTimeStamp)
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_BEGIN_SEND_TWO');
				}
			}

			//
			if ($_REQUEST['activeToChecked'] != 'Y' || !isset($_REQUEST["DATE_ACTUAL"]) || $_REQUEST["DATE_ACTUAL"] == '')
			{
				$dateActual = "";
			}
			else
			{
				if (strlen($_REQUEST["DATE_ACTUAL"]) == 16)
				{
					$act_date = $_REQUEST["DATE_ACTUAL"].":30";
				}
				else
				{
					$act_date = $_REQUEST["DATE_ACTUAL"];
				}

				$dateActual = $sms4b->GetFormatDateForSmsForm($act_date, $gmt);

				//
				if ($dateActual == -1)
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_ACTUAL_DATE');
				}

				$timestampDateActual = MakeTimeStamp($act_date);
				//
				$currTimeStamp = $greenWeechTimeStamp + ($gmt*3600);

				//     ,
				if ($timestampDateActual < $currTimeStamp)
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_ACTUAL_DATE_TWO');
				}

				//             15
				if ($startUp != "")
				{
					$timeX = $timestampDateActual-1800;
					if ($timeX < $timestampStartUp)
					{
						$dataFieldError = true;
						$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
						$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_ACTUAL_DATE_THREE');
					}
				}

				//   14
				$timeX = $timestampDateActual-(86400*14);
				if ($timeX > $currTimeStamp)
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_ACTUAL_DATE_FOUR');
				}
			}

			//
			if ($_REQUEST['nightTimeChecked'] == 'N' || !isset($_REQUEST["DATE_FROM_NS"]) || !isset($_REQUEST["DATE_TO_NS"]) || $_REQUEST["DATE_FROM_NS"] == "" ||  $_REQUEST["DATE_TO_NS"] == "" )
			{
				$period = "";
			}
			else
			{
				$formedLeftPart = '';
				$formedRightPart = '';

				// -,
				$dateFromNS = $_REQUEST["DATE_FROM_NS"];
				$dateToNS 	= $_REQUEST["DATE_TO_NS"];

				if (ord($dateFromNS) >= 65 && ord($dateFromNS) <= 88 && ord($dateToNS) >= 65 && ord($dateToNS) <= 88)
				{
					//    , ..  SMS-
					//
					if ($dateToNS == 'X')
					{
						$formedLeftPart = 'A';
					}
					else
					{
						$formedLeftPart = chr(ord($dateToNS)+1);
					}
					//
					if ($dateFromNS == 'A')
					{
						$formedRightPart = 'X';
					}
					else
					{
						$formedRightPart = chr(ord($dateFromNS)-1);
					}

					$period = $formedLeftPart.$formedRightPart;
				}
				else
				{
					$dataFieldError = true;
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"][] = GetMessage('ERROR_INTERVAL');
				}
			}


			//
			if (!$dataFieldError)
			{
				$arResult["RESULT_MESSAGE"]["TYPE"] = "OK";

				$sendingError = array();
				$sessid = $SMS4B->GetSID();
				$code =	$_REQUEST['uniformSending'] == 'Y' ? -2 : -1;
				$ston = $SMS4B->get_ton($sender);
				$snpi = $SMS4B->get_npi($sender);
				$body = $SMS4B->enCodeMessage($message);

				$encoded = $SMS4B->get_type_of_encoding($message);
				$sms_package = array();

				//preparing data
				foreach($destination as $arInd)
				{
					$dton = $SMS4B->get_ton($arInd);
					$dnpi = $SMS4B->get_npi($arInd);
					$outsms_guid = $SMS4B->CreateGuid();

					$one_sms = '';
					$one_sms = array(
						"G"	=> $outsms_guid, #guid
						"D"	=> $arInd,		 #
						"T" => $dton,		 #
						"N" => $dton,		 #numeric plan indicator
					);

					$sms_package[] = $one_sms;
					$arrparam[] = array(
						"GUID" => $outsms_guid,
						"SenderName" => $sender,
						"Destination" => $arInd,
						"StartSend" => $SMS4B->ForDb($_REQUEST["BEGIN_SEND_AT"]),
						"LastModified" => $SMS4B->ForDb($_REQUEST["BEGIN_SEND_AT"]),
						"CountPart" => "-1",
						"SendPart" => "-1",
						"CodeType" => $encoded,
						"TextMessage" => $SMS4B->decode($body, $encoded),
						"Sale_Order" => $IDOrder ? $IDOrder : 0,
						"Status" => 5,
						"Posting" => $Posting ? $Posting : 0,
						"Events" => $TypeEvents ? $TypeEvents : ''
					);

				}
				$SMS4B->ArrayAdd($arrparam);

				//
				$results_of_package_send["SEND"] = 0;
				$results_of_package_send["NOT_SEND"] = 0;

				//           sms4b_bitrix maxPackage
				if (count($sms_package) < $SMS4B->maxPackage)
				{
					$temp = array();
					$temp = $SMS4B->GetSOAP("SaveGroup", array(
						"SId"	=>	$sessid,
						"Cod"	=>	$code,
						"NRq"	=>  0,
						"Ton"	=>  $ston,
						"Npi"	=>  $snpi,
						"Src"	=>  $sender,
						"Enc"	=>  $encoded,
						"Bdy"	=>  $body,
						"Off"	=>  $dateActual,
						"SUp"	=>  $startUp,
						"Prd"	=>  $period,
						"List"	=>	$sms_package)
					);
					if (intval($temp['result']) > 0)
					{
						$results_of_package_send['SEND'] += $temp["result"];
						$results_of_package_send['NOT_SEND'] += $numbersForSendCount - $temp["result"];
					}
					else
					{
						$results_of_package_send['NOT_SEND'] += count($sms_package);
					}
				}
				else
				{
					//     maxPackage
					$big_array = array_chunk($sms_package, $sms4b_bitrix->maxPackage, true);

					//    - 15
					$countAlreadySendNumbers = 0;
					$i = 0;
					//
					foreach($big_array as $arIndex)
					{
						$currentChunkLength = count($arIndex);
						//      ,

						$temp = array();
						//
						$temp = $sms4b_bitrix->GetSOAP("SaveGroup", array(
							"SId"	=>	$sessid,
							"Cod"	=>	$code,
							"NRq"	=>  0,
							"Ton"	=>  $ston,
							"Npi"	=>  $snpi,
							"Src"	=>  $sender,
							"Enc"	=>  $encoded,
							"Bdy"	=>  $body,
							"Off"	=>  $dateActual,
							"SUp"	=>  $startUp,
							"Prd"	=>  $period,
							"List"	=>	$arIndex)
						);

						//
						if ($temp["groupCode"] > 0)
						{
							$code=$temp["groupCode"];
						}

						if (intval($temp['result']) > 0)
						{
							$results_of_package_send['SEND'] += $temp["result"];
							$results_of_package_send['NOT_SEND'] += $currentChunkLength - $temp["result"];
						}
						else
						{
							$results_of_package_send['NOT_SEND'] += count($sms_package);
						}

						$countAlreadySendNumbers += $currentChunkLength;
						$i++;
					}
				}
			}
		}
	}
}
else
{
	$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
	$arResult["RESULT_MESSAGE"]["MESSAGE"] = $SMS4B->LastError.GetMessage('MOD_OPTIONS');
	$arResult["CAN_SEND"] = "N";
}


if ($arResult["RESULT_MESSAGE"]["TYPE"] == "ERROR")
{
	$strError = $arResult["RESULT_MESSAGE"]["MESSAGE"];
	$dest = htmlspecialchars($_POST["destination_number"]);
	$sender = htmlspecialchars($_POST["sender_number"]);
	$mess = htmlspecialchars($_POST["message"]);
	$date = htmlspecialchars($_POST["DATE"]);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>" onsubmit="form1.sub.disabled=true;">
<script>
var mess = new Array();
mess['hours'] = "<?=GetMessage('HOURS')?>";
mess['minutes'] = "<?=GetMessage('MINUTES')?>";
mess['minutes-more'] = "<?=GetMessage('MINUTES_MORE')?>";
mess['seconds'] = "<?=GetMessage('SECONDS')?>";
mess['in-duration'] = "<?=GetMessage('IN_DURATION')?>";
mess['with-interval'] = "<?=GetMessage('WITH_INTERVAL')?>";
mess['no-balance'] = "<?=GetMessage('NO_BALANCE')?>";

a = new Array();

a["a"]  = "<?=GetMessage("a")?>";
a["A"]  = "<?=GetMessage("A")?>";
a["b"]  = "<?=GetMessage("b")?>";
a["B"] = "<?=GetMessage("B")?>";
a["v"]  = "<?=GetMessage("v")?>";
a["V"]  = "<?=GetMessage("V")?>";
a["w"]  = "<?=GetMessage("w")?>";
a["W"]  = "<?=GetMessage("W")?>";
a["g"]  = "<?=GetMessage("g")?>";
a["G"]  = "<?=GetMessage("G")?>";
a["d"]  = "<?=GetMessage("d")?>";
a["D"]  = "<?=GetMessage("D")?>";
a["e"]  = "<?=GetMessage("e")?>";
a["E"]  = "<?=GetMessage("E")?>";
a["yo"]  = "<?=GetMessage("yo")?>";
a["YO"] = "<?=GetMessage("YO")?>";
a["zh"]  = "<?=GetMessage("zh")?>";
a["Zh"] = "<?=GetMessage("Zh")?>";
a["z"]  = "<?=GetMessage("z")?>";
a["Z"]  = "<?=GetMessage("Z")?>";
a["i"]  = "<?=GetMessage("i")?>";
a["I"] = "<?=GetMessage("I")?>";
a["j"]  = "<?=GetMessage("j")?>";
a["k"]  = "<?=GetMessage("k")?>";
a["K"]  = "<?=GetMessage("K")?>";
a["l"]  = "<?=GetMessage("l")?>";
a["L"]  = "<?=GetMessage("L")?>";
a["m"]  = "<?=GetMessage("m")?>";
a["M"] = "<?=GetMessage("M")?>";
a["n"]  = "<?=GetMessage("n")?>";
a["N"]  = "<?=GetMessage("N")?>";
a["o"]  = "<?=GetMessage("o")?>";
a["O"]  = "<?=GetMessage("O")?>";
a["p"]  = "<?=GetMessage("p")?>";
a["P"]  = "<?=GetMessage("P")?>";
a["r"]  = "<?=GetMessage("r")?>";
a["R"]  = "<?=GetMessage("R")?>";
a["s"]  = "<?=GetMessage("s")?>";
a["S"] = "<?=GetMessage("S")?>";
a["t"]  = "<?=GetMessage("t")?>";
a["T"] = "<?=GetMessage("T")?>";
a["u"]  = "<?=GetMessage("u")?>";
a["U"]  = "<?=GetMessage("U")?>";
a["f"]  = "<?=GetMessage("f")?>";
a["F"]  = "<?=GetMessage("F")?>";
a["x"]  = "<?=GetMessage("x")?>";
a["X"]  = "<?=GetMessage("X")?>";
a["c"]  = "<?=GetMessage("c")?>";
a["C"]  = "<?=GetMessage("C")?>";
a["ch"]  = "<?=GetMessage("ch")?>";
a["Ch"] = "<?=GetMessage("Ch")?>";
a["sh"]  = "<?=GetMessage("sh")?>";
a["Sh"]  = "<?=GetMessage("Sh")?>";
a["shh"]  = "<?=GetMessage("shh")?>";
a["Shh"]  = "<?=GetMessage("Shh")?>";
a["y"]  = "<?=GetMessage("y")?>";
a["Y"] = "<?=GetMessage("Y")?>";
a["yu"]  = "<?=GetMessage("yu")?>";
a["Yu"] = "<?=GetMessage("Yu")?>";
a["ya"]  = "<?=GetMessage("ya")?>";
a["Ya"] = "<?=GetMessage("Ya")?>";

a["mark1"] = "<?=GetMessage('MARK1')?>";
a["mark2"] = "<?=GetMessage('MARK2')?>";
a["mark3"] = "<?=GetMessage('MARK3')?>";
a["mark4"] = "<?=GetMessage('MARK4')?>";

a["<"] = "«";
a[">"] = "»";
a["-"] = "-";

$(document).ready(function() {
	var params = {};
	params.summerTime = '<?=($arTime["tm_isdst"] > 0) ? 1 : 0?>';
	var obSendingForm = new SendingForm(params);
})

</script>

<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="action" value="<?=$action?>">
<input type="hidden" name="OLD_SID" value="<?=$SID?>">
<?

$aTabs = array(
	array("DIV"=>"edit1", "TAB"=>GetMessage("SEND_MESS"), "ICON"=>"sms4b_sendsms", "TITLE"=>GetMessage("SEND_MESS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<?
$tabControl->Begin();

$tabControl->BeginNextTab();

if (COption::GetOptionString("rarus.sms4b","sid") == "")
{
	echo '<tr><td colspan="2">'.CAdminMessage::ShowMessage(GetMessage('CHECK_MODULE_OPT')).'</td></tr>';

	$tabControl->Buttons();
	?>
		<input type = "submit" value="<?=GetMessage("REFRESH")?>" name="refresh">
	<?
	$tabControl->End();

	return;
}

if ($arResult["RESULT_MESSAGE"]["TYPE_DEF"] == "CHANGING_DEF_SENDER_NUMBER")
{
	echo "<tr><td><p>".ShowNote($arResult["RESULT_MESSAGE"]["MESSAGE_DEF"])."</p></td></tr>";
}

if ($arResult["RESULT_MESSAGE"]["TYPE"] == "ERROR")
{
	foreach($arResult["RESULT_MESSAGE"]["MESSAGE"] as $strError)
	{
		echo "<tr><td><p>".ShowMessage($strError)."</p></td></tr>";
	}
}

if ($arResult["RESULT_MESSAGE"]["TYPE"] == "OK")
{
	echo "<tr><td><p>".ShowNote(GetMessage('ENTERED_NUMBERS').$numbersForSendCount)."</p></td></tr>";
	echo '<tr><td><p style="font-size:100%">'.GetMessage('SEPARATED')."</p></td></tr>";
	echo '<tr><td><p style="font-size:100%">'.GetMessage('WAS-SEND').'<span class="was-send">'.$results_of_package_send["SEND"].'</span>'.GetMessage('SECOND-PART')."</p></td></tr>";
	echo '<tr><td><p style="font-size:100%">'.GetMessage('NOT_SEND').'<span class="not-send">'.$results_of_package_send["NOT_SEND"].'</span>'.GetMessage('SECOND-PART')."</p></td></tr>";
}

		global $USER;
		$rsUser_b = CUser::GetByID($USER->GetID());
		$arUser_b = $rsUser_b->Fetch();
	?>

			<tr>
				<td>
					<strong><?=GetMessage("NUMBER_SENDER")?></strong><span class="orange">*</span>
				</td>
				<td>
					<select name="sender_number" id="senderNumber">
					<?foreach ($arResult["ADRESSES"] as $arIndex):?>
						<option value = "<?=htmlspecialchars($arIndex)?>" <?if ($arIndex == COption::GetOptionString("rarus.sms4b","defsender") && !isset($_REQUEST["apply"])):?> selected <?endif;?>
							<?if ($sender == $arIndex):?> selected <?endif;?>><?=$arIndex?></option>
					<?endforeach;?>
					</select>
					<input type="checkbox" class="check" name="def_sender" id="defSender" value="Y">&nbsp;<label for="defSender"><?=GetMessage("DEF_SEND")?></label>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?=GetMessage("NUMBER_DESTINATION")?></strong><span class="orange">*</span><br/>
				</td>
				<td>
					<div class="counters">
						<div id="correct-nums-div"><?=GetMessage('RECEIVERS')?><span id="correct-nums-tip"></span><span id="correct-nums">0</span></div>
						<div id="need-sms-div"><?=GetMessage('NEED_SMS')?><span id="need-sms-tip"></span><span id="need-sms">0</span></div>
						<div id="countDoubled"><a href="javascript:void(0);" id="countDoubledLink"><?=GetMessage('KILL_DOUBLED_NUMBERS')?></a></div>
						<div class="clear"></div>
					</div>
					<textarea id="destinationNumber" name="destination_number" <?if (!$_REQUEST['destination_number']):?>class="gray"<?endif;?>><?if ($_REQUEST['destination_number']):?><?=implode("\n", $destination)?><?else:?><?=GetMessage('DEST_COMMENT')?><?endif;?></textarea>
				</td>

				<!--<td><?#GetMessage("NUMBER_DESTINATION")?></td>
				<td>
					<textarea name="destination_number" cols="50" rows="4"><?#($arResult["RESULT_MESSAGE"]["TYPE"] == "ERROR" ? $dest : '')?></textarea>
				</td>-->
			</tr>
			<tr>
				<td>
					<strong><?=GetMessage("MESSAGE_TEXT")?></strong><span class="orange">*</span>
					<div><a href="javascript:void(0);" id="caption"><?=GetMessage('CAPTION')?></a></div>
				</td>
				<td>
					<div class="counters">
						<div id="lengmess-div"><?=GetMessage('TEXT_LENGTH')?><span id="lengmess-tip"></span><span id="lengmess">0</span></div>
						<div id="size-part-div"><?=GetMessage('PART_SIZE')?><span id="size-part-tip"></span><span id="size-part">160</span></div>
						<div id="parts-div"><?=GetMessage('PARTS')?><span id="parts-tip"></span><span id="parts">0</span></div>
						<div class="clear"></div>
					</div>
					<textarea  id="message" rows="7" name="message" <?if (!$_REQUEST['message']):?>class="gray"<?endif;?>><?if ($_REQUEST['message']):?><?=$_REQUEST['message']?><?else:?><?=GetMessage('TEXT_COMMENT')?><?endif;?></textarea><br/>
					<div id="toLat-div">
					<?=GetMessage('TRANSLIT_TO')?>
						<span id="toLat"><?=GetMessage('LATIN')?></span>
						<?=GetMessage('OR')?>
						 <span id="toKir"><?=GetMessage('KIRIL')?></span>
					</div>
					<div class="clear"></div>
				</td>
			</tr>
			<tr>
				<td>
					<b class="time"><?=GetMessage('TIME_ZONE')?></b>
				</td>
				<td>
					<?=$arResult["GMT_CONTROL"]?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?=GetMessage("BEGIN_SEND_AT")?></b>
				</td>
				<td>
					<input type="text" class="typeinput" id="BEGIN_SEND_AT" name="BEGIN_SEND_AT" size="20" value="<?=gmdate("d.m.Y H:i", mktime()+(COption::GetOptionString('rarus.sms4b', 'gmt')*3600))?>" /><?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array("SHOW_INPUT" => "N", "FORM_NAME" => "form1", "INPUT_NAME" => "BEGIN_SEND_AT", "INPUT_NAME_FINISH" => "", "INPUT_VALUE" => "", "INPUT_VALUE_FINISH" => "", "SHOW_TIME" => "Y", "HIDE_TIMEBAR" => "N"), false);?>
				</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" id="ACTIVE_DATE_ACTUAL" name="ACTIVE_DATE_ACTUAL" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL','DATE_ACTUAL','');" <?if ($_REQUEST["ACTIVE_DATE_ACTUAL"] == "Y"):?> checked <?endif;?> />
					<b><label for="ACTIVE_DATE_ACTUAL" class="normal"><?=GetMessage("DATE_ACTUAL")?></label></b>
				</td>
				<td>
					<input type="text" class="typeinput" id="DATE_ACTUAL" name="DATE_ACTUAL" size="20" value="<?=gmdate("d.m.Y H:i", mktime()+(COption::GetOptionString('rarus.sms4b', 'gmt')*3600)+86400)?>" disabled /><?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array("SHOW_INPUT" => "N", "FORM_NAME" => "form1", "INPUT_NAME" => "DATE_ACTUAL", "INPUT_NAME_FINISH" => "", "INPUT_VALUE" => "", "INPUT_VALUE_FINISH" => "", "SHOW_TIME" => "Y", "HIDE_TIMEBAR" => "N"), false);?>
				</td>
			</tr>
			<tr>
				<td>
					<input type ="checkbox" id="ACTIVE_NIGHT_TIME_NS" name="ACTIVE_NIGHT_TIME_NS" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS','DATE_FROM_NS','DATE_TO_NS');"
					<?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] == "Y"):?> checked <?endif;?> />
							<b><label for="ACTIVE_NIGHT_TIME_NS" class="normal"><?=GetMessage("NIGHT_TIME_NS")?></label></b>
				</td>
				<td>
					<select id="DATE_FROM_NS" name="DATE_FROM_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?>>
						<?$checked_symbol_date_from_ns = chr(87);?>
						<?for ($i = 0; $i < 24; $i++):?>
							<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_from_ns):?> selected <?endif;?> ><?=$i?>:00</option>
						<?endfor;?>
					</select> <?=GetMessage('TO')?>
					<select id="DATE_TO_NS" name="DATE_TO_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?>  <?endif;?> >
						<?$checked_symbol_date_to_ns = chr(73);?>
						<?for ($i = 0; $i < 24; $i++):?>
							<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_to_ns):?> selected <?endif;?> ><?=$i+1?>:00</option>
						<?endfor;?>
					</select>
				</td>
			</tr>

			<tr>
				<td>
					<input type="checkbox" id="uniformSending" name="uniformSending" value="Y" <?if ($_REQUEST['uniformSending']):?>checked<?endif;?>/><label for="uniformSending" class="normal"> <b><?=GetMessage('UNIFORM')?></b></label>
				</td>
				<td>
					<div class="right" id="uniformText">
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" style = "text-align:right" ></td>
			</tr>
	<?

	$disable = true;
	if(($isAdmin || $isDemo) && $isEditMode)
			$disable = false;

	$tabControl->Buttons();
?>
	<input type="submit" value="<?=GetMessage("SUBMIT")?>" name="apply">
<?
	$tabControl->End();
?>
</form>

<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
