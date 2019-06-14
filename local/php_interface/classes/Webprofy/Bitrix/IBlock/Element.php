<?
	namespace Webprofy\Bitrix\IBlock;

	use Webprofy\Bitrix\IBlock\IBlock;
	use Webprofy\Bitrix\IBlockEntity;
	use Webprofy\Bitrix\Attribute\Attribute;
	use Webprofy\Bitrix\Attribute\Attributes;
	use Webprofy\Bitrix\Attribute\FieldAttributes;
	use Webprofy\Bitrix\Attribute\PropertyAttributes;
	use \WP;


	class Element extends IBlockEntity{
		protected function createData(){
			$iblock = WP::bit(array(
				'of' => 'element',
				'sel' => 'IBLOCK_ID',
				'f' => array(
					'ID' => $this->id
				),
				'one' => 'f.IBLOCK_ID'
			));

			return WP::bit(array(
				'of' => 'element',
				'f' => array(
					'ID' => $this->id,
					'iblock' => $iblock
				),
				'one' => function($d, $f, $p){
					return array(
						'f' => $f,
						'p' => $p
					);
				}
			));
		}

		function f($index){
			return parent::f('f', $index);
		}

		function p($index){
			return parent::f('p', $index, 'VALUE');
		}

		function get($type, $index = null){
			if($type instanceof Attribute){
				$index = $type->getCode();
				$type = $type->getType();
			}

			if($index == null){
				list($type, $index) = explode('.', $type);
			}
			switch($type){
				case 'f':
				case 'field':
					return $this->f($index);

				case 'p':
				case 'property':
					return $this->p($index);
			}
		}

		function getUpdateValues(Attribute $attribute, $callback){
			$before = $this->get(
				$attribute->getElementValueType(),
				$attribute->getCode()
			);

			$action = $attribute->getAction();
			$after = $action->run(
				$before,
				$action->value->get($this),
				$this
			);

			return array(
				'before' => $before,
				'after' => $after
			);
		}

		function passWithAttributes(Attributes $attributes, $callback){
			$element = $this;
			$attributes->each(function($attribute) use ($element, $callback){
				$before = $element->get(
					$attribute->getElementValueType(),
					$attribute->getCode()
				);


				$after = $attribute->getAction()->run(
					$before,
					$attribute->getValue(),
					$element
				);
				$callback($attribute, $element, $after, $before);
			});
		}

		function updateWithAttributes(Attributes $attributes){
			$elementUpdateData = array();

			$this->passWithAttributes(
				$attributes,
				function($attribute, $element, $value) use (&$elementUpdateData){
					if($attribute->isForElementUpdate){
						$code = $attribute->getActionCode();
						$elementUpdateData[$code] = $value;
					}
					else{
						$attribute->update($element, $value);
					}
				}
			);

			WP::log($elementUpdateData);
			//\CIBlockElement::Update($id, $elementUpdateData)
		}

		function getAttributes($type){
			if(!$this->iblock){
				return null;
			}
			
			switch($type){
				case 'field':
				case 'f':
					$a = new FieldAttributes($this->iblock);
					break;

				case 'property':
				case 'p':
					$a = new PropertyAttributes($this->iblock);
					break;

				case 'all':
				case 'a':
					return $this->getAttributes('f')->extend(
						$this->getAttributes('p')
					);

				default:
					return null;
			}

			return $a->fill();
		}
	}