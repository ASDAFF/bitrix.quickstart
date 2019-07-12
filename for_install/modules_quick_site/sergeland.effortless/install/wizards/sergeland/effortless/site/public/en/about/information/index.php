<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Information");
?>
<h2 class="title">Company details</h2>
<p>The page provides detailed information about the company, including Bank account details.</p>
<div class="table-responsive">
<table class="table">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Value</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>Full name in Russian</td>
			<td><i>The limited liability company &laquo;EFFORTLESS&raquo;</i></td>
		</tr>
		<tr>
			<td>2</td>
			<td>Short name</td>
			<td><i>LTD. &laquo;EFFORTLESS&raquo;</i></td>
		</tr>
		<tr>
			<td>3</td>
			<td>Legal address</td>
			<td><i>#ADDRESS#</i></td>
		</tr>
		<tr>
			<td>4</td>
			<td>Postal address</td>
			<td><i>#ADDRESS#</i></td>
		</tr>
		<tr>
			<td>5</td>
			<td>Primary state registration number (OGRN)</td>
			<td><i>1234567890</i></td>
		</tr>
		<tr>
			<td>6</td>
			<td>Identification number (TIN)</td>
			<td><i>1234567890</i></td>
		</tr>
		<tr>
			<td>7</td>
			<td>The reason code of tax registration (KPP)</td>
			<td><i>123456789</i></td>
		</tr>
		<tr>
			<td>8</td>
			<td>All-Russian classifier of enterprises and organizations (OKPO)</td>
			<td><i>12345678</i></td>
		</tr>
		<tr>
			<td>9</td>
			<td>The account</td>
			<td><i>40702810366000000000 in the Branch of Sberbank of Russia</i></td>
		</tr>
		<tr>
			<td>10</td>
			<td>Bank identification code (BIC)</td>
			<td><i>047003608</i></td>
		</tr>
		<tr>
			<td>11</td>
			<td>Correspondent account</td>
			<td><i>30101810300000000608</i></td>
		</tr>
		<tr>
			<td>12</td>
			<td>Email</td>
			<td><a href="mailto:#EMAIL#"><i>#EMAIL#</i></a></td>
		</tr>
		<tr>
			<td>13</td>
			<td>Phone</td>
			<td><i>#PHONE#</i></td>
		</tr>		
	</tbody>
</table>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>