<div class="packet-actions">
	<a data-user-id="<?=$this->data['USER_ID']?>" class="js-change-info btn-bordo">Изменить</a>
	<a data-user-id="<?=$this->data['USER_ID']?>" data-status="<?=$this->data['STATUS']?>" class="js-block-user btn-bordo"><?=( $this->data['STATUS'] == 'Y' ) ? 'Разблокировать' : 'Заблокировать'?></a>
</div>