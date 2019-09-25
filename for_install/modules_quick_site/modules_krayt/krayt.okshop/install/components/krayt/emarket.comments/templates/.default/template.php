<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php"); 

CJSCore::RegisterExt('lang_js_com', array(
    'lang' => $this->__folder."/lang/".LANGUAGE_ID.'/template.php'
));
CJSCore::Init(array('lang_js_com'));
//create Captcha
$cpt = new CCaptcha(); 
$captchaPass = COption::GetOptionString("main", "captcha_password", ""); 
if(strlen($captchaPass) <= 0) 
{ 
	$captchaPass = randString(10); 
	COption::SetOptionString("main", "captcha_password", $captchaPass); 
} 
$cpt->SetCodeCrypt($captchaPass);

//def
$ec_comm_bool = false;

function ec_get_stars($rating)
{
	$rating = intval($rating);
	for($i=1; $i<=10; $i++)
	{
		if(($i == $rating) && ($i%2)) 
		{
			echo '<div class="star half"></div>';
			$i++;
			continue;
		}
		if(!($i%2))
		{
			if($i < $rating)
			{
				echo '<div class="star"></div>';
			}
			elseif($i == $rating)
			{
				echo '<div class="star"></div>';
			}
			elseif($i > $rating)
			{
				echo '<div class="star empty"></div>';
			}
		}
	}
}
?>

<input id="ec_this_folder" type="hidden" value="<?=$this->GetFolder();?>">
<input id="ec_this_id" type="hidden" value="<?=$arResult['ID'];?>">
<input id="ec_this_iblock" type="hidden" value="<?=$arParams['IBLOCK_ID']?>">
<input id="ec_this_hlblock_pc" type="hidden" value="<?=$arParams['HLBLOCK_PROP_CODE']?>">

