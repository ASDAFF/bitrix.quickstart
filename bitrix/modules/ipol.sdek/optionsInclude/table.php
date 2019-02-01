<?
	$accounts = sqlSdekLogs::getAccountsList(false,true);
?>
<style>
	.sortTr{
		cursor:pointer;
	}
	.sortTr:hover{opacity:0.7;}
	.mdTbl{overflow:hidden;}
	.IPOLSDEK_TblStOk td{
		background-color:#E2FCE2!important;
	}
	.IPOLSDEK_TblStErr td{
		background-color:#FFEDED!important;
	}
	.IPOLSDEK_TblStTzt td{
		background-color:#FCFCBF!important;
	}	
	.IPOLSDEK_TblStDel td{
		background-color:#E9E9E9!important;
	}

	.IPOLSDEK_TblStStr td{
		background-color:#FCFFCE!important;
	}
	.IPOLSDEK_TblStCor td{
		background-color:#D9FFCE!important;
	}	
	.IPOLSDEK_TblStPVZ td{
		background-color:#D9FFCE!important;
	}	
	.IPOLSDEK_TblStOtk td{
		background-color:#FFCECE!important;
	}	
	.IPOLSDEK_TblStDvd td{
		background-color:#ABFFAB!important;
	}

	.IPOLSDEK_TblStOk:hover td,.IPOLSDEK_TblStErr:hover td, .IPOLSDEK_TblStTzt:hover td, .IPOLSDEK_TblStStr:hover td, .IPOLSDEK_TblStCor:hover td, .IPOLSDEK_TblStPVZ:hover td, .IPOLSDEK_TblStOtk:hover td, .IPOLSDEK_TblStDvd:hover td{
		background-color:#E0E9EC!important;
	}
	.IPOLSDEK_crsPnt{
		cursor:pointer;
	}
	.mdTbl{
		border-bottom: 1px solid #DCE7ED;
		border-left: 1px solid #DCE7ED;
		border-right: 1px solid #DCE7ED;
		border-top: 1px solid #C4CED2;
	}
	#IPOLSDEK_flrtAL,#IPOLSDEK_flrtTbl{
		background: url("/bitrix/panel/main/images/filter-bg.gif") transparent;
		border-bottom: 1px solid #A0ABB0;
		border-radius: 5px 5px 5px;
		text-overflow: ellipsis;
		text-shadow: 0px 1px rgba(255, 255, 255, 0.702);
	}
	.IPOLSDEK_mrPd td{
		padding: 5px;
	}
	.IPOLSDEK_account{
		text-align: center;
		cursor: pointer;
	}
