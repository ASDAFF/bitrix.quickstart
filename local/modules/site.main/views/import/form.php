<tr id="<?=$result["SERVICE"]?>">
	<td colspan="2">
		<table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td colspan="2">
					<input type=button value="Старт" id="<?=$result["SERVICE"]?>_work_start" class="js-work-start" data-service="<?=$result["SERVICE"]?>" />
					<input type=button value="Стоп" disabled id="<?=$result["SERVICE"]?>_work_stop" class="js-work-stop" data-service="<?=$result["SERVICE"]?>" />
				</td>
			</tr>
		</table>
		<div id="<?=$result["SERVICE"]?>_progress" style="display:none;" width="100%">
			<br />
			<div id="<?=$result["SERVICE"]?>_status"></div>
			<table border="0" cellspacing="0" cellpadding="2" width="100%">
				<tr>
					<td height="10">
						<div style="border:1px solid #B9CBDF">
							<div id="<?=$result["SERVICE"]?>_indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
						</div>
					</td>
					<td width=30>&nbsp;<span id="<?=$result["SERVICE"]?>_percent">0%</span></td>
				</tr>
			</table>
		</div>
		<div id="<?=$result["SERVICE"]?>_result" style="padding-top:10px"></div>
		<input type="hidden" id="<?=$result["SERVICE"]?>_sess_id" value="<?=bitrix_sessid_get()?>">
		<input type="hidden" id="<?=$result["SERVICE"]?>_iblock" value="<?=$result["IBLOCK"]?>">
		<input type="hidden" id="<?=$result["SERVICE"]?>_limit" value="<?=$result["LIMIT"]?>">
	</td>
</tr>