<div class="emarket-comments">
	<!--COMMENTS-HEAD-->
	<div class="ec-head">
		<h2><?=GetMessage('EMARKET_COMMENTS_TITLE')?> (<?=count($arResult['COMMENTS']);?>)</h2>
		<div class="ec-rating">
			<?=ec_get_stars($arResult['RATING'])?> 
			<span><?=((int)$arResult['RATING']/2)?></span>
		</div>
		<a id="ec_comment_show" class="ec-button ec-button-1" href="#"><?=GetMessage('EMARKET_COMMENTS_BT_SHOW')?></a>
	</div>
	<!--COMMENTS-ADD-->
	<div class="ec-comments-add">
		<div class="ec-left">
			<table>
				<tr>
					<td><span><?=GetMessage('EMARKET_COMMENTS_MESS_ADV')?></span></td>
					<td><textarea class="ec-input-param" name="UF_MESS_ADV"></textarea></td>
				</tr>
				<tr>
					<td><span><?=GetMessage('EMARKET_COMMENTS_MESS_LIM')?></span></td>
					<td><textarea class="ec-input-param" name="UF_MESS_LIM"></textarea></td>
				</tr>
				<tr>
					<td><span><?=GetMessage('EMARKET_COMMENTS_MESS_COMM')?></span></td>
					<td><textarea class="ec-input-param" name="UF_MESS_COMM"></textarea></td>
				</tr>
				<tr>
					<td><span><?=GetMessage('EMARKET_COMMENTS_USER_NAME')?>:</span></td>
					<td><input class="ec-input-param" name="UF_NAME" type="text" value="<?=$USER->GetLogin();?>"></td>
				</tr>
				<tr>
					<td>
						<div class="ec-comments-captcha_title">
							<span style="padding:0"><?=GetMessage('EMARKET_COMMENTS_TITLE_CAPTCHA')?></span>
						</div>
					</td>
					<td>
						<div class="ec-comments-captcha">
							<a href="#" id="ec_reload_captcha"></a>
							<input type="hidden" id="captcha_code" name="captcha_code" value="<?=htmlspecialchars($cpt->GetCodeCrypt());?>">
							<input type="text" class="ec-input-param" id="captcha_word" name="captcha_word"><img src="/bitrix/tools/captcha.php?captcha_code=<?=htmlspecialchars($cpt->GetCodeCrypt());?>">
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="ec-right">
			<div class="ec-term_of_Use">
				<span><?=GetMessage('EMARKET_COMMENTS_TERM_OF_USE')?>:</span>
				<select class="ec-input-param" name="UF_TERM_OF_USE">
					<option value="<?=GetMessage('EMARKET_COMMENTS_TIME_1')?>"><?=GetMessage('EMARKET_COMMENTS_TIME_1')?></option>
					<option value="<?=GetMessage('EMARKET_COMMENTS_TIME_2')?>"><?=GetMessage('EMARKET_COMMENTS_TIME_2')?></option>
					<option value="<?=GetMessage('EMARKET_COMMENTS_TIME_3')?>"><?=GetMessage('EMARKET_COMMENTS_TIME_3')?></option>
				</select>
			</div>

			<div class="ec-rating">	
				<div class="ec-criteria-full">
					<span><?=GetMessage('EMARKET_COMMENTS_RATING_OVERALL')?>:</span>
					<input type="hidden" class="ec-input-param" name="UF_RATING" value='0'>
					<input type="hidden" class="ec-input-param" name="UF_RATING_LIST" value='0'>
					<div class="ec-criteria-rating">
						<a class="star empty"></a>
						<a class="star empty"></a>
						<a class="star empty"></a>
						<a class="star empty"></a>
						<a class="star empty"></a>
					</div>
					<div class="ec-criteria-val">0</div>
				</div>
				<?foreach($arResult['CRITERIA'] as $arCriteria) {?>
					<div class="ec-criteria"
						 data-code="<?=$arCriteria['UF_XML_ID']?>" 
						 data-id="<?=$arCriteria['ID']?>">
						<span><?=$arCriteria['UF_NAME']?>:</span>
						<div class="ec-criteria-rating">
							<a class="star empty"></a>
							<a class="star empty"></a>
							<a class="star empty"></a>
							<a class="star empty"></a>
							<a class="star empty"></a>
						</div>
						<div class="ec-criteria-val">0</div>
					</div>
				<?}?>
			</div>	
			<a id="ec_comment_add" class="ec-button" href="#"><?=GetMessage('EMARKET_COMMENTS_BT_ADD')?></a>			
			<a id="ec_comment_cancel" class="ec-button ec-button-2" href="#"><?=GetMessage('EMARKET_COMMENTS_BT_CANCEL')?></a>			
		</div>		
	</div>
	<!--COMMENTS-LIST-->
	<div class="ec-comments-list clear">
		<?
		$arCriteriaAllRait = array();
		foreach($arResult['COMMENTS'] as $arComment)
		{
			if($arComment['UF_PRODUCT_ID'] != $arResult['ID'])
				continue;
			
			if($arParams['EMARKET_COMMENT_PREMODERATION'] == 'Y')
			{
				if($arComment['UF_ACTIVE'])
					$ec_comm_count = true;
				else
					continue 1;
			}
			else
				$ec_comm_count = true;
			
			$date = explode('_', $arComment['UF_XML_ID']);
			if(!$date[1])
            {
                $date[1] = time()-rand(100,1000);
            }
            $date_format = date("d.m.Y", $date[1]);
			?>
			<div class="ec-comment">
				<div class="author">
					<b><?if($arComment['UF_NAME']) echo $arComment['UF_NAME']; else echo GetMessage('EMARKET_COMMENTS_ANONYM');?></b>
                    <?if($date_format):?>
					<time datetime="<?=$date_format;?>">
						<?
						// FORMAT_DATETIME - константа с форматом времени сайта
						$arDate = ParseDateTime($date_format, FORMAT_DATETIME);
						echo ((int)$arDate["DD"]).' '.ToLower(GetMessage("MONTH_".intval($arDate["MM"])."_S")).$arDate["YYYY"];
						?>
					</time>
					<?endif;?>
					<?if($arComment['UF_TERM_OF_USE']) {?>
						<span>
							<?=GetMessage('EMARKET_COMMENTS_TERM_OF_USE')?>: 
							<?=$arComment['UF_TERM_OF_USE']?>
						</span>
					<?}?>
					
					<div class="ec-rating" style="padding: 25px 0 10px;">
						<?=ec_get_stars($arComment['UF_RATING'])?>
						<span><?=((int)$arComment['UF_RATING']/2)?></span>
					</div>
					
					<?if($arComment['UF_RATING_LIST']) {?>
						<a class="ec-rating-list-show" href="#" ><?=GetMessage('EMARKET_COMMENTS_RATING_SHOW')?></a>
						<div class="ec-rating-list clear">
							<?
							
							$decode = json_decode($arComment['UF_RATING_LIST'], true);
							foreach($decode as $raiting_code=>$rating_val)
							{
								foreach($arResult['CRITERIA'] as $arCriteria) 
								{
									if($arCriteria['UF_XML_ID'] == $raiting_code)
									{
									$arCriteriaAllRait[$arCriteria['UF_NAME']] += (int)$rating_val;
									?><span><?=$arCriteria['UF_NAME']?></span><?
									}
								}
								?><div class="ec-rating"><?=ec_get_stars($rating_val*2)?></div><?
							}
							?>
						</div>
					<?}?>
				</div>
				
				<div class="text">
					<div class="msg">
						<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_ADV')?></b><span><?=$arComment['UF_MESS_ADV']?></span></p>
						<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_LIM')?></b><span><?=$arComment['UF_MESS_LIM']?></span></p>
						<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_COMM')?></b><span><?=$arComment['UF_MESS_COMM']?></span></p>
					</div>
					<div class="control">
						<a href="#" class="complaint-link" data-name="UF_SOCIAL_COMPLAINT" data-id="<?=$arComment['ID']?>">
							<?
							if( intval($APPLICATION->get_cookie('ec_UF_SOCIAL_COMPLAINT_'.$arComment['ID'])) ||
								intval($_SESSION['ec_UF_SOCIAL_COMPLAINT_'.$arComment['ID']]))
								echo GetMessage('EMARKET_COMMENTS_BT_COMPLAIN_SEND');
							else
								echo GetMessage('EMARKET_COMMENTS_BT_COMPLAIN');
							?>
						</a>
						<a  href="#" 
							class="bt-link unlike <?if( intval($APPLICATION->get_cookie('ec_'.$arComment['ID'])) || intval($_SESSION['ec_'.$arComment['ID']])) echo 'deactive'?>" 
							data-name="UF_SOCIAL_UNLIKE" 
							data-id="<?=$arComment['ID']?>">
							<?=$arComment['UF_SOCIAL_UNLIKE']?>	
						</a>						
						<a  href="#" 
							class="bt-link like <?if( intval($APPLICATION->get_cookie('ec_'.$arComment['ID'])) || intval($_SESSION['ec_'.$arComment['ID']])) echo 'deactive'?>" 
							data-name="UF_SOCIAL_LIKE" 
							data-id="<?=$arComment['ID']?>">
							<?=$arComment['UF_SOCIAL_LIKE']?>
						</a>
					</div>
				</div>
			</div>
		<?
		}
		
		if(!$ec_comm_count)
			echo '<span class="no-comment">'.GetMessage('EMARKET_COMMENTS_NOCOMMENT').'</span>';
			
		//select best comment
		usort($arResult['COMMENTS'], "cmp");
		function cmp($a, $b)
		{
		   return ($a['UF_SOCIAL_LIKE'] >= $b['UF_SOCIAL_LIKE']) ? -1 : 1;
		}
		$bestComment = $arResult['COMMENTS'][0];
		?>
	</div>
