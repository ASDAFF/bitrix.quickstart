<?

/* Удалить после работы
$wizard->SetDefaultVars(
	     array
		 ("siteLogo" => $siteLogo,				
          "siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_slogan.php",            GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
          "siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),                                
          "siteContact" => $this->GetFileContent("", GetMessage("WIZ_COMPANY_CONTACT_DEF2")),
          "siteName" => $this->GetFileContent("", GetMessage("WIZ_COMPANY_NAME_DEF")),	
          //!!!!!!!!!!!!!!!!!!!!!!!!!!!						
	      "siteNameImage"=>$siteNameImage 
     	)
	);
}
	function ShowStep()
	{

               

        $wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);                

         //Картинка для лого
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-logo">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />';
		$this->content .= CFile::ShowImage($siteLogo, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		
		
		
		$siteNameI = $wizard->GetVar("siteNameImage", true);                 
		//картинка для названия
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-comnane">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= CFile::ShowImage($siteNameI, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteNameI", Array("show_file_info" => "N", "id" => "site-namei"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';



        $this->content .= '<tr><td>';
		$this->content .= '<label for="siteAdress">'.GetMessage("WIZ_COMPANY_ADRESS").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteAdress", Array("id" => "site-adress", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		
		     $this->content .= '<tr><td>';
		$this->content .= '<label for="sitePhone">'.GetMessage("WIZ_COMPANY_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-phone", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
	  

        $this->content .= '<tr><td>';
		$this->content .= '<label for="siteEmail">'.GetMessage("WIZ_COMPANY_EMAIL").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteEmail", Array("id" => "site-email", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="siteSkype">'.GetMessage("WIZ_COMPANY_SKYPE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteSkype", Array("id" => "site-skype", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
			$this->content .= '<tr><td>';
		$this->content .= '<label for="siteIcq">'.GetMessage("WIZ_COMPANY_ICQ").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteSkype", Array("id" => "site-icq", "style" => "width: 100%;"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';




*/


//CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".WIZARD_TEMPLATE_ID."/header.php", array("LOGO" => $siteLogo, "NAME" => $siteName, "TITLE" => $title, "ALT" => $alt, "SLOGAN" => $siteSlogan));
//CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".WIZARD_TEMPLATE_ID."/footer.php", array("NAME" => $siteName, "EMAIL" => $siteEmail));


/*
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/slogan.php",  array("NAME" =>$siteName, "SLOGAN" => $siteSlogan));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/logo.php",  array("LOGO" =>  "/images/logo.png"  ,  "TITLE" => $title, "ALT" => $alt));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/contacts-header.php", array("ADDRESS" => $siteContact));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/contacts-footer.php", array("EMAIL" => $siteEmail, "NAME" => $siteName));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/contacts/index.php", array("ADDRESS" => $siteContact, "YANDEX" => $siteYandex));
*/

/*
unlink($_SERVER["DOCUMENT_ROOT"]."/images/logo.png");
copy($_SERVER["DOCUMENT_ROOT"].$siteLogo, $_SERVER["DOCUMENT_ROOT"]."/images/logo.png");
*/

$wizard =& $this->GetWizard();
				
$siteCopyright=$wizard->GetVar("siteCopyright", true);
$siteTime=$wizard->GetVar("siteTime", true);
$companyname=$wizard->GetVar("siteName", true);
$siteLogo = $wizard->GetVar("siteLogo", true);
$siteSlogan=$wizard->GetVar("siteSlogan", true);
$siteAdress=$wizard->GetVar("siteAdress", true);
$sitePhone=$wizard->GetVar("sitePhone", true);
$siteEmail=$wizard->GetVar("siteEmail", true);
$siteSkype=$wizard->GetVar("siteSkype", true);
$siteICQ=$wizard->GetVar("siteICQ", true);



CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/copyright-macros.php",  array("Copyright"=>$siteCopyright));


CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/logo-macros.php",  array("LOGO"=>$siteLogo));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/cname-macros.php",  array("CNAME"=>$companyname));


CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/slogan-macros.php",  array("SLOGAN"=>$siteSlogan));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/adress-macros.php",  array("ADRESS"=>$siteAdress));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/phone-macros.php",  array("PHONE"=>$sitePhone));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/twork-macros.php",  array("TIMEW"=>$siteTime));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/email-macros.php",  array("EMAIL"=>$siteEmail));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/skype-macros.php",  array("SKYPE"=>$siteSkype));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/modules/icq-macros.php",  array("ICQ"=>$siteICQ));


