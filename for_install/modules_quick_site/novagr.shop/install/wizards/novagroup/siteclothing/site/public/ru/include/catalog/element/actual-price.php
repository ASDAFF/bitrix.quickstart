<div class="actual-price">
    <a href="#" class="addToBasket"><span class="icon-arrow-down"></span></a>
    <span id="sum" class="discount"></span>
	
	<div id="notifyme" class="accordion" style="display:none;">
		<div class="not-available">
			<div class="extremum-slide" id="box" style="display: none;">
				<div class="wrap-extremum-slide">
					<button class="close" type="button">&times;</button>
					<h3><?=GetMessage("NOTIFY_TT_H3")?></h3>
					<?=GetMessage("NOTIFY_TT_TX")?>
				</div>
				<div id="triangle-down"></div>
			</div>
			
			<div id="signed" class="accordion-heading" style="display:none;">
				<div id="notifyme-response" class="wr"><?=GetMessage("NOTIFY_YOU_ARE_SUBSCRIBE")?></div>
				<span id="unsubsc" class="btnclick btn not-avi"><?=GetMessage("NOTIFY_UNSUBSCRIBE_BTN")?></span>
			</div>			
			<div id="notifyme-form" style="display:none;">
<?
	if(CUser::IsAuthorized())
	{
?>
				<form id="notifyForm" method="post" name="notifyForm" action="/local/components/novagr.shop/catalog.list/templates/.default/ajax.php">
					<input type="hidden" value="" id="notify_elem_id" name="elemId" >
					<input type="hidden" value="Y" name="ajax" >
					<input type="hidden" value="productSubsribe" name="action" >
					<?=bitrix_sessid_post()?>
					<input type="hidden" id="notify_user_mail" autocomplete="on" value="<?=CUser::GetEmail();?>" name="user_mail">
					<input type="submit" class="btn bt3 not-extremum" value="<?=GetMessage("NOTIFY_SUBSCRIBE_BTN")?>">
					<!--a href="#collapseTwo" data-parent="#accordion2" data-toggle="collapse" class="btn bt3 not-extremum">Уведомить о появлении</a-->
				</form>
<?
	}else{
?>
				<div class="accordion-heading">
					<a href="#collapseTwo" data-parent="#accordion2" data-toggle="collapse" class="btn bt3 not-extremum"><?=GetMessage("NOTIFY_SUBSCRIBE_BTN")?></a>
				</div>
				
				<div class="accordion-body collapse" id="collapseTwo">
					<div class="accordion-inner">
						<form id="notifyForm" method="post" name="notifyForm" action="/local/components/novagr.shop/catalog.list/templates/.default/ajax.php">
							<input type="hidden" value="" id="notify_elem_id" name="elemId" >
							<input type="hidden" value="Y" name="ajax" >
							<input type="hidden" value="productSubsribe" name="action" >
							<?=bitrix_sessid_post()?>
							<div class="notice">
								<div class="login" id="notifyEmail">
									<div class=""><?=GetMessage('NOTIFY_EMAIL');?>:<span class="starrequired">*</span></div>
									<div class="value">
										<input type="text" id="notify_user_mail" autocomplete="on" value="" maxlength="50" name="user_mail">
										<input type="submit" class="btn" value="<?=GetMessage('NOTIFY_SEND_BTN');?>">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
<?
	}
?>
			</div>

		</div>

	</div>
	
	<span id="old-price" class="old-price"></span>
    <div class="clear"></div>
    <div id="buy-popup" style="display: none;">
        <div class="message-demo" id="message-demo"></div>
    </div>

    <div class="basket-tab">
        <a href="<?=SITE_DIR?>cabinet/cart/"><i class="icon-arrow-basket"></i></a>
    </div>
    <a href="#" class="btn bt3 addToBasket" id="btnsel"><?=GetMessage('ADD_TO_CART');?></a>
    <div class="clear"></div>
</div>