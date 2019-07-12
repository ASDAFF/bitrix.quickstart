<?php echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?".">"; ?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->SetPageProperty("description",$arResult["NAME"]);
$APPLICATION->SetPageProperty("keywords",$arResult["NAME"]);
?>

<?
function crop_str($string, $limit)
{

    $substring_limited = substr($string,0, $limit);
    return substr($substring_limited, 0, strrpos($substring_limited, ' ' ));
}
?>


<script>
$(document).ready(function(e) {
            $('.imgbox2 a').lightBox();
			$('.new-art-buy').click(function () {
				$('#loginbox').fadeIn('slow');
				 $('body,html').animate({
				    scrollTop:600
			       }, 1000);			
			
			});
			$('.close').click(function() {
				$('#loginbox').fadeOut('slow');
			});
			
			$('#loginbox').submit(function(e) 
			  {
				  
if($('#fphone').val()=='' 
|| 
$('#fphone').val()=='<?=GetMessage("JS_INPUT_FIELD")?>')
				  {
					  e.preventDefault();
					  alert("<?=GetMessage("JS_NO_PHONE")?>");
					  $('#fphone').css({'color':'red'});
		$('#fphone').val("<?=GetMessage("JS_INPUT_FIELD")?>");
					
					  
				  }
				  else
				  {
					  alert("<?=GetMessage("JS_ZAAVKA_PRINATA")?>");	   
				  }
				  
				  
				  $('#fphone').focus(
				   function()
				   {
					 $('#fphone').val('');
					   
				   }
				  );
				 
			  }
			
			);	
        });
		
</script>


<?
if(!empty($_POST['fname']) or  !empty($_POST['fphone']) or !empty($_POST['femnail'])):
    ?>
<script>
    $(document).ready(function(e) {

                $('body,html').animate({
                    scrollTop:600
                }, 1000);
            }
    )

</script>


<?
endif
?>



<div class="top"><h2 class="f14 f">	 <?=$arResult["NAME"]?>
</h2></div>
<div class="c33">
    <div class="mybord"></div>

    <div class="new-art">
        <div class="imgbox2">
            <a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>">
                <img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"  width="150" height="120" border="0"  >
            </a>


        </div>


        <div class="new-art-info">
            <p>
                <?=strip_tags(
                crop_str($arResult["PREVIEW_TEXT"],120)

            )?>

            </p>
            <div class="news-art-price">


                <b>

                    <?=GetMessage("MYCOMPANY_REMO_CENA")?></b>



                <?=$arResult["DISPLAY_PROPERTIES"]["PRICE"]["DISPLAY_VALUE"]?> <?=GetMessage("MYCOMPANY_REMO_RUB")?>.

            </div>
            <a href="#" class="new-art-buy"></a><br><br><br>
            <?if(!empty($arResult['fstatus'])):?>

            <h4 class="fstatus"> <?=$arResult['fstatus']?></h4>
            <?endif?>

            <?if(!empty($arResult['estatus'])):?>
            <h4 class="estatus"> <?=$arResult['estatus']?></h4>
            <?endif?>

            <span><b><?=GetMessage("MYCOMPANY_REMO_OPISANIE")?></b>
                <?=$arResult["DETAIL_TEXT"]?>

                                        </span>
        </div>
    </div>
</div>


<div id="loginbox" class="loginbox">
    <form id="loginform" action="" class="form" method="post">
        <div class="headform">
            <h1><?=GetMessage("MYCOMPANY_REMO_ZAKAZ")?></h1>
            <a class="close"></a>
        </div>
        <div class="dconter">
            <div class="inputs">
                <label><?=GetMessage("MYCOMPANY_REMO_VASE_FIO")?></label>
                <input type="text" class="inp" value="" name="fname"  id="fname"/>
                <label><?=GetMessage("MYCOMPANY_REMO_NOMER_TELEFONA")?></label>
                <input type="text" class="inp" value="" name="fphone" id="fphone"/>
                <label><?=GetMessage("MYCOMPANY_REMO_VAS")?> e-mail:</label>
                <input type="text" class="inp" value="" name="femail" id="femail"/>
                <input type="hidden" class="inp" value="flag" name="flag"/>



            </div>
            <div class="box">
                <button value="<?=GetMessage("MYCOMPANY_REMO_OTPRAVITQ_SOOBSENIE")?>" name="" class="sender"><?=GetMessage("MYCOMPANY_REMO_OTPRAVITQ_SOOBSENIE")?></button>
            </div>
        </div>
    </form>
</div>
