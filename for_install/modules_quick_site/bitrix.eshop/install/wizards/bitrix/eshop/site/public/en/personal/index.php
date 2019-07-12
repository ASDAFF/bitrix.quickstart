<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Personal Section");
?>
<h2>Subscribe</h2>
<ul>
	<li><a href="subscribe/">Edit subscriptions</a></li>
</ul>								

<div class="bx_page">
	<p>You can check the status of your cart, progress of your orders, and view or change your personal data in the your personal cabinet. You can also subscribe to updates or news threads. </p>
	<div>
		<h2>Personal information</h2>
		<ul>
			<li><a href="profile/">Change personal registration data</a></li>
		</ul>
	</div>
	<div>
		<h2>Orders</h2>
		<ul>
			<li><a href="order/">View order status</a></li>
			<li><a href="cart/">View contents of cart</a></li>
			<li><a href="order/?filter_history=Y">View order history</a></li>
		</ul>
	</div>
	<div>
		<h2>Subscribe</h2>
		<ul>
			<li><a href="subscribe/">Edit subscriptions</a></li>
		</ul>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>