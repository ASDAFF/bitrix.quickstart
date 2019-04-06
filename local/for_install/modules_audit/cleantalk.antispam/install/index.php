<?php

global $MESS;
IncludeModuleLangFile(__FILE__);

/**
 * Installer for CleanTalk module
 *
 * @author 	Cleantalk team <http://cleantalk.org>
 */
class cleantalk_antispam extends CModule {

    var $MODULE_ID = 'cleantalk.antispam';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    var $template_dir;
    var $template_file;
    var $system_template_dir;
    var $local_template_dir;
    var $local_compo_template_dir;
    var $pattern;
    var $ct_template_addon_tag;
    var $ct_template_addon_body_register;
    var $ct_template_addon_body_comment;
    var $errors;
    var $messages;
    var $template_messages;

    function cleantalk_antispam() {
        global $DOCUMENT_ROOT;
        $arModuleVersion = array();

	$path = str_replace("\\", "/", __FILE__);
	$path = substr($path, 0, strlen($path) - strlen("/index.php"));
	include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = "1.1.4";
            $this->MODULE_VERSION_DATE = "2014-03-20 00:00:00";
        }
        $this->MODULE_NAME = GetMessage('CLEANTALK_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('CLEANTALK_MODULE_DESCRIPTION');
	$this->PARTNER_NAME = "CleanTalk"; 
	$this->PARTNER_URI = "http://www.cleantalk.org";

	// Values for all templates
	$this->ct_template_addon_tag = 'CLEANTALK template addon';
	$this->ct_template_addon_body_register = "\n" . '<?php if(CModule::IncludeModule("cleantalk.antispam")) echo CleantalkAntispam::FormAddon("register"); ?>' . "\n";
	$this->ct_template_addon_body_comment = "\n" . '<?php if(CModule::IncludeModule("cleantalk.antispam")) echo CleantalkAntispam::FormAddon("comment"); ?>' . "\n";

	// Values for system.auth.registration default template
	$this->SAR_template_dir  = '.default'; // without ending slash
	$this->SAR_template_file = 'template.php';
	//...with ending slash
	$this->SAR_system_template_dir = $DOCUMENT_ROOT.'/bitrix/components/bitrix/system.auth.registration/templates/';
	$this->SAR_local_template_dir = $DOCUMENT_ROOT.'/bitrix/templates/.default/';
	$this->SAR_local_compo_template_dir = $this->SAR_local_template_dir.'components/bitrix/system.auth.registration/';
	$this->SAR_pattern = '/(<\?\/\/[\s\*]*\/User properties)/i';
	$this->SAR_message = "/bitrix/templates/.default/components/bitrix/system.auth.registration/.default/template.php";

	// Values for blog.post.comment default template
	$this->BPC_template_dir  = '.default'; // without ending slash
	$this->BPC_template_file = 'template.php';
	//...with ending slash
	// from /components/bitrix/blog/templates/.default/bitrix/blog.post.comment
        // to   /templates/.default/components/bitrix/blog/.default/bitrix/blog.post.comment
	$this->BPC_system_template_dir = $DOCUMENT_ROOT.'/bitrix/components/bitrix/blog/templates/.default/bitrix/blog.post.comment/';
	$this->BPC_local_template_dir = $DOCUMENT_ROOT.'/bitrix/templates/.default/';
	$this->BPC_local_compo_template_dir = $this->BPC_local_template_dir.'components/bitrix/blog/.default/bitrix/blog.post.comment/';
	$this->BPC_pattern = '/(<\/form>)/i';
	$this->BPC_message = "/bitrix/templates/.default/components/bitrix/blog/.default/bitrix/blog.post.comment/.default/template.php";

	// Values for forum.comments default template
	$this->FC_template_dir  = '.default'; // without ending slash
	$this->FC_template_file = 'template.php';
	//...with ending slash
        // from /components/bitrix/forum/templates/.default/bitrix/forum.post_form
        // to   /templates/.default/components/bitrix/forum/.default/bitrix/forum.post_form
	$this->FC_system_template_dir = $DOCUMENT_ROOT.'/bitrix/components/bitrix/forum/templates/.default/bitrix/forum.post_form/';
	$this->FC_local_template_dir = $DOCUMENT_ROOT.'/bitrix/templates/.default/';
	$this->FC_local_compo_template_dir = $this->FC_local_template_dir.'components/bitrix/forum/.default/bitrix/forum.post_form/';
	$this->FC_pattern = '/(<\/form>)/i';
	$this->FC_message = "/bitrix/templates/.default/components/bitrix/forum/.default/bitrix/forum.post_form/.default/template.php";

	// Values for prmedia.treelike_comments default template
	$this->PTLC_template_dir  = '.default'; // without ending slash
	$this->PTLC_template_file = 'template.php';
	//...with ending slash
        // from /components/prmedia/treelike_comments/templates
        // to   /templates/.default/components/prmedia/treelike_comments
	$this->PTLC_system_template_dir = $DOCUMENT_ROOT.'/bitrix/components/prmedia/treelike_comments/templates/';
	$this->PTLC_local_template_dir = $DOCUMENT_ROOT.'/bitrix/templates/.default/';
	$this->PTLC_local_compo_template_dir = $this->PTLC_local_template_dir.'components/prmedia/treelike_comments/';
	$this->PTLC_pattern = '/(<\/fieldset>)/i';
	$this->PTLC_message = "/bitrix/templates/.default/components/prmedia/treelike_comments/.default/template.php";

	$this->errors = array();
	$this->messages = array();
	$this->template_messages = array();
    }

    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        if($this->InstallDB() && $this->InstallFiles()){
	    RegisterModule('cleantalk.antispam');
            RegisterModuleDependences('main', 'OnEventLogGetAuditTypes', 'cleantalk.antispam', 'CleantalkAntispam', 'OnEventLogGetAuditTypesHandler');
	    RegisterModuleDependences('main', 'OnBeforeUserRegister', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeUserRegisterHandler');
            if (IsModuleInstalled('blog')){
              RegisterModuleDependences('blog', 'OnBeforeCommentAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeCommentAddHandler');
            }
            if (IsModuleInstalled('forum')){
              RegisterModuleDependences('forum', 'OnBeforeMessageAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeMessageAddHandler');
              RegisterModuleDependences('forum', 'OnAfterMessageAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnAfterMessageAddHandler');
              RegisterModuleDependences('forum', 'OnMessageModerate', 'cleantalk.antispam', 'CleantalkAntispam', 'OnMessageModerateHandler');
              RegisterModuleDependences('forum', 'OnBeforeMessageDelete', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeMessageDeleteHandler');
            }
            if (IsModuleInstalled('prmedia.treelikecomments')){
              RegisterModuleDependences('prmedia.treelikecomments', 'OnBeforePrmediaCommentAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforePrmediaCommentAddHandler');
            }
	}
	if(!empty($this->template_messages)){
	    $this->messages[] = GetMessage("CLEANTALK_TEMPLATES_HEADER");
	    foreach($this->template_messages as $val)
		$this->messages[] = $val;
	    $this->messages[] = '<br />' . GetMessage("CLEANTALK_TEMPLATES_FOOTER") . '<br />';
	}
        $GLOBALS["errors"] = $this->errors;
        $GLOBALS["messages"] = $this->messages;
        $APPLICATION->IncludeAdminFile(GetMessage('CLEANTALK_INSTALL_TITLE'), $DOCUMENT_ROOT.'/bitrix/modules/cleantalk.antispam/install/step.php');
    }

    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        if (IsModuleInstalled('blog')){
          UnRegisterModuleDependences('blog', 'OnBeforeCommentAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeCommentAddHandler');
        }
        if (IsModuleInstalled('forum')){
          UnRegisterModuleDependences('forum', 'OnBeforeMessageAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeMessageAddHandler');
          UnRegisterModuleDependences('forum', 'OnAfterMessageAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnAfterMessageAddHandler');
          UnRegisterModuleDependences('forum', 'OnMessageModerate', 'cleantalk.antispam', 'CleantalkAntispam', 'OnMessageModerateHandler');
          UnRegisterModuleDependences('forum', 'OnBeforeMessageDelete', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeMessageDeleteHandler');
        }
        if (IsModuleInstalled('prmedia.treelikecomments')){
          UnRegisterModuleDependences('prmedia.treelikecomments', 'OnBeforePrmediaCommentAdd', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforePrmediaCommentAddHandler');
        }
        UnRegisterModuleDependences('main', 'OnBeforeUserRegister', 'cleantalk.antispam', 'CleantalkAntispam', 'OnBeforeUserRegisterHandler');
        UnRegisterModuleDependences('main', 'OnEventLogGetAuditTypes', 'cleantalk.antispam', 'CleantalkAntispam', 'OnEventLogGetAuditTypesHandler');
        UnRegisterModule('cleantalk.antispam');
        $this->UnInstallDB();
        $this->UnInstallFiles();
	if(!empty($this->template_messages)){
	    $this->messages[] = GetMessage("CLEANTALK_TEMPLATES_HEADER");
	    foreach($this->template_messages as $val)
		$this->messages[] = $val;
	    $this->messages[] = '<br />' . GetMessage("CLEANTALK_TEMPLATES_FOOTER") . '<br />';
	}
        $GLOBALS["errors"] = $this->errors;
        $GLOBALS["messages"] = $this->messages;
        $APPLICATION->IncludeAdminFile(GetMessage('CLEANTALK_UNINSTALL_TITLE'), $DOCUMENT_ROOT.'/bitrix/modules/cleantalk.antispam/install/unstep.php');
    }

    function InstallFiles() {
	$ret_val = TRUE;
	// Copy system.auth.registration default template from system dir to local dir and insert addon into
	$SAR_res = $this->install_ct_template($this->SAR_template_dir,
			$this->SAR_template_file,
			$this->SAR_system_template_dir,
			$this->SAR_local_template_dir,
			$this->SAR_local_compo_template_dir,
			$this->SAR_pattern,
			$this->ct_template_addon_tag,
			$this->ct_template_addon_body_register
	);
	if($SAR_res != 0){
	    $this->errors[] = GetMessage('CLEANTALK_ERROR_FILES_'.sprintf('%02d', $SAR_res));
	    $ret_val = FALSE;
	}else{
	    $this->template_messages[] = $this->SAR_message;
	}

	// Copy blog.post.comment default template from system dir to local dir and insert addon into
        if (IsModuleInstalled('blog')){
		$BPC_res = $this->install_ct_template($this->BPC_template_dir,
			$this->BPC_template_file,
			$this->BPC_system_template_dir,
			$this->BPC_local_template_dir,
			$this->BPC_local_compo_template_dir,
			$this->BPC_pattern,
			$this->ct_template_addon_tag,
			$this->ct_template_addon_body_comment
	    );
	    if($BPC_res == 0){
		$this->template_messages[] = $this->BPC_message;
	    }
	}

	// Copy forum.comments default template from system dir to local dir and insert addon into
        if (IsModuleInstalled('forum')){
	    $FC_res = $this->install_ct_template($this->FC_template_dir,
			$this->FC_template_file,
			$this->FC_system_template_dir,
			$this->FC_local_template_dir,
			$this->FC_local_compo_template_dir,
			$this->FC_pattern,
			$this->ct_template_addon_tag,
			$this->ct_template_addon_body_comment
	    );
	    if($FC_res == 0){
		$this->template_messages[] = $this->FC_message;
	    }
	}

	// Copy prmedia.treelike_comments default template from system dir to local dir and insert addon into
        if (IsModuleInstalled('prmedia.treelikecomments')){
	    $PTLC_res = $this->install_ct_template($this->PTLC_template_dir,
			$this->PTLC_template_file,
			$this->PTLC_system_template_dir,
			$this->PTLC_local_template_dir,
			$this->PTLC_local_compo_template_dir,
			$this->PTLC_pattern,
			$this->ct_template_addon_tag,
			$this->ct_template_addon_body_comment
	    );
	    if($PTLC_res == 0){
		$this->template_messages[] = $this->PTLC_message;
	    }
	}

	
	return $ret_val;
    }

    function UnInstallFiles() {
	// Remove addon from local system.auth.registration default template
	$SAR_res = $this->uninstall_ct_template($this->SAR_template_dir,
			$this->SAR_template_file,
			$this->SAR_local_compo_template_dir,
			$this->ct_template_addon_tag
	);
	if($SAR_res == 0){
	    $this->template_messages[] = $this->SAR_message;
	}
	// Remove addon from local blog.post.comment default template
        if (IsModuleInstalled('blog')){
	    $BPC_res = $this->uninstall_ct_template($this->BPC_template_dir,
			$this->BPC_template_file,
			$this->BPC_local_compo_template_dir,
			$this->ct_template_addon_tag
	    );
	    if($BPC_res == 0){
		$this->template_messages[] = $this->BPC_message;
	    }
	}
	// Remove addon from local forum.comments default template
        if (IsModuleInstalled('forum')){
	    $FC_res = $this->uninstall_ct_template($this->FC_template_dir,
			$this->FC_template_file,
			$this->FC_local_compo_template_dir,
			$this->ct_template_addon_tag
	    );
	    if($FC_res == 0){
		$this->template_messages[] = $this->FC_message;
	    }
	}
	// Remove addon from local prmedia.treelike_comments default template
        if (IsModuleInstalled('prmedia.treelikecomments')){
	    $PTLC_res = $this->uninstall_ct_template($this->PTLC_template_dir,
			$this->PTLC_template_file,
			$this->PTLC_local_compo_template_dir,
			$this->ct_template_addon_tag
	    );
	    if($PTLC_res == 0){
		$this->template_messages[] = $this->PTLC_message;
	    }
	}
	return TRUE;	// always TRUE
    }

    function InstallDB() {
	global $DB;
	$DB->Query('DROP TABLE IF EXISTS cleantalk_timelabels');
	if(!$DB->Query('CREATE TABLE cleantalk_timelabels ( ct_key varchar(255), ct_value int(11), PRIMARY KEY (ct_key))')){
	    $this->errors[] = GetMessage('CLEANTALK_ERROR_CREATE_TIMELABELS');
	    return FALSE;
	}
	$DB->Query('DROP TABLE IF EXISTS cleantalk_cids');
	if(!$DB->Query('CREATE TABLE cleantalk_cids ( module varchar(255), cid int(11), ct_request_id varchar(255), ct_result_comment varchar(255), PRIMARY KEY (module, cid))')){
	    $this->errors[] = GetMessage('CLEANTALK_ERROR_CREATE_CIDS');
	    return FALSE;
	}
	$DB->Query('DROP TABLE IF EXISTS cleantalk_server');
	if(!$DB->Query('CREATE TABLE cleantalk_server ( work_url varchar(255), server_url varchar(255), server_ttl int(11), server_changed int(11))')){
	    $this->errors[] = GetMessage('CLEANTALK_ERROR_CREATE_SERVER');
	    return FALSE;
	}
        return TRUE;
    }

    function UnInstallDB($arParams = Array()) {
	global $DB;
	$DB->Query('DROP TABLE IF EXISTS cleantalk_timelabels');
	$DB->Query('DROP TABLE IF EXISTS cleantalk_cids');
	$DB->Query('DROP TABLE IF EXISTS cleantalk_server');
	return TRUE;
    }

    /**
     * Copies needed template from system dir to local dir and inserts CleanTalk addon into it
     *
     * @param 	&string $template_dir			Name of component's template dir (.default)
     * @param 	&string $template_file			Name of component's template file (template.php)
     * @param 	&string $system_template_dir		Full system dir of component templates (.../bitrix/components/bitrix/system.auth.registration/templates/)
     * @param 	&string $local_template_dir		Full local dir of templates (.../bitrix/templates/.default/)
     * @param 	&string $local_compo_template_dir	Full local dir of component template (.../bitrix/templates/.default/components/bitrix/system.auth.registration/)
     * @param 	&string $pattern			PCRE pattern to find place to insert CleanTalk addon before
     * @param 	&string $ct_template_addon_tag		Tag string to mark CleanTalk addon body
     * @param 	&string $ct_template_addon_body		HTML text of CleanTalk addon itself
     * @return 	int Returns error code or 0 when success
     */
    function install_ct_template($template_dir,	// without ending slash
			     $template_file,
			     $system_template_dir,	// with ending slash
			     $local_template_dir,	// with ending slash
			     $local_compo_template_dir,	// with ending slash
			     $pattern,
			     $ct_template_addon_tag,
			     $ct_template_addon_body)
    {
	// Check system folders
	if(!file_exists($system_template_dir) || !file_exists($local_template_dir)){
		// No required system folders
		return 1;
	}

	//Check component templates folder
	if(!file_exists($local_compo_template_dir)){
		if(!mkdir($local_compo_template_dir, 0777, TRUE)){
			// Cannot create component templates folder
			return 2;
		}
	}

	// Check template subfulder in component templates folder - the Bitrix template itself
	if(!file_exists($local_compo_template_dir.$template_dir)){
		if(!CopyDirFiles($system_template_dir.$template_dir, $local_compo_template_dir.$template_dir, FALSE, TRUE)){
			// Cannot copy template of conponent from system folder to local folder
			return 3;
		}
	}

	$template_file_path = $local_compo_template_dir.$template_dir.'/'.$template_file;
	// Last check - template PHP file
	if(!file_exists($template_file_path) || !is_file($template_file_path) || !is_writable($template_file_path)){
		// No template PHP file
		return 4;
	}

	// Here we are sure that
	// bitrix/templates/<template>/components/bitrix/<component>/<template>/<file>.php
	// exists and writable

	// Try to get template PHP file content
	$template_content = file_get_contents($template_file_path);
	if($template_content === FALSE){
		// Cannot read from template PHP file
		return 5;
	}

	// Check is it parsable
	if(!preg_match($pattern, $template_content) === 1){
		// Cannot find pattern for addon inserting in template PHP file
		return 6;
	}

	// First clean all previous CLEANTALK template addons
	$ct_template_addon_begin = '<!-- ' . $ct_template_addon_tag . ' -->';	// don't change this!
	$ct_template_addon_end   = '<!-- /' . $ct_template_addon_tag . ' -->';	// don't change this!

	$pos_begin = strpos($template_content, $ct_template_addon_begin);
	$pos_end   = strpos($template_content, $ct_template_addon_end);

	if($pos_begin !== FALSE && $pos_end === FALSE){
		// Cannot parse template PHP file - old CLEANTALK open tag exists only
		return 7;
	}elseif($pos_begin === FALSE && $pos_end !== FALSE){
		// Cannot parse template PHP file - old CLEANTALK close tag exists only
		return 8;
	}elseif($pos_begin !== FALSE && $pos_end !== FALSE){
		if($pos_begin < $pos_end){
			// Cleaning needed
			$template_content = substr($template_content, 0, $pos_begin) . substr($template_content, $pos_end + strlen($ct_template_addon_end));
		}else{
			// Cannot parse template PHP file - old CLEANTALK close tag before open tag
			return 9;
		}
	//}elseif($pos_begin === FALSE && $pos_end === FALSE){
	//	// Nothing to clean
	}

	// Second add current CLEANTALK template addon

	$ct_template_addon = $ct_template_addon_begin . $ct_template_addon_body . $ct_template_addon_end . "\n\n";

	$template_content = preg_replace($pattern, $ct_template_addon . '${1}', $template_content, 1);

	if(!file_put_contents($local_compo_template_dir.$template_dir.'/'.$template_file, $template_content)){
		// Cannot write new content to template PHP file
		return 10;
	}

	// Here all is OK - new template PHP file with CLEANTALK addon inserted is ready
	return 0;
    }

    /**
     * Remove addon from needed local component template
     *
     * @param 	&string $template_dir			Name of component's template dir (.default)
     * @param 	&string $template_file			Name of component's template file (template.php)
     * @param 	&string $local_compo_template_dir	Full local dir of component template (.../bitrix/templates/.default/components/bitrix/system.auth.registration/)
     * @param 	&string $ct_template_addon_tag		Tag string to mark CleanTalk addon body
     * @return 	int Returns error code or 0 when success
     */
    function uninstall_ct_template($template_dir,	// without ending slash
			     $template_file,
			     $local_compo_template_dir,		// with ending slash
			     $ct_template_addon_tag)
    {
	$template_file_path = $local_compo_template_dir.$template_dir.'/'.$template_file;
	// Last check - template PHP file
	if(!file_exists($template_file_path) || !is_file($template_file_path) || !is_writable($template_file_path)){
		// No template PHP file
		return 4;
	}

	// Here we are sure that
	// bitrix/templates/<template>/components/bitrix/<component>/<template>/<file>.php
	// exists and writable

	// Try to get template PHP file content
	$template_content = file_get_contents($template_file_path);
	if($template_content === FALSE){
		// cannot read from template PHP file
		return 5;
	}

	// Clean all CLEANTALK template addons
	$ct_template_addon_begin = '<!-- ' . $ct_template_addon_tag . ' -->';	// don't change this!
	$ct_template_addon_end   = '<!-- /' . $ct_template_addon_tag . ' -->';	// don't change this!

	$pos_begin = strpos($template_content, $ct_template_addon_begin);
	$pos_end   = strpos($template_content, $ct_template_addon_end);

	if($pos_begin !== FALSE && $pos_end === FALSE){
		// Cannot parse template PHP file
		return 7;
	}elseif($pos_begin === FALSE && $pos_end !== FALSE){
		// Cannot parse template PHP file
		return 8;
	}elseif($pos_begin !== FALSE && $pos_end !== FALSE){
		if($pos_begin < $pos_end){
			// Cleaning needed
			$template_content = substr($template_content, 0, $pos_begin) . substr($template_content, $pos_end + strlen($ct_template_addon_end));
		}else{
			// Cannot parse template PHP file
			return 9;
		}
	//}elseif($pos_begin === FALSE && $pos_end === FALSE){
	//	// Nothing to clean
	}

	if(!file_put_contents($local_compo_template_dir.$template_dir.'/'.$template_file, $template_content)){
		// Cannot write new content to template PHP file
		return 10;
	}

	// Here all is OK - new template PHP file without any CLEANTALK addon is ready
	return 0;
    }
}
?>
