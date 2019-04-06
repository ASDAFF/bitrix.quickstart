<?
	function dump($var, $die = false, $all = false)
	  {
	  	global $USER;
	    //($USER->GetID() == 1)
	  	if($USER->IsAdmin() || ($all == true))
	  	{
	  		?>
	  		<font style="text-align: left; font-size: 10px"><pre><?print_r($var)?></pre></font><br>
	  		<?
	  	}
	  	if($die)
	  	{
	  		die;
	  	}
	  }


?>