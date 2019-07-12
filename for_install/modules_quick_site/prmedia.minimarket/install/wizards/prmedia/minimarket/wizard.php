<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/install/wizard_sol/wizard.php';

// welcome
class WelcomeStep extends CWizardStep
{

	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('welcome');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_WELCOME_STEP_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetNextStep('select_site');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));
	}

	function ShowStep()
	{
		$this->content = Loc::getMessage('PRMEDIA_WMM_WELCOME_STEP_CONTENT');
	}

}

// cancel installation
class CancelStep extends CWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('cancel');
		$this->SetCancelStep('cancel');
		$this->SetCancelCaption(Loc::getMessage('PRMEDIA_WMM_CANCEL_STEP_CAPTION'));
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_CANCEL_STEP_TITLE'));
	}
	
	function OnPostForm()
	{
		// redirect to wizards page
		LocalRedirect('/bitrix/admin/wizard_list.php');
	}
	
	function ShowStep()
	{
		$this->content = Loc::getMessage('PRMEDIA_WMM_CANCEL_STEP_CONTENT');
	}
}

// select site
class SelectSiteStep extends CSelectSiteWizardStep
{

	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('select_site');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_SELECT_SITE_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('welcome');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));

		$this->SetNextStep('select_theme');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));
		
		$wizard = & $this->GetWizard();
		$wizard->solutionName = 'prmedia.minimarket';
		$wizard->SetVar('templateID', 'prmedia_mm');
	}

}

// select color theme
class SelectThemeStep extends CSelectThemeWizardStep
{

	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('select_theme');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_SELECT_THEME_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('select_site');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));

		$this->SetNextStep('site_settings');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));
	}

}

