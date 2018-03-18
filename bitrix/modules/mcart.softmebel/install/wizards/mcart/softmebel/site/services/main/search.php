<?
CModule::IncludeModule("search");
if (!isset($_SESSION['SearchFirst']))
	$NS = CSearch::ReIndexAll(false, 20, Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
else
	$NS = CSearch::ReIndexAll(false, 20, $_SESSION['SearchNS']);
           
if (is_array($NS))  //repeat step, if indexing doesn't finish
{   
	$this->repeatCurrentService = true; 
	$_SESSION['SearchNS'] = $NS;
	$_SESSION['SearchFirst'] = 1;	
}
else
{
	unset($_SESSION['SearchNS']);
	unset($_SESSION['SearchFirst']);       
}
?>