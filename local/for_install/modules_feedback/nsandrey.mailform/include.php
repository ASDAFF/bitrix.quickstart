<?
function custom_mail($to, $subject, $message, $addHeaders, $addParams)
{
	global $UPLOADED_FILES;

	$LF = CEvent::GetMailEOL();
	
	$boundary = md5(uniqid(time()));
	$b = '--'.$boundary;
	$br = $boundary.'--';
	
	$ct_pos = strpos($addHeaders, 'Content-Type:');
	$messT = substr($addHeaders, $ct_pos);
	$addHeaders = substr($addHeaders, 0, $ct_pos);

	if (preg_match("#charset=(.+)\n|\n\r#i", $messT, $matches) && !empty($matches[1]))
	{
		$charset = $matches[1];
	}
	else
	{
		$charset = 'Windows-1251';
	}

	$addHeaders .= "Subject: ".$subject."\n";
	$addHeaders .= "MIME-Version: 1.0\n";
	$addHeaders .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
	$addHeaders .= $b."\n";
	$addHeaders .= "Content-Type: text/plain; charset=".$charset."\n";
	$addHeaders .= "Content-Transfer-Encoding: base64\n";
	$addHeaders .= "\n".base64_encode($message)."\n";

	foreach($UPLOADED_FILES as $arFile)
	{
		$full_path = $_SERVER['DOCUMENT_ROOT'].'/'.trim($arFile['PATH'], '/');
		if(file_exists($full_path) && is_file($full_path))
		{
			if(!strlen($arFile['NAME']))
			{
				$arPathInfo = pathinfo($full_path);
				$arFile['NAME'] = $arPathInfo['basename'];
			}

			$enc_name = CAllEvent::EncodeMimeString($arFile['NAME'], $charset);

			$addHeaders .= 	$b."\n";
			$addHeaders .= 	"Content-Type: application/octet-stream; name=\"".$enc_name."\"\n";
			$addHeaders .= 	"Content-Disposition: attachment; filename=\"".$enc_name."\"\n";
			$addHeaders .= 	"Content-Transfer-Encoding: base64\n";
			$addHeaders .= 	"\n".chunk_split(base64_encode(file_get_contents($full_path)), 72)."\n";
		}
	}

	return mail($to, $subject, '', $addHeaders, $addParams);
}

