<?	if (empty($_POST)) return false;
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	if (!CModule::IncludeModule("lightweb.components")) return;
	$CLWOption = new CLWOption();
	
	//Обработка сохранения оций для платежной системы
	if ($_POST['TYPE']=='PAYMENT'){
		if (CLWTools::ArrayCheckElement(array('LOGIN','PASSWORD','PASSWORD_2'), $_POST)===true){
			$SetOption=false;
			$SetOption=$CLWOption->Set('RK_BOC', array('LOGIN'=>$_POST['LOGIN'],'PASSWORD'=>$_POST['PASSWORD'],'PASSWORD_2'=>$_POST['PASSWORD_2']));
			if ($SetOption){
				$arResult=array('RESULT'=>'Y', 'ERROR'=>'');
			} else {
				$arResult=array('RESULT'=>'N','ERROR'=>'Write error');	
			}
		} else {
			$arResult=array('RESULT'=>'N','ERROR'=>'Not enough options');	
		}
	}
	
	//Обработка сохранения данных для SMS
	if ($_POST['TYPE']=='SMS'){
		if (!empty($_POST['SMS_API_KEY'])){
			$SetOption=false;
			$SetOption=$CLWOption->Set('SMSRU_API_KEY', $_POST['SMS_API_KEY']);
			if ($SetOption){
				$arResult=array('RESULT'=>'Y', 'ERROR'=>'');
			} else {
				$arResult=array('RESULT'=>'N','ERROR'=>'Write error');	
			}
		} else {
			$arResult=array('RESULT'=>'N','ERROR'=>'Not enough options');	
		}
	}
	
	echo json_encode($arResult);
?>