</div>
<?$this->SetViewTarget("better_review", 52);?>
	
	<h2><?=GetMessage('EMARKET_COMMENTS_REV')?> (<?=count($arResult['COMMENTS']);?>)</h2>
	<div class="item_info_section">
		<div class="bx_item_rating big">
			<b><?=GetMessage('EMARKET_COMMENTS_RATING_OVERALL')?>:</b>
			<?=ec_get_stars($arResult['RATING'])?> 
			<span><?=((int)$arResult['RATING']/2)?></span>
		</div>
		
		<?foreach($arCriteriaAllRait as $name=>$rating_val) {?>
			<div class="bx_item_rating">
				<b class="small"><?=$name?>:</b>
				<?=ec_get_stars($rating_val*2/count($arResult['COMMENTS']))?> 
				<span><?=round($rating_val/count($arResult['COMMENTS']), 2)?></span>
			</div>
		<?}?>
	
		<?if(!empty($bestComment)) {?>
		<div class="ec-best-comment">
			<div class="author">
            
				<b><?if($bestComment['UF_NAME']) echo $bestComment['UF_NAME']; else echo GetMessage('EMARKET_COMMENTS_ANONYM');?></b>
				<time datetime="<?=$date_format;?>">
					<?echo ((int)$arDate["DD"]).' '.ToLower(GetMessage("MONTH_".intval($arDate["MM"])."_S")).$arDate["YYYY"];?>
				</time>
				<?if($bestComment['UF_TERM_OF_USE']) {?>
					<span>
						<?=GetMessage('EMARKET_COMMENTS_TERM_OF_USE')?>: 
						<?=$bestComment['UF_TERM_OF_USE']?>
					</span>
				<?}?>
			</div>
			<div class="msg">
				<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_ADV')?></b><span><?=$bestComment['UF_MESS_ADV']?></span></p>
				<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_LIM')?></b><span><?=$bestComment['UF_MESS_LIM']?></span></p>
				<p><b><?=GetMessage('EMARKET_COMMENTS_MESS_COMM')?></b><span><?=$bestComment['UF_MESS_COMM']?></span></p>
			</div>
		</div>
		<?}?>
	</div>
	
<?$this->EndViewTarget();?>