function unifGetField($APPLICATION, $key, $val, $arParams)
{
	$required = in_array($key, $arParams['REQUIRED_FIELDS']);

	switch($arParams[$key])
	{
		case 'FILE':
			$html = '<input type="file" name="FIELDS['.$key.']">';
			break;
		case 'EMAIL':
			$html = '<input type="email" name="FIELDS['.$key.']" value="'.$val.'">';
			break;
		case 'STRING':
			$html = '<input type="text" name="FIELDS['.$key.']" value="'.$val.'">';
			break;
		case 'INT':
			$html = '<input type="number" name="FIELDS['.$key.']" value="'.$val.'">';
			break;
		case 'CHECKBOX':
			$html = '<input type="checkbox" name="FIELDS['.$key.']" value="Y"'.($val == 'Y' ? ' checked' : '').'>';
			break;
		case 'TEXTAREA':
			$html = '<textarea name="FIELDS['.$key.']">'.$val.'</textarea>';
			break;
		case 'DATE_TIME_INTERVAL':
			ob_start();
			$APPLICATION->IncludeComponent("bitrix:main.calendar","",Array(
					"SHOW_INPUT" => "Y",
					"INPUT_NAME" => 'FIELDS['.$key.'][0]',
					"INPUT_NAME_FINISH" => 'FIELDS['.$key.'][1]',
					"INPUT_VALUE" => $val[0],
					"INPUT_VALUE_FINISH" => $val[1], 
					"SHOW_TIME" => "Y"
				)
			);
			$html = ob_get_clean();
			break;
		case 'DATE_TIME':
			ob_start();
			$APPLICATION->IncludeComponent("bitrix:main.calendar","",Array(
					"SHOW_INPUT" => "Y",
					"INPUT_NAME" => 'FIELDS['.$key.']',
					"INPUT_VALUE" => $val,
					"SHOW_TIME" => "Y"
				)
			);
			$html = ob_get_clean();
			break;
		case 'SELECT':
			$html = '<select name="FIELDS['.$key.']">';
			
			foreach($arParams[$key.'_SELECT_VALUE'] as $s_key => $s_value)
			{
				if ($s_value != '')
				{
					$html .= '<option value="' . $s_key . '"' . ($key == $s_key ? ' selected' : '') . '>' . $s_value . '</option>';
				}
			}
			
			$html .= '</select>';
			break;
		case 'RADIO':
			$html = '';

			foreach($arParams[$key.'_SELECT_VALUE'] as $s_key => $s_value)
			{
				if ($s_value != '')
				{
					$html .= '<div><input type="radio" name="FIELDS['.$key.']" value="' . $s_key . '"' . ($key == $s_key ? ' checked' : '') . '> ' . $s_value . '</div>';
				}
			}
			break;
		case 'MULTISELECT':
			$html = '<select name="FIELDS['.$key.'][]" multiple="multiple">';

			foreach($arParams[$key.'_SELECT_VALUE'] as $s_key => $s_value)
			{
				if ($s_value != '')
				{
					$html .= '<option value="' . $s_key . '"' . (in_array($s_key, $val) ? ' selected' : '') . '>' . $s_value . '</option>';
				}
			}

			$html .= '</select>';
			break;
		case 'MULTISELECT_CHECKBOXES':
			$html = '';

			foreach($arParams[$key.'_SELECT_VALUE'] as $s_key => $s_value)
			{
				if ($s_value != '')
				{
					$html .= '<div><input type="checkbox" name="FIELDS['.$key.'][]" value="' . $s_key . '"' . (in_array($s_key, $val) ? ' checked' : '') . '> ' . $s_value . '</div>';
				}
			}
			break;
		default:
			$html = '<input type="hidden" name="FIELDS['.$key.']" value="'.$arParams[$key.'_HIDDEN_VALUE'].'">';
			break;
	}

	$arReturn = array(
		'NAME' => $key,
		'VALUE' => $val,
		'TYPE' => $arParams[$key],
		'REQUIRED' => $required,
		'HTML' => $html
	);

	$rsEvents = GetModuleEvents("nsandrey.mailform", "OnAfterGetField");

	while ($arEvent = $rsEvents->Fetch())
	{
		ExecuteModuleEventEx($arEvent, array(&$arReturn));
	}

	return $arReturn;
}

function unifCheckField($fieldType, $fieldValue, $additional)
{
	$error = 'NONE';

	switch($fieldType)
	{
		case 'FILE':
			$f_err = CFile::CheckFile($fieldValue, 0, false, $additional);

			if (!isset($fieldValue['error']) || $fieldValue['error'] != 0)
			{
				$error = 'EMPTY';
			}
			else if($f_err != '' || $fieldValue['error'] != 0)
			{
				$error = 'WRONG';
			}
			break;

		case 'EMAIL':
			if (empty($fieldValue))
			{
				$error = 'EMPTY';
			}
			else if (!check_email($fieldValue))
			{
				$error = 'WRONG';
			}
			break;

		case 'CHECKBOX':
			if ($fieldValue != 'Y')
			{
				$error = 'EMPTY';
			}
			break;

		case 'DATE_TIME_INTERVAL':
			if ($fieldValue[0] == '' || $fieldValue[1] == '')
			{
				$error = 'EMPTY';
			}
			else if (MakeTimeStamp($fieldValue[0]) >= MakeTimeStamp($fieldValue[1]))
			{
				$error = 'WRONG';
			}
			break;

		case 'DATE_TIME':
			if (MakeTimeStamp($fieldValue) < 1)
			{
				$error = 'EMPTY';
			}
			break;

		case 'MULTISELECT_CHECKBOXES':
		case 'MULTISELECT':
			if (sizeof($fieldValue) < 1 || !is_array($fieldValue))
			{
				$error = 'EMPTY';
			}
			break;

		default:
			if (empty($fieldValue))
			{
				$error = 'EMPTY';
			}
			break;
	}

	$rsEvents = GetModuleEvents("nsandrey.mailform", "OnAfterCheckField");

	while ($arEvent = $rsEvents->Fetch())
	{
		ExecuteModuleEventEx($arEvent, array($fieldType, $fieldValue, &$error));
	}

	return $error;
}
?>