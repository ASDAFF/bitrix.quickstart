<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->AddHeadScript($templateFolder."/jquery.js");?>

<?if ($arResult["GOOD_SEND"] != "Y"):?>
	<h2><?=GetMessage('WHOM')?></h2>
	<div id="allSelectors">
		<div id="officerSelector"><?=GetMessage('OFFICER')?></div>
		<div id="toGroupSelector"><?=GetMessage('DEPARTMENT')?></div>
		<div id="freeNumSelector"><?=GetMessage('SOME_NUMBER')?></div>
		<div class="clearClass"></div>
	</div>
<?endif;?>

<?if (count($arResult["ERRORS"]) > 0):?>
	<?foreach ($arResult["ERRORS"] as $arIndex):?>
		<?ShowError($arIndex)?>
	<?endforeach;?>
<?endif;?>
<?if ($arResult["GOOD_SEND"] == "Y"):
	$str4 = GetMessage('NUMBERS_AFTER_SEND') . $arResult['allNumbersCount'];
	$str  = GetMessage('SMS_FOR_SEND') . $arResult["WAS_SEND"].GetMessage('NUMBERS');
	$str2 = GetMessage('INCORRECT_NUMBERS'). $arResult["NOT_SEND"];
	$str3 = GetMessage('DOUBLED_NUMBERS').$arResult['DOUBLED_NUMBERS'];
	
	echo "<p><b>$str4</b></font></p>";
	echo "<p>".GetMessage('FROM_THEM')."</p>";
	ShowNote($str);
	ShowError($str2);
	echo "<p><font color = #FF563F>$str3</font></p>";
	echo "<p><a href = 'http://www.sms4b.ru/office/reports.php' target = '_blank'>".GetMessage('REPORT')."</a></p>";
	echo "<p><a href = '".$APPLICATION->GetCurPage()."'>".GetMessage('SEND_MORE')."</a></p>";
else:?>

