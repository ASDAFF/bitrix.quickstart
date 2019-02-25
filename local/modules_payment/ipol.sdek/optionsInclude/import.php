<script>
	var IPOLSDEK_cityImport = {
		stat: false,

		timeout: 60,

		start: function(){
			if(IPOLSDEK_cityImport.stat == 'killed') return;
			IPOLSDEK_cityImport.hideButtons();
			IPOLSDEK_cityImport.timeout = IPOLSDEK_cityImport.getTime();
			IPOLSDEK_cityImport.onRezult({result:'',text:'<?=GetMessage('IPOLSDEK_IMPORT_PROCESS_SCHECK')?>'})
			IPOLSDEK_cityImport.request({mode:'setImport'});
			IPOLSDEK_cityImport.stat = 'preSync';
		},

		request: function(data){
			if(IPOLSDEK_cityImport.stat == 'killed') return;
			data.timeOut = IPOLSDEK_cityImport.timeout;
			data.isdek_action  = 'handleImport';
			if(typeof(data.mode) != 'undefined')
				IPOLSDEK_cityImport.setCount(IPOLSDEK_cityImport.timeout);
			$.ajax({
				url : '/bitrix/js/<?=$module_id?>/ajax.php',
				type: 'POST',
				dataType: 'json',
				data: data,
				error: function(a,b,c){console.log('error occured',b,c);},
				success: function(data){
					IPOLSDEK_cityImport.onRezult(data);
				}
			});
		},

		onRezult: function(data){
			console.log('onRezult',data);
			switch(data.step){
				case 'init': 
					data.text += ' <?=GetMessage("IPOLSDEK_IMPORT_PROCESS_ONINIT_1")?>: '+data.result.total+'.<br><?=GetMessage("IPOLSDEK_IMPORT_PROCESS_ONINIT_2")?>';
					IPOLSDEK_cityImport.request({mode:'process',text:'<?=GetMessage('IPOLSDEK_IMPORT_PROCESS_WORKINGOUT')?>'});
				break;
				case 'process': IPOLSDEK_cityImport.request({mode:'process'});
				break;
				case 'contSync': IPOLSDEK_cityImport.request({mode:'setSync'});
				break;
				case 'startImport':
					if(IPOLSDEK_cityImport.stat == 'aftSenc'){
						data.text += "<?=GetMessage("IPOLSDEK_IMPORT_PROCESS_IEND")?>";
						IPOLSDEK_cityImport.killCount();
					}else{
						data.text += "<?=GetMessage('IPOLSDEK_IMPORT_PROCESS_ISTART')?>";
						IPOLSDEK_cityImport.stat = 'import'; IPOLSDEK_cityImport.request({mode:'setImport'});
					}
				break;
				case 'endImport': 
					data.text += "<br><br><?=GetMessage('IPOLSDEK_IMPORT_PROCESS_SCHECK')?>";
					IPOLSDEK_cityImport.stat = 'aftSenc';
					IPOLSDEK_cityImport.request({mode:'setSync'});
				break;
				case false:
					IPOLSDEK_cityImport.killCount();
				break;
			}
					
			$('#IPOLSDEK_status').append('<div class="IPOLSDEK_import_'+data.result+'">'+data.text+'</div>');
			
		},

		// таймер
		counter: false,
		curTime: false,

		setCount: function(dur){
			IPOLSDEK_cityImport.killCount();
			IPOLSDEK_cityImport.counter = setInterval(IPOLSDEK_cityImport.count,1000);
			IPOLSDEK_cityImport.curTime = dur;
		},
		count: function(){
			$('#IPOLSDEK_timeout').html('<?=GetMessage('IPOLSDEK_IMPORT_LBL_ANSWER')?> '+(IPOLSDEK_cityImport.curTime --) + ' <?=GetMessage("IPOLSDEK_IMPORT_LBL_sec")?>.');
		},
		killCount: function(){
			if(IPOLSDEK_cityImport.counter)
				clearInterval(IPOLSDEK_cityImport.counter);
			$('#IPOLSDEK_timeout').html('');
		},

		// настройки
		timeOutCheck: function(){
			$('#IPOLSDEK_timeoutCnter').val(IPOLSDEK_cityImport.getTime());
		},
		getTime: function(){
			var val = parseInt($('#IPOLSDEK_timeoutCnter').val());
			if(isNaN(val))
				val = 60;
			return val;
		},
		// прочее
		kill: function(){
			IPOLSDEK_cityImport.hideButtons();
			IPOLSDEK_cityImport.stat = 'killed';
			$.post(
				"/bitrix/js/<?=$module_id?>/ajax.php",
				{isdek_action:'setImport',mode:'N'},
				function(data){window.location.reload();}
			);
		},
		hideButtons: function(){
			$('#IPOLSDEK_importStart').attr('disabled','disabled');
			$('#IPOLSDEK_killWnd').attr('disabled','disabled');
			$('#IPOLSDEK_cntrt').css('display','none');
		}
	}
</script>
<style>
	.IPOLSDEK_import_error{
		color: red;
	}
	.IPOLSDEK_import_done{
		color: green;
	}
	#IPOLSDEK_status{
		margin: 5px;
	}
	#IPOLSDEK_timeoutCnter{
		width: 15px;
		text-align: center;
	}
	#IPOLSDEK_cntrt{
		margin: 5px 0px;
	}
	.IPOLSDEK_import_errors{
		display:none;
		padding: 5px;
		font-size: 10px;
		border: 1px dotted black;
	}
</style>
<tr><td colspan="2"><?=GetMessage('IPOLSDEK_IMPORT_LBL_BEWIZE')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('IMPORT')?>
</td></tr>

<tr><td style="color:#555;" colspan="2">
<div id='IPOLSDEK_status'>
</div>
<div id='IPOLSDEK_timeout'></div>
<div id='IPOLSDEK_cntrt'><?=GetMessage('IPOLSDEK_IMPORT_LBL_TIMEOUT')?>: <input id='IPOLSDEK_timeoutCnter' value='60' type='text' onKeyUp = "IPOLSDEK_cityImport.timeOutCheck()"> <?=GetMessage('IPOLSDEK_IMPORT_LBL_sec')?></div>
<input id='IPOLSDEK_importStart' type='button' value='<?=GetMessage('IPOLSDEK_IMPORT_LBL_START')?>' onclick='IPOLSDEK_cityImport.start()'>&nbsp;&nbsp;
<input id='IPOLSDEK_killWnd' type='button' value='<?=GetMessage('IPOLSDEK_IMPORT_LBL_KILL')?>' onclick='IPOLSDEK_cityImport.kill()'>
</td></tr>