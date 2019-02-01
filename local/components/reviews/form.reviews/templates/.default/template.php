<form name="order-form" method="POST" id="ajax-form" class="form form_grey form_fullwidth" action=" ">
	<div class="alert-success" style="display:none">Сообщение отправлено.</div>
	<div class="alert-error" style="display:none">Возникла ошибка.</div>
	<input type="hidden" name="EMAIL_EVENT" value="<?=$arParams["EMAIL_EVENT"]?>"></input>

	<?foreach ($arResult['FIELDS'] as $code => $field):?>
		<?if(!isset($field['TYPE']) || strtolower($field['TYPE']) == 'input'):?>
			<input 
				type="text" 
				name="<?=$field['NAME']?>" 
				id="<?=$field['NAME']?>" 
				placeholder="<?=$field['PLACEHOLDER']?>" value="<?=$field['VALUE']?>" 
				class="form__input form__input_color_grey form__input_width_full <?=($field['REQUEST'] == "Y")?"form__input_required":"";?>">
		<?elseif(strtolower($field['TYPE']) == 'hidden'):?>
			<input 
				type="hidden" 
				name="<?=$field['NAME']?>" 
				id="<?=$field['NAME']?>" 
				placeholder="<?=$field['PLACEHOLDER']?>" 
				value="<?=$field['VALUE']?>" 
				class="form__input form__input_color_grey form__input_width_full <?=($field['REQUEST'] == "Y")?"form__input_required":"";?>">
		<?elseif(strtolower($field['TYPE']) == 'select'):?>
			<select id="<?=$field['NAME']?>" class="form__select" name="<?=$field['NAME']?>">
				<?foreach ($field['VALUE'] as $id => $value):?>
					<option <?=($field['DEFAULT']==$id)?"selected":""?> value="<?=$id?>"><?=$value?></option>
				<?endforeach?>
			</select>
		<?elseif(strtolower($field['TYPE']) == 'textarea'):?>
			<textarea
				name="<?=$field['NAME']?>" 
				id="<?=$field['NAME']?>" 
				placeholder="<?=$field['PLACEHOLDER']?>" 
				class="form__textarea form__input_required"><?=$field['VALUE']?></textarea>
		<?elseif(strtolower($field['TYPE']) == 'rating'):?>
			<div class="span12 rating-container rating-container_set">
				<div class="rating-stars rating-stars_active" data-name="<?=$field['NAME']?>" data-rate="<?=$field['VALUE']?>">
					<div class="rating-stars__star rating-stars__star_active"></div>
					<div class="rating-stars__star rating-stars__star_active"></div>
					<div class="rating-stars__star rating-stars__star_active"></div>
					<div class="rating-stars__star rating-stars__star_active"></div>
					<div class="rating-stars__star rating-stars__star_active"></div>
				</div>
			</div>
		<?endif?>

	<?endforeach?>
	<input type="submit" name="submit" class="form__button form__button_color_red form__button_width_full" value="БЫСТРЫЙ ЗАКАЗ">
</form>