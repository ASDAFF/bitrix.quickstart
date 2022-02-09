<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @author ibeono@gmail.com
 * @copyright Eldar Beono
 * @license GPLv2
 */

define('BEONO_COMPONENT_PATH', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/');

class WelcomeStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_WELCOME"));
		$this->SetNextStep("NameStep");
		$this->SetStepID("WelcomeStep");
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{			
		$this->content .= GetMessage("BEONO_MASTER_COMP_WELCOME_MESSAGE");
	}
}

class NameStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_NAME"));
		$this->SetPrevStep("WelcomeStep");
		$this->SetNextStep("ModuleStep");
		$this->SetStepID("NameStep");
		$this->SetCancelStep("CancelStep");		
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{			
			if (!$wizard->GetVar("type")) {					
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_TYPE'));
			}
			
			if (!$wizard->GetVar("new_namespace") && !$wizard->GetVar("namespace")) {					
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_NS'));
			}
						
			if (preg_match('/[^a-z0-9_\-\.]|[\.]{2,}/i', $wizard->GetVar("new_namespace"), $matches)) {
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_NS_INVALID_CHARS').implode(' ', $matches));
			}
			
			if (preg_match('/[^a-z0-9_\-\.]|[\.]{2,}/i', $wizard->GetVar("id"), $matches)) {
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_COMP_INVALID_CHARS').implode(' ', $matches));
			}
			
			if (!$wizard->GetVar("id")) {					
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_COMP'));
			} else {

				$new_component_path = false;
				if ($wizard->GetVar("new_namespace")) {
					$new_component_path = BEONO_COMPONENT_PATH.$wizard->GetVar("new_namespace")."/".$wizard->GetVar("id");
				} elseif ($wizard->GetVar("namespace")) {
					$new_component_path = $wizard->GetVar("namespace")."/".$wizard->GetVar("id");
				}

				if (file_exists($new_component_path)) {				
					$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_COMP_EXIST', array('#PATH#' => $new_component_path)));
				}				
			}
			
			$this->SaveFile("icon", Array("extensions" => "gif", "max_width" => 32, "max_height" => 16, "make_preview" => "Y"));
			
			if (!$wizard->GetVar("name")) {						
				$this->SetError(GetMessage('BEONO_MASTER_COMP_ERROR_COMP_NAME'));
			}
			
			if ($wizard->GetVar("type") == 'complex') {	
				$this->GetWizard()->UnSetVar('snippets');
				$this->GetWizard()->UnSetVar('template_options');				
				$wizard->SetCurrentStep("InstallStep");
			}			
		}
	}

	function ShowStep()
	{
		// Получим список пространств имен компонентов

		$arNamespaces = array(''=> GetMessage('BEONO_MASTER_COMP_NEW_NS'));
		
		$dir_content = scandir(BEONO_COMPONENT_PATH);

		$dir_content_igonre_list = array(".", "..", ".svn", ".DS_Store", "_notes", "Thumbs.db");
		$dir_content = array_diff($dir_content, $dir_content_igonre_list);
		foreach($dir_content as $key=>$content) {
			if(is_dir(BEONO_COMPONENT_PATH.$content)){
				$arNamespaces[BEONO_COMPONENT_PATH.$content] = $content;
			}
		}
			
		$this->content .= '<table width="100%" class="wizard-data-table">';
		$this->content .= "<tr><th>&nbsp;</th><th></th></tr>";
		
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_TYPE").' <span class="wizard-required">*</span></td>
			<td>'.$this->ShowRadioField("type", "simple", array('id'=>'type_simple', 'checked'=>'checked')).' <label for="type_simple">'.GetMessage('BEONO_MASTER_COMP_TYPE_SIMPLE').'</label><br/>'.
			$this->ShowRadioField("type", "complex", array('id'=>'type_complex')).' <label for="type_complex">'.GetMessage('BEONO_MASTER_COMP_TYPE_COMPLEX').'</label></td></tr>';

		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_NAMESPACE").' <span class="wizard-required">*</span></td><td>'.
		$this->ShowSelectField("namespace", $arNamespaces, Array("id"=>"namespace_field", "onchange"=>"beono_namespace_change(this)")).''.$this->ShowInputField("text", "new_namespace", Array("size" => "5", "id"=>"new_namespace_field"))."</td></tr>";
		
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_ID").' <span class="wizard-required">*</span></td><td>'.$this->ShowInputField("text", "id", Array("size" => "30"))."</td></tr>";
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_NAME").' <span class="wizard-required">*</span></td><td>'.$this->ShowInputField("text", "name", Array("size" => "30"))."</td></tr>";
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_DESCRIPTION")."</td><td>".$this->ShowInputField("text", "description", Array("size" => "30"))."</td></tr>";
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_PATH_ID").'</td><td>'.$this->ShowInputField("text", "path_id", Array("size" => "30"))."</td></tr>";
		$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_ICON")."</td><td>".$this->ShowFileField("icon")."</td></tr>";
		$this->content .= "</table>";
		
		$this->content .= <<<EOT
		<script type="text/javascript">
		function beono_namespace_change (el) {
			if (el.value == '') {
				document.getElementById('new_namespace_field').removeAttribute('disabled');
			} else {
				document.getElementById('new_namespace_field').setAttribute('disabled', 'disabled');
			}
		}
		beono_namespace_change(document.getElementById('namespace_field'));
		</script>
EOT;
	}
}


class ModuleStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_COMPONENT"));
		$this->SetPrevStep("NameStep");
		$this->SetNextStep("InstallStep");
		$this->SetStepID("ModuleStep");
		//$this->SetFinishStep("InstallStep");
		$this->SetCancelStep("CancelStep");
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
			
			if (is_array($wizard->GetVar("snippets")) && in_array('template', $wizard->GetVar("snippets"))) {					
				$wizard->SetCurrentStep("TemplateStep");
			}			
		}
	}

	function ShowStep()
	{
		$this->content .= GetMessage('BEONO_MASTER_COMP_SNIPPETS').'<br /><br />';
		$this->content .= $this->ShowCheckboxField("snippets[]", "lang", array("id"=>"lang_field", "checked"=>"checked")).' <label for="lang_field">'.GetMessage('BEONO_MASTER_COMP_LANG').'</label><br />';
		$this->content .= $this->ShowCheckboxField("snippets[]", "template", array("id"=>"template_field", "checked"=>"checked")).' <label for="template_field">'.GetMessage('BEONO_MASTER_COMP_TEMPLATE').'</label><br />';
		$this->content .= $this->ShowCheckboxField("snippets[]", "cache", array("id"=>"cache_field")).' <label for="cache_field">'.GetMessage('BEONO_MASTER_COMP_CACHE').'</label><br />';
        $this->content .= $this->ShowCheckboxField("snippets[]", "iblock", array("id"=>"ib_getlist_field")).' <label for="ib_getlist_field">'.GetMessage('BEONO_MASTER_COMP_IBLOCK').'</label><br />';
        $this->content .= $this->ShowCheckboxField("snippets[]", "http_404", array("id"=>"http_404_field")).' <label for="http_404_field">'.GetMessage('BEONO_MASTER_COMP_404').'</label><br />';
	}
}

class TemplateStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_TEMPLATE"));
		$this->SetPrevStep("ModuleStep");
		$this->SetNextStep("InstallStep");
		$this->SetStepID("TemplateStep");
		$this->SetCancelStep("CancelStep");
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick() || $wizard->IsFinishButtonClick())
		{
					
		}
	}

	function ShowStep()
	{
		$this->content .= GetMessage('BEONO_MASTER_COMP_TEMPLATE_OPTIONS').'<br /><br />';
		$this->content .= $this->ShowCheckboxField("template_options[]", "lang", array("id"=>"lang_field", "checked"=>"checked")).' <label for="lang_field">'.GetMessage('BEONO_MASTER_COMP_TEMPLATE_LANG').'</label><br />';
        $this->content .= $this->ShowCheckboxField("template_options[]", "css", array("id"=>"css_field")).' <label for="css_field">'.GetMessage('BEONO_MASTER_COMP_CSS').'</label><br />';
        $this->content .= $this->ShowCheckboxField("template_options[]", "js", array("id"=>"js_field")).' <label for="js_field">'.GetMessage('BEONO_MASTER_COMP_JS').'</label><br />';
        $this->content .= $this->ShowCheckboxField("template_options[]", "epilog", array("id"=>"epilog_field")).' <label for="epilog_field">'.GetMessage('BEONO_MASTER_COMP_EPILOG').'</label><br />';
	}
}

class InstallStep extends CWizardStep
{

	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_INSTALL"));
		$this->SetNextStep("FinalStep");
		$this->SetNextCaption(GetMessage("BEONO_MASTER_COMP_CREATE"));
		