<div id="officer">
	<?$APPLICATION->IncludeComponent("rarus.sms4b:selectors.search", ".default", array(
	"STRUCTURE_PAGE" => "structure.php",
	"PM_URL" => "/messages/form/#USER_ID#/",
	"STRUCTURE_FILTER" => "structure",
	"FILTER_1C_USERS" => "N",
	"USERS_PER_PAGE" => "10",
	"FILTER_SECTION_CURONLY" => "N",
	"NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#",
	"SHOW_ERROR_ON_NULL" => "Y",
	"NAV_TITLE" => GetMessage('STAFF'),
	"SHOW_NAV_TOP" => "N",
	"SHOW_NAV_BOTTOM" => "N",
	"SHOW_UNFILTERED_LIST" => "Y",
	"AJAX_MODE" => "Y",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "N",
	"AJAX_OPTION_HISTORY" => "N",
	"FILTER_NAME" => "company_search",
	"FILTER_DEPARTMENT_SINGLE" => "Y",
	"FILTER_SESSION" => "N",
	"DEFAULT_VIEW" => "list",
	"LIST_VIEW" => "list",
	"USER_PROPERTY_TABLE" => array(
		0 => "PERSONAL_PHOTO",
		1 => "FULL_NAME",
		2 => "PERSONAL_PHONE",
		3 => "WORK_POSITION",
		4 => "UF_DEPARTMENT",
	),
	"USER_PROPERTY_EXCEL" => array(
		0 => "FULL_NAME",
		1 => "EMAIL",
		2 => "PERSONAL_PHONE",
		3 => "PERSONAL_FAX",
		4 => "PERSONAL_MOBILE",
		5 => "WORK_POSITION",
		6 => "UF_DEPARTMENT",
		7 => "UF_PHONE_INNER",
	),
	"USER_PROPERTY_LIST" => array(
		0 => "EMAIL",
		1 => "PERSONAL_PHONE",
		2 => "PERSONAL_MOBILE",
		3 => "UF_DEPARTMENT",
	),
	"AJAX_OPTION_ADDITIONAL" => "Y"
	),
	false
);?>
	
	<form method="POST" action="" name="form1" id="form1" onsubmit="templateScriptObject.SWW()">
		<div id="destinationList">
			<div class="dest-list-controls">
				<div id="clearListOfficcerNumbers">
					<div class="inscription"><?=GetMessage('CLEAR_LIST')?></div> 
				</div>
				<div class="numbers-count">
					<b><?=GetMessage('NUMBERS')?></b><font class="fontColor"><span id="smsNumber">0</span></font>	
				</div>
			</div>
			
		    <div class="clearClass"></div>
			<textarea  
				onkeypress="getTelNumber('dest', 'smsNumber');Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber')" 
				onkeyup="getTelNumber('dest', 'smsNumber');Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber')" 
				name="destination" id="dest" wrap="off"><?if ($_REQUEST["TO_OFFICER"] && $arResult["GOOD_SEND"] != "Y"):?><?=htmlspecialchars($_REQUEST["destination"])?><?endif;?></textarea>
		</div>
		
		<div class="clearClass"></div>
		
		<h1><?=GetMessage('MESSAGE_TEXT')?></h1>
		<div id="messageText">
			<div class="counters"><?=GetMessage('TEXT_LENGTH')?><span class="fontColor" id="text-length">0</span></div>
			<div class="counters"><?=GetMessage('PART_SIZE')?><span class="fontColor" id="part-size">160</span></div>
			<div class="counters"><?=GetMessage('PARTS')?><span class="fontColor" id="parts">0</span></div>
			<div class="counters"><?=GetMessage('SMS_TAKEN')?><span class="fontColor" id="need-sms">0</span></div>
            <div class="clearClass"></div>
			<textarea id="message" name = "message" 
				onkeyup="Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber')"    
				onkeypress="Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber')"
				><?if ($_REQUEST["TO_OFFICER"] && $arResult["GOOD_SEND"] != "Y"):?><?=$_REQUEST["message"]?><?endif;?></textarea>
					<div>
						<span class="transliterationText" onclick="document.getElementById('message').value = trans(document.getElementById('message').value); Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber');"><?=GetMessage('TO_LAT')?></span>
					</div>
						<span class="transliterationText" onclick="document.getElementById('message').value = trans_lat_to_kir(document.getElementById('message').value); Counters('message','text-length', 'part-size', 'parts', 'need-sms', 'dest', 'smsNumber');"><?=GetMessage('TO_KIR')?></span>
		</div>
		<div style="clear:both"></div>
		
		<h1><?=GetMessage('ADVANCED')?></h1>
		<table cellpadding="3px">
		<tr>
			<td>
				<b><?=GetMessage('START_WITH')?></b>
				<span class="comments"><?=GetMessage('START_WITH_COMMENT')?></span>
			</td>
			<td>
				<input type="text" class="typeinput" id="BEGIN_SEND_AT" name="BEGIN_SEND_AT" size="20" 
				value = "<?if (isset($_REQUEST["BEGIN_SEND_AT"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["BEGIN_SEND_AT"])):?><?=$_REQUEST["BEGIN_SEND_AT"]?><?else:?><?=ConvertTimeStamp(time(),"FULL")?><?endif;?>" />
				<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
					"SHOW_INPUT" => "N",
					"FORM_NAME" => "form1",
					"INPUT_NAME" => "BEGIN_SEND_AT",
					"INPUT_NAME_FINISH" => "",
					"INPUT_VALUE" => "",
					"INPUT_VALUE_FINISH" => "",
					"SHOW_TIME" => "Y",
					"HIDE_TIMEBAR" => "N"
					),
					false
				);?>
			</td>
		</tr>
		
		<tr>
		<td>
			<input type=checkbox id="ACTIVE_DATE_ACTUAL1" name="ACTIVE_DATE_ACTUAL" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL1','DATE_ACTUAL1','');" 
			<?if ($_REQUEST["ACTIVE_DATE_ACTUAL"] == "Y"):?> checked <?endif;?> /> 
			<b><label for = "ACTIVE_DATE_ACTUAL1"><?=GetMessage('ACTUAL_DATE')?></label></b>
			<span class="comments"><?=GetMessage('ACTUAL_DATE_COMMENT')?></span>
		</td>
		<td>
			<input type="text" class="typeinput" id="DATE_ACTUAL1" name="DATE_ACTUAL" size="20"
			value = "<?if (isset($_REQUEST["DATE_ACTUAL"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["DATE_ACTUAL"])):?><?=$_REQUEST["DATE_ACTUAL"]?> <?else:?><?=ConvertTimeStamp(time()+86400,"FULL");?><?endif;?>" disabled /> 
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
				"SHOW_INPUT" => "N",
				"FORM_NAME" => "form1",
				"INPUT_NAME" => "DATE_ACTUAL",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => "",
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "N"
				),
				false
			);?>
		</td>
		</tr>
		
		<tr>
			<td>
				<input type ="checkbox" id="ACTIVE_NIGHT_TIME_NS1" name="ACTIVE_NIGHT_TIME_NS" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS1','DATE_FROM_NS1','DATE_TO_NS1');" 
				<?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] == "Y"):?> checked <?endif;?> />
				<b><label for="ACTIVE_NIGHT_TIME_NS1"><?=GetMessage('DONT_SEND_IN_NIGHT')?></label></b>
				<span class="comments"><?=GetMessage('DONT_SEND_IN_NIGHT_COMMENT')?></span>
			</td>
			<td>
				<select id="DATE_FROM_NS1" name="DATE_FROM_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?>>
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_from_ns = chr(87);
						}
						else
						{
							$checked_symbol_date_from_ns = $_REQUEST["DATE_FROM_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_from_ns):?> selected <?endif;?> ><?=$i?>:00</option>
					<?endfor;?>
				</select>
				&nbsp;
				<?=GetMessage('ON')?>
				&nbsp;
				<select id="DATE_TO_NS1" name="DATE_TO_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?> >
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_to_ns = chr(73);
						}
						else
						{
							$checked_symbol_date_to_ns = $_REQUEST["DATE_TO_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_to_ns):?> selected <?endif;?> ><?=$i+1?>:00</option>
					<?endfor;?>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type=hidden name="checking_f5" value="<?=md5(time())?>"></td>
			<td><input type="submit" value="<?=GetMessage('SEND')?>" name="TO_OFFICER" /></td>
		</tr>
		</table>
	</form>
</div>

<div id = "toGroup">
	<form method="POST" action = "" name="form2" onsubmit="templateScriptObject.SWW()">
		<div id = "org-structure">
			<h1><?=GetMessage('ORG_STRUCTURE')?></h1>
			
			<?ShowStructureSection($arResult['SECTIONS'], $usersInStructure, true);?>
		</div>
		
		<div id="destinationList">
			<h1><?=GetMessage('NUMBERS_FOR_SEND')?></h1>
			<div class="destinationlist-body">
				<div id="loadNumbers" title="<?=GetMessage('LOAD_NUMBERS_TITLE')?>">
					<div id="loader"><?=GetMessage('BY_GROUPS')?></div>
				</div>
				<div id="clearListNumbers">
					<div><?=GetMessage('CLEAR_LIST')?></div> 
				</div>
				<div id="sms-numbe-groups-counter">
					<b><?=GetMessage('NUMBERS')?></b><font class="fontColor"><span id="smsNumberGroups">0</span></font>	
				</div>
			</div>
		   <div class="clearClass"></div>
		   <textarea 
		   		onkeyup="getTelNumber('destGroups', 'smsNumberGroups');Counters('message2','group-text-length', 'group-part-size', 'group-parts', 'group-need-sms', 'destGroups', 'smsNumberGroups')"
		   		name="destination" id="destGroups" wrap="off"><?if ($_REQUEST['TO_DEPARTMENT'] && $arResult["GOOD_SEND"] != "Y"):?><?=htmlspecialchars($_REQUEST["destination"])?><?endif;?></textarea>
		</div>
		
		<div class="clearClass"></div>

		<div>
			<h1><?=GetMessage('MESSAGE_TEXT')?></h1>	
				<div class="counters"><?=GetMessage('TEXT_LENGTH')?><span class="fontColor" id="group-text-length">0</span></div>
				<div class="counters"><?=GetMessage('PART_SIZE')?><span class="fontColor" id="group-part-size">160</span></div>
				<div class="counters"><?=GetMessage('PARTS')?><span class="fontColor" id="group-parts">0</span></div>
				<div class="counters"><?=GetMessage('SMS_TAKEN')?><span class="fontColor" id="group-need-sms">0</span></div>
	            <div class="clearClass"></div>

				<textarea 
					id="message2" 
					name = "message" 
					onkeyup="Counters('message2','group-text-length', 'group-part-size', 'group-parts', 'group-need-sms', 'destGroups', 'smsNumberGroups')"    
					><?if ($_REQUEST['TO_DEPARTMENT'] && $arResult["GOOD_SEND"] != "Y"):?><?=$_REQUEST["message"]?><?endif;?></textarea>
								<div>
									<span class = "transliterationText" onclick="document.getElementById('message2').value = trans(document.getElementById('message2').value); Counters('message2','group-text-length', 'group-part-size', 'group-parts', 'group-need-sms', 'destGroups', 'smsNumberGroups');"><?=GetMessage('TO_LAT')?></span>
								</div>
									<span class = "transliterationText" onclick="document.getElementById('message2').value = trans_lat_to_kir(document.getElementById('message2').value); Counters('message2','group-text-length', 'group-part-size', 'group-parts', 'group-need-sms', 'destGroups', 'smsNumberGroups');"><?=GetMessage('TO_KIR')?></span>	
		</div>
		
		<h1><?=GetMessage('ADVANCED')?></h1>

		<table cellpadding="3px">
        <tr>
        <td>
			<b><?=GetMessage('START_WITH')?></b>
			<span class="comments"><?=GetMessage('START_WITH_COMMENT')?></span>
        </td>
        <td>
			<input type="text" class="typeinput" id = 'BEGIN_SEND_AT2' name="BEGIN_SEND_AT" size="20" 
			value = "<?if (isset($_REQUEST["BEGIN_SEND_AT"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["BEGIN_SEND_AT"])):?><?=$_REQUEST["BEGIN_SEND_AT"]?><?else:?><?=ConvertTimeStamp(time(),"FULL")?><?endif;?>" />
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
				"SHOW_INPUT" => "N",
				"FORM_NAME" => "form2",
				"INPUT_NAME" => "BEGIN_SEND_AT",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => "",
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "N"
				),
				false
			);?>
		</td>
		</tr>
		
        <tr>
		<td>
			<input type="checkbox" id="ACTIVE_DATE_ACTUAL2" name="ACTIVE_DATE_ACTUAL" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL2','DATE_ACTUAL2','');" 
			<?if ($_REQUEST["ACTIVE_DATE_ACTUAL"] == "Y"):?> checked <?endif;?> /> 
			<b><label for="ACTIVE_DATE_ACTUAL2"><?=GetMessage('ACTUAL_DATE')?></label></b>
			<span class="comments"><?=GetMessage('ACTUAL_DATE_COMMENT')?></span>
        </td>
        <td>
			<input type="text" class="typeinput" id = 'DATE_ACTUAL2' name="DATE_ACTUAL" size="20"
			value = "<?if (isset($_REQUEST["DATE_ACTUAL"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["DATE_ACTUAL"])):?><?=$_REQUEST["DATE_ACTUAL"]?> <?else:?><?=ConvertTimeStamp(time()+86400,"FULL");?><?endif;?>" disabled /> 
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
				"SHOW_INPUT" => "N",
				"FORM_NAME" => "form2",
				"INPUT_NAME" => "DATE_ACTUAL",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => "",
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "N"
				),
				false
			);?>
		</td>
		</tr>
		
		<tr>
			<td>
				<input type = checkbox id = "ACTIVE_NIGHT_TIME_NS2" name = "ACTIVE_NIGHT_TIME_NS" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS2','DATE_FROM_NS2','DATE_TO_NS2');" 
				<?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] == "Y"):?> checked <?endif;?> />
				<b><label for="ACTIVE_NIGHT_TIME_NS2"><?=GetMessage('DONT_SEND_IN_NIGHT')?></label></b>
				<span class="comments"><?=GetMessage('DONT_SEND_IN_NIGHT_COMMENT')?></span>
			</td>
			<td>
				<select id="DATE_FROM_NS2" name="DATE_FROM_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?>>
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_from_ns = chr(87);
						}
						else
						{
							$checked_symbol_date_from_ns = $_REQUEST["DATE_FROM_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value="<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_from_ns):?> selected <?endif;?> ><?=$i?>:00</option>
					<?endfor;?>
				</select>
				&nbsp;
				по
				&nbsp;
				<select id="DATE_TO_NS2" name="DATE_TO_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?> >
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_to_ns = chr(73);
						}
						else
						{
							$checked_symbol_date_to_ns = $_REQUEST["DATE_TO_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_to_ns):?> selected <?endif;?> ><?=$i+1?>:00</option>
					<?endfor;?>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type=hidden name='checking_f5' value="<?=md5(time())?>"></td>
			<td><input type="submit" value="<?=GetMessage('SEND')?>" name="TO_DEPARTMENT" /></td>
		</tr>
		</table>
	</form>
</div>


<div id="freeNum">
		<form method="POST" action = "" name="form3" onsubmit="templateScriptObject.SWW()"> 
		
		<div class="name-number-sender"><?=GetMessage('SENDER_NAME')?></div>
		<div class="free-sender">
			<select name="SENDER">
				<?foreach($arResult['ADDRESSES'] as $arIndex):?>
					<option value="<?=$arIndex?>" <?if ($arIndex == htmlspecialchars($arResult['ADDRESSES_BY_DEFAULT'])):?>selected<?endif;?>><?=$arIndex?></option>
				<?endforeach;?>
			</select>
		</div>
		<div class="clearClass"></div>
		
		<div id="freeNumLeftDiv">
			<h1><?=GetMessage('NUMBERS_FOR_SEND')?></h1>
			<div class="counters"><?=GetMessage('NUMBERS_LIST')?></div>
			<div class="clearClass"></div>
			<div class="counters"><?=GetMessage('NUM_WRITTEN_NUMBERS')?><span class="fontColor" id="freeNumbers">0</span></div>
			 <div class="clearClass"></div>
			<textarea name="destination" class="destNums" id="freeNums" onkeypress="getTelNumber('freeNums', 'freeNumbers');Counters('message3','free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers')" 
			><?if (!$_REQUEST["SIMPLE_SEND"] && $arResult["GOOD_SEND"] != "Y"):?><?=$_REQUEST["destination"]?><?endif;?></textarea>
			<div style="padding:5px 0 0 0">
				<?=GetMessage('COMMENT')?>
			</div>
		</div>
		
		<div id = "messageText">
			<h1><?=GetMessage('MESSAGE_TEXT')?></h1>
			<div class = "counters"><?=GetMessage('TEXT_LENGTH')?><span class="fontColor" id="free-text-length">0</span></div>
			<div class = "counters"><?=GetMessage('PART_SIZE')?><span class="fontColor" id="free-part-size">160</span></div>
			<div class = "counters"><?=GetMessage('PARTS')?><span class="fontColor" id="free-parts">0</span></div>
			<div class = "clearClass"></div>
			<div class = "counters"><?=GetMessage('SMS_TAKEN')?><span class="fontColor" id="free-need-sms">0</span></div>
	        <div class = "clearClass"></div>

			<textarea id="message3" name = "message" 
				onKeyUp="Counters('message3', 'free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers')" 
				onkeypress="return this.onkeyup();"
				><?if (!$_REQUEST["SIMPLE_SEND"] && $arResult["GOOD_SEND"] != "Y"):?><?=$_REQUEST["message"]?><?endif;?></textarea>
							<div>
								<span class="transliterationText" onclick="document.getElementById('message3').value = trans(document.getElementById('message3').value); Counters('message3','free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers');"><?=GetMessage('TO_LAT')?></span>
							</div>
								<span class="transliterationText" onclick="document.getElementById('message3').value = trans_lat_to_kir(document.getElementById('message3').value); Counters('message3','free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers');"><?=GetMessage('TO_KIR')?></span>
		</div>
		
		<div class = "clearClass"></div>
		
		<h1><?=GetMessage('ADVANCED')?></h1>

		<table cellpadding="3px">
        <tr>
        <td>
			<b><?=GetMessage('START_WITH')?></b>
			<span class = "comments"><?=GetMessage('START_WITH_COMMENT')?></span>
        </td>
        <td>
			<input type="text" class="typeinput" id = 'BEGIN_SEND_AT3' name="BEGIN_SEND_AT" size="20" 
			value = "<?if (isset($_REQUEST["BEGIN_SEND_AT"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["BEGIN_SEND_AT"])):?><?=$_REQUEST["BEGIN_SEND_AT"]?><?else:?><?=ConvertTimeStamp(time(),"FULL")?><?endif;?>" />
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
				"SHOW_INPUT" => "N",
				"FORM_NAME" => "form3",
				"INPUT_NAME" => "BEGIN_SEND_AT",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => "",
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "N"
				),
				false
			);?>
		</td>
		</tr>
		
        <tr>
		<td>
			<input type = checkbox id = 'ACTIVE_DATE_ACTUAL3' name = "ACTIVE_DATE_ACTUAL" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL3','DATE_ACTUAL3','');" 
			<?if ($_REQUEST["ACTIVE_DATE_ACTUAL"] == "Y"):?> checked <?endif;?> /> 
			<b><label for = 'ACTIVE_DATE_ACTUAL3'><?=GetMessage('ACTUAL_DATE')?></label></b>
			<span class = "comments"><?=GetMessage('ACTUAL_DATE_COMMENT')?></span>
        </td>
        <td>
			<input type="text" class="typeinput" id = 'DATE_ACTUAL3' name="DATE_ACTUAL" size="20"
			value = "<?if (isset($_REQUEST["DATE_ACTUAL"]) && $_REQUEST["ACTION"] == "SEND" && $DB->IsDate($_REQUEST["DATE_ACTUAL"])):?><?=$_REQUEST["DATE_ACTUAL"]?> <?else:?><?=ConvertTimeStamp(time()+86400,"FULL");?><?endif;?>" disabled /> 
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
				"SHOW_INPUT" => "N",
				"FORM_NAME" => "form3",
				"INPUT_NAME" => "DATE_ACTUAL",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => "",
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "N"
				),
				false
			);?>
		</td>
		</tr>
		
		<tr>
			<td>
				<input type = checkbox id = "ACTIVE_NIGHT_TIME_NS3" name = "ACTIVE_NIGHT_TIME_NS" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS3','DATE_FROM_NS3','DATE_TO_NS3');" 
				<?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] == "Y"):?> checked <?endif;?> />
				<b><label for = 'ACTIVE_NIGHT_TIME_NS3'><?=GetMessage('DONT_SEND_IN_NIGHT')?></label></b>
				<span class = "comments"><?=GetMessage('DONT_SEND_IN_NIGHT_COMMENT')?></span>
			</td>
			<td>
				<select id="DATE_FROM_NS3" name="DATE_FROM_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?>>
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_from_ns = chr(87);
						}
						else
						{
							$checked_symbol_date_from_ns = $_REQUEST["DATE_FROM_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_from_ns):?> selected <?endif;?> ><?=$i?>:00</option>
					<?endfor;?>
				</select>
				&nbsp;
				по
				&nbsp;
				<select id='DATE_TO_NS3' name="DATE_TO_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?> >
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["ACTION"]))
						{
							$checked_symbol_date_to_ns = chr(73);
						}
						else
						{
							$checked_symbol_date_to_ns = $_REQUEST["DATE_TO_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value="<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_to_ns):?> selected <?endif;?> ><?=$i+1?>:00</option>
					<?endfor;?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<input type=hidden name='checking_f5' value="<?=md5(time())?>">
			</td>
			<td>					
				<input type="submit" value="<?=GetMessage('SEND')?>" name="SIMPLE_SEND" /> 
			</td>
		</tr>
		</table>
	</form>	
</div>
<div class="clearClass"></div>

<script>
activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL1','DATE_ACTUAL1', '');
activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS1','DATE_FROM_NS1','DATE_TO_NS1');
activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL2','DATE_ACTUAL2', '');
activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS2','DATE_FROM_NS2','DATE_TO_NS2');
activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL3','DATE_ACTUAL3', '');
activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS3','DATE_FROM_NS3','DATE_TO_NS3');

a = new Array();

a["tz"]  = "<?=GetMessage("tz")?>";
a["u"]  = "<?=GetMessage("u")?>";
a["k"]  = "<?=GetMessage("k")?>";
a["e"]  = "<?=GetMessage("e")?>";
a["n"]  = "<?=GetMessage("n")?>";
a["g"]  = "<?=GetMessage("g")?>";
a["sh"]  = "<?=GetMessage("sh")?>";
a["sch"]  = "<?=GetMessage("sch")?>";
a["z"]  = "<?=GetMessage("z")?>";
a["h"]  = "<?=GetMessage("h")?>";
a["f"]  = "<?=GetMessage("f")?>";
a["v"]  = "<?=GetMessage("v")?>";
a["a"]  = "<?=GetMessage("a")?>";
a["p"]  = "<?=GetMessage("p")?>";
a["r"]  = "<?=GetMessage("r")?>";
a["o"]  = "<?=GetMessage("o")?>";
a["l"]  = "<?=GetMessage("l")?>";
a["d"]  = "<?=GetMessage("d")?>";
a["zh"]  = "<?=GetMessage("zh")?>";
a["ye"]  = "<?=GetMessage("ye")?>";
a["ya"]  = "<?=GetMessage("ya")?>";
a["ch"]  = "<?=GetMessage("ch")?>";
a["s"]  = "<?=GetMessage("s")?>";
a["m"]  = "<?=GetMessage("m")?>";
a["i"]  = "<?=GetMessage("i")?>";
a["t"]  = "<?=GetMessage("t")?>";
a["yo"]  = "<?=GetMessage("yo")?>";
a["b"]  = "<?=GetMessage("b")?>";
a["yu"]  = "<?=GetMessage("yu")?>";
a["yi"] = "<?=GetMessage("yi")?>";
a["y"]  = "<?=GetMessage("y")?>";

a["Y"] = "<?=GetMessage("Y")?>";
a["YI"] = "<?=GetMessage("YI")?>";
a["Tz"]  = "<?=GetMessage("Tz")?>";
a["U"]  = "<?=GetMessage("U")?>";
a["K"]  = "<?=GetMessage("K")?>";
a["E"]  = "<?=GetMessage("E")?>";
a["N"]  = "<?=GetMessage("N")?>";
a["G"]  = "<?=GetMessage("G")?>";
a["Sh"]  = "<?=GetMessage("Sh")?>";
a["Sch"]  = "<?=GetMessage("Sch")?>";
a["Z"]  = "<?=GetMessage("Z")?>";
a["H"]  = "<?=GetMessage("H")?>";
a["F"]  = "<?=GetMessage("F")?>";
a["V"]  = "<?=GetMessage("V")?>";
a["A"]  = "<?=GetMessage("A")?>";
a["P"]  = "<?=GetMessage("P")?>";
a["R"]  = "<?=GetMessage("R")?>";
a["O"]  = "<?=GetMessage("O")?>";
a["L"]  = "<?=GetMessage("L")?>";
a["D"]  = "<?=GetMessage("D")?>";
a["Zh"]  = "<?=GetMessage("Zh")?>";
a["Ye"]  = "<?=GetMessage("Ye")?>";
a["Ya"]  = "<?=GetMessage("Ya")?>";
a["Ch"]  = "<?=GetMessage("Ch")?>";
a["S"]  = "<?=GetMessage("S")?>";
a["M"]  = "<?=GetMessage("M")?>";
a["I"]  = "<?=GetMessage("I")?>";
a["T"]  = "<?=GetMessage("T")?>";
a["YO"]  = "<?=GetMessage("YO")?>";
a["B"]  = "<?=GetMessage("B")?>";
a["Yu"]  = "<?=GetMessage("Yu")?>";
a["<"] = "Ђ";
a[">"] = "ї";
a["-"] = "Ц";

function trans(text)
{
	var ntext = '';
	var ch = '';
	for (var d6 = 0; d6 < text.length; d6++)
	{
		ch = '';
		for(val in a)
		{
			if (text.substr(d6,1) == 'ь' || text.substr(d6,1) == '№')
				ch = "'";
			if (text.substr(d6,1) == 'ъ' || text.substr(d6,1) == 'Џ')
				ch = "\"";
			if (text.substr(d6,1) == '['  || text.substr(d6,1) == '{')
				ch = "(";
			if (text.substr(d6,1) == ']'  || text.substr(d6,1) == '}')
				ch = ")";
			if (text.substr(d6,1) == '\\')
				ch = "/";
			if (text.substr(d6,1) == '^')
				ch = "'";
			if (text.substr(d6,1) == '_')
				ch = "-";
			if (text.substr(d6,1) == '`')
				ch = "'";
			if (text.substr(d6,1) == '|')
				ch = "i";
			if (text.substr(d6,1) == '~')
				ch = "-";
			if (text.substr(d6,1) == 'є')
				ch = "N";
			if (text.substr(d6,1) == 'Ф')
				ch = "\"";	
			
		  	if (text.substr(d6,1) == a[val])
				ch = val;
		}
		
		if (ch == "")
		    ntext = ntext + text.substr(d6,1);
		else
			ntext = ntext + ch;
    }
	return ntext;
}

function trans_lat_to_kir(text)
{
	var ntext = '';
	
	for (var d6 = 0; d6 < text.length; d6++)
	{
		var ch = '';
		for(var val in a)
		{ 
			if (text.substr(d6,3) == val) ch = a[val];
		}
		//if search by 3 gave not result
		if (ch == "")
		{
			//search by 2
			for(var val in a)
			{
		 		if (text.substr(d6,2) == val) ch = a[val];
			}
			
			if (ch == "")
			{
				//search by 1
				for(var val in a)
				{
					if (text.substr(d6,1) == val) ch = a[val];
					
					if (text.substr(d6,1) == "'") ch = "ь";
					if (text.substr(d6,1) == "\"") ch = "ъ";
				}
				
				if (ch == "")
					ntext = ntext + text.substr(d6,1);
				else
					ntext = ntext + ch;	
			}
			else
			{
				ntext = ntext + ch;
				d6+=1;
			}
		}
		else
		{
			ntext = ntext + ch;
			d6+=2;
		}
	}		
	return ntext;
}

function isRus(text)
{
	for (var d6 = 0; d6 < text.length; d6++)
	{
		if (text.charCodeAt(d6) > 126 || text.charAt(d6) == '[' || text.charAt(d6) == "]" || text.charAt(d6) == "\\" || text.charAt(d6) == "^" || text.charAt(d6) == "_" || text.charAt(d6) == "`" || text.charAt(d6) == "{" || text.charAt(d6) == "}" || text.charAt(d6) == "|" || text.charAt(d6) == "~")
		return true;
	}
	return false;
}

$(document).ready(function()
{
	/*var message = $('#message');
	message.val(trans(message.val()));*/	
	var is_set = false;
	<?if (strlen($_REQUEST["TO_OFFICER"]) > 0 || strlen($_REQUEST["FOR_OFFICER"]) > 0 || strlen($_REQUEST["PAGEN_1"]) > 0):?>
		$('#officer').fadeIn('slow');
		$('#toGroup').hide();
		$('#freeNum').hide();
		is_set = true;	
	<?else:?>
		$('#officer').fadeIn('slow');	      
	<?endif;?>
	
	<?if (strlen($_REQUEST["TO_DEPARTMENT"]) > 0):?>
		$('#officer').hide();
		$('#toGroup').fadeIn('slow');
		$('#freeNum').hide();	
		is_set = true;
	<?endif;?>
	
	<?if (strlen($_REQUEST["SIMPLE_SEND"]) > 0):?>
		$('#officer').hide();
		$('#toGroup').hide();
		$('#freeNum').fadeIn('slow');
		is_set = true;	
	<?endif;?>
	
	if(!is_set)
	{
		$('#toGroup').hide();
		$('#freeNum').hide();
	}
	
	$('#officerSelector').click(function () 
		{
			$('#toGroup').hide();
			$('#freeNum').hide();
			$('#officer').fadeIn('slow');
		});
		
	$('#toGroupSelector').click(function () 
	{
		$('#officer').hide();
		$('#freeNum').hide();
		$('#toGroup').fadeIn('slow');
	});
	
	$('#freeNumSelector').click(function () 
	{
		$('#officer').hide();
		$('#toGroup').hide();
		$('#freeNum').fadeIn('slow');
	});
});

var templateScriptObject = new CAjaxSms('<?=$arResult['PATH_FOR_ASYNC_REQUESTS']?>', 'org-structure', '<?=bitrix_sessid()?>', '<?=$arResult['MESS']?>');
</script>
<?endif;?>
