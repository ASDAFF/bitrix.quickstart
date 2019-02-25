<style>
	.IPOLSDEK_badInput{
		background-color: #FFBEBE !important;
	}
</style>
<script>
	function IPOLSDEK_courierTimeCheck(start,end,day){
		if(!start)
			return {
				'error' : 'start',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_fillStart')?>',	
			}
		if(!end)
			return {
				'error' : 'end',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_fillEnd')?>',
			}
		start = start.split(':');
		start[0] = parseInt(start[0]);
		start[1] = parseInt(start[1]);
		if(start[0] < 9)
			return {
				'error' : 'start',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badStart')?>',
			}
		end   = end.split(':');
		end[0] = parseInt(end[0]);
		end[1] = parseInt(end[1]);
		if(end[0] > 18 || (end[0] == 18 && end[1]))
			return {
				'error' : 'end',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badEnd')?>',
			}
		if((end[0] - start[0]) * 60 + end[1] - start[1] < 180)
			return {
				'error' : 'both',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_badBoth')?>',
			}
		if(typeof(day) != 'undefined' && day == '<?=date('d.m.Y')?>' && start[0] > 14)
			return {
				'error' : 'start',
				'text'  : '<?=GetMessage('IPOLSDEK_JS_TIME_bad15')?>',
			}
		return true;
	}
</script>