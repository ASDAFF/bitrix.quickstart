<?
CModule::AddAutoloadClasses(
	'fairytale.tpic',
	array(
		'ft\CTPic' => 'classes/general/CTPic.php',
	)
);


if(!defined('FAIRYTALE_TPIC_NO_INIT')) {

	$arJSDescription = array(
		//'js' 	=> '/bitrix/js/fairytale.tpic/script.js',
		'css' 	=> '/bitrix/js/fairytale.tpic/css/style.css',
		//'rel' 	=> array('jquery'),
		'skip_core' => true,
	);

	CJSCore::RegisterExt('fairytale_tpic', $arJSDescription);
	CJSCore::Init(array('fairytale_tpic'));

	AddEventHandler('main', 'OnEndBufferContent', 'addPreloadScript');
	function addPreloadScript($content) {
		
		//global $APPLICATION;
		//if(stripos($APPLICATION->GetCurDir(), 'bitrix/admin') === false) {
		
			$content .= "
				<script>
					if(typeof(jQuery) == 'function') {
						$.fn.preload = function() {
							
							this.each(function(){
								
								if($(document).scrollTop() + $(window).height() < $(this).offset().top || $(this).offset().top + $(this).height() < $(document).scrollTop()) {
									return;
								}
								
								var thisImage = $(this);
								var thisImageBlock = thisImage.parent();
								var imageSrc = $(this).data('src');
								var imageCurrentSrc = $(this).attr('src'); 
								
								if(imageCurrentSrc == imageSrc || thisImageBlock.hasClass('ft-loading') || thisImageBlock.hasClass('ft-loading-error')) {
									return;
								}
								
								thisImageBlock.addClass('ft-loading');
								
								
								$('<img/>').load(function() {
									thisImage.attr('src', imageSrc);
									thisImageBlock.removeClass('ft-loading').addClass('ft-loaded');
								}).error(function() {
									thisImageBlock.removeClass('ft-loading').addClass('ft-loading-error');
								}).attr('src', imageSrc);
								
							});
							
						}

						$(function() {
							$('.ft-image-block img.ft-image').preload();
						});
						
						
						$(function() {
							$(document).scroll(function() {
								$('.ft-image-block img.ft-image').preload();
								
							});
						});
					}
				</script>
		   ";
	   
		//}
	   
	   return $content;
	}
}

?>