<?include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
foreach($_COOKIE as $i=>$val) if(preg_match("#order_#",$i)) $_COOKIE[preg_replace("#order_#","",$i)] = $val;
IncludeTemplateLangFile($template.'/header.php');
include_once($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

// Extract directory from filename
if(!$tf){
	$tf = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']);
	$tfs = explode('/',$tf);
	$tf = str_replace($tfs[sizeof($tfs)-1],'',$tf);
}

if($_POST['person_type']!='') $p_t = $_POST['person_type'];
else $p_t = $person_type['ID'];
if($p_t):
	$groups = CSaleOrderPropsGroup::GetList(Array("SORT"=>"ASC"),Array("PERSON_TYPE_ID"=>$p_t));
	while($group = $groups->GetNext()):
?>

		<!-- <div class="hr"></div> -->
		<?$props = CSaleOrderProps::GetList(Array("SORT"=>"ASC"),Array("PROPS_GROUP_ID"=>$group['ID']));
		while($prop = $props->GetNext()):
			
			if($prop['REQUIED']=='Y') $prop['NAME'] = '<b>'.$prop['NAME'].'</b>';?>
				<div class="hr"></div>
					<dl>
						<dt><?=$prop['NAME']?>:</dt>
				<?if($prop['TYPE']=='LOCATION'):
					
					if($_SESSION['location']=='' || (int) $_SESSION['location'] < 1){
						$adr = "http://ipgeobase.ru:7020/geo?ip=".$_SERVER['REMOTE_ADDR'];
						//print $adr;
						$curl = curl_init($adr);
						curl_setopt($curl, CURLOPT_TIMEOUT, 5);
						curl_setopt($curl, CURLOPT_HEADER, 1);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
						$file = curl_exec($curl);
						if(BX_UTF=='Y') $file = iconv('windows-1251','utf-8',$file);
						if(!preg_match("#Not found#",$file)){
							$city = trim(iarga::take("<city>","</city>",$file));
						}
						$place = CSaleLocation::GetList(Array("SORT"=>"ASC"),Array("LID" => LANGUAGE_ID,"CITY_NAME"=>$city))->GetNext();
						if(!$place) $place = CSaleLocation::GetList(Array("SORT"=>"ASC"),Array())->GetNext();
						$_COOKIE[$prop['CODE'].'_text'] = $place['CITY_NAME'];
						setcookie($prop['CODE'].'_text',$place['CITY_NAME'],"/",time()+3600*240);						
						$d_p = $place;
						$_SESSION['location'] = $d_p['ID'];
					}

					?>

                    <dd>
                        <div class="select-box location">
                            <select class="styled" name="prop_<?=$prop['ID']?>">
                            	<option><?=GetMessage("SELECT_THE_CITY")?></option>
                            	<?$places = CSaleLocation::GetList(Array("SORT"=>"ASC","CITY_NAME"=>"ASC"),Array("LID" => LANGUAGE_ID));
                            	while($place = $places->GetNext()):
                            		if($place['CITY_NAME']!=""):?>
                                		<option <?=$place['ID']==$_SESSION['location']?'selected':''?> value="<?=$place['ID']?>"><?=$place['CITY_NAME']?></option>
                                	<?endif;?>
                                <?endwhile?>
                            </select>
                        </div><!--.select-box-end-->
                        <p class="descr"><?=GetMessage("SELECT_CITY_TOOLTIP")?></p>
                    </dd>
                <?elseif($prop['TYPE']=="TEXTAREA"):?>
					<dd><textarea name="prop_<?=$prop['ID']?>" class="inp-text style-1"><?=$_COOKIE['prop_'.$prop['ID']]!=''?$_COOKIE['prop_'.$prop['ID']]:$user[$prop['CODE']]?></textarea></dd>
				<?else:?>
					<dd><input type="text" value="<?=$_COOKIE['prop_'.$prop['ID']]!=''?$_COOKIE['prop_'.$prop['ID']]:$user[$prop['CODE']]?>" name="prop_<?=$prop['ID']?>" class="inp-text style-1"></dd>
				<?endif;?>
			</dl>
		<?endwhile;?>
	<?endwhile;?>
	<div class="hr"></div>
	<dl>
		<dt><?=GetMessage("DISCOUNT_CODE")?></dt>
		<dd>
			<input type="text" value="<?=$_COOKIE["discount_code"]?>" name="discount_code" class="inp-text style-1 discount_code">
			<span class="discount_value"></span>

		</dd>
	</dl>
	<div class="delivery_ajax">
		<?include($_SERVER['DOCUMENT_ROOT'].$tf.'/delivery.php');?>
	</div>

	<?$pay_types = CSalePaySystem::GetList(Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("ACTIVE"=>"Y", "PERSON_TYPE_ID"=>$p_t));
	$payn = $pay_types->SelectedRowsCount();
	if($payn > 0):?>
		<div class="hr"></div>
		<dl>
			<dt><?=GetMessage("PAY_TYPE")?></dt>
			<dd>
				<?$i = 0;
				while($type = $pay_types->GetNext()):
					$i++;?>
					<label>
						<span class="input">
							<input class="styled" <?=($payn<=1 || $type['ID']==$_COOKIE['paytype'])?'checked':''?> type="radio" value="<?=$type['ID']?>" name="paytype"> 
						</span>
						<span class="description">
							<?if($type['PSA_LOGOTIP']!=""):?>
								<img src="<?=iarga::res($type['PSA_LOGOTIP'],400,100,1)?>"><br><?=($type['DESCRIPTION']!='')?''.$type['DESCRIPTION'].'':''?>
							<?else:?>
								<strong><?=$type['NAME']?></strong><?=($type['DESCRIPTION']!='')?''.$type['DESCRIPTION'].'':''?>
							<?endif;?>
						</span>
					</label>
					<?if($i<$pay_types->SelectedRowsCount()):?><div class="hr"></div><?endif;?>
				<?endwhile;?>
			</dd>
		</dl>
	<?endif;?>
	<div class="hr"></div>


<?endif;?>