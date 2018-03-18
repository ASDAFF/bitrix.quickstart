<?
######################################################
# Name: energosoft.slider                            #
# File: tools.php                                    #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
class ESSlider
{
	static public function ES_GetHash($es_show_buttons,$es_block_witdh,$es_block_height,$es_block_margin,$es_count)
	{
		return strtolower(md5("$es_show_buttons:$es_block_witdh:$es_block_height:$es_block_margin:$es_count"));
	}

	static public function ES_GenerateCSS($es_hash,$cPath,$cTemplate,$es_orientation,$es_show_buttons,$es_block_witdh,$es_block_height,$es_block_margin,$es_count)
	{
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].$cTemplate."/template".$es_hash.".css"))
		{
			$file = file_get_contents($_SERVER["DOCUMENT_ROOT"].$cPath."/template.css", FILE_USE_INCLUDE_PATH);
			$file = str_replace("#ES_HASH#", $es_hash, $file);
			if($es_show_buttons=="Y")
			{
				$file = str_replace("#ES_SB_PADDING_H#", "20px 40px", $file);
				$file = str_replace("#ES_SB_PADDING_V#", "40px 20px", $file);
				if($es_orientation == "true")
				{
					$file = str_replace("#ES_LEFT50#", (($es_block_height+40)/2)-16, $file);
					$file = str_replace("#ES_TOP50#", (($es_block_witdh+80)/2)-16, $file);
				}
				else
				{
					$file = str_replace("#ES_TOP50#", (($es_block_height+40)/2)-16, $file);
					$file = str_replace("#ES_LEFT50#", (($es_block_witdh+80)/2)-16, $file);
				}
			}
			else
			{
				$file = str_replace("#ES_SB_PADDING_H#", "0px", $file);
				$file = str_replace("#ES_SB_PADDING_V#", "0px", $file);
				$file = str_replace("#ES_TOP50#", "0", $file);
				$file = str_replace("#ES_LEFT50#", "0", $file);
			}
			$file = str_replace("#ES_WIDTH_V#", ($es_block_witdh+$es_block_margin)*$es_count, $file);
			$file = str_replace("#ES_HEIGHT_V#", $es_block_height, $file);
			$file = str_replace("#ES_WIDTH_H#", $es_block_witdh, $file);
			$file = str_replace("#ES_HEIGHT_H#", ($es_block_height+$es_block_margin)*$es_count, $file);
			$file = str_replace("#ES_HALF_MARGIN#", $es_block_margin/2, $file);
			
			file_put_contents($_SERVER["DOCUMENT_ROOT"].$cTemplate."/template".$es_hash.".css", $file);
		}
	}
}
?>