<?
Class MDTicket
{
	function CheckUser($email)
	{
		$rsUser = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL"=>$email))->Fetch();
		if($rsUser["ID"]>0)
		{
			return false;
		}
		return true;
	}
	
	
}


?>
 