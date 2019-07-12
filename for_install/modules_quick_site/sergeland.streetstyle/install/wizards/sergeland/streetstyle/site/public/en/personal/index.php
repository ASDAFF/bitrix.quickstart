<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Personal section");
?>
<?$APPLICATION->IncludeComponent("sergeland:sale.auth.hash", ".default", array(), false);?>
<?if($USER->IsAuthorized()):?>
<div class="personal-page-nav">
	<p>You can check the status of your cart, progress of your orders, and view or change your personal data in the your personal cabinet. You can also subscribe to updates or news threads.</p>
	<div>
		<h2>Personal information</h2>
		<ul class="lsnn">
			<li><a href="profile/">Change personal registration data</a></li>
		</ul>
	</div>
	<div>
		<h2>Orders</h2>
		<ul class="lsnn">
			<li><a href="order/">View order status</a></li>
			<li><a href="order/?filter_history=Y&filter_status=F">View order history</a></li>
		</ul>
	</div>
	<div>
		<h2>Subscribe</h2>
		<ul class="lsnn">
			<li><a href="subscribe/">Edit subscriptions</a></li>
		</ul>
	</div>	
	<div>
		<h2>Logout</h2>
		<ul class="lsnn">
			<li><a href="logout/">End the session authorization</a></li>
		</ul>
	</div>	
</div>
<?else:?>
	<?$APPLICATION->AuthForm("")?>
<?endif?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>