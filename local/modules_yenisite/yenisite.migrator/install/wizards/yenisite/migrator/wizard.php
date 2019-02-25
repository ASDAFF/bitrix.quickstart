<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "migrator";
		$this->SetNextStep("SelectCMS"); 		
	}
}




class SelectCMS extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("SelectCMS"); 
		$this->SetTitle(GetMessage("SelectCMS")); 
		
		//$this->SetNext(GetMessage("SelectCMS")); 
		
		$this->SetNextStep("SelectBitrixModule"); 
		$this->SetPrevStep("SelectSiteStep"); 
		
		$this->SetNextCaption(GetMessage("NEXT"));
		$this->SetPrevCaption(GetMessage("PREV"));
		
	}

	function ShowStep()
	{


             	  
		$wizard =& $this->GetWizard();

	        $this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
                $this->content .= '<tr><td>';

		$site = CSite::GetByID($wizard->GetVar("siteID"))->GetNext();
		if(!$site)
		{
			$arFields = Array(
			  "LID"              => $wizard->GetVar("siteID"),
			  "ACTIVE"           => "Y",			  
			  "NAME"             => "migrator",
			  "DIR"              => "/".$wizard->GetVar("siteID")."/",
			  "FORMAT_DATE"      => "DD.MM.YYYY",
			  "FORMAT_DATETIME"  => "DD.MM.YYYY HH:MI:SS",
			  "CHARSET"          => "windows-1251",			  
			  "LANGUAGE_ID"      => "ru",
			  );
			$obSite = new CSite;
			$obSite->Add($arFields);

		}


		$this->content .= "
			<script>
			
				function img_click(id)
				{
				
					var obj = document.getElementsByClassName('trs');
					for(i=0; i<obj.length; i++)
						obj[i].style.background = '#ffffff';
						
					el = document.getElementById(id);
					el.checked = true;
					
					el.parentNode.parentNode.style.background = '#ffefaa';
					
				}
			</script>
		";
		
		/* формирование массива доступных CMS --- начало */
		$path = str_replace("wizard.php", "", __FILE__)."CMS/";
		$CMSpath = opendir($path);
		$cmses = array();
		$this->content .= '<table width="100%" cellspacing=0 cellpadding=0 border=0>';
		$i=0;
		$chk = false;
		$dirs = array();
		while($dir = readdir($CMSpath))
			$dirs[] = $dir;
			
		asort($dirs);
		
		
		foreach($dirs as $dir)
		{
			$i++;
			
			if($dir != '.' && $dir != '..' && is_dir($path.$dir."/"))
			{
				$name = "";
				if(file_exists($path.$dir."/lang/ru/.description.php"))
				{
					include($path.$dir."/lang/ru/.description.php");
					$name = $CMS["NAME"];	
					$name = $name?$name:$dir;
					$path_logo = str_replace($_SERVER['DOCUMENT_ROOT'],'', "{$path}{$dir}/logo.jpg" );				
					if($dir != "PassSubscribe")
					{
								
						//$this->content .= "<tr><td><img src='{$path_logo }' /></td>";  
						
						$arr =  array('id' => 'ch'.$i);
						$style='';
						if(!$chk && !$wizard->GetVar("cms")){ $arr['checked'] = 'checked'; $chk=true; $style=" style='background: #ffefaa;' "; }
						elseif($wizard->GetVar("cms"))
						{
							if($wizard->GetVar("cms") == $dir) $style=" style='background: #ffefaa;' ";
						}
						
						$this->content .= '<tr '.$style." class='trs' onclick='img_click(\"ch{$i}\");'><td style='display: none; border: none;'>".$this->ShowRadioField("cms", $dir, $arr)."</td><td style='border: none;' width='95px'><img style='border: 1px solid #cccccc; cursor: pointer; margin: 10px; ' width='65px'  src='{$path_logo }' /></td><td style='border: none; padding: 5px;' align='left'>{$name}</td></tr>";  
					}
					else
						$ps = "<tr><td colspan='4'><hr><br/>".GetMessage('USERPASSSUB')."</td></tr><tr class='trs' onclick='img_click(\"ch{$i}\");'><td style='display: none; border: none;'>".$this->ShowRadioField("cms", $dir, array('id' => 'ch'.$i))."</td><td style='border: none;' width='95px'><img width='65px' style='border: 1px solid #cccccc; cursor: pointer; margin: 10px; '  src='{$path_logo }' /></td><td style='border: none; padding: 5px;' align='left'>{$name}</td></tr>";  
						//$ps = "<hr><br/>".GetMessage("USERPASSSUB")."<br/>".$this->ShowRadioField("cms", $dir).$name."<br />";
					//$this->content .= $this->ShowHiddenField("prefix", $CMS["PREFIX"]);      				
				}
			}
		}
		$this->content .= "<br/>".$ps;  
		$this->content .= '</table>';
		
		/* формирование массива доступных CMS --- конец */

                $this->content .= '</td></tr>';		
		$this->content .= '</table>';

		//$installCaption = $this->GetNextCaption();
		//$nextCaption = GetMessage("NEXT_BUTTON");
	}
	
}