</style>
<script type='text/javascript'>
	IPOLSDEK_setups.table = {
		ready: function(){
			IPOLSDEK_setups.table.getTable();
		},
		// служебное
		isEmpty: function(obj){
			if(typeof(obj) == 'object')
				for(var i in obj)
					return false;
			return true;
		},
		settedFltr: {},
		getTable: function(params){
			if(typeof params == 'undefined' || typeof params == 'function')
				params={};

			IPOLSDEK_setups.table.settedFltr=IPOLSDEK_setups.table.filter.set();

			for(var i in IPOLSDEK_setups.table.settedFltr)
				params[i]=IPOLSDEK_setups.table.settedFltr[i];

			params['pgCnt']=(typeof params['pgCnt'] == 'undefined')?$('#IPOLSDEK_tblPgr').val():params['pgCnt'];
			params['page']=(typeof params['page'] == 'undefined')?$('#IPOLSDEK_crPg').html():params['page'];
			params['by']=(typeof params['by'] == 'undefined')?'ID':params['by'];
			params['sort']=(typeof params['sort'] == 'undefined')?'DESC':params['sort'];
			params['isdek_action']='tableHandler';

			$('#IPOLSDEK_tblPls').find('td').css('opacity','0.4');

			IPOLSDEK_setups.ajax({
				data: params,
				dataType: 'json',
				success: function(data){
					if(data['ttl']==0){
						if(IPOLSDEK_setups.table.isEmpty(IPOLSDEK_setups.table.settedFltr))
							$('#IPOLSDEK_flrtTbl').parent().html('<?=GetMessage('IPOLSDEK_OTHR_NO_REQ')?>');
						else{
							$('#IPOLSDEK_requests').css('display','none');
							$('#IPOLSDEK_notFound').css('display','block');
						}
					}else{
						$('#IPOLSDEK_requests').css('display','block');
						$('#IPOLSDEK_notFound').css('display','none');
						$('[onclick="IPOLSDEK_setups.table.nxtPg(-1)"]').css('visibility','visible');
						$('[onclick="IPOLSDEK_setups.table.nxtPg(1)"]').css('visibility','visible');
						if(data.cP==1)
							$('[onclick="IPOLSDEK_setups.table.nxtPg(-1)"]').css('visibility','hidden');
						if(parseInt(data.cP)>=parseInt(data.mP))
							$('[onclick="IPOLSDEK_setups.table.nxtPg(1)"]').css('visibility','hidden');
						$('#IPOLSDEK_crPg').html(data.cP);
						
						$('#IPOLSDEK_ttlCls').html('<?=GetMessage('IPOLSDEK_TABLE_COLS')?> '+((parseInt(data.cP)-1)*data.pC+1)+' - '+Math.min(parseInt(data.ttl),parseInt(data.cP)*data.pC)+' <?=GetMessage('IPOLSDEK_TABLE_FRM')?> '+data.ttl);
						$('#IPOLSDEK_tblPls').html(data.html);
					}
				}
			});
		},
		// удаление
		killReq(oid,mode){ // отзыв и удаление
			if(confirm('<?=GetMessage("IPOLSDEK_JSC_SOD_IFKILL")?>'))
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'killReqOD',oid:oid,mode:mode},
					success: function(data){
						if(data.indexOf('GD:')===0){
							alert(data.substr(3));
							IPOLSDEK_setups.table.getTable();
							if(IPOLSDEK_setups.table.ultraKillWnd)
								IPOLSDEK_setups.table.ultraKillWnd.Close();
						}else
							alert(data);
					}
				});
		},

		delReq(oid,mode){ // просто удаление
			if(confirm('<?=GetMessage("IPOLSDEK_JSC_SOD_IFDELETE")?>'))
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'delReqOD',oid:oid,mode:mode},
					success: function(data){
						alert(data);
						IPOLSDEK_setups.table.getTable();
					}
				});
		},

		forceKill(oid,mode){ // делай что угодно, только удались
			if(confirm('<?=GetMessage("IPOLSDEK_JSC_SOD_IFKILL")?>'))
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'killReqOD',oid:oid,mode:mode},
					success: function(data){
						if(data.indexOf('GD:')===0){
							alert(data.substr(3));
							IPOLSDEK_setups.table.getTable();
							if(IPOLSDEK_setups.table.ultraKillWnd)
								IPOLSDEK_setups.table.ultraKillWnd.Close();
						}else
							IPOLSDEK_setups.ajax({
								data: {isdek_action:'delReqOD',oid:oid,mode:mode},
								success: function(data){
									alert(data);
									IPOLSDEK_setups.table.getTable();
									if(IPOLSDEK_setups.table.ultraKillWnd)
										IPOLSDEK_setups.table.ultraKillWnd.Close();
								}
							});
					}
				});
		},

		ultraKillWnd: false,
		ultraKill: function(){
			if(!IPOLSDEK_setups.table.ultraKillWnd){
				IPOLSDEK_setups.table.ultraKillWnd = new BX.CDialog({
					title: '<?=GetMessage('IPOLSDEK_OTHR_killReq_TITLE')?>',
					content: "<div><a href='javascript:void(0)' onclick='$(this).next().toggle(); return false;'>?</a><div style='display:none'><small><?=GetMessage('IPOLSDEK_OTHR_killReq_DESCR')?></small></div><table><tr><td><?=GetMessage('IPOLSDEK_OTHR_killReq_LABEL')?></td><td><input size='3' type='text' id='IPOLSDEK_delDeqOrId'></td></tr><tr><td><?=GetMessage('IPOLSDEK_OTHR_killReq_TYPE')?></td><td><?if(sdekOption::canShipment()){?><select id='IPOLSDEK_delDeqType'><option value='order'><?=GetMessage('IPOLSDEK_STT_order')?></option><option value='shipment'><?=GetMessage('IPOLSDEK_STT_shipment')?></option></select><?}else{?><input id='IPOLSDEK_delDeqType' type='hidden' value='order'><?}?></td></tr></table><?=GetMessage('IPOLSDEK_OTHR_killReq_HINT')?></div>",
					icon: 'head-block',
					resizable: false,
					draggable: true,
					height: '170',
					width: '200',
					buttons: ['<input type="button" value="<?=GetMessage('IPOLSDEK_OTHR_killReq_BUTTON')?>" onclick="IPOLSDEK_setups.table.forceKill($(\'#IPOLSDEK_delDeqOrId\').val(),$(\'#IPOLSDEK_delDeqType\').val())"/>']
				});
			}
			else
				$('#IPOLSDEK_delDeqOrId').val('');
			IPOLSDEK_setups.table.ultraKillWnd.Show();
		},

		// адекватные действия
		follow: function(wat){
			window.open("http://www.edostavka.ru/track.html?order_id="+wat,"_blank");
		},

		print: function(oId,mode){
			IPOLSDEK_setups.ajax({
				data: {isdek_action : 'printOrderInvoice',oId : oId,mode : mode},
				dataType: 'json',
				success: function(data){
					if(data.result == 'ok')
						for(var i in data.files)
							window.open('/upload/<?=$module_id?>/'+data.files[i]);
					else
						alert(data.error);
				}
			});
		},

		checkState: function(dispNumber){
			IPOLSDEK_setups.ajax({
				data: {isdek_action : 'getOrderState',DispatchNumber : dispNumber},
				success: function(){
					IPOLSDEK_setups.table.getTable();
				}
			});
		},

		// сортировка
		clrCls: function(){
			$('.adm-list-table-cell-sort-up').removeClass('adm-list-table-cell-sort-up');
			$('.adm-list-table-cell-sort-down').removeClass('adm-list-table-cell-sort-down');
		},

		sort: function(wat,handle){
			if(handle.hasClass("adm-list-table-cell-sort-down"))
			{
				IPOLSDEK_setups.table.clrCls();
				handle.addClass("adm-list-table-cell-sort-up");
				IPOLSDEK_setups.table.getTable({'by':wat,'sort':'ASC'});
			}
			else
			{
				if(handle.hasClass("adm-list-table-cell-sort-up"))
				{
					IPOLSDEK_setups.table.clrCls();
					IPOLSDEK_setups.table.getTable();
				}
				else
				{
					IPOLSDEK_setups.table.clrCls();
					handle.addClass("adm-list-table-cell-sort-down");
					IPOLSDEK_setups.table.getTable({'by':wat,'sort':'DESC'});
				}
			}
		},

		//навигация
		nxtPg: function(cntr){
			var page=parseInt($("#IPOLSDEK_crPg").html())+cntr;
			if(page<1)
				page=1;
				
			if(page!=parseInt($("#IPOLSDEK_crPg").html()))
			{
				IPOLSDEK_setups.table.getTable({"page":page});
				$("#IPOLSDEK_crPg").html(page);
			}
		},

		// параметры
		shwPrms: function(handle){
			handle.siblings('a').hide();
			handle.css('height','auto');
			var height=handle.height();
			handle.css('height','0px');
			handle.animate({'height':height},500);
		},

		// фильтр
		filter:{
			set: function(){
				var params={};
				$('[id^="IPOLSDEK_Fltr_"]').each(function(){
					var crVal=$(this).val();
					if(crVal)
						params['F'+$(this).attr('id').substr(14)]=crVal;
				});
				return params;
			},
			reset: function(){
				$('[id^="IPOLSDEK_Fltr_"]').each(function(){
					$(this).val('');
				});
			}
		}
	};
