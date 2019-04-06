<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
IncludeModuleLangFile(__FILE__);
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/acmee.maketornado/option.php");


if(isset($_POST['save']))
{
	if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['url']) && !empty($_POST['url']))
	{
		if(check_email($_POST['email'],true))
		{
			

			$params = http_build_query(array(
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'url' => $_POST['url'], 
				'phone' => $_POST['phone']
				));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"http://hmead.maketornado.com/api/register/bitrix/");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec ($ch);
			curl_close ($ch);


			$data = json_decode($output, true);

			if ($data['status'] == 'success')
			{

			    $f = fopen($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/acmee.maketornado/option.php","w");
			    fwrite($f,'<?$option=array("maketornadoID"=>"'.$data['id'].'",);?>');
			    fclose($f);		 
			    $option['maketornadoID'] = $data['id'];   
			} 
			else 
			{
				$error = $data['message'];
			}
		}
		else
		{
			$error = GetMessage("ERROR_EMAIL");
		}	
	}
	else
	{
		if(!isset($_POST['email']) || empty($_POST['email']))
		{
			$error = GetMessage("ERROR_EMAIL2");
		}
		else
		{
			$error = GetMessage("ERROR_URL");
		}
	}
}
elseif(isset($_POST['editiD']))
{
	if(is_numeric($_POST['id']))
	{
	    $f = fopen($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/acmee.maketornado/option.php","w");
	    fwrite($f,'<?$option=array("maketornadoID"=>"'.$_POST['id'].'",);?>');
	    fclose($f);
	    $option['maketornadoID'] = $_POST['id'];
	}
	else
	{
		$error = GetMessage("ERROR_ID");
	}
}
?>
<? if($option['maketornadoID'] <= 0): ?>
	<form name="captcha_form" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>">
		<?=bitrix_sessid_post()?>
		<?if(isset($error) && !empty($error)):?>
			<div style="color:red;">
			<?=$error?>
			</div>
		<?endif;?>

		<table style="margin-bottom:30px;">
		  	<tr>
		    	<td><span class="required">*</span><?=GetMessage("SERVER_NAME")?>:</td>
		    	<td><input type="text" name="url" value="<?=$_SERVER["SERVER_NAME"]?>" size="30" maxlength="100"></td>
		  	</tr>	
		  	<tr>
		    	<td><?=GetMessage("NAME")?>:</td>
		    	<td><input type="text" name="name" value="<?=$option['EMAIL']?>" size="30" maxlength="100"></td>
		  	</tr>		
		  	<tr>
		    	<td><span class="required">*</span><?=GetMessage("EMAIL")?>:</td>
		    	<td><input type="text" name="email" value="" size="30" maxlength="100"></td>
		  	</tr>
		  	<tr>
		    	<td><?=GetMessage("PHONE")?>:</td>
		    	<td><input  type="text" name="phone" value="" size="30" maxlength="100"></td>
		  	</tr>
		 </table>
		
		<input class="adm-btn-save" type="submit" name="save" value="<?=GetMessage("SAVE")?>" />&nbsp;
		<input class="mybutton" type="button" value="<?=GetMessage("CANCEL")?>"  onClick="window.location.href = '/bitrix/admin/'" />
	</form>
<?else:?>
	<?if(isset($error) && !empty($error)):?>
		<div style="color:red;">
		<?=$error?>
		</div>
	<?endif;?>
	<form name="captcha_form" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>">
			<table style="margin-bottom:30px;">
			  	<tr>
			    	<td><span class="required">*</span><?=GetMessage("TU_ID")?>:</td>
			    	<td><input type="text" name="id" value="<?=$option['maketornadoID']?>" size="30" maxlength="100"></td>
			  	</tr>	
			 </table>
			<input class="adm-btn-save" type="submit" name="editiD" value="<?=GetMessage("SAVE")?>" />&nbsp;
			<input class="mybutton" type="button" value="<?=GetMessage("CANCEL")?>"  onClick="window.location.href = '/bitrix/admin/'" />
	</form>
<? endif; ?>