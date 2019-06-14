<?
	namespace Webprofy\Bitrix\IBlock;

	use Webprofy\Bitrix\IBlock\IBlock;
	use Webprofy\Bitrix\IBlockEntity;
	use Webprofy\Bitrix\Attribute\Attribute;
	use Webprofy\Bitrix\Attribute\Attributes;
	use Webprofy\Bitrix\Attribute\SectionFieldAttributes;
	use Webprofy\Bitrix\Attribute\SectionUserAttributes;
	use \WP;

	class Section extends IBlockEntity{
		private $fields = null;

		function u($index){
			return parent::f('u', $index);
		}

		function f($index){
			return parent::f('f', $index);
		}

		function get($type, $index){
			if($type instanceof Attribute){
				$index = $type->getCode();
				$type = $type->getType();
			}

			switch($type){
				case 'section-user':
				case 'u':
					return $this->u($index);

				case 'section-field':
				case 'f':
					return $this->f($index);
			}
		}

		protected function createData(){
			if(!$this->iblock){
				$iblock = WP::bit(array(
					'of' => 'sections',
					'sel' => 'IBLOCK_ID',
					'f' => array(
						'ID' => $this->id
					),
					'one' => 'f.IBLOCK_ID'
				));

				$iblock = new IBlock($iblock);
			}

			return WP::bit(array(
				'of' => 'sections',
				'sel' => $iblock->getSectionAttributes()->map(function($sa){
					return $sa->getActionCode();
				}),
				'f' => array(
					'ID' => $this->id,
					'iblock' => $iblock
				),
				'one' => function($d, $f, $u){
					return array(
						'f' => $f,
						'u' => $u
					);
				},
			));
		}

		function getAttributes($type){
			switch($type){
				case 'field':
				case 'f':
					$a = new SectionFieldAttributes($this->iblock);
					break;

				case 'user':
				case 'u':
					$a = new SectionUserAttributes($this->iblock);
					break;

				case 'all':
				case 'a':
					return $this->getAttributes('f')->extend(
						$this->getAttributes('u')
					);

				default:
					return null;
			}
		}
	}