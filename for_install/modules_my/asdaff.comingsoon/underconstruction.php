<?
$header = COption::GetOptionString("asdaff.comingsoon", "CS_header_".SITE_ID);
$bg = COption::GetOptionString("asdaff.comingsoon", "CS_bg_".SITE_ID);
$logo = COption::GetOptionString("asdaff.comingsoon", "CS_logo_".SITE_ID);
$text = COption::GetOptionString("asdaff.comingsoon", "CS_text_".SITE_ID);
$default_time = COption::GetOptionString("asdaff.comingsoon", "CS_date_".SITE_ID);
if($default_time)
    $ar_time = explode('.',$default_time);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.comingsoon/underconstruction.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//RU	"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>">
<?header('Content-Type: text/html; charset=echo LANG_CHARSET');?>
	<title><?=$header;?></title>
	<link rel="stylesheet" href="/bitrix/themes/asdaff.comingsoon/style.css" type="text/css" charset="utf-8" />
	<link rel="stylesheet" href="/bitrix/themes/asdaff.comingsoon/ie.css" type="text/css" charset="utf-8" />
	<script language="Javascript" type="text/javascript" src="/bitrix/js/asdaff.comingsoon/jquery-1.7.1.min.js"></script>
	<script language="Javascript" type="text/javascript" src="/bitrix/js/asdaff.comingsoon/jquery.lwtCountdown-1.0.js"></script>
	<script language="Javascript" type="text/javascript" src="/bitrix/js/asdaff.comingsoon/misc.js"></script>
 
</head>

<body style="background:<?=$bg;?>">
	<div id="wrapper">
		<div id="logo"><img src="<?=$logo;?>" />
		</div> <!-- end of logo -->
		
		<div id="main">
			<div id="text"><?=$text;?></div>
			<div id="countdown">
				
					<div class="dash weeks_dash">
						<span class="dash_title"><?=GetMessage("WEEKS");?></span>
						<div class="digit">0</div>
						<div class="digit">0</div>
					</div>

					<div class="dash days_dash">
						<span class="dash_title"><?=GetMessage("DAYS");?></span>
						<div class="digit">0</div>
						<div class="digit">0</div>
					</div>

					<div class="dash hours_dash">
						<span class="dash_title"><?=GetMessage("HOURS");?></span>
						<div class="digit">0</div>
						<div class="digit">0</div>
					</div>

					<div class="dash minutes_dash">
						<span class="dash_title"><?=GetMessage("MINUTES");?></span>
						<div class="digit">0</div>
						<div class="digit">0</div>
					</div>

					<div class="dash seconds_dash">
						<span class="dash_title"><?=GetMessage("SECOUNDS");?></span>
						<div class="digit">0</div>
						<div class="digit">0</div>
					</div>

				
					
			</div> <!-- end of countdown -->
			<a href="http://www.epir.biz" target="_blank"><div id="copyright"></div></a>
		</div> <!-- end of main -->
		
		<!-- start of the javascript code that handles the countdown -->
		<script language="javascript" type="text/javascript">
			jQuery(document).ready(function() {
				$('#countdown').countDown({
					targetDate: {
						'day': 		<?=$ar_time[0];?>,
						'month': 	<?=$ar_time[1];?>,
						'year': 	<?=$ar_time[2];?>,
						'hour': 	0,
						'min': 		0,
						'sec': 		0
					}
				});										
			});
		</script>
		<!-- end of the javascript code that handles the countdown -->
		
	</div> <!-- end of wrapper -->
	
</body>
</html>