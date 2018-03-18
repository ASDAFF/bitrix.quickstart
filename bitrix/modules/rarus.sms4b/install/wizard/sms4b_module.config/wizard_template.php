<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
 class RedTemplate extends CWizardTemplate
{

	function GetLayout()
	{
		$wizard = $this->GetWizard();

		$formName = htmlspecialchars($wizard->GetFormName());

		$adminScript = CAdminPage::ShowScript();

		$charset = LANG_CHARSET;
		$wizardName = htmlspecialcharsEx($wizard->GetWizardName());

		$nextButtonID = htmlspecialchars($wizard->GetNextButtonID());
		$prevButtonID = htmlspecialchars($wizard->GetPrevButtonID());
		$cancelButtonID = htmlspecialchars($wizard->GetCancelButtonID());
		$finishButtonID = htmlspecialchars($wizard->GetFinishButtonID());

		IncludeAJAX();
		$ajaxScripts = $GLOBALS["APPLICATION"]->GetHeadStrings();
		$ajaxScripts .= $GLOBALS["APPLICATION"]->GetHeadScripts();

		$obStep = $wizard->GetCurrentStep();
		$arErrors = $obStep->GetErrors();
		$strError = $strJsError = "";
		if (count($arErrors) > 0)
		{
			foreach ($arErrors as $arError)
			{
				$strError .= $arError[0]."<br />";

				if ($arError[1] !== false)
					$strJsError .= ($strJsError <> ""? ", ":"")."{'name':'".CUtil::addslashes($wizard->GetRealName($arError[1]))."', 'title':'".CUtil::addslashes(htmlspecialcharsback($arError[0]))."'}";
			}

			if (strlen($strError) > 0)
				$strError = '<div id="step_error">'.$strError."</div>";

			$strJsError = '
			<script type="text/javascript">
				ShowWarnings(['.$strJsError.']);
			</script>';
		}

		$stepTitle = $obStep->GetTitle();
		$stepSubTitle = $obStep->GetSubTitle();

		$autoSubmit = "";
		if ($obStep->IsAutoSubmit())
			$autoSubmit = 'setTimeout("AutoSubmit();", 500);';

		$alertText = GetMessage("MAIN_WIZARD_WANT_TO_CANCEL");
		$loadingText = GetMessage("MAIN_WIZARD_WAIT_WINDOW_TEXT");

		$package = $wizard->GetPackage();

		if ($package !== null)
		{
			$wizardPath = $package->GetPath();
			$arDescription = $package->GetDescription();
			$masterIcon = "";
			if (isset($arDescription["ICON"]) && strlen($arDescription["ICON"]) > 0)
				$masterIcon = ' style="background-image:url('.$wizardPath.'/'.$arDescription["ICON"].')"';
		}
		$themeID = ADMIN_THEME_ID;

		return <<<HTML
<html>
	<head>
		<title>{$wizardName}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
		{$ajaxScripts}
		<style type="text/css">
			body
			{
				margin:0;
				padding:0;
				background-color: #DDE8F1;
				font-family:Verdana,Arial,helvetica,sans-serif;
				font-size:75%;
			}
			table {font-size:100%;}
			form {margin:0;}

			#border-box
			{
				margin:2px 2px 0 2px;
				border:1px solid #A9BBC8;
			}

			#step_info
			{
				height:45px;
				padding:8px 30px 8px 55px;
				border-bottom:1px solid #ccc;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				overflow:hidden;
				background:#F2F5F9 url(/bitrix/themes/{$themeID}/images/wizard/wizard.gif) 10px center no-repeat;

			}

			#step_title
			{
				font-weight:bold;
			}

			#step_description
			{
				font-size:95%;
				margin-left:10px;
			}

			#step_content
			{
				padding:20px 20px;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				float:left;
			}
			#step_buttons
			{
				height:50px;
				text-align:right;
				padding-right:20px;
				padding-top:5px;
				overflow:hidden;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
			}

			#step_content_container
			{
				height:290px;
				overflow:auto;
				background:#fff;
			}

			#step_error
			{
				color:red;
				background:white;
				border-bottom:1px solid #ccc;
				padding:2px 30px;
			}

			#hidden-layer
			{
				background:#F8F9FC none repeat scroll 0%;
				height:100%;
				left:0pt;
				opacity:0.01;
				filter:alpha(opacity=1);
				-moz-opacity:0.01;
				position:absolute;
				top:0pt;
				width:100%;
				z-index:10001;
			}

			/*Data table*/
			table.wizard-data-table
			{
				border:1px solid #B2C4DD;
				border-collapse:collapse;
			}

			/*Any cell*/
			table.wizard-data-table td
			{
				border:1px solid #B2C4DD;
				background-color:#FFFFFF;
				padding:3px 5px;
			}

			/*Head cell*/
			table.wizard-data-table thead td, table.wizard-data-table th
			{
				background-color:#E4EDF5;
				font-weight:normal;
				background-image:none;
				border:1px solid #B2C4DD;
				padding:4px;
			}

			/*Body cell*/
			table.wizard-data-table tbody td
			{
				background-color:#FFF;
				background-image:none;
			}

			/*Foot cell*/
			table.wizard-data-table tfoot td
			{
				background-color:#F2F5F9;
				padding:4px;
			}

			.wizard-note-box
			{
				background:#FEFDEA;
				padding:7px;
				border:1px solid #D7D6BA;
			}

			.wizard-required
			{
				color:red;
			}
		</style>

		{$adminScript}

		<script type="text/javascript">

			function OnLoad()
			{
				var title = self.parent.window.document.getElementById("wizard_dialog_title");
				if (title)
					title.innerHTML = "{$wizardName}";

				var form = document.forms["{$formName}"];

				if (form)
					form.onsubmit = OnFormSubmit;

				var cancelButton = document.forms["{$formName}"].elements["{$cancelButtonID}"];
				var nextButton = document.forms["{$formName}"].elements["{$nextButtonID}"];
				var prevButton = document.forms["{$formName}"].elements["{$prevButtonID}"];
				var finishButton = document.forms["{$formName}"].elements["{$finishButtonID}"];

				if (cancelButton && !nextButton && !prevButton && !finishButton)
					cancelButton.onclick = CloseWindow;
				else if(cancelButton)
					cancelButton.onclick = ConfirmCancel;

				{$autoSubmit}
			}

			function OnFormSubmit()
			{
				var div = document.body.appendChild(document.createElement("DIV"));
				div.id = "hidden-layer";
			}

			function AutoSubmit()
			{
				var nextButton = document.forms["{$formName}"].elements["{$nextButtonID}"];
				if (nextButton)
				{
					var wizard = self.parent.window.WizardWindow;
					if (wizard)
					{
						wizard.messLoading = "{$loadingText}";
						wizard.ShowWaitWindow();
					}

					nextButton.click();
					nextButton.disabled=true;
				}
			}

			function ConfirmCancel()
			{
				return (confirm("{$alertText}"));
			}

			function ShowWarnings(warnings)
			{
				var form = document.forms["{$formName}"];
				if(!form)
					return;

				for(var i in warnings)
				{
					var e = form.elements[warnings[i]["name"]];
					if(!e)
						continue;

					var type = (e.type? e.type.toLowerCase():"");
					var bBefore = false;
					if(e.length > 1 && type != "select-one" && type != "select-multiple")
					{
						e = e[0];
						bBefore = true;
					}
					if(type == "textarea" || type == "select-multiple")
						bBefore = true;

					var td = e.parentNode;
					var img;
					if(bBefore)
					{
						img = td.insertBefore(new Image(), e);
						td.insertBefore(document.createElement("BR"), e);
					}
					else
					{
						img = td.insertBefore(new Image(), e.nextSibling);
						img.hspace = 2;
						img.vspace = 2;
						img.style.verticalAlign = "bottom";
					}
					img.src = "/bitrix/themes/"+phpVars.ADMIN_THEME_ID+"/images/icon_warn.gif";
					img.title = warnings[i]["title"];
				}
			}

			document.onkeydown = EnterKeyPress;

			function EnterKeyPress(event)
			{
				if (!document.getElementById)
					return;

				if (window.event)
					event = window.event;

				if (!event.ctrlKey)
					return;

				var key = (event.keyCode ? event.keyCode : (event.which ? event.which : null) );

				if (!key)
					return;

				if (key == 13 || key == 39)
				{
					var nextButton = document.forms["{$formName}"].elements["{$nextButtonID}"];
					if (nextButton)
						nextButton.click();
				}
				else if (key == 37)
				{
					var prevButton = document.forms["{$formName}"].elements["{$prevButtonID}"];
					if (prevButton)
						prevButton.click();
				}
			}

			function CloseWindow()
			{
				if (self.parent.window.WizardWindow)
					self.parent.window.WizardWindow.Close();
			}

		</script>

	</head>

	<body onload="OnLoad();">

		{#FORM_START#}
		<div id="border-box">
			<div id="step_info"{$masterIcon}>
				<div id="step_title">{$stepTitle}</div>
				<div id="step_description">{$stepSubTitle}</div>
			</div>

			<div id="step_content_container">
				{$strError}
				<div id="step_content">{#CONTENT#}</div>
			</div>
		</div>

		<div id="step_buttons">{#BUTTONS#}</div>

		{#FORM_END#}
		{$strJsError}

	</body>
</html>
HTML;

	}

}

 ?>