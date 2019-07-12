<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление");
?>
<h2>Заголовок H2</h2>
<p>Интернет-магазин — сайт, торгующий товарами в интернете. Позволяет пользователям сформировать заказ на покупку, выбрать способ оплаты и доставки заказа в сети Интернет.&nbsp;</p>
<blockquote>Отслеживание ведется с помощью методов веб-аналитики. Часто при оформлении заказа предусматривается возможность сообщить некоторые дополнительные пожелания от покупателя продавцу. 	</blockquote> 
<h3>Заголовок H3</h3>
<p><i>Однако, в этом случае следует быть осторожным, поскольку доказать неполучение товара электронным способом существенно сложнее, чем в случае физической доставки.</i></p>
<h4>Маркированный список H4</h4>
<ul>
	<li>В интернет-магазинах, рассчитанных на повторные покупки, также ведется отслеживание возвратов песетителя и история покупок.</li>
	<li>Кроме того, существуют сайты, в которых заказ принимается по телефону, электронной почте, Jabber или ICQ.</li>
</ul>
<h5>Нумерованный список H5</h5>
<ol>
	<li>В интернет-магазинах, рассчитанных на повторные покупки, также ведется отслеживание возвратов песетителя и история покупок.</li>
	<li>Кроме того, существуют сайты, в которых заказ принимается по телефону, электронной почте, Jabber или ICQ.</li>
</ol>
<hr class="long"/>
<h5>Таблица</h5>
<table class="colored_table">
	<thead>
		<tr>
			<td>#</td>
			<td>First Name</td>
			<td>Last Name</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>Tim</td>
			<td>Tors</td>
		</tr>
		<tr>
			<td>2</td>
			<td>Denis</td>
			<td>Loner</td>
		</tr>
	</tbody>
</table>
<hr class="long"/>

<div class="view_sale_block">
	<div class="count_d_block">
		<span class="active_to hidden">30.10.2017</span>
		<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
		<span class="countdown values"></span>
	</div>
	<div class="quantity_block">
		<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
		<div class="values">
			<span class="item">
				<span>10</span>
				<div class="text"><?=GetMessage("TITLE_QUANTITY");?></div>
			</span>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>