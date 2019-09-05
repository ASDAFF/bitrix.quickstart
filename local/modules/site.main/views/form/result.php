<div class="form-result-new">
	<div class="form">
		<?
		if($result->success)
		{
			if($result->data['result'] == 'addok') {
				ShowNote('Сообщение отправлено. Мы свяжемся с Вами в ближайшее время.');
			} else {
				ShowError('Не удалось сохранить результаты заполнения формы.');
			}
		}
		else
		{
			ShowError($result->message);
		}
		?>
		<div class="footer">
			<button onclick="$.fancybox.close()">Закрыть</button>
		</div>
	</div>
</div>