// site settings
class SiteSettingsStep extends CSiteSettingsWizardStep
{

	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('site_settings');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('select_theme');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));

		$this->SetNextStep('shop_settings');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));

		$wizard = & $this->GetWizard();
		
		$siteIncludesPath = WIZARD_SITE_PATH . 'include_areas';
		$includesPath = $_SERVER['DOCUMENT_ROOT'] . $wizard->GetPath() . '/site/public/' . LANGUAGE_ID . '/include_areas';
		$wizard->SetDefaultVars(array(
			'sitename' => $this->GetFileContent("$siteIncludesPath/sitename.php", $this->GetFileContent("$includesPath/sitename.php", '')),
			'sitelogo' => WIZARD_SITE_DIR . 'images/logo.png',
			'deflogo' => $wizard->GetPath() . '/site/public/' . LANGUAGE_ID . '/images/def-logo.png',
			'slogan' => $this->GetFileContent("$siteIncludesPath/slogan.php", $this->GetFileContent("$includesPath/slogan.php", '')),
			'headercontact' => $this->GetFileContent("$siteIncludesPath/header_contact.php", $this->GetFileContent("$includesPath/header_contact.php", '')),
			'schedule' => $this->GetFileContent("$siteIncludesPath/schedule.php", $this->GetFileContent("$includesPath/schedule.php", '')),
			'phone' => $this->GetFileContent("$siteIncludesPath/phone.php", $this->GetFileContent("$includesPath/phone.php", '')),
			'sitenamefooter' => $this->GetFileContent("$siteIncludesPath/sitename_footer.php", $this->GetFileContent("$includesPath/sitename_footer.php", '')),
			'sloganfooter' => $this->GetFileContent("$siteIncludesPath/slogan_footer.php", $this->GetFileContent("$includesPath/slogan_footer.php", '')),
			'phonelabelfooter' => $this->GetFileContent("$siteIncludesPath/phonelabel_footer.php", $this->GetFileContent("$includesPath/phonelabel_footer.php", '')),
			'phonefooter' => $this->GetFileContent("$siteIncludesPath/phone_footer.php", $this->GetFileContent("$includesPath/phone_footer.php", '')),
			'metadescription' => Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_META_DESCRIPTION'),
			'metakeywords' => Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_META_KEYWORDS'),
		));
	}

	function ShowStep()
	{
		$wizard = & $this->GetWizard();
		
		// get logo images
		$logoImg = '';
		$logo = $wizard->GetVar('logo');
		if (intval($logo) == 0)
		{
			$logo = $wizard->GetVar('sitelogo', true);
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $logo))
			{
				$logo = $wizard->GetVar('deflogo', true);
			}
			$logoImg = "<img src='$logo' width='250px' />";
		}
		else
		{
			$logoImg = CFile::ShowImage($logo, 250, 0);
		}

		$siteSettingsPreview = $wizard->GetPath() . '/images/ru/sitesettings.png';
		$this->content .= '
			<div class="inst-note-block inst-note-block-yellow">
					<div class="inst-note-block-icon"></div>
					<div class="inst-note-block-text">' . str_replace('#SRC#', $siteSettingsPreview, Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SITE_NOTE_1')) . '</div>
				</div>';
		$this->content .= '
			<div class="inst-note-block inst-note-block-yellow">
					<div class="inst-note-block-icon"></div>
					<div class="inst-note-block-text">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SITE_NOTE_2') . '</div>
				</div>';

		$this->content .= '<div class="wizard-input-form">';

		// site name
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="sitename" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SITE_NAME_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'sitename', array('id' => 'sitename', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// logo
		$this->content .=
			'<div class="wizard-input-form-block">
				<label for="logo" class="wizard-input-title">' . GetMessage('PRMEDIA_WMM_SITE_SETTINGS_LOGO_LABEL') . ':</label>
				<div>'
					. $logoImg . '<br>'
					. $this->ShowFileField('logo', array('show_file_info' => 'N', 'id' => 'logo')) .
        '</div>
			</div>';

		// slogan
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="slogan" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SLOGAN_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'slogan', array('id' => 'slogan', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// header contact
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="headercontact" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_HEADER_CONTACT_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'headercontact', array('id' => 'headercontact', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// phone
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="slogan" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_PHONE_LABEL') . ':</label>
				<div>' . $this->ShowInputField('text', 'phone', array('id' => 'phone', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// schedule
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="slogan" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SCHEDULE_LABEL') . ':</label>
				<div>' . $this->ShowInputField('text', 'schedule', array('id' => 'schedule', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// site name footer
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="sitenamefooter" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SITE_NAME_FOOTER_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'sitenamefooter', array('id' => 'sitenamefooter', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// slogan footer
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="sloganfooter" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_SLOGAN_FOOTER_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'sloganfooter', array('id' => 'sloganfooter', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// phone label footer
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="phonelabelfooter" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_PHONE_LABEL_FOOTER_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'phonelabelfooter', array('id' => 'phonelabelfooter', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// phone label footer
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="phonefooter" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_PHONE_FOOTER_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'phonefooter', array('id' => 'phonefooter', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// install demodata
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_INSTALL_DEMODATA_TITLE') . '</div>
				<div class="inst-note-block inst-note-block-yellow">
					<div class="inst-note-block-icon"></div>
					<div class="inst-note-block-text">
						' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_INSTALL_DEMODATA_DESCRIPTION') . '
					</div>
				</div>'
				. $this->ShowCheckboxField('installDemoData', 'Y', (array('id' => 'installDemoData', 'checked' => 'checked'))) .
				'<label for="installDemoData" id="labelInstallDemoData">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_INSTALL_DEMODATA_LABEL') . '</label>
			</div>';
		
		// meta description/keywords
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_INSTALL_META_TITLE') . '</div>
				<label for="metadescription" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_META_DESCRIPTION_LABEL') . ':</label>
				<div>' . $this->ShowInputField('textarea', 'metadescription', array('id' => 'metadescription', 'rows' => '3', 'class' => 'wizard-field')). '</div>
			</div>';
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="metakeywords" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SITE_SETTINGS_META_KEYWORDS_LABEL') . ':</label>
				<div>' . $this->ShowInputField('textarea', 'metakeywords', array('id' => 'metakeywords', 'rows' => '3', 'class' => 'wizard-field')) . '</div>
      </div>';

		$this->content .= '</div>';
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$this->SaveFile('logo', array(
			'extensions' => 'gif,jpg,jpeg,png',
			'max_height' => 70,
			'max_width' => 190,
			'make_preview' => 'Y'
		));
	}

}

// shop settings
class ShopSettingsStep extends CWizardStep
{
	
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('shop_settings');
		$this->SetTitle(GetMessage('PRMEDIA_WMM_SHOP_SETTINGS_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('site_settings');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));

		$this->SetNextStep('person_type');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));

		$wizard = & $this->GetWizard();
		$siteId = $wizard->GetVar('siteID');
		$moduleId = 'prmedia.minimarket';

		$wizard->SetDefaultVars(array(
			'shopemail' => COption::GetOptionString($moduleId, 'shopemail', 'sale@' . $_SERVER['SERVER_NAME'], $siteId),
			'shopname' => COption::GetOptionString($moduleId, 'shopname', Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_NAME_DEFAULT'), $siteId),
			'shoplocation' => COption::GetOptionString($moduleId, 'shoplocation', Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_LOCATION_DEFAULT'), $siteId),
			'shopaddr' => COption::GetOptionString($moduleId, 'shopaddr', Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_ADDRESS_DEFAULT'), $siteId),
			'shopinn' => COption::GetOptionString($moduleId, 'shopinn', '1234567890', $siteId),
			'shopkpp' => COption::GetOptionString($moduleId, 'shopkpp', '123456789', $siteId),
			'shopns' => COption::GetOptionString($moduleId, 'shopns', '0000 0000 0000 0000 0000', $siteId),
			'shopbank' => COption::GetOptionString($moduleId, 'shopbank', Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_BANK_DEFAULT'), $siteId),
			'shopbankrekv' => COption::GetOptionString($moduleId, 'shopbankrerk', Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_BANKREKV_DEFAULT'), $siteId),
			'shopks' => COption::GetOptionString($moduleId, 'shopks', '30101 810 4 0000 0000225', $siteId),
			'shopstamp' => COption::GetOptionString($moduleId, 'siteStamp', $shopStamp, $siteId)
		));
	}

	function ShowStep()
	{
		$wizard = & $this->GetWizard();
		
		// get stamp image
		$shopStamp = '';
		$stamp = $wizard->GetVar('shopstamp');
		if (intval($stamp) == 0)
		{
			$stamppath = WIZARD_SITE_DIR . 'include_areas/stamp.gif';
			
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $stamppath))
			{
				$shopStamp = "<img src='$stamppath' />";
			}
		}
		else
		{
			$shopStamp = CFile::ShowImage($stamp);
		}

		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
			<div class="inst-note-block inst-note-block-yellow">
					<div class="inst-note-block-icon"></div>
					<div class="inst-note-block-text">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_SITE_NOTE_1') . '</div>
				</div>';
		
		// shop email
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="shopemail" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_EMAIL_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'shopemail', array('id' => 'shopemail', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// shop name
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="shopname" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_NAME_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'shopname', array('id' => 'shopname', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// shop location
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="shoplocation" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_LOCATION_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'shoplocation', array('id' => 'shoplocation', 'class' => 'wizard-field')) . '</div>
      </div>';
		
		// shop address
		$this->content .=
			'<div class="wizard-input-form-block">
        <label for="shopaddr" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_ADDRESS_LABEL') . ':</label></h4>
				<div>' . $this->ShowInputField('text', 'shopaddr', array('id' => 'shopaddr', 'class' => 'wizard-field')) . '</div>
      </div>';

		// bank rekv
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_BANKREKV_LABEL') . '</div>
				<table class="wizard-input-table">
					<tr>
						<td class="wizard-input-table-left"><label for="shopinn" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_INN_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopinn', array('id' => 'shopinn', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopkpp" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_KPP_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopkpp', array('id' => 'shopkpp', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopns" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_NS_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopns', array('id' => 'shopns', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopbank" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_BANK_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopbank', array('id' => 'shopbank', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopbankrekv" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_BANKREKV_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopbankrekv', array('id' => 'shopbankrekv', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopks" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_KS_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowInputField('text', 'shopks', array('id' => 'shopks', 'class' => 'wizard-field')) .
						'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left"><label for="shopstamp" class="wizard-input-title">' . Loc::getMessage('PRMEDIA_WMM_SHOP_SETTINGS_STAMP_LABEL') . ':</label></th>
						<td class="wizard-input-table-right">'
							. $this->ShowFileField('shopstamp', array('id' => 'shopstamp', 'show_file_info' => 'N')) . '<br>'
							. $shopStamp .
						'</td>
					</tr>
				</table>
			</div>
		';

		$this->content .= '</div>';
	}

	function OnPostForm()
	{
		$wizard = & $this->GetWizard();
		$this->SaveFile('shopstamp', array(
			'extensions' => 'gif,jpg,jpeg,png',
			'max_height' => 70,
			'max_width' => 190,
			'make_preview' => 'Y'
		));
	}

}

// person type
class PersonTypeStep extends CWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('person_type');
		$this->SetTitle(GetMessage('PRMEDIA_WMM_PERSON_TYPE_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('shop_settings');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));
		
		$this->SetNextStep('pay_system');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));

		$wizard = & $this->GetWizard();
		$siteId = $wizard->GetVar('siteID');
		$moduleId = 'prmedia.minimarket';

		$wizard->SetDefaultVars(array(
			'persontype' => array(
				'f' => COption::GetOptionString($moduleId, 'person_type_f', 'Y', $siteId),
				'u' => COption::GetOptionString($moduleId, 'person_type_u', 'Y', $siteId),
			)
		));
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$this->content .= '<div class="wizard-input-form label-pointer">';
		$this->content .= '
			<div class="inst-note-block inst-note-block-yellow">
				<div class="inst-note-block-icon"></div>
				<div class="inst-note-block-text">' . Loc::getMessage('PRMEDIA_WMM_PERSON_TYPE_SITE_NOTE_1') . '</div>
			</div>';
		
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-catalog-form-item">'
					. $this->ShowCheckboxField('persontype[f]', 'Y', (array('id' => 'person_type_f'))) .
					' <label for="person_type_f">' . Loc::getMessage('PRMEDIA_WMM_PERSON_TYPE_F_LABEL') . '</label><br>
				</div>
				<div class="wizard-catalog-form-item">
					'.$this->ShowCheckboxField('persontype[u]', 'Y', (array('id' => 'person_type_u'))).
					' <label for="person_type_u">' . Loc::getMessage('PRMEDIA_WMM_PERSON_TYPE_U_LABEL') . '</label><br>
				</div>
			</div>';
				
		$this->content .= '</div>';
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$personType = $wizard->GetVar('persontype');
		if ($wizard->IsNextButtonClick() && empty($personType['f']) && empty($personType['u']))
		{
			$this->SetError(Loc::getMessage('PRMEDIA_WMM_PERSON_TYPE_ERROR_EMPTY'));
		}
	}

}

// pay system
class PaySystemStep extends CWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('pay_system');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_TITLE'));
		$this->SetCancelStep('cancel');

		$this->SetPrevStep('person_type');
		$this->SetPrevCaption(Loc::getMessage('PRMEDIA_WMM_PREV_BUTTON'));

		$this->SetNextStep('data_install');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_NEXT_BUTTON'));

		$wizard = & $this->GetWizard();

		$wizard->SetDefaultVars(array(
			'paysystem' => array(
				'cash' => 'Y',
				'sber' => 'Y',
				'bill' => 'Y',
				'collect' => 'Y' // cash on delivery
			),
			'delivery' => array(
				'courier' => 'Y',
				'self' => 'Y'
			)
		));
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$personType = $wizard->GetVar('persontype');
		
		$this->content .= '<div class="wizard-input-form label-pointer">';
		
		
		// pay systems
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-catalog-title">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_PAY_TITLE') . '</div>';
		$this->content .= '
			<div class="inst-note-block inst-note-block-yellow">
				<div class="inst-note-block-icon"></div>
				<div class="inst-note-block-text">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_SITE_NOTE_1') . '</div>
			</div>';
		$this->content .= '
			<div class="wizard-catalog-form-item">'
				. $this->ShowCheckboxField('paysystem[cash]', 'Y', array('id' => 'paysystemC')) .
				' <label for="paysystemC">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_C_LABEL') . '</label>
			</div>';
			if ($personType['f'] == 'Y')
			{
				$this->content .=
					'<div class="wizard-catalog-form-item">'
						. $this->ShowCheckboxField('paysystem[sber]', 'Y', array('id' => 'paysystemS')) .
						' <label for="paysystemS">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_S_LABEL') . '</label>
					</div>';
			}
			$this->content .=
				'<div class="wizard-catalog-form-item">'
					. $this->ShowCheckboxField('paysystem[collect]', 'Y', array('id' => 'paysystemCOL')) .
					' <label for="paysystemCOL">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_COL_LABEL') . '</label>
				</div>';
			if($personType['u'] == 'Y')
			{
				$this->content .=
					'<div class="wizard-catalog-form-item">'
						. $this->ShowCheckboxField('paysystem[bill]', 'Y', array('id' => 'paysystemB')) .
						' <label for="paysystemB">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_B_LABEL') . '</label>
					</div>';
			}
		$this->content .= '</div>';
		
		// delivery
		$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-catalog-title">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_DELIVERY_TITLE') . '</div>
				<div class="wizard-catalog-form-item">'
					. $this->ShowCheckboxField('delivery[courier]', 'Y', array('id' => 'deliveryC')) .
					' <label for="deliveryC">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_DELIVERY_C_LABEL') . '</label>
				</div>
				<div class="wizard-catalog-form-item">'
					. $this->ShowCheckboxField('delivery[self]', 'Y', array('id' => 'deliveryS')) .
					' <label for="deliveryS">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_DELIVERY_S_LABEL') . '</label>
				</div>
			</div>';
		
		// location (@todo always false)
		$wizardInstalled = COption::GetOptionString('prmedia.minimarket', 'wizard_installed', 'N', WIZARD_SITE_ID) != 'Y' ? true : false;
		if ($wizardInstalled === false)
		{
			$this->content .= '
				<div class="wizard-input-form-block">
					<div class="wizard-catalog-title">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_LOC_TITLE') . '</div>
					<div class="inst-note-block inst-note-block-yellow">
						<div class="inst-note-block-icon"></div>
						<div class="inst-note-block-text">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_SITE_NOTE_2') . '</div>
					</div>
					<div class="wizard-catalog-form-item">'
						. $this->ShowRadioField('locations_csv', 'loc_ussr.csv', array(
							'id' => 'loc_ussr',
							'checked' => 'checked'
						)) .
						' <label for="loc_ussr">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_LOC_USSR_LABEL') . '</label>
					</div>
					<div class="wizard-catalog-form-item">'
						. $this->ShowRadioField('locations_csv', '', array('id' => 'loc_none')) .
					' <label for="loc_none">' . Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_LOC_NONE_LABEL') . '</label>
				</div>';
		}

		$this->content .= '</div>';
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$paysystem = $wizard->GetVar('paysystem');

		if (empty($paysystem['cash'])
			&& empty($paysystem['sber'])
			&& empty($paysystem['bill'])
			&& empty($paysystem['paypal'])
			&& empty($paysystem['oshad'])
			&& empty($paysystem['collect'])
		)
			$this->SetError(Loc::getMessage('PRMEDIA_WMM_PAY_SYSTEM_ERROR_EMPTY'));
	}

}