class SelectBitrixModule extends CWizardStep
{

	function InitStep()
	{
		$this->SetStepID("SelectBitrixModule"); 
		$this->SetTitle(GetMessage("SelectBitrixModule")); 		
		$this->SetNextStep("ConnectionSettings"); 
		$this->SetPrevStep("SelectCMS"); 
		$this->SetNextCaption(GetMessage("NEXT"));
		$this->SetPrevCaption(GetMessage("PREV"));

		
 

		
		
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();	
		
		
		$cms = $wizard->GetVar("cms");
		
		$html ='';
		
		if($cms == "PassSubscribe")
		{
			global $USER;
			$res = CUser::GetList();
			while($u = $res->GetNext())
				$ar = $USER->SendPassword($u["LOGIN"], $u["EMAIL"]);
			$this->content .= "<b style='color: green;'>".GetMessage("PASSSUBSCRIBE")."</b>";
			$this->SetNextStep("COMPLETE");		
		}
		else
		{
			
			$this->content .= $this->ShowHiddenField("prefix", $prefix);      
			/* формирование массива доступных шаблонов --- начало */
			$path = str_replace("wizard.php", "", __FILE__)."CMS/".$cms."/";
			$CMSpath = opendir($path);
			$cmses = array();
			while($dir = readdir($CMSpath))
				if($dir != '.' && $dir != '..' && is_dir($path.$dir."/"))
				{
					$arDir[] = $dir;
					

				}				
				
				asort($arDir);
				
				$html .='<table>';
				$i = 0;
				foreach($arDir as $dir)
				{
					$i++;
					$name = "";
					if(file_exists($path.$dir."/lang/ru/.description.php"))
					{
						$arr = array("id" => "ch".$i);
						if($i == 1) $arr['checked'] = 'checked';
						include($path.$dir."/lang/ru/.description.php");
						$name = $CMS["NAME"];	
						$name = $name?$name:$dir;
						$html.= "<tr><td style='padding: 5px;'>".$this->ShowRadioField("template", $path.$dir."/index.php", $arr)."</td><td><label for='ch{$i}'>".$name."</label></td></tr>";  
						include($path."/lang/ru/.description.php");
						$html .= $this->ShowHiddenField("prefix", $CMS["PREFIX"]); 
					
				
					}
				}
				$html .='</table>';
				
				
			$this->content .= $html;
			/* формирование массива доступных шаблонов --- конец */
		}
	}
}



class ConnectionSettings extends CWizardStep
{

	function InitStep()
	{
		$this->SetStepID("ConnectionSettings"); 
		$this->SetTitle(GetMessage("ConnectionSettings")); 
		$this->SetNextStep("StartMigration"); 
		$this->SetPrevStep("SelectBitrixModule"); 
		$this->SetNextCaption(GetMessage("NEXT"));
		$this->SetPrevCaption(GetMessage("PREV"));

	}

	function ShowStep()
	{
		$this->content .= '<table>';

		$wizard =& $this->GetWizard();
		$template_path = $wizard->GetVar("template");
		$this->content .= $this->ShowHiddenField("template", $template_path);

		$prefix = $wizard->GetVar("prefix");

                $this->content .= '<tr><td>';		
		$this->content .= GetMessage("HOST").": </td><td>".$this->ShowInputField("text", "dbhost", Array("value"=>"localhost", "size" => "20"))."";
                $this->content .= '</td></tr>';		

                $this->content .= '<tr><td>';		
		$this->content .= GetMessage("DBNAME").": </td><td>".$this->ShowInputField("text", "dbname", Array("size" => "20"))."";
                $this->content .= '</td></tr>';		

                $this->content .= '<tr><td>';		
		$this->content .= GetMessage("LOGIN").": </td><td>".$this->ShowInputField("text", "dblogin", Array("size" => "20"))."";
                $this->content .= '</td></tr>';	

                $this->content .= '<tr><td>';		
		$this->content .= GetMessage("PASS").": </td><td>".$this->ShowInputField("password", "dbpass", Array("size" => "20"))."";
                $this->content .= '</td></tr>';			

                $this->content .= '<tr><td>';		
		$this->content .= GetMessage("PREFIX").": </td><td>".$this->ShowInputField("text", "prefix", Array("size" => "20", "value" => $prefix))."<br/>";
                $this->content .= '</td></tr>';	
		
		$this->content .= '<tr><td>';		
		$this->content .= GetMessage("URL").": </td><td>".$this->ShowInputField("text", "site", Array("size" => "20"))."<br/>";
                $this->content .= '</td></tr>';	


		$this->content .= $this->ShowHiddenField("left", 0);
		$this->content .= $this->ShowHiddenField("right", 10);
		$this->content .= $this->ShowHiddenField("step", 0);
		$this->content .= $this->ShowHiddenField("finish", 0);
		

		$this->content .= '</table>';
	}
}



