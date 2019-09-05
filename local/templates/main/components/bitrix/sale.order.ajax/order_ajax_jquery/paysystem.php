<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? $data = json_decode($_POST["DATA"], true); ?>
<? $paysystems = $data["PAY_SYSTEM"]; ?>
<? $payfromaccount = $data["PAY_FROM_ACCOUNT"]; ?>

<div class="order-form">
	<h4>Способ оплаты</h4>
	<ul class="paysystems">
		<? if($payfromaccount == "Y"): ?>
			<li>
				<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
				<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y">
				<label for="PAY_CURRENT_ACCOUNT">
					<b><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></b>
				</label>
				<?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")?> 
				<b><?=$arResult["CURRENT_BUDGET_FORMATED"]?></b>, <?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?>
			</li>
		<? endif; ?>
		<? foreach($paysystems as $key => $paysystem): ?>
			<li>
				<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $paysystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $paysystem["ID"] ?>"/>
				<div class="radio-column">
					<label for="ID_PAY_SYSTEM_ID_<?= $paysystem["ID"] ?>">
						<strong><?= $paysystem["PSA_NAME"] ?></strong>
						<?if (strlen($paysystem["DESCRIPTION"]) > 0):?>
							<p><?=$paysystem["DESCRIPTION"]?></p>
						<? endif; ?>
					</label>
				</div>
			</li>
		<? endforeach; ?>
	</ul>
</div>