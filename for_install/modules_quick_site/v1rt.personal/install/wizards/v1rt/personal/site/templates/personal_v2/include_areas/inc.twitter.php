<?if(!CModule::IncludeModule("v1rt.personal")) return;

$twiAcc = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter");
$twiCK  = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter_consumer_key");
$twiCS  = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter_consumer_secret");
$twiUT  = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter_user_token");
$twiUS  = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter_user_secret");

if($twiAcc == "" || $twiCK == "" || $twiCS == "" || $twiUT == "" || $twiUS == "")
    return;
?>
<?$APPLICATION->IncludeComponent("v1rt.personal:twitter", "last.v2", array(
        "ACCOUNT" => $twiAcc,
       	"COUNT" => "1",
       	"CONSUMER_KEY" => $twiCK,
       	"CONSUMER_SECRET" => $twiCS,
       	"USER_TOKEN" => $twiUT,
       	"USER_SECRET" => $twiUS,
       	"CACHE_TYPE" => "A",
       	"CACHE_TIME" => "1800"
   	),
   	false
);?>