		if ($this->GetWizard()->GetVar("type") == 'complex') {			
			$this->SetPrevStep("NameStep");
		} else if (is_array($this->GetWizard()->GetVar("snippets")) && in_array('template', $this->GetWizard()->GetVar("snippets"))) {					
			$this->SetPrevStep("TemplateStep");	
		} else {
			$this->SetPrevStep("ModuleStep");	
		}

		$this->SetStepID("InstallStep");
		$this->SetCancelStep("CancelStep");
	}
	
	function __getContentBySnippets ($component_file_content, $snippets) {
		
		$_GET['snippets'] = $snippets; // Sorry, по другому $arResult никак не хочет попадать в колбэк-функцию ниже
			
		$component_file_content = preg_replace_callback("/(?:\/\/.\+.)([0-9a-zA-Z\_]+)\s(.+)(?:\/\/.\-.)([0-9a-zA-Z\_]+)\s/Usm",  create_function(
            '$matches',
            '
            if (in_array($matches[1], $_GET["snippets"])) {
           		return $matches[2];
            }
            '
        ), $component_file_content);        
		$component_file_content = preg_replace("/\n(\s+)\n/ms", "\n\n", $component_file_content);	

		return $component_file_content;		
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick())
		{
			$arResult = $wizard->GetVars(true);
			
			// папка пространства имен
			
			$arCreateDirs = array();
			
			if($arResult['new_namespace']) {
				$arResult['namespace'] = BEONO_COMPONENT_PATH.$arResult['new_namespace'];
			}
			
			$arCreateDirs[] = $arResult['namespace'];
			
			// папка компонента
			
			$new_component_path = $arResult['namespace']."/".$arResult['id'];			
			$arCreateDirs[] = $new_component_path;			
			
			// component.php
			
			if (!isset($arResult['snippets'])) {
				$arResult['snippets'] = array();				
			}
			
			if ($arResult['type'] == 'complex') {
				$arResult["snippets"][] = 'complex';
			}
			
			$arCreateFiles = array(); // Список файлов, которые нужно создать
			
			$component_file_content = file_get_contents(dirname(__FILE__)."/src/component.php");	
			$arCreateFiles[$new_component_path."/component.php"] = $this->__getContentBySnippets($component_file_content, $arResult["snippets"]);
		
			// .description.php
					
			$component_file_content = file_get_contents(dirname(__FILE__)."/src/.description.php");	

			$component_file_content = strtr($component_file_content, array(
				"#name#" => htmlspecialchars($arResult['name']),
				"#description#" => htmlspecialchars($arResult['description']),
				"#path_id#" => htmlspecialchars($arResult['path_id']),
				"#path_child_id#" => htmlspecialchars($arResult['path_child_id']),
				"#path_child_name#" => htmlspecialchars($arResult['path_child_name']),
				"#type#" => ($arResult['type']=='complex')?'Y':'N',
			));
			
			$arCreateFiles[$new_component_path."/.description.php"] = $component_file_content;
			
			// .parameters.php
		
			$parameters_snippets = $arResult["snippets"];
			if ($arResult['type'] == 'complex' && !in_array('iblock', $arResult["snippets"])) {
				$parameters_snippets[] = 'iblock';			
			}
			
			$component_file_content = file_get_contents(dirname(__FILE__)."/src/.parameters.php");			
			$arCreateFiles[$new_component_path."/.parameters.php"] = $this->__getContentBySnippets($component_file_content, $parameters_snippets);

			if ($arResult['type'] == 'complex') {
				$arResult["snippets"][] = 'template';
			}
			
			if (in_array('template', $arResult["snippets"])) {
				
				$new_component_template_path = $new_component_path."/templates/.default";
				
				$arCreateDirs[] = $new_component_template_path;
				
				if ($arResult['type'] == 'complex') {
					$arCreateFiles[$new_component_template_path."/list.php"] = file_get_contents(dirname(__FILE__)."/src/templates/.default/list.php");
					$arCreateFiles[$new_component_template_path."/detail.php"] = file_get_contents(dirname(__FILE__)."/src/templates/.default/detail.php");
				} else {
					$arCreateFiles[$new_component_template_path."/template.php"] = file_get_contents(dirname(__FILE__)."/src/templates/.default/template.php");
				}
				
				if (in_array('css', $arResult["template_options"])) {					
					$arCreateFiles[$new_component_template_path."/style.css"] = " ";					
				}
				
				if (in_array('js', $arResult["template_options"])) {					
					$arCreateFiles[$new_component_template_path."/script.js"] = " ";					
				}
				
				if (in_array('epilog', $arResult["template_options"])) {					
					$arCreateFiles[$new_component_template_path."/component_epilog.php"] = '<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>';					
				}
				
				if (in_array('lang', $arResult["template_options"])) {					
					$arCreateDirs[] = $new_component_template_path."/lang";
					$arCreateDirs[] = $new_component_template_path."/lang/ru";
					$arCreateFiles[$new_component_template_path."/lang/ru/template.php"] = '<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>';				
				}
			}
			
			// Lang
			
			if (in_array('lang', $arResult["snippets"])) {
				$arCreateDirs[] = $new_component_path."/lang";
				$arCreateDirs[] = $new_component_path."/lang/ru";
				$arCreateFiles[$new_component_path."/lang/ru/.description.php"] = '<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>';
				$arCreateFiles[$new_component_path."/lang/ru/.parameters.php"] = '<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>';
				$arCreateFiles[$new_component_path."/lang/ru/component.php"] = '<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>';
			}
			
			// icon.gif			
			$arCreateDirs[] = $new_component_path."/images";
			
			if(!$wizard->GetVar("icon")) {				
				$arCreateFiles[$new_component_path."/images/icon.gif"] = file_get_contents(dirname(__FILE__)."/src/images/icon.gif");			
			}
			
			// Создаем папки
			foreach($arCreateDirs as $new_dir_path) {
				if (!file_exists($new_dir_path)) {				
					if(!mkdir($new_dir_path, BX_DIR_PERMISSIONS, true)) {
						$this->SetError("Can't create directory ".$new_dir_path);
						return false;
					}			
				}
			}
			
			// Создаем файлы
			foreach($arCreateFiles as $new_file_path=>$new_file_content) {
				if(!file_put_contents($new_file_path, $new_file_content)) {					
					$this->SetError("Can't create file ".$new_file_path);
					return false;
				} else {
					chmod($new_file_path, BX_FILE_PERMISSIONS);
				}
			}

			if($wizard->GetVar("icon")) {	
				CWizardUtil::CopyFile($wizard->GetVar("icon"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new_component_path."/images/icon.gif"));							
			}
		}
	}

	function ShowStep()
	{
		$wizard = &$this->GetWizard();
		$arResult = $wizard->GetVars(true);
		
		$this->content .= GetMessage("BEONO_MASTER_COMP_INSTALL_MESSAGE").'</br></br>';

		$this->content .= '<table width="100%" class="wizard-data-table">';
		
		$this->content .= "<tr><th>".GetMessage('BEONO_MASTER_COMP_INSTALL_PARAM')."</th><th>".GetMessage('BEONO_MASTER_COMP_INSTALL_VALUE')."</th><tr>";
		
		foreach($arResult as $field_name=>$field_value) {
			
			$this->content .= "<tr><td>".GetMessage("BEONO_MASTER_COMP_".strtoupper($field_name))."</td><td>";
			
			if (is_array($field_value)) {
				$this->content .= implode(', ', $field_value);
			} else {
				
				if ($field_name == 'icon') {
					$field_value = '<img src="'.CFile::GetPath($field_value).'"/>';
				}
				
				$this->content .= $field_value;
			}
			
			$this->content .= "</td></tr>";
		}
		$this->content .= "</table>";
	}
}

class FinalStep extends CWizardStep
{

	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_FINAL"));
		$this->SetStepID("FinalStep");
		$this->SetCancelCaption(GetMessage("BEONO_MASTER_COMP_CLOSE"));
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{
		$this->content .= GetMessage("BEONO_MASTER_COMP_FINAL_MESSAGE"); 
		// TODO добавить в сообщение сслыку на компонент в админке
	}
}

class CancelStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("BEONO_MASTER_COMP_STEP_CANCEL"));
		$this->SetStepID("CancelStep");
		$this->SetCancelCaption(GetMessage("BEONO_MASTER_COMP_CLOSE"));
		$this->SetCancelStep("CancelStep");
	}

	function ShowStep()
	{		
		$wizard = &$this->GetWizard();
		
		if($wizard->GetVar("icon")) {	
			CFile::Delete($wizard->GetVar("icon"));
		}
		
		$this->content .= GetMessage("BEONO_MASTER_COMP_CANCELED_MESSAGE");
	}
}
?>