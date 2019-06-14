<?
//kill postfix from string
function kill_post_fix($str = '')
{
	$parts = explode("@",$str);
	return $parts[0];
}

//adds phone.sms to the tel num
function add_postfix($str = '')
{
	$trig = false;
	$parts = explode("@",$str);
	foreach($parts as $index)
	{
		if ($index == "phone.sms")
		{
			$trig = true;
		}
	}
	if (!$trig) 
		$str = $str."@phone.sms"; 
	return $str;
}
?>