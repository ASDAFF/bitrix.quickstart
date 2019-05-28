<?

	namespace Autoreplace;

	class View{
		function showSelect($data){
			if(!is_array($data['search'])){
				$data['search'] = array();
			}

			$search = '';

			foreach(array_merge(array(
				'name',
				'id',
			), $data['search']) as $name){
				$search .= ','.$name.':$select.search';
			}

			$search = substr($search, 1);

			foreach(array(
				'model' => 'model_empty',
				'placeholder' => '',
				'all' => 'null_emty',
				'more' => 'id',
				'name' => 'name'
			) as $i => $v){
				if(empty($data[$i])){
					$data[$i] = $v;
				}
			}

			?>
				<ui-select
                    ng-model="<?=$data['model']?>"
                    <?=@$data['change'] ? 'ng-change="'.$data['change'].'"' : ''?>
                    <?=@$data['if'] ? 'ng-if="'.$data['if'].'"' : ''?>
					theme="select2"
                    <?=(@$data['getset'] === false ? '' : 'ng-model-options="{getterSetter: true}"')?>
				>
					<ui-select-match
						placeholder="<?=$data['placeholder']?>"
					>
						{{$select.selected.<?=$data['name']?>}}
					</ui-select-match>

					<ui-select-choices
						<?=@$data['group'] ? 'group-by="\''.$data['group'].'\'"' : ''?>
						repeat="one in <?=$data['all']?> | propsFilter: {<?=$search?>}"
					>
						<div ng-bind-html="one.<?=$data['name']?> | highlight: $select.search"></div>
						<small>
							<span ng-bind-html="one.<?=$data['more']?> | highlight: $select.search"></span>
						</small>
					</ui-select-choices>

				</ui-select>
			<?
		}
	}