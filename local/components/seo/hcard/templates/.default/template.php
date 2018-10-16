<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="vcard">
 <div>
   <?if(!empty($arParams['CATEGORY'])):?>
       <span class="category"><?=$arParams['CATEGORY']?></span>
   <?endif?>
   <?if(!empty($arParams['ORG_NAME'])):?>
       <span class="fn org"><?=$arParams['ORG_NAME']?></span>
   <?endif?>
 </div>
 <div class="adr">
   <?if(!empty($arParams['LOCALITY'])):?>
       <span class="locality"><?=$arParams['LOCALITY']?></span>,
   <?endif?>
   <?if(!empty($arParams['ADRES'])):?>
      <span class="street-address"><?=$arParams['ADRES']?></span>
   <?endif?>
 </div>
 <div><?=GetMessage('EPIR_TAMPLATE_TEL')?>
     <?foreach($arParams['TEL'] as $key => $tel):?><?
            if(empty($tel))
                continue;
         ?><?if($key != 0):?>, <?endif?><span class="tel"><?=trim($tel)?></span><?endforeach?>
 </div>
 <div><?=GetMessage('EPIR_TAMPLATE_MAIL')?>
    <?foreach($arParams['EMAILS'] as $key => $email):?><?
            if(empty($email))
                continue;
         ?><?if($key != 0):?>, <?endif?><a class="email" href="mailto:<?=trim($email)?>"><?=trim($email)?></a><?endforeach?>
 </div>
 <div><?=GetMessage('EPIR_TAMPLATE_WORK')?>
     <?if(!empty($arParams['WORKHOURS'])):?>
        <span class="workhours"><?=$arParams['WORKHOURS']?></span>
     <?endif?>
        <span class="url">
            <span class="value-title" title="<?=$arParams['URL']?>"> </span>
        </span>
 </div>
</div>



