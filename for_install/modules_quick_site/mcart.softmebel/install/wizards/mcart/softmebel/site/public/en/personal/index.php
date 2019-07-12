<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Personal Section");
?><p>You can check the status of your cart, progress of your orders, and view or change your personal data in the your personal cabinet. You can also subscribe to updates or news threads. </p>
							
<h2>Personal information</h2>
<ul>
	<li><a href="profile/">Change personal registration data</a></li>
	<li><a href="profile/?change_password=yes">Change password</a></li>
	<li><a href="profile/?forgot_password=yes">Forget password?</a></li>
</ul>

<h2>Orders</h2>
<ul>
	<li><a href="order/">View order status</a></li>
	<li><a href="cart/">View contents of cart</a></li>
	<li><a href="cart/">View reserved items</a></li>

	<li><a href="order/?filter_history=Y">View order history</a></li>
</ul>

<h2>Subscribe</h2>
<ul>
	<li><a href="subscribe/">Edit subscriptions</a></li>
</ul>								<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>