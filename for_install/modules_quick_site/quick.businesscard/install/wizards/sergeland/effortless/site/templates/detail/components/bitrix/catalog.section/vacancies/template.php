<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["~ITEMS"])):

	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

foreach($arResult["~ITEMS"] as $key => $arSection):?>
	<h2 class="title"><?=$arSection["NAME"]?></h2>
	<?if(!empty($arSection["DESCRIPTION"])):?>
		<div class="block text-muted"><?=$arSection["DESCRIPTION"]?></div>
	<?endif?>
	<div class="panel-group panel-transparent" id="accordion-faq-<?=$arSection["IBLOCK_SECTION_ID"]?>">
	<?foreach($arSection["ITEMS"] as $cell=>$arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	?>
	<div class="panel panel-default" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion-faq-<?=$arSection["IBLOCK_SECTION_ID"]?>" href="#collapse<?=$arItem["ID"]?>" class="accordion-toggle <?if(empty($arItem["PROPERTIES"]["IN"]["VALUE"])):?>collapsed<?endif?>">
					<?if(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?><i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?> pr-10"></i><?endif?> <?=$arItem["NAME"]?>
				</a>
			</h4>
		</div>
		<div id="collapse<?=$arItem["ID"]?>" class="panel-collapse collapse <?if(!empty($arItem["PROPERTIES"]["IN"]["VALUE"])):?>in<?endif?>">
			<div class="panel-body">
				<?=$arItem["PREVIEW_TEXT"]?>
				<?if(!empty($arItem["PROPERTIES"]["SHOW_FORM"]["VALUE"])):?>
				<script>
				/*----------- VACANCIES Form -----------*/
				jQuery(function(){
					var context = '#collapse<?=$arItem['ID']?>',
						form = $('form', context);

					form.submit(function() {
						$('#form-loading-vacancies-<?=$arItem['ID']?>', context).fadeIn();
						$('#error-vacancies-<?=$arItem['ID']?>, #success-vacancies-<?=$arItem['ID']?>, #beforesend-vacancies-<?=$arItem['ID']?>', context).hide();	
						if(validate()){ 
							submission();  
						} else{
							$('#form-loading-vacancies-<?=$arItem['ID']?>', context).hide();
							$('#beforesend-vacancies-<?=$arItem['ID']?>, #results-vacancies-<?=$arItem['ID']?>', context).fadeIn();	  
						};
						$('input, select, textarea, button', form).blur();
						return false;
					});

					function validate() {
						var errors = [];
						$('.req', form).each(function() {
							if(!$(this).val()){
								errors.push(1);
								$(this).addClass('error');
							} else $(this).removeClass('error');
						});
						if(errors.length === 0)
							 return true;
						else return false;
					};
				  
					function submission(){
						form.ajaxSubmit({
								type: 'POST',  
								url: form.attr('action'),
								dataType: 'json',
								data: form.serialize(),
								success: function(data){
									$('#form-loading-vacancies-<?=$arItem['ID']?>', context).hide();
									$('input, textarea', form).removeClass('error');
									if(data.MESSAGE.ERROR < 1){
										$('#results-vacancies-<?=$arItem['ID']?>, #success-vacancies-<?=$arItem['ID']?>', context).fadeIn();
										$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
									}else $('#results-vacancies-<?=$arItem['ID']?>, #error-vacancies-<?=$arItem['ID']?>', context).hide().fadeIn();
								},
								error: function(data){
									$('#form-loading-vacancies-<?=$arItem['ID']?>', context).hide();
									$('#results-vacancies-<?=$arItem['ID']?>, #error-vacancies-<?=$arItem['ID']?>', context).hide().fadeIn();
								}
						});	
						return false;
					};
				});
				</script>
				<div class="row">
					<div class="col-md-12 mt-20">
						<div id="results-vacancies-<?=$arItem['ID']?>">
							<div class="alert alert-danger" id="beforesend-vacancies-<?=$arItem['ID']?>">
								<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_BEFORESEND")?>
							</div>
							<div class="alert alert-danger" id="error-vacancies-<?=$arItem['ID']?>">
								<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_ERROR")?>
							</div>
							<div class="alert alert-success" id="success-vacancies-<?=$arItem['ID']?>">
								<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_SUCCESS")?>
							</div>
						</div>
						<img src="<?=SITE_DIR?>images/loading.gif" alt="Loading" id="form-loading-vacancies-<?=$arItem['ID']?>" class="pull-right mb-10" />
						<div class="clearfix"></div>
						<form name="VACANCIES" action="<?=SITE_DIR?>include/" method="POST" enctype="multipart/form-data" role="form">
							<input type="hidden" name="VACANCIES[SITE_ID]" value="<?=SITE_ID?>"/>
							<input type="hidden" name="VACANCIES[TITLE]" value="<?=$arItem["NAME"]?>"/>							
							<div class="form-group has-feedback">
								<input type="text" name="VACANCIES[NAME]" class="form-control req" placeholder="<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_NAME")?>">
								<i class="fa fa-user form-control-feedback"></i>
							</div>
							<div class="form-group has-feedback">
								<input type="tel" name="VACANCIES[PHONE]" class="form-control req" placeholder="<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}">
								<i class="fa fa-phone form-control-feedback"></i>
							</div>
							<div class="form-group has-feedback">
								<input type="email" name="VACANCIES[EMAIL]" class="form-control" placeholder="<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_EMAIL")?>">
								<i class="fa fa-envelope form-control-feedback"></i>
							</div>
							<div class="form-group">
								<input type="file" name="FILE">
							</div>
							<div class="form-group has-feedback">
								<textarea name="VACANCIES[COMMENT]" class="form-control" rows="4" placeholder="<?=GetMessage("QUICK_EFFORTLESS_VACANCIES_COMMENT")?>"></textarea>
								<i class="fa fa-pencil form-control-feedback"></i>
							</div>
							<button type="submit" class="btn btn-sm btn-default pull-right"><i class="fa fa-check pr-5"></i><?=GetMessage("QUICK_EFFORTLESS_VACANCIES_SEND")?></button>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
				<?endif?>
			</div>
		</div>
	</div>
	<?endforeach?>
	</div><?if(++$key < count($arResult["~ITEMS"])):?><br><?endif?>
<?endforeach?>
<?endif?>