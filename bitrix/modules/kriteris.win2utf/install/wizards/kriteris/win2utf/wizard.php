<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

function include_jQuery(){
  return '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
}

class Start extends CWizardStep{
	function InitStep(){
		$this->SetStepID("Start"); 
		$this->SetTitle(GetMessage("STEEP_START_TITLE"));
    $this->SetSubTitle(GetMessage("STEEP_START_TITLE2")); 
		$this->SetNextStep("ConvertBitrix"); 		
		$this->SetNextCaption(GetMessage("STEEP_START_NEXT"));
	}
	function ShowStep(){
    $this->content .= GetMessage("STEEP_START_MESSAGE");	
		$wizard = &$this->GetWizard();
		$template_path = $wizard->GetVar("template");    
		$this->content .= $this->ShowHiddenField("template", $template_path);					
	}
}


class ConvertBitrix extends CWizardStep{
	function InitStep(){
		$this->SetStepID("ConvertBitrix"); 
		$this->SetTitle(GetMessage("STEEP_2_TITLE"));       
		$this->SetNextStep("FinishStep");					
		$this->SetNextCaption(GetMessage("STEEP_2_NEXT"));    
	}  
	function ShowStep(){
		$wizard = &$this->GetWizard();
		$template_path = $wizard->GetVar("template");
    $this->content .= $this->ShowHiddenField("template", $template_path);
    $this->content .=	include_jQuery();
    $this->content .=	"<script>bitrix_sessid = '".bitrix_sessid()."';</script>";     
    $this->content .= "<script type=\"text/javascript\" src=\"/bitrix/wizards/kriteris/win2utf/script.js?id=<?=rand(0, 1000)?>\"></script>"; ###<?
		$this->content .= "<div class='ajax_convert'></div>";								
	}
}      
           
class FinishStep extends CWizardStep{
	function InitStep(){
		$this->SetStepID("FinishStep"); 
		$this->SetTitle(GetMessage("STEEP_FINISH_TITLE")); 
	}  
	function ShowStep(){        
    $this->content .= GetMessage("STEEP_FINISH_MESSAGE");							
	}
}            
?>