</script>

<div id="pop-statuses" class="b-popup" style="display: none; ">
	<div class="pop-text"><?=GetMessage("IPOLSDEK_HELPER_statuses")?></div>
	<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
</div>

<tr><td colspan='2'>
		<table id='IPOLSDEK_flrtTbl'>
		  <tbody>
			<tr class='IPOLSDEK_mrPd'>
			  <td><?=GetMessage('IPOLSDEK_JS_SOD_number')?></td><td><input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_>=ORDER_ID'><span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span><input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_<=ORDER_ID'></td>
			</tr>
			<tr class='IPOLSDEK_mrPd'>
				<td><?=GetMessage('IPOLSDEK_JS_SOD_STATUS')?> <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup("pop-statuses", this);'></a></td>
				<td>
					<select id='IPOLSDEK_Fltr_STATUS'>
						<option value=''      ></option>
						<option value='NEW'   >NEW</option>
						<option value='ERROR' >ERROR</option>
						<option value='OK'    >OK</option>
						<option value='STORE' >STORE</option>
						<option value='TRANZT'>TRANZT</option>
						<option value='CORIER'>CORIER</option>
						<option value='PVZ'   >PVZ</option>
						<option value='OTKAZ' >OTKAZ</option>
						<option value='DELIVD'>DELIVD</option>
					</select>
				</td>
			</tr>
			<?if(sdekHelper::isConverted()){?>
				<tr class='IPOLSDEK_mrPd'>
					<td><?=GetMessage('IPOLSDEK_TABLE_SENDTYPE')?></td>
					<td>
						<select id='IPOLSDEK_Fltr_SOURCE'>
							<option value='' ></option>
							<option value='0'><?=GetMessage('IPOLSDEK_STT_order')?></option>
							<option value='1'><?=GetMessage('IPOLSDEK_STT_shipment')?></option>
						</select>
					</td>
				</tr>
			<?}?>
			<tr class='IPOLSDEK_mrPd'>
			  <td><?=GetMessage('IPOLSDEK_JS_SOD_SDEK_ID')?></td><td><input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_>=SDEK_ID'><span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span><input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_<=SDEK_ID'></td>
			</tr>
			<tr class='IPOLSDEK_mrPd'>
				<td><?=GetMessage('IPOLSDEK_TABLE_UPTIME')?></td>
				<td>
					<div class="adm-input-wrap adm-input-wrap-calendar">
						<input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_>=UPTIME' name='IPOLSDEKupF' disabled>
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'IPOLSDEKupF', form: '', bTime: true, bHideTime: false});"></span>
					</div>
					<span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span>
					<div class="adm-input-wrap adm-input-wrap-calendar">
						<input type='text' class='adm-workarea' id='IPOLSDEK_Fltr_<=UPTIME' name='IPOLSDEKupD' disabled>
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'IPOLSDEKupD', form: '', bTime: true, bHideTime: false});"></span>
					</div>
				</td>
			</tr>
			<?if(count($accounts)>1){?>
				<tr class='IPOLSDEK_mrPd'>
					<td><?=GetMessage('IPOLSDEK_TABLE_ACCOUNT')?></td>
					<td><?=sdekOption::makeSelect('IPOLSDEK_Fltr_ACCOUNT',array('' => '')+$accounts)?></td>
				</tr>
			<?}?>
			<tr>
				<td colspan='2'><div class="adm-filter-bottom-separate" style="margin-bottom:0px;"></div></td>
			</tr>
			<tr class='IPOLSDEK_mrPd'>
				<td colspan='2'><input class="adm-btn" type="button" value="<?=GetMessage('MAIN_FIND')?>" onclick="IPOLSDEK_setups.table.getTable()">&nbsp;&nbsp;&nbsp;<input class="adm-btn" type="button" value="<?=GetMessage('MAIN_RESET')?>" onclick="IPOLSDEK_setups.table.filter.reset()"></td>
			</tr>
		  </tbody>
		</table>
		<br><br>
		<div id='IPOLSDEK_requests'>
			<table class="adm-list-table mdTbl">
				<thead>
					<tr class="adm-list-table-header">
						<td class="adm-list-table-cell"><div></div></td>
						<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLSDEK_setups.table.sort("ID",$(this))'><div class='adm-list-table-cell-inner'>ID</div></td>
						<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLSDEK_setups.table.sort("MESS_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_JS_SOD_MESS_ID')?></div></td>
						<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLSDEK_setups.table.sort("ORDER_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_ORDN')?></div></td>
						<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLSDEK_setups.table.sort("STATUS",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_JS_SOD_STATUS')?></div></td>
						<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLSDEK_setups.table.sort("SDEK_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_JS_SOD_SDEK_ID')?></div></td>
						<?if(sdekHelper::isConverted()){?>
						<td class="adm-list-table-cell" style='width:77px;'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_SENDTYPE')?></div></td>
						<?}?>
						<td class="adm-list-table-cell"><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_PARAM')?></div></td>
						<td class="adm-list-table-cell"><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_MESS')?></div></td>
						<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLSDEK_setups.table.sort("UPTIME",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_UPTIME')?></div></td>
						<?if(count($accounts)>1){?>
						<td class="adm-list-table-cell sortTr" style='width:50px;' onclick='IPOLSDEK_setups.table.sort("UPTIME",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_ACCOUNT')?></div></td>
						<?}?>
					</tr>
				</thead>
				<tbody id='IPOLSDEK_tblPls'>
				</tbody>
			</table>
			<div class="adm-navigation">
				<div class="adm-nav-pages-block">
					<span class="adm-nav-page adm-nav-page-prev IPOLSDEK_crsPnt" onclick='IPOLSDEK_setups.table.nxtPg(-1)'></span>
					<span class="adm-nav-page-active adm-nav-page" id='IPOLSDEK_crPg'>1</span>
					<span class="adm-nav-page adm-nav-page-next IPOLSDEK_crsPnt" onclick='IPOLSDEK_setups.table.nxtPg(1)'></span>
				</div>
				<div class="adm-nav-pages-total-block" id='IPOLSDEK_ttlCls'><?=GetMessage('IPOLSDEK_TABLE_COLS?')?> 1 Ц 5 <?=GetMessage('IPOLSDEK_TABLE_FRM')?> 5</div>
				<div class="adm-nav-pages-number-block">
					<span class="adm-nav-pages-number">
						<span class="adm-nav-pages-number-text"><?=GetMessage('admin_lib_sett_rec')?></span>
						<select id='IPOLSDEK_tblPgr' onchange='IPOLSDEK_setups.table.getTable()'>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="20" selected="selected">20</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="0"><?=GetMessage('MAIN_OPTION_CLEAR_CACHE_ALL')?></option>
						</select>
					</span>
				</div>
			</div>
		
			<input type='button' style='margin-top:20px' value='<?=GetMessage('IPOLSDEK_OTHR_killReq_BUTTON')?>' onclick='IPOLSDEK_setups.table.ultraKill()'>&nbsp;
			<input type='button' style='margin-top:20px' value='<?=GetMessage('IPOLSDEK_OTHR_getOutLst_BUTTON_OT')?>' onclick='IPOLSDEK_syncOutb()'/>
		</div>
		<div id='IPOLSDEK_notFound'><?=GetMessage('IPOLSDEK_OTHR_NO_REQ_FILTER')?></div>
	</td></tr>