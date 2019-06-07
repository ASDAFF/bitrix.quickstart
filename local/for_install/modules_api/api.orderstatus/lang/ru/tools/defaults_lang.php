<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['AOS_DEFAULTS_SALE_LOGO'] = '';
$MESS['AOS_DEFAULTS_SALE_PHONE']    = '+7 (999) 999-99-99';
$MESS['AOS_DEFAULTS_SALE_ADDRESS']  = 'г. Москва, Ленинский проспект, дом 555А, офис 999';
$MESS['AOS_DEFAULTS_MAIL_HEADER']   = '<table style="max-width: 640px; width:100%; margin:0 auto;padding:0;font-family:Arial, Helvetica, sans-serif" cellpadding="0" cellspacing="0" border="0">
	<tbody>
	<tr>
		<td style="border:none;padding:5px 0;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tbody>
				<tr>
					<td border="0" align="left" valign="middle">
						<a rel="noopener noreferrer" target="_blank">#SALE_LOGO#</a>
					</td>
					<td border="0" align="right" valign="middle">
						<p style="font-size:18px;line-height:18px;margin:0 0 0 0;padding:0 0 10px 0;">
							<a rel="noopener noreferrer" href="tel:#SALE_PHONE#" target="_blank"  style="color: #000;">#SALE_PHONE#</a>
						</p>
						<p style="font-size:18px;line-height:18px;margin:0 0 0 0;padding:0 0 10px 0;">
							<a href="mailto:#SALE_EMAIL#" target="_blank" style="color: #000;">#SALE_EMAIL#</a>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td border="0">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<td style="padding:10px;border:1px solid #dadada;">';
$MESS['AOS_DEFAULTS_MAIL_CONTENT']  = '<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tbody>
<tr>
	<td>#WORK_AREA#</td>
</tr>
</tbody>
</table>';
$MESS['AOS_DEFAULTS_MAIL_FOOTER']   = '					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table  width="100%" border="0" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<td style="border:0;color:#2f2f2f;text-align:center;line-height:30px;font-size:11px;">
						© 2016 <a rel="noopener noreferrer" href="#SALE_URL#" target="_blank">#SALE_NAME#</a>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>';
$MESS['AOS_DEFAULTS_EVENT_TYPE'] = '';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER'] = '#BLOCK_HEADER#<br>
#BLOCK_BUYER#<br>
#BLOCK_SHIPMENT#<br>
#BLOCK_PAYMENT#<br>
#BLOCK_BASKET#<br>
#BLOCK_FOOTER#';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_TYPE'] = 'html';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_HEADER'] = '<p>
	 #ORDER_USER#, здравствуйте!<br>
 <br>
	 Ваш заказ № #ORDER_ID# от #ORDER_DATE# принят.<br>
 <br>
	 Вы можете следить за выполнением своего заказа, войдя в Ваш персональный раздел на сайте.<br>
 <br>
	 Обратите внимание, что для входа в этот раздел Вам необходимо будет ввести логин и пароль пользователя сайта.<br>
 <br>
	 Для того, чтобы аннулировать заказ, воспользуйтесь функцией отмены заказа, которая доступна в Вашем персональном разделе сайта.<br>
 <br>
	 Благодарим за покупку!
</p>';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_HEADER_TYPE'] = 'html';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_FOOTER'] = '<div style="font-size:21px;color:#64219e;text-align:center;">
	 Благодарим Вас за заказ и желаем приятных покупок в дальнейшем!
</div>
 <br>';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_FOOTER_TYPE'] = 'html';
$MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_SUBJECT'] = '#SITE_NAME# - Ваш заказ № #ORDER_ID# принят';