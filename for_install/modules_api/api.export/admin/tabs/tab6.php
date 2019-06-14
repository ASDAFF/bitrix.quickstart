<?
/**
 * Bitrix vars
 *
 * @var array      $arFieldTitle
 * @var array      $profile
 * @var CAdminForm $tabControl
 *
 * @var CUser      $USER
 * @var CMain      $APPLICATION
 *
 */

use Bitrix\Main\Localization\Loc,
	 Api\Export\Tools;

Loc::loadMessages(__FILE__);
?>
<? $tabControl->BeginCustomField('', ''); ?>
	<tr>
		<td colspan="2">
			<p>
				<input type="button" class="adm-btn adm-btn-save api-export-cron-manual" value="<?=Loc::getMessage('AEAT_EXPORT_SUBMIT_TEXT')?>">
			</p>
			<style>
				.progress *, .progress ::after, .progress ::before {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
				.progress {
					display: -webkit-box;
					display: -ms-flexbox;
					display: flex;
					height: 1rem;
					overflow: hidden;
					font-size: .75rem;
					background-color: #e9ecef;
					border-radius: .25rem;
				}
				.progress-bar {
					display: -webkit-box;
					display: -ms-flexbox;
					display: flex;
					-webkit-box-orient: vertical;
					-webkit-box-direction: normal;
					-ms-flex-direction: column;
					flex-direction: column;
					-webkit-box-pack: center;
					-ms-flex-pack: center;
					justify-content: center;
					color: #fff;
					text-align: center;
					background-color: #007bff;
					transition: width .6s ease;
				}
				.progress-bar-striped {
					background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
					background-size: 1rem 1rem;
				}
			</style>
			<script>
				function apiExportNext() {

					console.log('Hi');
					var progress = $('#progress6 .progress-bar');

					$(progress).text('0%');
					$(progress).css('width','0%');

					BX.showWait('wait1');
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: '/bitrix/admin/api_export_manual.php',
						data: {
							'IS_AJAX': 1,
							'ID': <?=intval($profile['ID'])?>
						},
						//async: true,
						//cache: false,
						error: function (jqXHR, textStatus, errorThrown) {
							BX.closeWait('wait1');
							console.info('Error');
							console.info('statusCode', jqXHR.status);
							console.info('statusText', jqXHR.statusText);
							console.info('responseText', jqXHR.responseText);
						},
						success: function (data) {
							BX.closeWait('wait1');

							$(progress).text(data.percent + '%');
							$(progress).css('width',data.percent + '%');
							console.info(data);
						}
					});
				}

				$(function () {
					//tab6
					$('.api-export-cron-manual').on('click',function(e){
						e.preventDefault();
						apiExportNext();
					})
				});
			</script>
			<div id="progress6" class="progress">
				<div class="progress-bar progress-bar-striped" style="width: 0%"></div>
			</div>
		</td>
	</tr>
<? $tabControl->EndCustomField(''); ?>