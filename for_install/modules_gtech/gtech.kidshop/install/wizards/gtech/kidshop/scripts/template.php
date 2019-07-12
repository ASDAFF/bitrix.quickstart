<?php
class WizardTemplate extends CWizardTemplate
{
	function GetLayout()
	{
		global $arWizardConfig;
		$wizard =& $this->GetWizard();

		$formName = htmlspecialchars($wizard->GetFormName());

		$nextButtonID = htmlspecialchars($wizard->GetNextButtonID());
		$prevButtonID = htmlspecialchars($wizard->GetPrevButtonID());
		$cancelButtonID = htmlspecialchars($wizard->GetCancelButtonID());
		$finishButtonID = htmlspecialchars($wizard->GetFinishButtonID());

		$wizardPathCustom = $wizard->GetPath();
		$wizardPath = "/bitrix/images/main/wizard_sol";

		$obStep =& $wizard->GetCurrentStep();
		$arErrors = $obStep->GetErrors();
		$strError = "";
		if (count($arErrors) > 0)
		{
			foreach ($arErrors as $arError)
				$strError .= $arError[0]."<br />";

			if (strlen($strError) > 0)
				$strError = '<div id="step-error">'.$strError."</div>";
		}

		$stepTitle = $obStep->GetTitle();
		$stepSubTitle = $obStep->GetSubTitle();

		$BX_ROOT = BX_ROOT;
		$productVersion = "";

		//wizard customization file
		$bxProductConfig = array();
		if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php"))
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php");

		if(isset($bxProductConfig["intranet_wizard"]["product_name"]))
			$title = $bxProductConfig["intranet_wizard"]["product_name"];
		elseif(GetMessage("WIZARD_TITLE_SOL")!="")
			$title = GetMessage("WIZARD_TITLE_SOL");
		else
			$title = GetMessage("WIZARD_TITLE");
		$title = str_replace("#VERS#", $productVersion , $title);

		if(isset($bxProductConfig["intranet_wizard"]["copyright"]))
			$copyright = $bxProductConfig["intranet_wizard"]["copyright"];
		else
			$copyright = GetMessage("COPYRIGHT");
		$copyright = str_replace("#CURRENT_YEAR#", date("Y") , $copyright);

		if(isset($bxProductConfig["intranet_wizard"]["links"]))
			$support = $bxProductConfig["intranet_wizard"]["links"];
		else
			$support = GetMessage("SUPPORT");

		if(isset($bxProductConfig["intranet_wizard"]["title"]))
			$wizardName = $bxProductConfig["intranet_wizard"]["title"];
		else
			$wizardName = $wizard->GetWizardName();

		//Images
		$logoImage = "";
		$boxImage = "";
		$pathToBitrixLogo = $wizardPathCustom.'/images/'.LANGUAGE_ID.'/bitrix-logo.gif';
		if(isset($bxProductConfig["intranet_wizard"]["logo"]))
		{
			$logoImage = $bxProductConfig["intranet_wizard"]["logo"];
		}
		else
		{
			if (GetMessage('WIZARD_LOGO')!='')
				$logoImage = GetMessage('WIZARD_LOGO');
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/".LANGUAGE_ID."/logo.gif"))
				$logoImage = '<img src="'.$wizardPathCustom.'/images/'.LANGUAGE_ID.'/logo.gif" alt="" class="logo-image" />';
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/".LANGUAGE_ID."/logo.png"))
				$logoImage = '<img src="'.$wizardPathCustom.'/images/'.LANGUAGE_ID.'/logo.png" alt="" class="logo-image" />';
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/en/logo.gif"))
				$logoImage = '<img src="'.$wizardPathCustom.'/images/en/logo.gif" alt="" class="logo-image" />';
		}

		if(isset($bxProductConfig["intranet_wizard"]["product_image"]))
		{
			$boxImage = $bxProductConfig["intranet_wizard"]["product_image"];
		}
		else
		{
			if (GetMessage('PRODUCT_BOX')!='')
				$boxImage = GetMessage('PRODUCT_BOX');
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/".LANGUAGE_ID."/box.jpg"))
				$boxImage = '<img src="'.$wizardPathCustom.'/images/'.LANGUAGE_ID.'/box.jpg" alt="" class="box-image" />';
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/".LANGUAGE_ID."/box.png"))
				$boxImage = '<img src="'.$wizardPathCustom.'/images/'.LANGUAGE_ID.'/box.png" alt="" class="box-image" />';
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPathCustom."/images/en/box.jpg"))
				$boxImage = '<img src="'.$wizardPathCustom.'/images/en/box.jpg" alt="" class="box-image" />';
		}

		$loc = array(
			'compability' => GetMessage('AREAL_CRM_COMPABILITY'),
			'links_to_crm' => GetMessage('AREAL_CRM_LINKS'),
			'crm_site' => GetMessage('AREAL_CRM_SITE'),
			'links_to_areal' => GetMessage('AREAL_SITE_WIZARD_LINKS_TO_AREAL'),
		);


		$strErrorMessage = "";
		$strWarningMessage = "";
		$strNavigation = "";

		$arSteps = $wizard->GetWizardSteps();
		$currentStepID = $wizard->GetCurrentStepID();
		if ($currentStepID == "ldap_settings" || $currentStepID == "ldap_groups")
				$currentStepID = "site_settings";

		$currentSuccess = false;
		$stepNumber = 1;

		foreach ($arSteps as $stepID => $stepObject)
		{
			if ($stepID == "ldap_settings" || $stepID == "ldap_groups")
				continue;

			if ($stepID == $currentStepID)
			{
				$class = 'class="selected"';
				$currentSuccess = true;
			}
			elseif ($currentSuccess)
				$class = '';
			else
				$class = 'class="done"';

			$strNavigation .= '
			<tr '.$class.'>
				<td class="menu-number">'.$stepNumber.'</td>
				<td class="menu-name">'.$stepObject->GetTitle().'</td>
				<td class="menu-end"></td>
			</tr>
			<tr class="menu-separator">
				<td colspan="3"></td>
			</tr>';

			$stepNumber++;
		}

		if (strlen($strNavigation) > 0)
			$strNavigation = '<table width="100%" cellpadding="0" cellspacing="0" id="menu">'.$strNavigation.'</table>';

		$jsCode = "";
		$jsCode = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/script.js");

		$noscriptInfo = GetMessage("INST_JAVASCRIPT_DISABLED");
		$charset = LANG_CHARSET;

		$currentStep =& $wizard->GetCurrentStep();

		$buttons = "";

		if ($currentStep->GetNextStepID() != null)
			$buttons .= '<a onclick="this.blur(); return SubmitForm(\'next\');" href="" class="button-next"><span id="next-button-caption">'.$currentStep->GetNextCaption().'</span></a>';

		if ($currentStep->GetPrevStepID() != null)
			$buttons .= '<a onclick="this.blur(); return SubmitForm(\'prev\');" href="" class="button-prev"><span id="prev-button-caption">'.$currentStep->GetPrevCaption().'</span></a>';

		return <<<HTML
