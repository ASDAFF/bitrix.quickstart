/**
 * Created by Vampiref92Home on 09.05.2017.
 */
const sFunction = 'function';
const sContinue = 'continue';
//Функция ajax
if (typeof DoAjax !== sFunction) {
	function DoAjax(url, data, dataTypeValue, successFunction) {
		// $.ajax({
		// 	type: "POST",
		// 	url: url,
		// 	data: data,
		// 	dataType: dataTypeValue,
		// 	success: returnFunction
		// });

		BX.ajax({
			url: url,
			data: data,
			method: 'POST',
			dataType: dataTypeValue,
			timeout: 300,
			async: true,
			processData: true,
			// scriptsRunFirst: true,
			// emulateOnload: true,
			start: true,
			cache: false,
			onsuccess: successFunction
		});
	}
}
if (typeof successNormalize !== sFunction) {
	function successStepScript(res) {
		if (!!res.status && res.status === sContinue) {
			$('.messages').append('<p>' + Date() + ' - Завершен шаг ' + res.currentStep.action + '№' + res
					.currentStep.index + ' пошагвоой обработки "'+window['name']+'"</p>');
			DoAjax(window['path'], {'ajax': 'Y'}, 'json', successNormalize)
		}
		else {
			$('.messages').append('<p>' + Date() + ' - Пошаговая обработка "'+window['name']+'" завершена</p>');
		}
	}
}

if (typeof startStepScript !== sFunction){
	function startStepScript(){
		window['path'] = $( this ).attr( 'data-path' );
		window['name'] = $( this ).text();
		$( '.messages' ).empty().append( '<p>' + Date() + ' - Начало пошаговой обработки "'+window['name']+'"...</p>' );
		DoAjax( window['path'], {'ajax': 'Y'}, 'json', successStepScript )
	}
}
$(function () {
	$('.fullNormalize').on('click', startStepScript);
	$('.partialNormalize').on('click', startStepScript);
});

// var scriptBySteps = function(){
// 	startStepScript:function(){
//
// 	}
// };