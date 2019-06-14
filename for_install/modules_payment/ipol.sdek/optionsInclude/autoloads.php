<script type='text/javascript'>
	IPOLSDEK_setups.autoloads = {
		ready: function(){
			IPOLSDEK_setups.autoloads.getTable();
		},

		settedFltr: {},
		getTable: function(params){
			if(typeof params == 'undefined' || typeof params == 'function')
				params={};
			IPOLSDEK_setups.autoloads.settedFltr=IPOLSDEK_setups.autoloads.filter.set();

			for(var i in IPOLSDEK_setups.autoloads.settedFltr)
				params[i]=IPOLSDEK_setups.autoloads.settedFltr[i];

			params['pgCnt']=(typeof params['pgCnt'] == 'undefined')?$('#IPOLSDEK_ALtblPgr').val():params['pgCnt'];
			params['page']=(typeof params['page'] == 'undefined')?$('#IPOLSDEK_ALcrPg').html():params['page'];
			params['by']=(typeof params['by'] == 'undefined')?'ORDER_ID':params['by'];
			params['sort']=(typeof params['sort'] == 'undefined')?'DESC':params['sort'];
			params['isdek_action']='autoLoadsHandler';

			$('#IPOLSDEK_ALPls').find('td').css('opacity','0.4');
			IPOLSDEK_setups.ajax({
				data: params,
				dataType: 'json',
				success: function(data){
					if(data['ttl']==0){
						if(IPOLSDEK_setups.isEmpty(IPOLSDEK_setups.autoloads.settedFltr))
							$('#IPOLSDEK_flrtAL').parent().html('<?=GetMessage('IPOLSDEK_OTHR_NO_REQ')?>');
						else{
							$('#IPOLSDEK_autoloads').css('display','none');
							$('#IPOLSDEK_ALNotFound').css('display','block');
						}
					}else{
						$('#IPOLSDEK_autoloads').css('display','block');
						$('#IPOLSDEK_ALNotFound').css('display','none');
						$('[onclick="IPOLSDEK_setups.autoloads.nxtPg(-1)"]').css('visibility','visible');
						$('[onclick="IPOLSDEK_setups.autoloads.nxtPg(1)"]').css('visibility','visible');
						if(data.cP==1)
							$('[onclick="IPOLSDEK_setups.autoloads.nxtPg(-1)"]').css('visibility','hidden');
						if(data.cP>=data.mP)
							$('[onclick="IPOLSDEK_setups.autoloads.nxtPg(1)"]').css('visibility','hidden');
						$('#IPOLSDEK_ALcrPg').html(data.cP);
						
						$('#IPOLSDEK_ALttlCls').html('<?=GetMessage('IPOLSDEK_TABLE_COLS')?> '+((parseInt(data.cP)-1)*data.pC+1)+' - '+Math.min(parseInt(data.ttl),parseInt(data.cP)*data.pC)+' <?=GetMessage('IPOLSDEK_TABLE_FRM')?> '+data.ttl);
						$('#IPOLSDEK_ALPls').html(data.html);
					}
				}
			});
		},
		// сортировка
		clrCls: function(){
			$('.adm-list-table-cell-sort-up').removeClass('adm-list-table-cell-sort-up');
			$('.adm-list-table-cell-sort-down').removeClass('adm-list-table-cell-sort-down');
		},

		sort: function(wat,handle){
			if(handle.hasClass("adm-list-table-cell-sort-down")){
				IPOLSDEK_setups.autoloads.clrCls();
				handle.addClass("adm-list-table-cell-sort-up");
				IPOLSDEK_setups.autoloads.getTable({'by':wat,'sort':'ASC'});
			}else{
				if(handle.hasClass("adm-list-table-cell-sort-up")){
					IPOLSDEK_setups.autoloads.clrCls();
					IPOLSDEK_setups.autoloads.getTable();
				}else{
					IPOLSDEK_setups.autoloads.clrCls();
					handle.addClass("adm-list-table-cell-sort-down");
					IPOLSDEK_setups.autoloads.getTable({'by':wat,'sort':'DESC'});
				}
			}
		},

		//навигация
		nxtPg: function(cntr){
			var page=parseInt($("#IPOLSDEK_ALcrPg").html())+cntr;
			if(page<1)
				page=1;
				
			if(page!=parseInt($("#IPOLSDEK_ALcrPg").html())){
				IPOLSDEK_setups.autoloads.getTable({"page":page});
				$("#IPOLSDEK_ALcrPg").html(page);
			}
		},

		// фильтр
		filter:{
			set: function(){
				var params={};
				$('[id^="IPOLSDEK_ALFltr_"]').each(function(){
					if($(this).attr('type') != 'checkbox' || $(this).attr('checked')){
						var crVal=$(this).val();
						if(crVal)
							params['F'+$(this).attr('id').substr(16)]=crVal;
					}
				});
				return params;
			},
			reset: function(){
				$('[id^="IPOLSDEK_ALFltr_"]').each(function(){
					$(this).val('');
				});
			}
		},

		// отключение
		turnOff: function(){
			$('#IPOLSDEK_DEAUTO').attr('disabled','disabled');
			IPOLSDEK_setups.ajax({
				data: {'isdek_action':'setAutoloads','mode':'N'},
				success: IPOLSDEK_setups.reload
			});
		}
	}
