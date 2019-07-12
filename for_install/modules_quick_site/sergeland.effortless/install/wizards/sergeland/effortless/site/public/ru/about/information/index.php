<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Реквизиты");
?>
<h2 class="title">Реквизиты компании</h2>
<p>На странице представлена полная информация о компании, включая реквизиты банковского счета.</p>
<div class="table-responsive">
<table class="table">
	<thead>
		<tr>
			<th>#</th>
			<th>Название</th>
			<th>Значение</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>Полное наименование на русском языке</td>
			<td><i>Общество с ограниченной ответственностью &laquo;EFFORTLESS&raquo;</i></td>
		</tr>
		<tr>
			<td>2</td>
			<td>Сокращенное наименование</td>
			<td><i>ООО &laquo;EFFORTLESS&raquo;</i></td>
		</tr>
		<tr>
			<td>3</td>
			<td>Юридический адрес</td>
			<td><i>#ADDRESS#</i></td>
		</tr>
		<tr>
			<td>4</td>
			<td>Почтовый адрес</td>
			<td><i>#ADDRESS#</i></td>
		</tr>
		<tr>
			<td>5</td>
			<td>Основной государственный регистрационный номер (ОГРН)</td>
			<td><i>1234567890</i></td>
		</tr>
		<tr>
			<td>6</td>
			<td>Идентификационный номер (ИНН)</td>
			<td><i>1234567890</i></td>
		</tr>
		<tr>
			<td>7</td>
			<td>Код причины постановки на налоговый учет (КПП)</td>
			<td><i>123456789</i></td>
		</tr>
		<tr>
			<td>8</td>
			<td>Общероссийский классификатор предприятий и организаций (ОКПО)</td>
			<td><i>12345678</i></td>
		</tr>
		<tr>
			<td>9</td>
			<td>Расчетный счет</td>
			<td><i>40702810366000000000 в Отделении Сбербанка России</i></td>
		</tr>
		<tr>
			<td>10</td>
			<td>Банковский идентификационный код (БИК)</td>
			<td><i>047003608</i></td>
		</tr>
		<tr>
			<td>11</td>
			<td>Корреспондентский счет</td>
			<td><i>30101810300000000608</i></td>
		</tr>
		<tr>
			<td>12</td>
			<td>Email</td>
			<td><a href="mailto:#EMAIL#"><i>#EMAIL#</i></a></td>
		</tr>
		<tr>
			<td>13</td>
			<td>Телефон</td>
			<td><i>#PHONE#</i></td>
		</tr>		
	</tbody>
</table>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>