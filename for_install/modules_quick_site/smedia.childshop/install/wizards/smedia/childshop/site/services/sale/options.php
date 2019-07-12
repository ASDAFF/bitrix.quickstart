<?
 if (WIZARD_IS_RERUN)
	return;
COption::SetOptionString("sale", "location", '1', WIZARD_SITE_ID);
COption::SetOptionString("sale", "location_zip", '101000', WIZARD_SITE_ID);
COption::SetOptionString("sale", "WEIGHT_different_set", 'N', WIZARD_SITE_ID);
COption::SetOptionString("sale", "weight_koef", '1000', WIZARD_SITE_ID);
COption::SetOptionString("sale", "weight_unit", GetMessage("sale_weight_unit"), WIZARD_SITE_ID);
?>