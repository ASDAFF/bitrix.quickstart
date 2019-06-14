<?
$old_data = COption::GetOptionString("akazakov.reindex","full_text_engine","-1");
if ($old_data>=0) {
	COption::SetOptionString("akazakov.reindex", 'period', $old_data);
	COption::RemoveOption("akazakov.reindex", "full_text_engine");
}

$akazakov_reindex_default_option = array(
    "period"                => "0",
    "clear_now"       		=> "N",
    "hide_alert"            => "N",
	"informer"				=> "Y"
);

?>