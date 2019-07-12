<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div class="mfeedback">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
}
?>





				    			                               
                               <form class="generic-form" action="<?=$APPLICATION->GetCurPage()?>" method="POST">
                                  <?=bitrix_sessid_post()?>
                                   <p>
                                       <input  type="text" size="30" placeholder="<?=GetMessage("MFT_NAME")?>" value="" name="user_name"/>
                                   </p>
                                   <p>
                                       <input  type="text" size="30" placeholder="<?=GetMessage("MFT_FNAME")?>" value="" name="user_name"/>
                                   </p>
                                   <p>
                                       <input  type="text" size="30"  placeholder="<?=GetMessage("MFT_PHONE")?>" value="" name="user_phone"/>
                                   </p>
                                   <p>
                                       <input  type="text" size="30" placeholder="<?=GetMessage("MFT_EMAIL")?>" value="" name="user_email"/>
                                   </p>
                                   <p>
                                       <textarea  cols="45" rows="10" placeholder="<?=GetMessage("MFT_MESSAGE")?>"  name="MESSAGE"></textarea>
                                   </p>
                                   <p>
                                       <input type="submit" name="submit"  class="btn btn-xxl" value="<?=GetMessage("MFT_SUBMIT")?>"/>
                                   </p>                                    
                               </form>

</div>