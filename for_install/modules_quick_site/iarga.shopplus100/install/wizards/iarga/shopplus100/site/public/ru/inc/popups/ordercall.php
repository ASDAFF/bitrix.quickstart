<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
// ������?>
<div class="lightbox" id="feedback" style="display: block;">
	<form action="/inc/ajax/ordercall.php" class="uniform unipopup">
		<p class="title">�������� ������:</p>
		<dl>
			<dt>���� ���:</dt>
			<dd><input value="" name="name" type="text" class="inp-text"></dd>
		</dl>
		<dl>
			<dt>����� ��������:</dt>
			<dd><input value="" name="phone" type="text" class="inp-text"></dd>
		</dl>
		<a href="#" class="bt_gray submit">��������</a>
		<p class="error"></p>
	</form>
	<a href="#" class="close"></a>
</div>