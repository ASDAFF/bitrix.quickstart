<?
interface DSocialPosterConnection
{
	public function Open();
	public function Close();
	public function Execute($returnType=false);
	public function Send($url, $arFields = "", $returnType=false);
	public function GetLastResult();
}


class DSocialPosterConnectionReturnStates
{
	const HEADER_ONLY = 1;
	const BODY_ONLY = 2;
	const SUMMARY = 3;
}
?>