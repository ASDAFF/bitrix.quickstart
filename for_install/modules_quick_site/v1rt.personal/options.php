<?
$module_id = "v1rt.personal";
global $USER;
if($USER->IsAdmin()):

    global $MESS;
    include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/", "/options.php"));
    include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/lang/", "/options.php"));
    
    $DEMO = COption::GetOptionString($module_id, "v1rt_personal_demo_data");
    $SITE_ID_INSTALL = COption::GetOptionString($module_id, "v1rt_personal_site_id");
    
    $aTabs = array(
    	array("DIV" => "edit1", "TAB" => GetMessage("V1RT_SETTING_TAB_1"), "ICON" => "v1rt_personal_page_icon", "TITLE" => GetMessage("V1RT_SETTING_TAB_1_DESC")),
        array("DIV" => "edit2", "TAB" => GetMessage("V1RT_SETTING_TAB_2"), "ICON" => "v1rt_personal_page_icon", "TITLE" => GetMessage("V1RT_SETTING_TAB_2_DESC")),
        array("DIV" => "edit3", "TAB" => GetMessage("V1RT_SETTING_TAB_3"), "ICON" => "v1rt_personal_page_icon", "TITLE" => GetMessage("V1RT_SETTING_TAB_3_DESC")),
        array("DIV" => "edit5", "TAB" => GetMessage("V1RT_SETTING_TAB_5"), "ICON" => "v1rt_personal_page_icon", "TITLE" => GetMessage("V1RT_SETTING_TAB_5_DESC")),
    );
    
    if($DEMO == "Y")
        $aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("V1RT_SETTING_TAB_4"), "ICON" => "v1rt_personal_page_icon", "TITLE" => GetMessage("V1RT_SETTING_TAB_4_DESC"));
    
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    
    if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && check_bitrix_sessid())
    {
        if(strlen($RestoreDefaults) > 0)
    	{
    		COption::RemoveOption($module_id);
    	}
    	else
    	{
            COption::SetOptionString($module_id, "v1rt_personal_twitter", strip_tags($_POST["v1rt_personal_twitter"]));
            COption::SetOptionString($module_id, "v1rt_personal_phone", strip_tags($_POST["v1rt_personal_phone"]));
            COption::SetOptionString($module_id, "v1rt_personal_email", strip_tags($_POST["v1rt_personal_email"]));
            COption::SetOptionString($module_id, "v1rt_personal_vk", strip_tags($_POST["v1rt_personal_vk"]));
            COption::SetOptionString($module_id, "v1rt_personal_fb", strip_tags($_POST["v1rt_personal_fb"]));
            COption::SetOptionString($module_id, "v1rt_personal_twitter_consumer_key", strip_tags($_POST["v1rt_personal_twitter_consumer_key"]));
            COption::SetOptionString($module_id, "v1rt_personal_twitter_consumer_secret", strip_tags($_POST["v1rt_personal_twitter_consumer_secret"]));
            COption::SetOptionString($module_id, "v1rt_personal_twitter_user_token", strip_tags($_POST["v1rt_personal_twitter_user_token"]));
            COption::SetOptionString($module_id, "v1rt_personal_twitter_user_secret", strip_tags($_POST["v1rt_personal_twitter_user_secret"]));
            COption::SetOptionString($module_id, "v1rt_personal_type_header", strip_tags($_POST["v1rt_personal_type_header"]));
            COption::SetOptionString($module_id, "v1rt_personal_header_image", strip_tags($_POST["v1rt_personal_header_image"]));
            COption::SetOptionString($module_id, "v1rt_personal_ver_2_logo", strip_tags($_POST["v1rt_personal_ver_2_logo"]));
            COption::SetOptionString($module_id, "v1rt_personal_wm_google", strip_tags($_POST["v1rt_personal_wm_google"]));
            COption::SetOptionString($module_id, "v1rt_personal_wm_yandex", strip_tags($_POST["v1rt_personal_wm_yandex"]));
    	}
    
    	if(strlen($_REQUEST["back_url_settings"]) > 0)
    	{
    		if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
    			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
    		else
    			LocalRedirect($_REQUEST["back_url_settings"]);
    	}
    	else
    	{
    		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
    	}
    }
    
    $arOptions_["V1RT_PERSONAL_PHONE"]                      = COption::GetOptionString($module_id, "v1rt_personal_phone");
    $arOptions_["V1RT_PERSONAL_EMAIL"]                      = COption::GetOptionString($module_id, "v1rt_personal_email");
    $arOptions_["V1RT_PERSONAL_VK"]                         = COption::GetOptionString($module_id, "v1rt_personal_vk");
    $arOptions_["V1RT_PERSONAL_FB"]                         = COption::GetOptionString($module_id, "v1rt_personal_fb");
    $arOptions_["V1RT_PERSONAL_TWITTER"]                    = COption::GetOptionString($module_id, "v1rt_personal_twitter");
    $arOptions_["V1RT_PERSONAL_TWITTER_CONSUMER_KEY"]       = COption::GetOptionString($module_id, "v1rt_personal_twitter_consumer_key");
    $arOptions_["V1RT_PERSONAL_TWITTER_CONSUMER_SECRET"]    = COption::GetOptionString($module_id, "v1rt_personal_twitter_consumer_secret");
    $arOptions_["V1RT_PERSONAL_TWITTER_USER_TOKEN"]         = COption::GetOptionString($module_id, "v1rt_personal_twitter_user_token");
    $arOptions_["V1RT_PERSONAL_TWITTER_USER_SECRET"]        = COption::GetOptionString($module_id, "v1rt_personal_twitter_user_secret");
    $arOptions_["V1RT_PERSONAL_TYPE_HEADER"]                = COption::GetOptionString($module_id, "v1rt_personal_type_header");
    $arOptions_["V1RT_PERSONAL_HEADER_IMAGE"]               = COption::GetOptionString($module_id, "v1rt_personal_header_image");
    $arOptions_["V1RT_PERSONAL_LOGO"]                       = COption::GetOptionString($module_id, "v1rt_personal_ver_2_logo");
    $arOptions_["V1RT_PERSONAL_WM_GOOGLE"]                  = COption::GetOptionString($module_id, "v1rt_personal_wm_google");
    $arOptions_["V1RT_PERSONAL_WM_YANDEX"]                  = COption::GetOptionString($module_id, "v1rt_personal_wm_yandex");
    
    $tabControl->Begin();
    ?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>" enctype="multipart/form-data" name="post_form" id="post_form"><?
    bitrix_sessid_post();
    
    $tabControl->BeginNextTab();?>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_type_header"><?=GetMessage("V1RT_PERSONAL_TYPE_HEADER")?>:</label></td>
    		<td valign="top" width="50%">
                <select name="v1rt_personal_type_header" id="v1rt_personal_type_header">
                    <option value="0"<?=($arOptions_["V1RT_PERSONAL_TYPE_HEADER"] == 0 ? ' selected="selected"' : '')?>><?=GetMessage("V1RT_PERSONAL_TYPE_HEADER_1")?></option>
                    <option value="1"<?=($arOptions_["V1RT_PERSONAL_TYPE_HEADER"] == 1 ? ' selected="selected"' : '')?>><?=GetMessage("V1RT_PERSONAL_TYPE_HEADER_2")?></option>
                    <option value="2"<?=($arOptions_["V1RT_PERSONAL_TYPE_HEADER"] == 2 ? ' selected="selected"' : '')?>><?=GetMessage("V1RT_PERSONAL_TYPE_HEADER_3")?></option>
                </select>
            </td>
        </tr>
        <tr>
    		<td><?=GetMessage("V1RT_PERSONAL_HEADER_IMAGE")?>:</td>
    		<td>
    			<input type="text" name="v1rt_personal_header_image" id="v1rt_personal_header_image" value="<?=$arOptions_["V1RT_PERSONAL_HEADER_IMAGE"]?>" size="30"/>
    			<input type="button" value="<?=GetMessage("V1RT_PERSONAL_OPEN")?>" OnClick="BtnClick()"/>
    			<?
    			CAdminFileDialog::ShowScript
    			(
    				Array(
    					"event" => "BtnClick",
    					"arResultDest" => array("FORM_NAME" => "post_form", "FORM_ELEMENT_NAME" => "v1rt_personal_header_image"),
    					"arPath" => array("SITE" => SITE_ID, "PATH" => SITE_TEMPLATE_PATH),
    					"select" => 'F',
    					"operation" => 'O',
    					"showUploadTab" => true,
    					"showAddToMenuTab" => false,
    					"fileFilter" => 'image',
    					"allowAllFiles" => true,
    					"SaveConfig" => true,
    				)
    			);
    			?>
    		</td>
    	</tr>
        <tr>
    		<td><?=GetMessage("V1RT_PERSONAL_LOGO")?>:</td>
    		<td>
    			<input type="text" name="v1rt_personal_ver_2_logo" id="v1rt_personal_ver_2_logo" value="<?=$arOptions_["V1RT_PERSONAL_LOGO"]?>" size="30"/>
    			<input type="button" value="<?=GetMessage("V1RT_PERSONAL_OPEN")?>" OnClick="BtnClick_Logo()"/>
    			<?
    			CAdminFileDialog::ShowScript
    			(
    				array(
    					"event" => "BtnClick_Logo",
    					"arResultDest" => array("FORM_NAME" => "post_form", "FORM_ELEMENT_NAME" => "v1rt_personal_ver_2_logo"),
    					"arPath" => array("SITE" => SITE_ID, "PATH" => SITE_TEMPLATE_PATH),
    					"select" => 'F',
    					"operation" => 'O',
    					"showUploadTab" => true,
    					"showAddToMenuTab" => false,
    					"fileFilter" => 'image',
    					"allowAllFiles" => true,
    					"SaveConfig" => true,
    				)
    			);
    			?>
    		</td>
    	</tr>
    <?$tabControl->BeginNextTab();?>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_twitter"><?=GetMessage("V1RT_PERSONAL_TWITTER")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_TWITTER"])?>" name="v1rt_personal_twitter" size="40" id="v1rt_personal_twitter"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_twitter_consumer_key"><?=GetMessage("V1RT_PERSONAL_TWITTER_CONSUMER_KEY")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_TWITTER_CONSUMER_KEY"])?>" name="v1rt_personal_twitter_consumer_key" size="40" id="v1rt_personal_twitter_consumer_key"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_twitter_consumer_secret"><?=GetMessage("V1RT_PERSONAL_TWITTER_CONSUMER_SECRET")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_TWITTER_CONSUMER_SECRET"])?>" name="v1rt_personal_twitter_consumer_secret" size="40" id="v1rt_personal_twitter_consumer_secret"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_twitter_user_token"><?=GetMessage("V1RT_PERSONAL_TWITTER_USER_TOKEN")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_TWITTER_USER_TOKEN"])?>" name="v1rt_personal_twitter_user_token" size="40" id="v1rt_personal_twitter_user_token"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_twitter_user_secret"><?=GetMessage("V1RT_PERSONAL_TWITTER_USER_SECRET")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_TWITTER_USER_SECRET"])?>" name="v1rt_personal_twitter_user_secret" size="40" id="v1rt_personal_twitter_user_secret"/></td>
        </tr>
    <?$tabControl->BeginNextTab();?>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_phone"><?=GetMessage("V1RT_PERSONAL_PHONE")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_PHONE"])?>" name="v1rt_personal_phone" size="40" id="v1rt_personal_phone"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_email"><?=GetMessage("V1RT_PERSONAL_EMAIL")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_EMAIL"])?>" name="v1rt_personal_email" size="40" id="v1rt_personal_email"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_vk"><?=GetMessage("V1RT_PERSONAL_VK")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_VK"])?>" name="v1rt_personal_vk" size="40" id="v1rt_personal_vk"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_fb"><?=GetMessage("V1RT_PERSONAL_FB")?>:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_FB"])?>" name="v1rt_personal_fb" size="40" id="v1rt_personal_fb"/></td>
        </tr>
    <?$tabControl->BeginNextTab();?>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_wm_google">Google:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_WM_GOOGLE"])?>" name="v1rt_personal_wm_google" size="40" id="v1rt_personal_wm_google"/></td>
        </tr>
        <tr>
    		<td valign="top" width="50%" style="padding-top: 14px;"><label for="v1rt_personal_wm_yandex">Yandex:</label></td>
    		<td valign="top" width="50%"><input type="text" value="<?echo htmlspecialchars($arOptions_["V1RT_PERSONAL_WM_YANDEX"])?>" name="v1rt_personal_wm_yandex" size="40" id="v1rt_personal_wm_yandex"/></td>
        </tr>
    <?$tabControl->BeginNextTab();?>
        <?if($DEMO == "Y"):?>
            <tr>
        		<td valign="top" style="padding-top: 14px;" colspan="2"><?=GetMessage("V1RT_DEMO_DATA")?></td>
            </tr>
        <?endif;?>    
    <?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>"/>
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>"/>
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'"/>
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>"/>
	<?endif?>
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>"/>
	<?=bitrix_sessid_post();?>
    <?$tabControl->End();?>
    </form>
<?endif;?>