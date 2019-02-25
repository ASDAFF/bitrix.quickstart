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
		margin-bottom: 10px;
	}

	.ipol_smallHeader{
		cursor: pointer;
		display:block;
		color:#2E569C;
	}

	.ipol_subFaq{
		margin-bottom:10px;
	}

	img{border: 1px dotted black;}
	.IPOLSDEK_optName{
		font-weight: bold;
	}
	.IPOLSDEK_warning{
		color:red;
	}
	.IPOLSDEK_converted{
		<?=($converted)?'':'display:none !important;'?>
	}
	.IPOLSDEK_notConverted{
		<?=($converted)?'display:none !important;':''?>
	}
	.IPOLSDEK_mp1{
		<?=($migrated)?'display:none !important;':''?>
	}
	.IPOLSDEK_mp2{
		<?=($migrated)?'':'display:none !important;'?>
	}
	.IPOLSDEK_importHasCity{
		<?=($ctId)?'':'display:none !important;'?>
	}
	.IPOLSDEK_importHasNotCity{
		<?=($ctId)?'display:none !important;':''?>
	}
</style>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_FAQ_HDR_SETUP')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('WTF')?>
	<?sdekOption::placeFAQ('HIW')?>
</td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_FAQ_HDR_ABOUT')?></td></tr> 
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('TURNON')?>
	<?sdekOption::placeFAQ('DELSYS')?>
	<?sdekOption::placeFAQ('SEND')?>
	<?sdekOption::placeFAQ('PELENG')?>
</td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_FAQ_HDR_WORK')?></td></tr>
<tr><td style="color:#555; " colspan="2">
	<?sdekOption::placeFAQ('PRINTFULL')?>
	<?sdekOption::placeFAQ('ACCOUNTS')?>
	<?sdekOption::placeFAQ('RBK')?>
	<?sdekOption::placeFAQ('PC')?>
	<?sdekOption::placeFAQ('SENDER')?>
	<?if(sdekOption::isConverted())
		sdekOption::placeFAQ('SHIPMENTS')?>
	<?sdekOption::placeFAQ('COMPONENT')?>
	<?sdekOption::placeFAQ('AUTOMATIZATION')?>
	<?sdekOption::placeFAQ('MULTISITE')?>
	<?sdekOption::placeFAQ('DELIVERYPRICE')?>
	<?sdekOption::placeFAQ('DIFFERENTSENDERS')?>
</td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_FAQ_HDR_HELP')?></td></tr>
<tr><td style="color:#555; " colspan="2">
	<?sdekOption::placeFAQ('CNTDOST')?>
	<?sdekOption::placeFAQ('CALLCOURIER')?>
	<?sdekOption::placeFAQ('ERRORS')?>
	<?sdekOption::placeFAQ('PROBLEMS')?>
	<?sdekOption::placeFAQ('OTHER')?>
</td></tr>