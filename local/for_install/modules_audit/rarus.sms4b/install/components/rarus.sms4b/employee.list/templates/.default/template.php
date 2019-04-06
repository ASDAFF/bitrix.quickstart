<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<h1><?=GetMessage('EMPLOYEE')?></h1>

<?if (count($arResult["USERS"]) == 0):?>
	<?ShowError(GetMessage('EMPLOYEE_NOT_FOUND'))?>
<?else:?>
	<div style="float:left;">
		<table border="0" class = "usersList">
			<thead>
				<tr>
					<td class=ogg></td>
					<td class=ogg><b><?=GetMessage('FIO')."/".GetMessage('TELEPHONE')."/".GetMessage('DEPARTMENT')?></b></td>
					<td class=ogg style="text-align:center"><b>+</b></td>
				</tr>
			</thead>
			<tbody>
			<?$i=0;?>
			<?foreach ($arResult["USERS"] as $arIndex):?>
			    <?
    				
    				
    				if (strlen($arIndex["LAST_NAME"].$arIndex["NAME"]) < 1)
    				{
    					$showedName = $arIndex["LOGIN"];
    				}
    				else
    				{
    					$showedName = $arIndex["LAST_NAME"].' '.$arIndex["NAME"];
    				}
    				$department = array_pop($arIndex["UF_DEPARTMENT"]);
    				if ($department == '') 
    					$department = GetMessage('NOT_SET');
			    ?>
				<tr>
					<td style="text-align:center">
						<?if ($arIndex["PERSONAL_PHOTO"]):?>
							<img src = '<?=$arIndex["PERSONAL_PHOTO"]?>' border="0" width="55" height="55" />
						<?else:?>
							<div>
								<img src = "<?=$templateFolder?>/images/questionMark.jpg" />
							</div>
						<?endif;?>
					</td>
					<td <?=$i % 2 != 0? "class=ogg" : "class=nogg" ?> >
						<div style="padding-bottom:5px"><?=$showedName?></div>
						<div style="padding-bottom:5px">
						<?$defUserProperty = COption::GetOptionString('rarus.sms4b', 'user_property_phone', '', SITE_ID);?>
						<?if (!empty($arIndex[$defUserProperty])):?>
							<?=$arIndex[$defUserProperty];?>
						<?else:?>
							<?=GetMessage('NOT_SET');?>
						<?endif;?>
						</div>
						<div style="padding-bottom:5px"><?=$department?></div>
					</td>
					<td align="center"><button onclick="addNumber({phone: '<?=$arIndex["PERSONAL_MOBILE"]?>', name: '<?=$showedName?>', department: '<?=$department?>'}, 'dest')" value="add" class="button"></button></td>
				</tr>
				<?$i++;?>
			<?endforeach;?>
			</tbody>
		</table>
		<div><?=$arResult['USERS_NAV'];?></div>
	</div>
<?endif;?>