// data install step
class DataInstallStep extends CDataInstallWizardStep
{}

// finish step
class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID('finish');
		$this->SetTitle(Loc::getMessage('PRMEDIA_WMM_FINISH_TITLE'));

		$this->SetNextStep('finish');
		$this->SetNextCaption(Loc::getMessage('PRMEDIA_WMM_FINISH_NEXT_BUTTON'));
		
		$wizard =& $this->GetWizard();
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();		   
		
		// set link to site index page
		$dir = '/'; 
		$siteId = WizardServices::GetCurrentSiteID($wizard->GetVar('siteID'));
		$rsSite = CSite::GetByID($siteId);
		if ($site = $rsSite->Fetch())
		{
			$dir = $site['DIR'];
		}
		$wizard->SetFormActionScript(str_replace('//', '/', "$dir/?finish"));

		$this->CreateNewIndex();
		COption::SetOptionString('main', 'wizard_solution', $wizard->solutionName, false, $siteId);

		$this->content .=
			'<table class="wizard-completion-table">
				<tr>
					<td class="wizard-completion-cell">'
						. Loc::getMessage('PRMEDIA_WMM_FINISH_COMPLETE') .
					'</td>
				</tr>
			</table>';		
		
	}

}

// styles
echo 
	'<style>
		p {line-height: 1.5em}
		.inst-sequence-step-item:last-child{display:none}
		.wizard-cancel-button {
			float: left;
			background: url("ni/instal-sprite.png") no-repeat 10px -114px, linear-gradient(to bottom, #f8f8f9, #eef3f5) 0 0;
			border-radius: 4px;
			-webkit-box-shadow: 0 1px 0 #a9a9a9, 0 0 1px rgba(0,0,0,.5), 0 1px 1px rgba(0,0,0,.3), inset 0 1px #fff, inset 0 0 1px rgba(255,255,255,.5);
			box-shadow: 0 1px 0 #a9a9a9, 0 0 1px rgba(0,0,0,.25), 0 1px 1px rgba(0,0,0,.7), inset 0 1px 0 #fff, inset 0 0 0 1px rgba(255,255,255,.4);
			border: none;
			display: inline-block;
			color: #505d67;
			cursor: pointer;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 15px;
			font-weight: bold;
			line-height: 36px;
			min-width: 133px;
			margin: 0 12px 0 0;
			height: 36px;
			padding: 0 23px;
			position: relative;
			text-shadow: 0 1px rgba(255,255,255,.8);
			text-align: center;
			vertical-align: top;
		}
		h4 {display: inline-block; margin: 0 0 0 10px;}
		#labelInstallDemoData {cursor: pointer; }
		.label-pointer label, .label-pointer input {cursor: pointer; }
	</style>
';

// scripts
echo
	'<script>
		window.onload = function () {
			document.getElementsByClassName("wizard-cancel-button")[0].onclick = function () {
				return confirm("' . GetMessage('PRMEDIA_WMM_CANCEL_STEP_CONFIRM') . '");
			};
		}
	</script>';