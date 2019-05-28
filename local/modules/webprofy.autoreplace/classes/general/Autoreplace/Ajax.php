<?

	namespace Autoreplace;

	use Webprofy\Bitrix\IBlock\IBlock;
	use Webprofy\Bitrix\Attribute\Attribute;
	use Webprofy\Bitrix\Attribute\Attributes;
	use Webprofy\Bitrix\Attribute\AttributesTree;
	use \WP;

	class Ajax{
		/*private function getExampleTableAmount($info){
			$td = $this->parseTableData($info);
			return array(
				'total' => $td['iblock']->itemsByAttributes(
					$td['attributes']['compare'],
					array(
						'of' => $info['iblock']['of'],

						'object' => true,
						'sel' => 'ID',
						'sort' => 'ID',

						'get-count' => true,
					)
				),
				'iblock' => array(
					'id' => $td['iblock']->getId(),
					'name' => $td['iblock']->getName(),
				)
			);
		}*/

		private function parseTableData($info){
			$iblock = new IBlock($info['iblock']['id']);
			$page = intval($info['page']);

			$compare = AttributesTree::generate($info['compare'], 'and');
			$update = AttributesTree::generate($info['update'], 'or');
			$all = new Attributes();

			if(empty($info['selects'])){
				$fields = $iblock->getAttributes($info['iblock']['of'], 'all');
				foreach(array(
					'ID', 'NAME'
				) as $id){
					$all->add($fields->filter('getId', $id, true));
				}
			}
			else{
				foreach($info['selects'] as $info){
					$all->add(Attribute::generate($info, $iblock));
				}
			}

			$updateAttributes = $update->getAttributes();

			$all
				->extend($compare->getAttributes())
				->removeSame('getId')
				->extend($updateAttributes);

			$updateAttributes->each(function($ua) use (&$all){
				$value = $ua->getAction()->getValue();
				if($value->isOther()){
					$all->extend($value->getAttributes($iblock)->each(function($a){
						$a->setAdditional(true);
					}));
				}
			});

			return array(
				'iblock' => $iblock,
				'page' => $page,
				'attributes' => array(
					'compare' => $compare,
					'update' => $update,
					'all' => $all
				)
			);
		}

		private function getExampleTable($info){
			$result = array();

			$td = $this->parseTableData($info);

			$elements = array();

			$td['iblock']->itemsByAttributes(
				$td['attributes']['compare'],
				array(
					// 'debug' => 1,

					'of' => $info['iblock']['of'],
					'object' => true,
					'sel' => $td['attributes']['all']->map(function($attribute){
						return $attribute->getActionCode('select');
					}),
					'each' => function($d, $element) use (&$td, &$elements){
						$element_ = array();
						foreach($td['attributes']['all'] as $attribute){

							if($attribute->isUpdater()){
								$values = $element->getUpdateValues($attribute);
								$element_[] = $values['after'];
								continue;
							}
							else{
								$element_[] = $element->get($attribute);
							}

						}
						
						$elements[] = $element_;
					},
					'sort' => 'ID',
					'page' => $td['page'],
					'per-page' => 20
				)
			);

			$total = WP::bit('pages');
			$totalElements = WP::bit('total');

			return array(
				'name' => $td['iblock']->getName(),
				'iblock' => $td['iblock']->getId(),
				'rows' => $td['attributes']['all']->map(function($attribute){
					return array(
						'name' => $attribute->getName().($attribute->isUpdater() ? ' (после)' : '')
					);
				}),
				'total' => $total,
				'totalElements' => $totalElements,
				'elements' => $elements,
			);
		}

		function getOutputArray($input){
			$result = array(
				'ok' => true
			);

			if(!empty($input['get'])){
				switch($input['get']){

	                case 'iblocks':
						$result['iblocks'] = array();

						WP::bit(array(
							'of' => 'iblock-types',
							'skip-filter' => true,
							'map' => function($d, $f) use (&$result){
								$name = $f['LANG']['NAME'];
								$type = $f['ID'];

								WP::bit(array(
								    'of' => 'iblock',
								    'f' => array(
								    	'TYPE' => $type
								    ),
								    'each' => function($d, $f) use ($name, &$result){
								        $result['iblocks'][] = array(
								            'id' => $f['ID'],
								            'name' => $f['NAME'],
								            'type' => $name,
								            'def' => $f['ID'] == 300
								        );
								    },
								    'sort' => 'iblock_type'
								));
							}
						));
	                    break;

					case 'example':
						$result['example']['tables'] = array();
						foreach($input['iblocks'] as $info){
							$result['example']['tables'][] = $this->getExampleTable($info);
						}
						break;

					case 'attributes':
						$iblock = new IBlock($input['iblock']['id']);
						$result['attributes'] = $iblock
							->getAttributes($input['iblock']['of'], 'all')
							->getJson();
						break;

	                case 'actions':
						$iblock = new IBlock($input['iblock']['id']);
	                	$result['actions'] = $iblock
							->getAttributes($input['iblock']['of'], 'all')
							->getByTypeAndId(
								$input['attribute']['type'],
								$input['attribute']['id']
							)
							->first()
							->getActions($input['type'])
							->getJson();
	                	break;

	                case 'values':
						$iblock = new IBlock($input['iblock']['id']);
						$result['values'] = $iblock
							->getAttributes($input['iblock']['of'], 'all')
							->getByTypeAndId(
								$input['attribute']['type'],
								$input['attribute']['id']
							)
							->first()
							->setIBlock($iblock)
							->getListValues($iblock);

	                	break;

					/*case 'amount':
						$result['amount'] = array();
						foreach($input['iblocks'] as $info){
							$result['amount'][] = $this->getExampleTableAmount($info);
						}
						break;*/
				}
			}
			return $result;
		}
	}