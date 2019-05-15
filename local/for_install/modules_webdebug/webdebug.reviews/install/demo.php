<?
IncludeModuleLangFile(__FILE__);

function webdebug_reviews_demo_expired() {
	$DemoMode = CModule::IncludeModuleEx('webdebug.reviews');
	if ($DemoMode==MODULE_DEMO) {
		$now=time();
		if (defined('webdebug_reviews_OLDSITEEXPIREDATE')) {
			if ($now>=webdebug_reviews_OLDSITEEXPIREDATE) {
				return true;
			}
		} else{ 
			return true;
		}
	} elseif ($DemoMode==MODULE_DEMO_EXPIRED) {
		return true;
	}
	return false;
}

function webdebug_reviews_show_demo() {
	$DemoMode = CModule::IncludeModuleEx('webdebug.reviews');
	if ($DemoMode==MODULE_DEMO) {
		$now=time();
		if (defined('webdebug_reviews_OLDSITEEXPIREDATE')) {
			if ($now<webdebug_reviews_OLDSITEEXPIREDATE) {
				print BeginNote();
				$expire_arr = getdate(webdebug_reviews_OLDSITEEXPIREDATE);
				$expire_date = gmmktime($expire_arr['hours'],$expire_arr['minutes'],$expire_arr['seconds'],$expire_arr['mon'],$expire_arr['mday'],$expire_arr['year']);
				$now_arr = getdate($now);
				$now_date = gmmktime($expire_arr['hours'],$expire_arr['minutes'],$expire_arr['seconds'],$now_arr['mon'],$now_arr['mday'],$now_arr['year']);
				$days = ($expire_date-$now_date)/86400; 
				print GetMessage('WEBDEBUG_REVIEWS_DEMO_MESSAGE_DAYS_REMAIN',array('#DAYS#'=>$days));
				print EndNote();
			} else {
				print BeginNote();
				print GetMessage('WEBDEBUG_REVIEWS_DEMO_MESSAGE_EXPIRED');
				print EndNote();
			}
		} else{ 
			print BeginNote();
			print GetMessage('WEBDEBUG_REVIEWS_DEMO_MESSAGE_EXPIRED');
			print EndNote();
		}
	} elseif ($DemoMode==MODULE_DEMO_EXPIRED) {
		print BeginNote();
		print GetMessage('WEBDEBUG_REVIEWS_DEMO_MESSAGE_EXPIRED');
		print EndNote();
	}
}

?>