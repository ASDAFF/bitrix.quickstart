<style>
	.ipol_header {
		font-size: 16px;
		cursor: pointer;
		display:block;
		color:#2E569C;
	}

	.ipol_inst {
		display:none; 
		margin-left:10px;
		margin-top:10px;
	}
	img{border: 1px dotted black;}
</style>
<script>
	function IPOLSDEK_auth(){
		$("[onclick='IPOLSDEK_auth()']").attr('disabled','disabled');
		var login    = $('#IPOLSDEK_login').val();
		var password = $('#IPOLSDEK_pass').val();
		
		if(!login){
			alert('<?=GetMessage("IPOLSDEK_ALRT_NOLOGIN")?>');
			$("[onclick='IPOLSDEK_auth()']").removeAttr('disabled');
			return;
		}
		if(!password){
			alert('<?=GetMessage("IPOLSDEK_ALRT_NOPASS")?>');
			$("[onclick='IPOLSDEK_auth()']").removeAttr('disabled');
			return;
		}
		$.post(
			"/bitrix/js/<?=$module_id?>/ajax.php",
			{
				'isdek_action' : 'auth',
				'login'        : login,
				'password'     : password,
			},
			function(data){
				if(data.trim().indexOf('G')===0){
					alert(data.trim().substr(1));
					window.location.reload();
				}
				else{
					alert(data);
					$("[onclick='IPOLSDEK_auth()']").removeAttr('disabled');
					$('.ipol_inst').css('display','block');
					$('#ipol_mistakes').css('display','block');
				}
			}
		);
	}
	function IPOLSDEK_doSbmt(e){
		if(e.keyCode==13)
			IPOLSDEK_auth();
	}
	
	$(document).ready(function(){
		$('#IPOLSDEK_login').on('keyup',IPOLSDEK_doSbmt);
		$('#IPOLSDEK_pass').on('keyup',IPOLSDEK_doSbmt);
	});
</script>
<tr><td colspan='2'><?=GetMessage("IPOLSDEK_LABEL_authHint")?></td></tr>
<tr><td>Account</td><td><input type='text' id='IPOLSDEK_login'></td></tr>
<tr><td>Secure_password</td><td><input type='password' id='IPOLSDEK_pass'></td></tr>
<tr><td></td><td><input type='button' value='<?=GetMessage('IPOLSDEK_LBL_AUTHORIZE')?>' onclick='IPOLSDEK_auth()'></td></tr>

<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('API')?>	
</td></tr>