class StartMigration extends CWizardStep
{

	function onPostForm()
	{
	}

	function InitStep()
	{
		$this->SetStepID("StartMigration"); 
		$this->SetTitle(GetMessage("StartMigration")); 
		$this->SetNextStep("StartMigration");
		$wizard =& $this->GetWizard();	
		$finish = $wizard->GetVar("finish");	
		if($finish)
			$this->SetNextStep("COMPLETE");

		$this->SetPrevStep("ConnectionSettings");
			
		$this->SetNextCaption(GetMessage("NEXT"));
		$this->SetPrevCaption(GetMessage("PREV"));

	}

	function ShowStep()
	{
		$this->content .= '<table>';
		$arResult = array();
		$wizard =& $this->GetWizard();
		$arResult["template"] = $wizard->GetVar("template");
		$arResult["dbhost"] = $wizard->GetVar("dbhost");
		$arResult["dbname"] = $wizard->GetVar("dbname");
		$arResult["dblogin"] = $wizard->GetVar("dblogin");
		$arResult["dbpass"] = $wizard->GetVar("dbpass");
		$arResult["prefix"] = $wizard->GetVar("prefix");
		$arResult["site"] = $wizard->GetVar("site");
		$left = $wizard->GetVar("left");
		$right = $wizard->GetVar("right");

		$this->content .= $this->ShowHiddenField("finish", 0);

		$finish = $wizard->GetVar("finish");
		$step = $wizard->GetVar("step");

		if($step > 0)
		{
			
			$arResult["template"] = str_replace("index.php", "index".$step.".php", $arResult["template"]);			
			if(!file_exists($arResult["template"]))
			{
				$this->content .= $this->ShowHiddenField("finish", 1);
			}
		}


        $err = false;
		//if(!$link = mysql_connect($arResult["dbhost"], $arResult["dblogin"], $arResult["dbpass"], true))
		if(!$link = mysql_pconnect($arResult["dbhost"], $arResult["dblogin"], $arResult["dbpass"]))
		{
			$this->content .= '<b style="color: red">'.GetMessage("ERROR_CONNECTION").'</b><br/>';				
			$err = true;
			//return;
		}
		else
			$this->content .= '<b style="color: green">'.GetMessage("SUCCESFULL_CONNECTION").'</b><br/>';	

		if(!mysql_select_db($arResult["dbname"]))
		{
			$this->content .= '<b style="color: red">'.GetMessage("ERROR_DB_CONNECTION").'</b><br/>';	
    		$err = true;
			//return;
		}
		else
			$this->content .= '<b style="color: green">'.GetMessage("SUCCESFULL_DB_CONNECTION").'</b><br/>';	


		

        if(!$err)
		    include($arResult["template"]);

		//mysql_close($link);
		$this->content .= '</table>';
            
        if(!$err)
   		    $this->content	.= "<script>SubmitForm('next');</script>";

	}
}


class COMPLETE extends CWizardStep
{
	function InitStep()
	{

		$this->SetStepID("COMPLETE"); 
		$this->SetTitle(GetMessage("COMPLETE")); 
		$this->SetNextStep("FinishStep");
		$this->SetNextCaption(GetMessage("MORE"));
		$this->SetPrevCaption(GetMessage("PREV"));


	}

	function ShowStep()
	{
	
	
		
								
		$this->content .= '<div style="float: left; margin-top: 30px;"><a href="/bitrix/admin/" class="button-prev"><span id="next-button-caption">'.GetMessage("GO_TO_SITE").'</span></a></div>';

	
		//$this->content .= '<b style="color: green">'.GetMessage("MASTER_COMPLETE").'</b><br/>';
		//$this->content .= '<br/><br/><b style="display:block; float: right; font-size: 12px; font-family: verdana; text-align: right;"><a href="/bitrix/admin/">'.GetMessage("ADMIN").'</a></b><br/><br/><br/>';
		//$this->content .= '<br/><span style="font-size: 12px; font-family: verdana;">'.GetMessage("NOTICE_FINISH").'</span>';
	}


}


class FinishStep extends CFinishWizardStep
{
}	




?>
