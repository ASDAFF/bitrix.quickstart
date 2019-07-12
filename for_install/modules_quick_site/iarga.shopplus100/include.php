<?
IncludeModuleLangFile(__FILE__);
// small thing to shange view type
if($_GET['view_type']!=''){
	if($_GET['view_type']=='list') $_SESSION['view_type'] = 'list';
	else $_SESSION['view_type'] = '.default';
	LocalRedirect($_SERVER['HTTP_REFERER']);
}
if($_SESSION['view_type'] == '') $_SESSION['view_type'] = '.default';

class IargaShop
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "eshop")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/bitrix/eshop/css/panel.css"); 

			$arMenu = Array(
				Array(		
					"ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=iarga.shopplus100&".bitrix_sessid_get())."')",
					"ICON" => "bx-popup-item-wizard-icon",
					"TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
					"TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
				),
			);

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=iarga.shopplus100&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "eshop_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
				"MENU" => $arMenu,
			));
		}
	}
}




if(!class_exists("iarga")){
	class iarga{
		function getIBlockElement_i($id){
			$el = CIBlockElement::GetById($id)->GetNext();
			$props = CIBlockElement::GetProperty($el['IBLOCK_ID'],$el['ID'],Array("SORT"=>"ASC"),Array());
			while($prop = $props->GetNext()){
				if($prop['MULTIPLE']!='Y') $el['PROPERTIES'][$prop['CODE']] = $prop;
				else $el['PROPERTIES'][$prop['CODE']][] = $prop;
			}
			return $el;
		}
		function checkphone($phone){
			$phone = preg_replace("#[^0-9]#", "", $phone);
			if(strlen($phone)<9 || strlen($phone)>14) return false;
			else return true;
		}
		function take($start, $stop, $response){
			$ar = explode($start, $response);
			$content = explode($stop, $ar[1]);
			$content = $content[0];
			return $content;
		}
		function getprice($id,$curr="RUB"){
			global $USER;
			$price = CCatalogProduct::GetOptimalPrice($id,$USER->GetUserGroupArray());
			$priceRub = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'],$price['PRICE']["CURRENCY"],$curr);
			return $priceRub;
		}

		function getbase($str){
			preg_match("#([^\?]*)#",$str,$mat);
			return $mat[1];
		}
		function prep_br($str){
			return str_replace('\n','<br>',$str);
		}

		function sublet($str, $letters, $mode=1){
			$arr = explode(" ", $str);
			$l = 0;
			$ret = '';
			foreach($arr as $el){$l += strlen($el); if($l < $letters) $ret .= $el.' ';}
			if(strlen($ret) < strlen($str)) $add = '&#8230';
			return trim($ret).$add;
		}
		function prep($summ,$nbsp=1){
			$des = $summ-floor($summ);
			$summ = (string) floor($summ);
			$res = "";
			$cnt = 0;
			for($i=(strlen($summ)-1);$i>=0;$i--){
				$res = substr($summ, $i, 1) . $res;
				$cnt++;
				if($cnt==3){ $res = " " . $res; $cnt = 0;} 		
				
			}
			$des = substr($des,0,3) * 100;
			if($des == 0) $des = '';
			elseif($des < 10) $des = ".".'0'. $des;
			else $des = ".". (int) $des;
			$res = trim($res.$des);
			if($nbsp) $res = preg_replace("#\s#","&nbsp;",$res);
			return $res;
		} 
		function dateprocess($date=0, $day=1, $month=1, $year=1){
				if($date == 0) $date = date("d.m.Y");
			$date = strtotime($date);
			switch(date("m", $date)){
				case 1: $m = "января"; break;
				case 2: $m = "февраля"; break;
				case 3: $m = "марта"; break;
				case 4: $m = "апреля"; break;
				case 5: $m = "мая"; break;
				case 6: $m = "июня"; break;
				case 7: $m = "июля"; break;
				case 8: $m = "августа"; break;
				case 9: $m = "сентября"; break;
				case 10: $m = "октября"; break;
				case 11: $m = "ноября"; break;
				case 12: $m = "декабря"; break;
			}
				if($day) $day1 = (int) date("d", $date);
				if($month) $month1 = $m;
				if($year) $year1 = date("Y", $date);
			return $day1 . " " . $month1 ." " . $year1;
		}
		function dayprocess($date=0){
				if($date == 0) $date = date("d.m.Y");
			$date = strtotime($date);
			switch(date("w", $date)){
				case 1: $m = "понедельник"; break;
				case 2: $m = "вторник"; break;
				case 3: $m = "среда"; break;
				case 4: $m = "четверг"; break;
				case 5: $m = "пятница"; break;
				case 6: $m = "суббота"; break;
				case 7: $m = "воскресенье"; break;	
				case 0: $m = "воскресенье"; break;	
			}
			return $m;
		}



		function unhtmlentities ($string){
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
			$trans_tbl = array_flip ($trans_tbl);
			return strtr ($string, $trans_tbl);
		}

		function sklon($num, $form1, $form2, $form3){
			if(iarga::rest10($num)==0 || iarga::rest10($num)>4 || ($num>=11 && $num<=14)) return $form1;
			elseif(iarga::rest10($num)==1) return $form2;
			else return $form3;
		}

		function rest10($num){
			return $num - floor($num/10)*10;
		}

		function comment($iblock_id,$code1,$code2,$name){
			include($_SERVER['DOCUMENT_ROOT']."/inc/comments.php");
		}
		function comms($id){
			return CIBlockElement::GetList(Array(), Array("SECTION_CODE"=>$id))->SelectedRowsCount();
		}
		function comments($item){
			$num = $item['PROPERTIES']['comments']['VALUE'];
			if($num > 0) return ' <span class="comments-number">'.$num.'</span>';
		}

		function videolink($inp){
			if(preg_match("#youtu.be/([0-9a-zA-Z\-_]+)#",$inp,$mat))  $inp = $mat[1];
			elseif(preg_match("#youtube.com/embed/([0-9a-zA-Z\-_]+)#",$inp,$mat))  $inp = $mat[1];
			elseif(preg_match("#\?v=#",$inp,$mat)){
				$arr = explode("?v=",$inp);
				if(sizeof($arr)>=2){
					$inp = preg_replace("#&.*#","",$arr[1]);
				}
			}elseif(preg_match("#vimeo.com/([0-9a-zA-Z\-_])+#",$inp,$mat))  $inp = $mat[1];
			
			return $inp;
		}





		function loadlink($file,$name=false,$ext=false){
			$path = CFile::GetPath($file);
			if(!$name){
				$f = CFile::GetById($file)->GetNext();
				$name = $f['original_name'];
				$ext = explode('.',$path);
				$name = $name.'.'.$ext[(sizeof($ext)-1)];
			}elseif(!$ext){
				$ext = explode('.',$path);
				$name = $name.'.'.$ext[(sizeof($ext)-1)];
			}
			
			$link = '/inc/file.php/'.$name.'?file='.$path;
			return $link;
		}


		// Отправка письма с файлом
		// file:Адрес##Имя##
		function custom_mail($to='',$subject='',$message='',$headers=false,$params=false){
			if($to=='info@anabel.ru') return false;
			if(preg_match("#file:#",$message)){
				$exp = explode('file:',$message);
				foreach($exp as $i=>$el){
					if($i==0) continue;
					$sec = explode('##',$el);
					$files[] = Array("file"=>$sec[0],"name"=>$sec[1]);
					$message = str_replace('file:'.$sec[0].'##'.$sec[1].'##','',$message);
				}

				if(sizeof($files) > 0){
					$exp = explode("\n",$headers);
					$from = str_replace('From: ','',$exp[0]);			
				  
					$boundary = "---"; //Разделитель
					/* Заголовки */
					$headers = "From: $from\nReply-To: $from\n";
					$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";
					$body = "--$boundary\n";
					/* Присоединяем текстовое сообщение */
					$body .= "Content-type: text/html; charset='utf-8'\n";
					$body .= "Content-Transfer-Encoding: quoted-printable\n\n";
					//$body .= "Content-Disposition: attachment; filename==".base64_encode($files[0]['name'])." \n\n";
					$body .= $message."\n";
					foreach($files as $file){
						$filename = $_SERVER['DOCUMENT_ROOT'].$file['file']; //Имя файла для прикрепления
						$exp = explode('.',$filename);
						$ext = $exp[sizeof($exp)-1];
						$name = $file['name'].'.'.$ext;

						$body .= "--$boundary\n";
						$file = fopen($filename, "r"); //Открываем файл
						$text = fread($file, filesize($filename)); //Считываем весь файл
						fclose($file); //Закрываем файл
						/* Добавляем тип содержимого, кодируем текст файла и добавляем в тело письма */
						$body .= "Content-Type: file/".$ext."; name=".($name)."\n"; 
						$body .= "Content-Transfer-Encoding: base64\n";
						$body .= "Content-Disposition: attachment; filename=".($name)."\n\n";
						$body .= chunk_split(base64_encode($text))."\n";
					}
					$body .= "--".$boundary ."--\n";
					return mail($to, $subject, $body, $headers); //Отправляем письмо


					//return sendMail($to,$from_mail,$from_name,$subject,$message,$_SERVER['DOCUMENT_ROOT'].$files[0]['file'],$files[0]['name']);
				}else return mail($to,$subject,$message,$headers,$params);
			}else{
				return mail($to,$subject,$message,$headers,$params);
			}
		}


		function res($img,$w,$h,$id=0){
			$res = CFile::ResizeImageGet($img,Array("width"=>$w,"height"=>$h),BX_RESIZE_IMAGE_PROPORTIONAL,true);
			return ($id)?$res['src']:$res;
		}
		function crop($img,$w,$h,$id=0){
			$res = CFile::ResizeImageGet($img,Array("width"=>$w,"height"=>$h),BX_RESIZE_IMAGE_EXACT,true);
			return ($id)?$res['src']:$res;
		}

		function bcrop($img,$w,$h,$id=0){
			//$h+=5;
			if(is_array($img)) $img = $img['ID'];
			$tempfile = CFile::GetPath($img);	
			$res = CFile::ResizeImageGet($img,Array("width"=>$w,"height"=>$h),BX_RESIZE_IMAGE_PROPORTIONAL,true);
			if($tempfile != $res['src'] && ($res['width']!=$w || $res['height']!=$h)) crop_man($_SERVER['DOCUMENT_ROOT'].$tempfile, $w, $h, $_SERVER['DOCUMENT_ROOT'].$res['src']);
			return ($id)?$res['src']:$res;
		}


		function crop_man($source, $w, $h, $dest=false,$fromtop=false){
			$size = getimagesize($source);
			if(!$dest && $size[0]==$w && $size[1]==$h) return true;
			if(!$dest) $dest =  $source;



				
			if($source=="") return false;

			$size = getimagesize($source);
			switch($size[2]){
				case 1: $img = imagecreatefromgif($source); break;
				case 2: $img = imagecreatefromjpeg($source); break;
				case 3: $img = imagecreatefrompng($source); break;
				case 6: $img = imagecreatefromwbmp($source); break;
			}
			$bw = $size[0];
			$bh = $size[1];

			/*Расчёт с кадрированием*/
			if($bw - $w < $bh - $h){
				$nw = $bw;
				$nh = floor($nw * $h / $w);
				$x = 0;
				$y = floor(($bh - $nh) / 2);
			}else{
				$nh = $bh;
				$nw = floor($w * $nh / $h);
				$y = 0;
				$x = floor(($bw - $nw) / 2);
			}
			//print 'x='.$x.' y='.$y.' w='.$w.' h='.$h.' nw='.$nw.' nh='.$nh.' bw='.$bw.' bh='.$bh.'<br>';
			

			$img2 = imagecreatetruecolor($w, $h);
			imagesavealpha($img2, true);
			imagealphablending($img2, false);	
			

			$col = imagecolorallocate($img2, 255, 255, 255);
			imagefill($img2, 0, 0, $col);
			imagecopyresampled($img2, $img, 0, 0, $x, $y, $w, $h, $nw, $nh);
			
			
			imagepng ($img2, $dest);
		}



		function crop_man_old($source, $w, $h, $dest=false,$fromtop=false){
		$size = getimagesize($source);
		if(!$dest && $size[0]==$w && $size[1]==$h) return true;
		if(!$dest) $dest =  $source;

		$b = 0;
		$m = 0;
		//$r = 20;

		$sx = 0;
		$sy = 0;
		$alf = 0;

			
		if($source!=""):
			$size = getimagesize($source);
			switch($size[2]){
				case 1: $img = imagecreatefromgif($source); break;
				case 2: $img = imagecreatefromjpeg($source); break;
				case 3: $img = imagecreatefrompng($source); break;
				case 6: $img = imagecreatefromwbmp($source); break;
			}
			

			/*Расчёт с кадрированием*/
			if($size[0]/$w > $size[1]/$h){$croph = $size[1]; $cropw = $size[1] * $w/$h; }
			else{$cropw = $size[0]; $croph = $size[0] * $h/$w;}
			

			$img2 = imagecreatetruecolor($w+$b*2, $h+$b*2);
			imagesavealpha($img2, true);
			imagealphablending($img2, false);	
			

			$col = imagecolorallocate($img2, 255, 255, 255);
			imagefill($img2, 0, 0, $col);
			imagecopyresampled($img2, $img, $b, $b, floor(($cropw/$w)/2), floor(($croph/$h)/2), $w, $h, $cropw, $croph);
			$img = $img2;
			$size[0] = $w+$b*2;
			$size[1] = $h+$b*2;

			//imagecopyresampled($img2, $img, 0, 0, 0, 0, $size[0], $size[1], $size[0], $size[1]);

			
			imagepng ($img2, $dest);
		endif;

		}

		function roundcrop($img,$width,$height,$id){
			if(is_array($img)) $img = $img['ID'];
			$source = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($img);

			$crop = CFile::ResizeImageGet($img,Array('width'=>$width,'height'=>$height),BX_RESIZE_IMAGE_PROPORTIONAL,true);
			$crop['width'] = $width;
			$crop['height'] = $height;

			$desc = $_SERVER['DOCUMENT_ROOT'].$crop['src'];
			if($source==$desc) return $crop;
			
			copy($source,$desc);
			crop_man($desc,$width,$height);
			
			
			$filename = $desc;
			$radius = ceil($width/2);

			/**
			* Чем выше rate, тем лучше качество сглаживания и больше время обработки и
			* потребление памяти.
			*
			* Оптимальный rate подбирается в зависимости от радиуса.
			*/
			$rate = 3;

			$img = imagecreatefromstring(file_get_contents($filename));
			imagealphablending($img, false);
			imagesavealpha($img, true);

			$width = imagesx($img);
			$height = imagesy($img);

			$rs_radius = $radius * $rate;
			$rs_size = $rs_radius * 2;

			$corner = imagecreatetruecolor($rs_size, $rs_size);
			imagealphablending($corner, false);

			$trans = imagecolorallocatealpha($corner, 255, 255, 255, 127);
			imagefill($corner, 0, 0, $trans);

			$positions = array(
			array(0, 0, 0, 0),
			array($rs_radius, 0, $width - $radius, 0),
			array($rs_radius, $rs_radius, $width - $radius, $height - $radius),
			array(0, $rs_radius, 0, $height - $radius),
			);

			foreach ($positions as $pos) {
			imagecopyresampled($corner, $img, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
			}

			$lx = $ly = 0;
			$i = -$rs_radius;
			$y2 = -$i;
			$r_2 = $rs_radius * $rs_radius;

			for (; $i <= $y2; $i++) {

			$y = $i;
			$x = sqrt($r_2 - $y * $y);

			$y += $rs_radius;
			$x += $rs_radius;

			imageline($corner, $x, $y, $rs_size, $y, $trans);
			imageline($corner, 0, $y, $rs_size - $x, $y, $trans);

			$lx = $x;
			$ly = $y;
			}

			foreach ($positions as $i => $pos) {
			imagecopyresampled($img, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
			}

			imagepng($img,$desc);
			return ($id)?$crop['src']:$crop;
		}


		

	}
}
?>