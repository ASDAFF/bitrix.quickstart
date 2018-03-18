<?
if (($_REQUEST['put_statistic']==1)&&(CModule::IncludeModule('iblock'))):

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.speedtest/prolog.php");
	$speed_up = $_REQUEST['speed_up'];
	$speed_down = $_REQUEST['speed_down'];
	$el = new CIBlockElement;
	$E_IP = $_SERVER["REMOTE_ADDR"];
	$now = date('d.m.Y H:i:s');
	
	$name = $E_IP;
	
	$PROP = array();
	$PROP["DATE_TEST"] = $now;
	//$PROP['IP_ADDRESS'] = $E_IP;
	$PROP["SPEED_UP"] = $speed_up;
	$PROP['SPEED_DOWN'] = $speed_down;

$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
  "IBLOCK_ID"      => SPEEDTEST_IBLOCK_ID,
  "PROPERTY_VALUES"=> $PROP,
  "NAME"           => $name,
  "ACTIVE"         => "Y"
  );
		
if($REC_ID = $el->Add($arLoadProductArray))
  echo "New rec ID: ".$REC_ID;
else
  echo "Error: ".$el->LAST_ERROR;		
	
	

else:
?>
	<form id="test_speed_form" action="<?=POST_FORM_ACTION_URI?>" method="post">
		<center>
			<div id="progressbar"></div>
			<div id = "iblock">
				<?=GetMessage("MCART_TS_PAGE_DETAIL_TEXT")?>
			</div>
			<br>
			<div>
			<input type="button" value="<?=GetMessage("MCART_TS_PAGE_TEST")?>" onclick="Start()">
			</div>
			<div id="resume"></div>
		</center>
	</form>
	<script>
	
var req;
 var txtbody;
 
 var TimeUpInterval;
 var TimeDownInterval;
 var SpeedUp;
 var SpeedDown;
 var ip;
 
 
 
function DownloadXMLDoc(url) {

	var s;
	
	req = null;
	
	s = (url+"?hash=" + Math.random());
	var TimeStartD = new Date();
	
        if (window.XMLHttpRequest) {
		
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("GET", s, false);
			
	    req.send(null);
		lengthDown = req.responseText.length;
		//document.write (lengthDown);
			
		} 
	else if (window.ActiveXObject) {
	   
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = processReqChange;
            req.open("GET", s, false);
			
            
			req.send();
			
			lengthDown = req.responseText.length;
			//document.write (lengthDown);
			}
		}
	
	var TimeEndD = new Date();
	TimeDownInterval=(TimeEndD.getTime()-TimeStartD.getTime())*0.001;
	
	SpeedDown=(lengthDown*0.0000080)/TimeDownInterval; 
	
}

function UploadXMLDoc(url) {
        
        var s;
		var TimeStart = new Date();
		s = (url+"?hash=" + Math.random());
		req.onreadystatechange = processReqUp;
        req.open("POST", s, false);

		
        req.send(txtbody);
		
		var lengthUp = txtbody.length;
	   	var TimeEnd = new Date();
		TimeUpInterval=(TimeEnd.getTime()-TimeStart.getTime())*0.001;
		SpeedUp=(lengthUp*0.0000080)/TimeUpInterval;
		
}
function processReqChange() {
    
    if (req.readyState == 4) {

	 if (req.status == 200) {

		txtbody = req.responseText;
		
        } else 
		
		{
            alert("<?=GetMessage('MCART_TS_ERROR')?>"+req.statusText);
        }
    }
}


function processReqUp() {
    
    if (req.readyState == 4) {
	 if (req.status == 200) {
	
        } else {
          
        }
    }
}

function SendValueResume() {
    if (req.readyState == 4) {
	 if (req.status == 200) {
		document.getElementById('progressbar').innerHTML	= '<?=GetMessage("MCART_TS_FINISH");?>';
		document.getElementById('iblock').innerHTML	=	 '<br/>'+'<?=GetMessage("MCART_TS_DOWN_SPEED");?>'
														+ SpeedDown.toPrecision(5) +'<?=GetMessage("MCART_TS_SPEED_EI");?>'+'<br />' + 
														'<?=GetMessage("MCART_TS_UP_SPEED");?>'+ SpeedUp.toPrecision(5)+'<?=GetMessage("MCART_TS_SPEED_EI");?>';
			$.post("",{speed_down:SpeedDown.toPrecision(5), speed_up: SpeedUp.toPrecision(5), put_statistic:1}, 
			function(msg){});											
        } else {
        }
    }
} 

function Start()
{

document.getElementById('progressbar').innerHTML = '<img src="/bitrix/components/mcart/test.speed/images/ajax-loader.gif" border="0" alt="Loading, please wait..." />';
document.getElementById('iblock').innerHTML	= '';

DownloadXMLDoc('/bitrix/components/mcart/test.speed/tmpTest'); 
UploadXMLDoc('/');
SendValueResume();
							
}
	</script>
<?endif;?>