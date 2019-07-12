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

		$wizardPath = $wizard->GetPath();

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
		
		if($_GET['editCBlock'] == "Y"){
			$title = GetMessage("WIZARD_TITLE_EDIT");
		} else {
			$title = GetMessage("WIZARD_TITLE");
		}
		$title = str_replace("#VERS#", $productVersion , $title);

		$copyright = GetMessage("COPYRIGHT");
		$copyright = str_replace("#CURRENT_YEAR#", date("Y") , $copyright);

		$support = GetMessage("SUPPORT");

		$wizardName = $wizard->GetWizardName();

		//Images
		$logoImage = "";
		$boxImage = "";

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPath."/images/".LANGUAGE_ID."/logo.gif"))
			$logoImage = '<img src="'.$wizardPath.'/images/'.LANGUAGE_ID.'/logo.gif" alt="" />';
		elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPath."/images/en/logo.gif"))
			$logoImage = '<img src="'.$wizardPath.'/images/en/logo.gif" alt="" />';

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPath."/images/".LANGUAGE_ID."/box.jpg"))
			$boxImage = '<img src="'.$wizardPath.'/images/'.LANGUAGE_ID.'/box.jpg" alt="" />';
		elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$wizardPath."/images/en/box.jpg"))
			$boxImage = '<img src="'.$wizardPath.'/images/en/box.jpg" alt="" />';

		$strErrorMessage = "";
		$strWarningMessage = "";
		$strNavigation = "";

		$arSteps = $wizard->GetWizardSteps();
		$currentStepID = $wizard->GetCurrentStepID();

		$currentSuccess = false;
		$stepNumber = 1;

		foreach ($arSteps as $stepID => $stepObject)
		{
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
		$jsCode = file_get_contents($_SERVER["DOCUMENT_ROOT"].$wizardPath."/scripts/script.js");

		$noscriptInfo = GetMessage("INST_JAVASCRIPT_DISABLED");
		$charset = LANG_CHARSET;

		$currentStep =& $wizard->GetCurrentStep();

		$buttons = "";

		if ($currentStep->GetNextStepID() != null)
			$buttons .= '<a onclick="this.blur(); return SubmitForm(\'next\');" href="" class="button-next"><span id="next-button-caption">'.GetMessage("WIZARD_NEXT_BUTTON").'</span></a>';

		if ($currentStep->GetPrevStepID() != null)
			$buttons .= '<a onclick="this.blur(); return SubmitForm(\'prev\');" href="" class="button-prev"><span id="prev-button-caption">'.GetMessage("WIZARD_PREV_BUTTON").'</span></a>';

		$head = $GLOBALS["APPLICATION"]->GetHeadStrings();
			
		return <<<HTML
<html>
	<head>
		<title>{$wizardName}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
		{$head}
		<style type="text/css">

			html {height:100%;}

			body 
			{
				background:#781813 url({$wizardPath}/images/bg_fill.gif) repeat;
				margin:0;
				padding:0;
				padding-bottom:6px;
				font-family: Arial, Verdana, Helvetica, sans-serif;
				font-size:82%;
				height:100%;
				color:black;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
			}

			#noscript {display:none;}

			table {font-size:100.01%;}

			a {color:#2676b9}

			h3 {font-size:120%;}

			#container
			{
				padding-top:6px;
				height:100%;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
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
				background:#fefbd2 url({$wizardPath}/images/top_gradient_fill.gif) repeat-x; 
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

			#menu td.menu-number, #menu td.menu-name 
			{
				background:#eaeaea url({$wizardPath}/images/menu_fill.gif) repeat-x;
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
				background:#b41d07 url({$wizardPath}/images/menu_fill_selected.gif) repeat-x;
				color:white;
			}

			#menu tr.done
			{
				color:black;
			}

			#menu td.menu-end
			{
				background: url({$wizardPath}/images/menu_end.gif) repeat-x;
				width:11px;
			}

			#menu tr.selected td.menu-end
			{
				background: url({$wizardPath}/images/menu_end_selected.gif) repeat-x;
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

			input.wizard-prev-button {background: #ffe681 url({$wizardPath}/images/prev.gif); border:none; width:116px; height:31px; font-weight:bold; padding-bottom:4px; cursor:pointer; cursor:hand;}
			input.wizard-next-button {background: #ffe681 url({$wizardPath}/images/next.gif); border:none; width:116px; height:31px; font-weight:bold; padding-bottom:4px; cursor:pointer; cursor:hand;}

			form {margin:0; padding:0;}
			#step-error {color:red; padding:4px 4px 4px 25px;margin-bottom:4px; background:url({$wizardPath}/images/error.gif) no-repeat;}
			small{font-size:85%;}

			.required {color:red;}

			a.button-next
			{
				background: transparent url({$wizardPath}/images/button_next.png) no-repeat scroll top right;
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
				background: transparent url({$wizardPath}/images/button_next.png) no-repeat;
				display: block;
				line-height: 17px;
				color:black;
				padding: 5px 0 9px 18px;
			}

			a.button-prev
			{
				background: transparent url({$wizardPath}/images/button_prev.png) no-repeat scroll top right;
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
				background: transparent url({$wizardPath}/images/button_prev.png) no-repeat;
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
			div.wizard-input-form-field-file input,
			div.wizard-input-form-field-text select
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
			
			#propstable td
			{
				padding: 10px;
			}

			#propstable .propsrow td
			{
				border-bottom: 1px #E1E1E1 solid;
				vertical-align: top;
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
			<td width="10" height="10"><img src="{$wizardPath}/images/corner_top_left.gif" width="10" height="10" alt="" /></td>
			<td width="100%">
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="215" height="10" style="background:white;"></td>
						<td width="525" height="10" style="background:#fefbd2;"></td>
					</tr>
				</table>
			</td>
			<td width="10" height="10"><img src="{$wizardPath}/images/corner_top_right.gif" width="10" height="10" alt="" /></td>
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
								<tr><td align="center" height="100">{$logoImage}</td></tr>
							</table>
						</td>
						<td width="535" valign="top">
							<!-- Right column -->
							<table width="100%" height="77" cellpadding="0" cellspacing="0">
								<tr>
									<td width="9" style="background:#fefbd2;"><img src="{$wizardPath}/images/top_gradient_begin.gif" width="9" height="77" alt="" /></td>
									<td class="wizard-title" width="14">&nbsp;</td>
									<td class="wizard-title">{$title}</td>
								</tr>
							</table>
							<div id="step-title">{$stepTitle}</div>
							{#FORM_START#}
							<div id="step-content">
								{$strError}
								{#CONTENT#}
								<br /><br /><div class="buttons">{$buttons}</div><br /><br /><br />
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
			<td width="10" height="10" valign="bottom"><img src="{$wizardPath}/images/corner_bottom_left.gif" width="10" height="10" alt="" /></td>
			<td width="100%" style="background:#f7f7f7;"></td>
			<td width="10" height="10" valign="bottom"><img src="{$wizardPath}/images/corner_bottom_right.gif" width="10" height="10" alt="" /></td>
		</tr>
	</table>
	<script type="text/javascript">PreloadImages("{$wizardPath}/images/");</script>

</div>
</body>
</html>

HTML;
	}
}
?>