</script>

<tr><td colspan='2'>
	<table id='IPOLSDEK_flrtAL'>
	  <tbody>
		<tr class='IPOLSDEK_mrPd'>
		  <td><?=GetMessage('IPOLSDEK_JS_SOD_number')?></td><td><input type='text' class='adm-workarea' id='IPOLSDEK_ALFltr_>=ORDER_ID'><span class="adm-filter-text-wrap" style='margin: 4px 12px 0px'>...</span><input type='text' class='adm-workarea' id='IPOLSDEK_ALFltr_<=ORDER_ID'></td>
		</tr>
		<tr class='IPOLSDEK_mrPd'>
			<td><?=GetMessage('IPOLSDEK_ALTABLE_FAILS')?></td>
			<td><input type='checkbox' value='Y' id='IPOLSDEK_ALFltr_STATUS'></td>
		</tr>
		<tr>
			<td colspan='2'><div class="adm-filter-bottom-separate" style="margin-bottom:0px;"></div></td>
		</tr>
		<tr class='IPOLSDEK_mrPd'>
			<td colspan='2'><input class="adm-btn" type="button" value="<?=GetMessage('MAIN_FIND')?>" onclick="IPOLSDEK_setups.autoloads.getTable()">&nbsp;&nbsp;&nbsp;<input class="adm-btn" type="button" value="<?=GetMessage('MAIN_RESET')?>" onclick="IPOLSDEK_setups.autoloads.filter.reset()"></td>
		</tr>
	  </tbody>
	</table>
	<br><br>
	<div id='IPOLSDEK_autoloads'>
		<table class="adm-list-table mdTbl">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell sortTr" style='width:77px;' onclick='IPOLSDEK_setups.autoloads.sort("ORDER_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_TABLE_ORDN')?></div></td>
					<td class="adm-list-table-cell sortTr" style='width: 145px;' onclick='IPOLSDEK_setups.autoloads.sort("SDEK_ID",$(this))'><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_JS_SOD_SDEK_ID')?></div></td>
					<td class="adm-list-table-cell"><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_ALTABLE_STATUS')?></div></td>
					<td class="adm-list-table-cell" style='width:77px;' ><div class='adm-list-table-cell-inner'><?=GetMessage('IPOLSDEK_ALTABLE_STATUSDEK')?></div></td>
				</tr>
			</thead>
			<tbody id='IPOLSDEK_ALPls'>
			</tbody>
		</table>
		<div class="adm-navigation">
			<div class="adm-nav-pages-block">
				<span class="adm-nav-page adm-nav-page-prev IPOLSDEK_crsPnt" onclick='IPOLSDEK_setups.autoloads.nxtPg(-1)'></span>
				<span class="adm-nav-page-active adm-nav-page" id='IPOLSDEK_ALcrPg'>1</span>
				<span class="adm-nav-page adm-nav-page-next IPOLSDEK_crsPnt" onclick='IPOLSDEK_setups.autoloads.nxtPg(1)'></span>
			</div>
			<div class="adm-nav-pages-total-block" id='IPOLSDEK_ALttlCls'><?=GetMessage('IPOLSDEK_TABLE_COLS?')?> 1 Ц 5 <?=GetMessage('IPOLSDEK_TABLE_FRM')?> 5</div>
			<div class="adm-nav-pages-number-block">
				<span class="adm-nav-pages-number">
					<span class="adm-nav-pages-number-text"><?=GetMessage('admin_lib_sett_rec')?></span>
					<select id='IPOLSDEK_ALtblPgr' onchange='IPOLSDEK_setups.autoloads.getTable()'>
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
	</div>
	<div id='IPOLSDEK_ALNotFound'><?=GetMessage('IPOLSDEK_OTHR_NO_REQ_FILTER')?></div>
</td></tr>
<tr><td colspan='2'><br><input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_TurnOffautoloads')?>' onclick='IPOLSDEK_setups.autoloads.turnOff()' id='IPOLSDEK_DEAUTO'></td></tr>