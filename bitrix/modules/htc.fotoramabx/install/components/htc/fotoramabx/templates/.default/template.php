<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
$parameters = $arResult['PARAMETERS'];
?>
<div class="fotorama" 
     data-width="100%" 
     data-ratio="<? echo $parameters['RATIO']; ?>" 
     data-allowfullscreen="<? echo $parameters['ALLOW_FULLSCREEN']; ?>" 
     data-nav="<? echo $parameters['NAVIGATION_STYLE']; ?>"
	<? if ($parameters['SHUFFLE']): ?>
		data-shuffle="true"
	<? endif; ?>
	<? if ($parameters['CHANGE_HASH']): ?>
		data-hash="true"
	<? endif; ?>
	<? if ($parameters['LOOP']): ?>
		data-loop="true"
	<? endif; ?>
	<? if ($parameters['NAVIGATION_ON_TOP']): ?>
		data-navposition="top"
	<? endif; ?>
	<? if ($parameters['AUTOPLAY']): ?>
		data-autoplay="<? echo $parameters['AUTOPLAY']; ?>"
	<? endif; ?>
	<? if ($parameters['SHOW_ARROWS'] === false): ?>
		data-arrows="false"
	<? endif; ?>
	<? if ($parameters['TRANSITION_EFFECT']): ?>
		data-transition="<? echo $parameters['TRANSITION_EFFECT']; ?>"
	<? endif; ?>>
	<? foreach ($arResult['IMAGES'] as $key => $image): ?>
		<a href="<? echo $image['PATH']; ?>" id="fotorama-<? echo $key; ?>" 
			<?if ($parameters['SHOW_CAPTION'] && !empty($image['DESCRIPTION'])):?>
				data-caption="<? echo $image['DESCRIPTION']; ?>"
			<? endif; ?>
			<? if ($parameters['LAZY_LOAD']): ?>
				data-thumb="<? echo $image['THUMB_PATH']; ?>"
			<? else: ?>
				><img src="<? echo $image['THUMB_PATH']; ?>" alt="<? echo $image['DESCRIPTION']; ?>"
			<? endif; ?>
				></a>
	<? endforeach; ?>
</div>
