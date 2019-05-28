<?
	
	namespace MHT;

	use \WP;
	use \CUtil;

	class IBlocksParser{
		private
			$iblocks = null,
			$templates = null;

		public function __construct(){
			$this->setIBlocks();
			$this->setTemplates();
		}

		private function setTemplates(){
			$templates = array(
				'.section' => '',
				'index' => '',
			);

			foreach($templates as $i => $template){
				$templates[$i] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/catalog/template/'.$i.'.php');
			}

			$this->templates = $templates;
		}

		private function setIBlocks(){
			$iblocks = array();
			WP::query(
				'
					SELECT
						*
					FROM
						b_iblock
					WHERE
						`IBLOCK_TYPE_ID` = "mht_products" AND
						`NAME` NOT LIKE "Пакет предложений%"
					ORDER BY
						NAME
				',
				function($row) use (&$iblocks){
					$iblocks[$row['ID']] = array(
						'id' => $row['ID'],
						'name' => $row['NAME'],
						'code' => CUtil::translit(
							trim(preg_replace('/\(.*?\)/', '', $row['NAME'])),
							LANGUAGE_ID,
							array(
								"max_len" => 100,
								"change_case" => 'l',
								"replace_space" => '_',
								"replace_other" => '_',
								"delete_repeat_replace" => true,
							)
						)
					);
				}
			);

			foreach($iblocks as $id => &$iblock){
				$code = $iblock['code'];
				$iblock['url'] = array();
				$iblock['url']['list'] = '/catalog/'.$code.'/';
				$iblock['url']['section'] = $iblock['url']['list'].'#SECTION_CODE_PATH#/';
				$iblock['url']['detail'] = $iblock['url']['section'].'#ELEMENT_CODE#/';
				$iblock['dir'] = $_SERVER['DOCUMENT_ROOT'].'/catalog/'.$iblock['code'].'/';
				$iblock['picture'] = $this->getPictureID($iblock);
			}

			$this->iblocks = $iblocks;
		}

		function getPictureID($iblock){
			$file = $_SERVER['DOCUMENT_ROOT'].'/img/catalog/sections/'.$iblock['code'].'.png';
			if(!file_exists($file)){
				return 0;
			}
			$tmp = tempnam("/tmp", "iblockparser_image_");
			copy($file, $tmp);

			return \CFile::SaveFile(
				array(
					'name' => $iblock['name'],
					'tmp_name' => $tmp,
					'type' => 'image/jpeg'
				),
				'catalog_images'
			);
		}

		function updateTable($iblock){
			WP::query('
				UPDATE
					b_iblock
				SET
					CODE = "'.$iblock['code'].'",
					LIST_PAGE_URL = "'.$iblock['url']['list'].'",
					SECTION_PAGE_URL = "'.$iblock['url']['section'].'",
					DETAIL_PAGE_URL = "'.$iblock['url']['detail'].'",
					PICTURE = '.$iblock['picture'].'
				WHERE
					ID = '.$iblock['id'].'
			');
			//WP::log($query);
		}

		function createFiles($iblock){
			$replacement = array(
				'%ID' => $iblock['id'],
				'%NAME' => $iblock['name'],
				'%LIST' => $iblock['url']['list'],
				'%COMPARE' => $this->getComparePropertiesHTML($iblock),
			);
			if(!is_dir($iblock['dir'])){
				mkdir($iblock['dir'], 0776, false);
			}
			foreach($this->templates as $i => $template){
				$html = strtr($template, $replacement);
				//WP::log($html);
				file_put_contents($iblock['dir'].$i.'.php', $html);
			}
		}

		function parse(){
			foreach($this->iblocks as $id => &$iblock){
				$this->updateTable($iblock);
				$this->createFiles($iblock);
			}
		}

		function getComparePropertiesHTML($iblock){
			$html = '';
			$i = 0;
			WP::query(
				'
					SELECT
						CODE
					FROM
						`b_iblock_property`
					WHERE
						IBLOCK_ID = '.$iblock['id'].' AND
						ACTIVE = "Y"
				',
				function($row) use (&$i, &$html){
					$html .= '			';
					$html .= '"'.$i.'" => "'.$row['CODE'].'",'."\n";
					$i++;
				}
			);
			return $html;
		}
	}
