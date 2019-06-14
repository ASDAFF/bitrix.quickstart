<?

	namespace Webprofy\Bitrix\Attribute\FieldAttribute;
	use Webprofy\Bitrix\Attribute\FieldAttribute;
	
	class SectionAttribute extends FieldAttribute{
		function __construct(){
			parent::__construct('IBLOCK_SECTION_ID');
		}

		function getActionCode($type = 'select'){
			if($type == 'filter'){
				return 'SECTION_ID';
			}
			return parent::getActionCode($type);
		}

		function getName(){
			return 'Раздел';
		}

		function getValueType(){
			return 'list';
		}

		function getListValues(){
			return \WP::bit(array(
				'of' => 'sections-tree',
				'f' => array(
					'iblock' => $this->iblock->getId()
				),
				'map' => function($d, $f){
					if(!$f['ID']){
						$d['event']['skip'] = true;
					}
					return array(
						'name' => str_repeat('=', $f['DEPTH_LEVEL']).' '.$f['NAME'],
						'id' => $f['ID']
					);
				}
			));
		}
	}