WizardServices::IncludeServiceLang("type.php", 'ru');


$rsET = CEventType::GetByID("eletro_mess", "ru");
//$arET = $rsET->Fetch();

if($rsET->Fetch()==null)
{

$et = new CEventType;
$emess = new CEventMessage;


$res_et = $et->Add(array(
					"LID"       	=> "ru",
					"EVENT_NAME"    => "eletro_mess",
					"NAME"          => "Заказ на сайте",
					"DESCRIPTION"   => ""
	)
);


//Заказ на сайте
$arrOrder=array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "eletro_mess",
			"LID" =>WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
			"BCC" => "",
			"SUBJECT" => "Заказ",
			"BODY_TYPE" => "html",
			"MESSAGE"=>
"
ФИО: #USERNAME#<br>
Телефон: #PHONE#<br>
E-mail: #USEREMAIL#<br>
Название товара: #NAMEITEM#<br>
Cсылка на товар: #LINKITEM#<br>
Цена: #PRICEITEM#<br>						
"
);

$emess->Add($arrOrder);


//Обратный звонок
$arrCall=array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "eletro_mess",
			"LID" =>WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
			"BCC" => "",
			"SUBJECT" => "Обратный звонк",
			"BODY_TYPE" => "html",
			"MESSAGE"=>
"
Контакты заказчика<br>
Телефон: #PHONE#<br>
E-mail: #USEREMAIL#
"
);

$emess->Add($arrCall);


//Обратный звонок
$arrMailuser=array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "eletro_mess",
			"LID" =>WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" =>"#USEREMAIL#",
			"BCC" => "",
			"SUBJECT" => "Письмо пользователю",
			"BODY_TYPE" => "html",
			"MESSAGE"=>
"
Спасибо за заявку на сайте  #SITE_NAME#.<br> <br>

Ваши контактные данные<br>
ФИО: #USERNAME#<br>
Телефон: #PHONE#<br>
E-mail: #USEREMAIL#<br>

<br>
Сведения о товаре<br>
Название товара: #NAMEITEM#<br>
Cсылка на товар: #LINKITEM#<br>
Цена: #PRICEITEM#
"
);

$emess->Add($arrMailuser);

}



/*
$et = new CEventType;

$res_et = $et->Add(array(
					"LID"       	=> "ru",
					"EVENT_NAME"    => "COURSE",
					"NAME"          => GetMessage("EVENT_NAME_2"),
					"DESCRIPTION"   => 
					"
					#NAME#  - ".GetMessage("NAME")."
					#EMAIL# - ".GetMessage("EMAIL")."
					#PHONE# - ".GetMessage("PHONE")."
					#MESSAGE#   ".GetMessage("MESSAGE")."
					#IP#   ".GetMessage("IP")."
					"
	)
);



$res_et = $et->Add(array(
					"LID"       	=> "ru",
					"EVENT_NAME"    => "ANSWER",
					"NAME"          => GetMessage("EVENT_NAME_1"),
					"DESCRIPTION"   => 
					"
					#NAME#  - ".GetMessage("NAME")."
					#EMAIL# - ".GetMessage("EMAIL")."
					#PHONE# - ".GetMessage("PHONE")."
					#MESSAGE#   ".GetMessage("MESSAGE")."
					#IP#   ".GetMessage("IP")."
					"
	)
);

$arr=Array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "ANSWER",
			"LID" =>WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM# ",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM# ",
			"BCC" => "",
			"SUBJECT" => "#SITE_NAME#: ". GetMessage("EVENT_NAME_2"),
			"BODY_TYPE" => "text",
			"MESSAGE" => "
".GetMessage("NAME").": #NAME#
".GetMessage("EMAIL").": #EMAIL#
".GetMessage("PHONE").": #PHONE#
".GetMessage("MESSAGE").": #MESSAGE#
".GetMessage("IP").": #IP#
");

$emess = new CEventMessage;
$emess->Add($arr);


$arr=Array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => "COURSE",
			"LID" =>WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM# ",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM# ",
			"BCC" => "",
			"SUBJECT" => "#SITE_NAME#: ". GetMessage("EVENT_NAME_2"),
			"BODY_TYPE" => "text",
			"MESSAGE" => "
".GetMessage("NAME").": #NAME#
".GetMessage("EMAIL").": #EMAIL#
".GetMessage("PHONE").": #PHONE#
".GetMessage("PASSPORT").": #MESSAGE#
".GetMessage("IP").": #IP#
");

$emess = new CEventMessage;
$emess->Add($arr);
*/


?>