<html>
	<head>
		<title>{$wizardName}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
		<style type="text/css">

			html {height:100%;}

			body 
			{
				background:#781813 url({$wizardPath}/bg_fill.gif) repeat;
				margin:0;
				padding:0;
				padding-bottom:6px;
				font-family: Arial, Verdana, Helvetica, sans-serif;
				font-size:82%;
				height:100%;
				color:black;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing: border-box;
				-khtml-box-sizing: border-box;
			}

			#noscript {display:none;}

			table {font-size:100.01%;}

			a {color:#2676b9}

			h3 {font-size:120%;}
			
			.logo-image, .box-image {border:0}

			#container
			{
				padding-top:6px;
				height:100%;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing: border-box;
				-khtml-box-sizing: border-box;
			}

			#main-table
			{
				width:760px;
				height:100%;
				border-collapse:collapse;
			}

			#main-table td {padding:0;}

			td.wizard-title
			{
				background:#fefbd2 url({$wizardPath}/top_gradient_fill.gif) repeat-x; 
				height:77px; 
				color:#19448a; 
				font-size:140%; 
			}
			#step-title
			{
				color:#cd4d3e; 
				margin: 20px; 
				padding-bottom:20px; 
				border-bottom:1px solid #d9d9d9; 
				font-weight:bold;
				font-size:120%;
			}
			#step-content {margin:20px 25px; zoom:1;}

			table.data-table
			{
				width:100%;
				border-collapse:collapse;
				border:1px solid #d0d0d0;
			}

			table.data-table td
			{
				padding:5px !important;
				border:1px solid #d0d0d0;
			}

			table.data-table td.header
			{
				background: #e3f0f9;
				font-weight: bold;
			}
			table.data-table-no-border
			{
				width:100%;
			}

			table.data-table-no-border td, table.data-table-no-border th
			{
				padding:3px;
			}

			table.data-table-no-border th
			{
				text-align:right;
				vertical-align:top;
				font-weight: normal;
			}

			#menu td.menu-number, #menu td.menu-name
			{
				background:#eaeaea url({$wizardPath}/menu_fill.gif) repeat-x;
				height:40px;
				color:#c0c0c0;
			}

			#menu tr.menu-separator
			{
				height:2px;
				background: none;
			}

			#menu tr.selected td.menu-number, #menu tr.selected td.menu-name
			{
				background:#b41d07 url({$wizardPath}/menu_fill_selected.gif) repeat-x;
				color:white;
			}

			#menu tr.done
			{
				color:black;
			}

			#menu td.menu-end
			{
				background: url({$wizardPath}/menu_end.gif) repeat-x;
				width:11px;
			}

			#menu tr.selected td.menu-end
			{
				background: url({$wizardPath}/menu_end_selected.gif) repeat-x;
				width:11px;
			}

			#menu td.menu-number
			{
				width:30px;
				font-size: 170%;
				text-align:center;
			}

			#menu td.menu-name
			{
				font-size:110%;
				padding-bottom:1px;
			}

			#copyright {font-size:95%; color:#606060; margin:4px 7px 0 7px; zoom:1;}

			input.wizard-prev-button {background: #ffe681 url({$wizardPath}/prev.gif); border:none; width:116px; height:31px; font-weight:bold; padding-bottom:4px; cursor:pointer; cursor:hand;}
			input.wizard-next-button {background: #ffe681 url({$wizardPath}/next.gif); border:none; width:116px; height:31px; font-weight:bold; padding-bottom:4px; cursor:pointer; cursor:hand;}

			form {margin:0; padding:0;}
			#step-error {color:red; padding:4px 4px 4px 25px;margin-bottom:4px; background:url({$wizardPath}/error.gif) no-repeat;}
			small{font-size:85%;}

			.required {color:red;}

			div.buttons
			{
				padding-bottom: 35px;
			}
			a.button-next
			{
				background: transparent url({$wizardPath}/button_next.png) no-repeat scroll top right;
				display: block;
				float: right;
				font-size:14px;
				height: 31px;
				padding-right: 35px;
				margin-left:15px;
				text-decoration: none;
				font-weight:bold;
			}
	
			a.button-next span
			{
				background: transparent url({$wizardPath}/button_next.png) no-repeat;
				display: block;
				line-height: 17px;
				color:black;
				padding: 5px 0 9px 18px;
			}

			a.button-prev
			{
				background: transparent url({$wizardPath}/button_prev.png) no-repeat scroll top right;
				display: block;
				float: right;
				font-size:14px;
				height: 31px;
				padding-right: 18px;
				text-decoration: none;
				font-weight:bold;
			}
	
			a.button-prev span
			{
				background: transparent url({$wizardPath}/button_prev.png) no-repeat;
				display: block;
				line-height: 17px;
				color:black;
				padding: 5px 0 9px 35px;
			}
			
			#solutions-container
			{
				margin-bottom: 15px;
			}
			
			.solution-item
			{
				display:block; 
				border: 0; 
				margin-bottom: 10px; 
				color: Black;
				text-decoration: none;
				outline: none;
			}
						
			.solution-item h4
			{
				margin: 10px;
				margin-top: 9px; /*compensating 1px padding*/
				font-family:Helvetica;
				font-size:1.5em;
			}
			.solution-item p
			{
				margin: 10px;
			}
			
			div.solution-item-wrapper
			{
				width: 97px;
				float: left;
			}
			
			.solution-picture-item
			{
				margin: 3px;
				text-align: center;
			}
			
			div.solution-description
			{
				margin-top: 3px;
				margin-left: 4px;
				color: #999;
				text-align:left;
			}
			
			.solution-picture-item img.solution-image
			{
				width: 70px; 
				float: none;
				margin: 7px 0px 7px;
			}
			
			img.solution-image
			{
				width: 100px; 
				float: left; 
				margin: 10px;
				border: 1px solid #CFCFCF;
			}
			input.solution-radio
			{
				float: left;
				margin: 10px 7px 10px 10px;
				height: 1.5em;
			}
			div.solution-inner-item
			{
				padding: 1px;
				overflow: hidden;
				zoom: 1;
			}
			
			.solution-item div.solution-inner-item, 
			.solution-item b 
			{
				background-color:#F7F7F7;
				cursor: pointer;
				cursor: hand;
			}
			
			.solution-item:hover div.solution-inner-item, 
			.solution-item:hover b 
			{
				background-color: #FFF0B2;
			}
			
			.solution-item-selected div.solution-inner-item, 
			.solution-item-selected b,
			.solution-item-selected:hover div.solution-inner-item, 
			.solution-item-selected:hover b
			{
				background-color: #CADBEC;
			}
			
			#solution-preview
			{
				margin-top: 10px;
			}
			
			#solution-preview div.solution-inner-item, 
			#solution-preview b 
			{
				background-color:#F7F7F7;
			}
			
			#solution-preview div.solution-inner-item
			{
				padding: 10px;
				text-align: center;
			}
			
			#solution-preview-image
			{
				border: 1px solid #CFCFCF;
				width: 450px;
			}
			
			/* Round Corners */
			.r0, .r1, .r2, .r3, .r4 { overflow: hidden; font-size:1px; display: block; height: 1px;}
			.r4 { margin: 0 4px; }
			.r3 { margin: 0 3px; }
			.r2 { margin: 0 2px; }
			.r1 { margin: 0 1px; }

			div.wizard-input-form
			{
			}
			
			div.wizard-input-form-block
			{
				margin-bottom:30px;
			}
			
			div.wizard-input-form-block h4
			{
				font-size:14px;
				margin-bottom:12px;
				color: #5E7CAD;
			}
			
			div.wizard-input-form-block-content
			{
				margin-left: 30px;
				margin-bottom: 25px;
				zoom:1;
			}
			
			div.wizard-input-form-block-content img
			{
				border: solid 1px #D6D6D6;
				margin-bottom: 5px;
			}
			
			div.wizard-input-form-block-content img.no-border
			{
				border: none;
			}		
			
			div.wizard-input-form-field-text input,
			div.wizard-input-form-field-textarea textarea,
			div.wizard-input-form-field-file input
			{
				width: 90% !important;
				border: solid 1px #CECECE;
				background-color: #F5F5F5;
				padding: 3px;

				font: 100%/100% Arial, sans-serif;
				float: left;
			}
			
			div.wizard-input-form-field-desc
			{
				color: rgb(119, 119, 119);
				zoom:1;
			}

			div.wizard-input-form-field
			{
				overflow: hidden;
				margin-bottom: 5px;
			}

		</style>

		<noscript>
			<style type="text/css">
				div {display: none;}
				#noscript {padding: 3em; font-size: 130%; background:white; display:block;}
			</style>
		</noscript>

		<script type="text/javascript">
		<!--

			function SubmitForm(button)
			{
				var buttons = {
					"next" : "{$nextButtonID}",
					"prev" : "{$prevButtonID}",
					"cancel" : "{$cancelButtonID}",
					"finish" : "{$finishButtonID}"
				};

				var form = document.forms["{$formName}"];
				if (form)
				{
					hiddenField = document.createElement("INPUT");
					hiddenField.type = "hidden";
					hiddenField.name = buttons[button];
					hiddenField.value = button;
					form.appendChild(hiddenField);
					form.submit();
				}

				return false;

			}
			{$jsCode}
		//-->
		</script>


	</head>

<body id="bitrix_install_template">
<p id="noscript">{$noscriptInfo}</p>
<div id="container">

	<table id="main-table" align="center">
		<tr>
			<td width="10" height="10"><img src="{$wizardPath}/corner_top_left.gif" width="10" height="10" alt="" /></td>
			<td width="100%">
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="215" height="10" style="background:white;"></td>
						<td width="525" height="10" style="background:#fefbd2;"></td>
					</tr>
				</table>
			</td>
			<td width="10" height="10"><img src="{$wizardPath}/corner_top_right.gif" width="10" height="10" alt="" /></td>
		</tr>
		<tr>
			<td colspan="3" height="100%" style="background:white">
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="225" valign="top">
							<!-- Left column -->
							<table width="100%" height="100%" cellpadding="0" cellspacing="0">
								<tr><td align="center" height="185">{$boxImage}</td></tr>
								<tr>
									<td height="100%" valign="top">
										<!-- Menu -->
										{$strNavigation}
									</td>
								</tr>
								<tr><td align="center" height="100">
									<div class="areal-crm">
										<p class="areal">
										</p>
									</div>
									{$loc['compability']}<br />
									<a href="{$loc['crm_site']}" target="_blank">{$logoImage}</a><br />
									{$loc['links_to_crm']}.<br />
									<img src="{$pathToBitrixLogo}" alt="Bitrix Logo" />
									
								</td></tr>
							</table>
						</td>
						<td width="535" valign="top">
							<!-- Right column -->
							<table width="100%" height="77" cellpadding="0" cellspacing="0">
								<tr>
									<td width="9" style="background:#fefbd2;"><img src="{$wizardPath}/top_gradient_begin.gif" width="9" height="77" alt="" /></td>
									<td class="wizard-title" width="14">&nbsp;</td>
									<td class="wizard-title">{$title}</td>
								</tr>
							</table>
							<div id="step-title">{$stepTitle}</div>
							{#FORM_START#}
							<div id="step-content">
								{$strError}
								{#CONTENT#}
								<br /><br /><div class="buttons">{$buttons}</div><br />
							</div>
							
							{#FORM_END#}
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr height="20" style="background:#f7f7f7;">
			<td colspan="3">
				<div id="copyright">
					<table width="100%" height="100%" cellpadding="0" cellspacing="5">
						<tr>
							<td>{$copyright}</td>
							<td align="right">{$support}</td>
						</tr>
					</table>
				</div>
		</tr>
		<tr>
			<td width="10" height="10" valign="bottom"><img src="{$wizardPath}/corner_bottom_left.gif" width="10" height="10" alt="" /></td>
			<td width="100%" style="background:#f7f7f7;"></td>
			<td width="10" height="10" valign="bottom"><img src="{$wizardPath}/corner_bottom_right.gif" width="10" height="10" alt="" /></td>
		</tr>
	</table>
	<script type="text/javascript">PreloadImages("{$wizardPath}/");</script>

</div>
</body>
</html>

HTML;
	}
}
?>