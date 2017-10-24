function wizibfFlood(STEP, arTmpData)
{
	if (STEP == null) STEP = 0;
	if (typeof(STEP) == 'object') STEP = 0;

	function __refreshLog(data)
	{
		var obContainer = document.getElementById('output');
		
		if (obContainer)
		{
			obContainer.innerHTML += data;
		}
		
		PCloseWaitMessage('wait_message', true);		
	}

	PShowWaitMessage('wait_message', true);

	if (STEP <= 2)
	{
		if (arTmpData != null) 
		{
			var percent = Math.round((arTmpData.POS/arTmpData.AMOUNT) * 100);
			jsPB.Update(percent);
			
			var obWaitMessage = document.getElementById('wait_message');
				
			if(arTmpData.ERROR_MESSAGE != null) {

				var obSpan = document.createElement('span');
				obSpan.innerHTML = ' ' + percent + '%, <span style="color: red">' + arTmpData.ERROR_MESSAGE + '</span>';
				obWaitMessage.appendChild(obSpan);
			
			} else {
			
				obWaitMessage.appendChild(document.createTextNode(' ' + percent + '%'));
			
			}
			
		}
		else
		{
			jsPB.Init('wiz_ibf_progress');
			var obWaitMessage = document.getElementById('wait_message');
			obWaitMessage.appendChild(document.createTextNode(' 0%'));
			
		}
	}
	else if (STEP == 3)
	{
		jsPB.Update(100);
	}
	
	var arData = 
	{
		'WIZ_IBF_LANG':wiz_ibf_lang,		
		'STEP_LENGTH':step_length,
		'STEP':STEP,
		'sessid':sessid
	}
	
	var TID = CPHttpRequest.InitThread();
	CPHttpRequest.SetAction(TID,__refreshLog);
	CPHttpRequest.Send(TID, path + '/scripts/flood.php', arData);
}

function RunError()
{
	var obErrorMessage = document.getElementById('error_message');
	if (obErrorMessage) obErrorMessage.style.display = 'inline';
}

function RunAgain()
{
	var obOut = document.getElementById('output');
	var obErrorMessage = document.getElementById('error_message');
	
	obOut.innerHTML = '';
	obErrorMessage.style.display = 'none';
	Run(1);
}

function DisableButton(e)
{
	var obNextButton = document.forms[formID][nextButtonID];
	obNextButton.disabled = true;
	var obPrevButton = document.forms[formID][prevButtonID];
	obPrevButton.disabled = true;
}

function EnableButton()
{
	var obNextButton = document.forms[formID][nextButtonID];
	obNextButton.disabled = false;
	var obPrevButton = document.forms[formID][prevButtonID];
	obPrevButton.disabled = false;
}

var jsPB = {

	bInit:false,
	curValue:0,
	width:0,
	
	Init: function(cont_id)
	{
		if (this.bInit)
		{
			this.Update(0)
			return;
		}
		
		var obContainer = document.getElementById(cont_id);
		if (!obContainer) return false;

		this.width = obContainer.offsetWidth;
		
		var obPb = document.createElement('DIV');
		obPb.id = 'wiz_ibf_pb';
		obPb.style.width = this.width + 'px';
		
		var obIndicator = document.createElement('DIV');
		obIndicator.id = 'wiz_ibf_pb_indicator';

		obIndicator.style.width = '0px';
		
		obPb.appendChild(obIndicator);
		obContainer.appendChild(obPb);

		this.bInit = true;
	},

	Update: function(percent)
	{
		this.curValue = percent;
	
		var obIndicator = document.getElementById('wiz_ibf_pb_indicator');
		obIndicator.style.width = Math.round(this.width * percent / 100)+'px';
	},
	
	Remove: function(bRemoveParent)
	{
		if (bRemoveParent == null) bRemoveParent = false;
		var obPb = document.getElementById('wiz_ibf_pb');
		
		if (obPb)
		{
			if (!bRemoveParent)
				obPb.parentNode.removeChild(obPb);
			else
				obPb.parentNode.parentNode.removeChild(obPb.parentNode);
		}
	}
}