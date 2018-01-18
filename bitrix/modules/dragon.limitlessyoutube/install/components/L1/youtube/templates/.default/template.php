<?

	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	if ( preg_match( '/(http:\/\/|https:\/\/)(m\.)?youtu.be\/([a-zA-Z0-9-_]+)/', $arParams[ 'URL' ], $m ) )
	{

		// https://youtu.be/SToTWUwo87Q
		$ID = $m[ count( $m ) - 1 ];

	} elseif ( preg_match( '/(http:\/\/|https:\/\/)(www\.)?(m\.)?youtube\.(com|ru)\/watch\?v=([-a-zA-Z0-9-_]+)/', $arParams[ 'URL' ], $m ) ) {

		// https://www.youtube.com/watch?v=gs7-VXCya2s
		// https://www.youtube.com/watch?v=KkZedspTHeY&feature=youtu.be
		$ID = $m[ count( $m ) - 1 ];

	} elseif ( preg_match( '/([-a-zA-Z0-9-_]+)/', $arParams[ 'URL' ], $m ) ) {

		// gs7-VXCya2s
		$ID = $m[ count( $m ) - 1 ];
	}

	//$GLOBALS['APPLICATION']->AddHeadString('<script src="https://www.youtube.com/iframe_api"></script>');
	//$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="' . $componentPath . '/.default/script.js"></script>');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('style.css');
	$GLOBALS['APPLICATION']->AddHeadScript('script.js');

	$data['data-name'] = $ID;
	$data['data-width'] = $arParams['W'];
	$data['data-height'] = $arParams['H'];
	$data['data-autoplay'] = $arParams['Autoplay'];
	$data['data-controls'] = $arParams['Control'];
	$data['data-loop'] = $arParams['LOOP'];
	$data['data-iv_load_policy'] = $arParams['IV'];
	$data['data-cc_load_policy'] = $arParams['CC'];
	$data['data-fs'] = $arParams['FULL'];
	$data['data-img'] = $arParams['IMG'];
	$data['data-logo'] = ( $arParams['LOGO'] == 0 ? 1 : 0 );
	$data['data-origin'] = '//' . $_SERVER['SERVER_NAME'];

	$style = ' style="width: ' . $arParams['W'] . ( $arParams['W'] > 0 ? 'px;' : null ) . ' height:' . $arParams['H'] . ( $arParams['H'] > 0 ? 'px;' : null ) . '" ';
?>
<div class="l1-youtube-player"<? echo $style; foreach( $data as $k => $v ) echo ' ' . $k . '="' . $v . '"'; ?>>
	<div class="l1-youtube-screen"<?= $style ?>></div>
	<div class="l1-youtube-back"<?= $style ?>></div>
	<div class="l1-youtube-preload">Loading player...</div>
	<? if ( trim( $data['data-img'] ) != '' || $data['data-cc_load_policy'] == 0 ) : ?>
		<div class="l1-youtube-background"<?= $style ?>></div>
		<div class="l1-youtube-play"></div>
	<? endif; ?>
</div>