<?
if(!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	Array("webdebug_ruble_font_char", GetMessage("WEBDEBUG_RUBLE_FONT_CHAR"), "a", Array("radio-with-image", 5)),
	Array("webdebug_ruble_symbol_location", GetMessage("WEBDEBUG_RUBLE_SYMBOL_LOCATION"), "R", Array("radio", 1), array("R"=>GetMessage("WEBDEBUG_RUBLE_SYMBOL_LOCATION_R"), "L"=>GetMessage("WEBDEBUG_RUBLE_SYMBOL_LOCATION_L"))),
	Array("webdebug_ruble_add_space", GetMessage("WEBDEBUG_RUBLE_ADD_SPACE"), "Y", Array("checkbox", 1)),
	Array("webdebug_ruble_regex_exclude", GetMessage("WEBDEBUG_RUBLE_REGEX_EXCLUDE"), "", Array("textarea", 50, 10)),
	Array("webdebug_ruble_regex_include", GetMessage("WEBDEBUG_RUBLE_REGEX_INCLUDE"), "", Array("textarea", 50, 10)),
	Array("webdebug_ruble_skip_post", GetMessage("WEBDEBUG_RUBLE_SKIP_POST"), "Y", Array("checkbox", 1)),
	#Array("webdebug_ruble_skipseoprops", GetMessage("WEBDEBUG_RUBLE_SKIPSEOPROPS"), "", Array("checkbox", 1)),
	Array("webdebug_ruble_own_tag", GetMessage("WEBDEBUG_RUBLE_OWN_TAG"), "", Array("checkbox", 1)),
	Array("webdebug_ruble_additional_code", GetMessage("WEBDEBUG_RUBLE_ADDITIONAL_CODE"), "", Array("textarea", 50, 14)),
	Array("webdebug_ruble_title", GetMessage("WEBDEBUG_RUBLE_TITLE"), "", Array("text", 50)),
);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("WEBDEBUG_RUBLE_TAB_1"), "ICON" => "webdebug_ruble_params", "TITLE" => GetMessage("WEBDEBUG_RUBLE_TAB_1_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if(strlen($RestoreDefaults)>0) {
		COption::RemoveOption("webdebug.ruble");
	} else {
		foreach($arAllOptions as $arOption) {
			$name=$arOption[0];
			$val=$_REQUEST[$name];
			if($arOption[2][0]=="checkbox" && $val!="Y")
				$val="N";
			COption::SetOptionString("webdebug.ruble", $name, $val, $arOption[1]);
		}
	}
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

$tabControl->Begin();
CModule::IncludeModule("wedebug.ruble");
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
	<?$tabControl->BeginNextTab();?>
		<tr>
			<td width="50%"><?=GetMessage("WEBDEBUG_RUBLE_EXAMPLE_1");?>:</td>
			<td style="font-size:24px;"><?=CurrencyFormat_Ruble(mt_rand(10000, 999999).".34", "RUB", 2);?></td>
		</tr>
		<?
		foreach($arAllOptions as $arOption):
			$val = COption::GetOptionString("webdebug.ruble", $arOption[0], $arOption[2]);
			$OptionValues = $arOption[4];
			$type = $arOption[3];
		?>
		<tr>
			<td valign="top" width="50%"><?
				if($type[0]=="checkbox")
					echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
				else
					echo $arOption[1];?>:</td>
			<td valign="top" width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked='checked'";?> />
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" />
				<?elseif($type[0]=="textarea"):?>
					<textarea cols="<?echo $type[1]?>" rows="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?elseif($type[0]=="radio"):?>
					<?foreach ($OptionValues as $OptionValue => $OptionName):?>
						<label>
							<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="<?=$OptionValue?>"<?if($OptionValue==$val)echo" checked='checked'";?> />
							<?=$OptionName?>
						</label><br/>
					<?endforeach?>
				<?elseif($type[0]=="radio-with-image"):?>
					<label style="font-size:14px; font-family:'Arial';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="a" <?if($val=="a")echo" checked";?> />
						Arial Regular (10 999 <span class='webdebug-ruble-symbol'>a</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="b" <?if($val=="b")echo" checked";?> />
						Arial Italic (10 999 <span class='webdebug-ruble-symbol'>b</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="c" <?if($val=="c")echo" checked";?> />
						Arial Bold (10 999 <span class='webdebug-ruble-symbol'>c</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="d" <?if($val=="d")echo" checked";?> />
						Arial Bold Italic (10 999 <span class='webdebug-ruble-symbol'>d</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="e" <?if($val=="e")echo" checked";?> />
						Georgia Regular (10 999 <span class='webdebug-ruble-symbol'>e</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="f" <?if($val=="f")echo" checked";?> />
						Georgia Italic (10 999 <span class='webdebug-ruble-symbol'>f</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="g" <?if($val=="g")echo" checked";?> />
						Georgia Bold (10 999 <span class='webdebug-ruble-symbol'>g</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="h" <?if($val=="h")echo" checked";?> />
						Georgia Bold Italic (10 999 <span class='webdebug-ruble-symbol'>h</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Tahoma';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="i" <?if($val=="i")echo" checked";?> />
						Tahoma Regular (10 999 <span class='webdebug-ruble-symbol'>i</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Tahoma'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="j" <?if($val=="j")echo" checked";?> />
						Tahoma Bold (10 999 <span class='webdebug-ruble-symbol'>j</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="k" <?if($val=="k")echo" checked";?> />
						Times Regular (10 999 <span class='webdebug-ruble-symbol'>k</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="l" <?if($val=="l")echo" checked";?> />
						Times Italic (10 999 <span class='webdebug-ruble-symbol'>l</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="m" <?if($val=="m")echo" checked";?> />
						Times Bold (10 999 <span class='webdebug-ruble-symbol'>m</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="n" <?if($val=="n")echo" checked";?> />
						Times Bold Italic (10 999 <span class='webdebug-ruble-symbol'>n</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Lucida';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="o" <?if($val=="o")echo" checked";?> />
						Lucida Regular (10 999 <span class='webdebug-ruble-symbol'>o</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="p" <?if($val=="p")echo" checked";?> />
						Verdana Regular (10 999 <span class='webdebug-ruble-symbol'>p</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="q" <?if($val=="q")echo" checked";?> />
						Verdana Italic (10 999 <span class='webdebug-ruble-symbol'>q</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="r" <?if($val=="r")echo" checked";?> />
						Verdana Bold (10 999 <span class='webdebug-ruble-symbol'>r</span>)
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="s" <?if($val=="s")echo" checked";?> />
						Verdana Bold Italic (10 999 <span class='webdebug-ruble-symbol'>s</span>)
					</label><br/>
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=GetMessage("WEBDEBUG_RUBLE_SAVE")?>" title="<?=GetMessage("WEBDEBUG_RUBLE_SAVE")?>">
		<input type="submit" name="Apply" value="<?=GetMessage("WEBDEBUG_RUBLE_APPLY")?>" title="<?=GetMessage("WEBDEBUG_RUBLE_APPLY")?>">
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="button" name="Cancel" value="<?=GetMessage("WEBDEBUG_RUBLE_CANCEL")?>" title="<?=GetMessage("WEBDEBUG_RUBLE_CANCEL")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("WEBDEBUG_RUBLE_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("WEBDEBUG_RUBLE_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("WEBDEBUG_RUBLE_RESTORE_DEFAULTS")?>">
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>