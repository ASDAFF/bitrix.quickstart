<div class="window-wrap packet-actions">
	<form action="" name="PACKET_CANCEL" data-packet-id="<?=$this->data['PACKET_ID']?>">
		<div class="filter__checkbox">
			<input type="checkbox" name="cancel1" id="cancel1" class="filter__chboxhide">
			<label for="cancel1" class="filter__labl"></label>
			<span id="ch1" class="filter__textchbox">Неверный формат</span>
		</div>
		<div class="filter__checkbox">
			<input type="checkbox" name="cancel2" id="cancel2" class="filter__chboxhide">
			<label for="cancel2" class="filter__labl"></label>
			<span id="ch1" class="filter__textchbox">Верификация не пройдена</span>
		</div>
		<div class="filter__checkbox">
			<input type="checkbox" name="cancel3" id="cancel3" class="filter__chboxhide">
			<label for="cancel3" class="filter__labl"></label>
			<span id="ch1" class="filter__textchbox">Низкое качество верификации</span>
		</div>

		<div class="buttons-wrap">
			<a href="#" class="btn btn_header js-cancel-confirm">Ок</a>
			<a href="#" class="btn btn_header js-close-fb">Отмена</a>
		</div>
	</form>
</div>