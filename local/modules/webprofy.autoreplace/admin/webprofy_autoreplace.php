<?php
	$mainPath = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main";

	require_once($mainPath."/include/prolog_admin_before.php");

	use Webprofy\Bitrix\Module;

	use Autoreplace\View;
	use Autoreplace\Ajax;

	$module = new Module(
		__FILE__,
		'webprofy.autoreplace', // ID модуля
		'WEBPROFY_AUTOREPLACE_', // Префикс в языковых файлах
		$GLOBALS['APPLICATION']
	);

	$module
		->setLang()
		->setTitle() 
		->setJS(array(
			'jquery.min.js',
			'angular.min.js',
			'angular-sanitize.min.js',
			'select.min.js',
			'script.js'
		))
		->setCSS(array(
			'select2.css',
			'select.min.css',
			'main.css'
		));

	$m_ = $module;
	require($mainPath."/include/prolog_admin_after.php");
	$module = $m_;	

	try{
		if(($input = $module->getAjaxData()) !== false){
			$module->ajaxStart();
				$ajax = new Ajax();
				$output = $ajax->getOutputArray($input);
			$module->ajaxEnd($output);
		}
	}
	catch(Exception $e){
		WP::log($e.'', 'clr');
	}


	/*if($module->posting($save, $apply)){
		$module->showMessage(
			'Выполняем действие',
			$save ? $save : $apply
		);
	}*/

	/*$module
		->showMessage(
			'Модуль успешно запущен.',
			'А что ещё от него можно ожидать?'
		);*/

	$module
	    ->showCSS()
		->showJS();


	$tabs = $module->getTabs(array(
		array(
			"DIV" => "edit1",
			"TAB" => 'Автозамена',
			"ICON" => "smile",
			"TITLE" => ''
		)/*,
		array(
			"DIV" => "edit2",
			"TAB" => 'Вторая вкладка',
			"ICON" => "smile",
			"TITLE" => ''
		),*/
	));

	$view = new View();

	?>
		<div
			ng-app="WPAutoreplaceApp"
			ng-controller="MainController"
			class="autoreplace-root"
			ng-class="{loading : loading}"
		>

			<div class="templates" ng-non-bindable>
				<div class="attributes">
					<div ng-if="holders && !holders.any()">
						<div class="centrify">
							{{ holders.lang.no }}
						</div>
					</div>
					<div ng-repeat="holder in holders.all()" class="attributes">

						<div class="header h2">
							<span ng-if="holders.getType() == 'update'">
								{{ holders.lang[holder.condition().isSingle() ? 'one' : 'many'] }}
							</span>
							<span ng-if="holders.getType() == 'compare'">
								<?
									$view->showSelect(array(
										'model' => 'holder.condition',
										'all' => 'holder.conditions.all()'
									));
								?>
							</span>

							<div class="buttons">
								<input
									ng-if="!holder.condition().isSingle()"
									type="button"
									value="Добавить"
									tabindex="-1"
									ng-click="addHolder(holder)"
								>

								<input
									ng-if="holder.condition().isSingle()"
									type="button"
									value="Удалить"
									tabindex="-1"
									ng-click="holders.remove(holder)"
								>
							</div>
						</div>

						<div
							ng-if="!holder.condition().isSingle()"
							data-attribute-holders="holder.holders"
							data-parent-index="$index"
							data-iblock="iblock"
						></div>

						<div ng-if="holder.condition().isSingle()">
							<div class="row">
								<div class="label">
									Свойство:
								</div>
								<div class="value">

									<?
										$view->showSelect(array(
											'model' => 'holder.attribute',
											'all' => 'holder.attributes.all()'
										));
									?>

									<input
										type="button"
										value="Сократить"
										ng-if="example.avaliable()"
										ng-click="holder.attributes.shorten(example)"
									/>
								</div>
							</div>

							<div class="row" ng-if="holder.attribute().actions.any()">
								<div class="label">
									Действие:
								</div>
								<div class="value">
									<?
										$view->showSelect(array(
											'model' => 'holder.attribute().action',
											'all' => 'holder.attribute().actions.all()'
										));
									?>
								</div>
							</div>

							<div ng-if="holder.get('action')">

								<div class="row" ng-if="holder.get('values').canAnything()">
									<div class="label"></div>
									<div class="value">
										<div
											ng-if="holder.get('values').canOther"
										>
											<label>
												<input
													type="checkbox"
													ng-model="holder.get('values').other"
										            ng-model-options="{getterSetter: true}"
												/>
												Выбрать из другого свойства
											</label>
										</div>

										<div
											ng-if="holder.get('values').canMany"
										>
											<label>
												<input
													type="checkbox"
													ng-model="holder.get('values').many"
										            ng-model-options="{getterSetter: true}"
												/>
												Несколько
											</label>

											<input
												ng-if="holder.get('values').many()"
												type="button"
												ng-click="holder.get('values').addOne()"
												value="Добавить"
											/>
										</div>
									</div>
								</div>

								<div class="row" ng-if="holder.get('values').other() && iblockHolders.many()">
									<div class="label">
										Инфоблок другого свойства
									</div>
									<div class="value">
										
									</div>
								</div>

								<div class="row" ng-repeat="value in holder.get('values').all()">
									<div class="label">
										{{ value.getLabel($index)}}
									</div>
									<div class="value">
										<input
											type="text"
											ng-if="value.type == 'text' || value.type == 'string' || value.type == 'limit'"
											ng-model="value.value"
										>

										<textarea
											ng-if="value.type == 'textarea'"
											ng-model="value.value"
										></textarea>

										<?
											$view->showSelect(array(
												'model' => 'value.value',
												'all' => 'holder.attributes.all()',
												'if' => 'value.type == \'other\''
											));
										?>
										

										<?
											$view->showSelect(array(
												'model' => 'value.value',
												'all' => 'holder.get(\'values\').list.all()',
												'if' => 'value.type == \'list\''
											));
										?>

									</div>
								</div>

							</div>

						</div>

					</div>
				</div>
			</div>
			<?
				$module->formStart();
					$tabs->Begin();
					$tabs->BeginNextTab();
						?>
								</tbody>
							</table>

							<div
								class="iblock"
								ng-repeat="holder in iblockHolders.all()"
							>
								<div class="header">
									Инфоблок №{{ $index + 1 }}
									<div class="buttons">
										<input
											type="button"
											ng-if="iblockHolders.many()"
											ng-click="iblockHolders.remove(holder)"
											value="Удалить"
											tabindex="-1"
										>
									</div>
								</div>

								<div class="row" ng-show="holder.iblocks.any()">
									<div class="label">Инфоблок: </div>
									<div class="value">
										<?
											$view->showSelect(array(
												'model' => 'holder.iblock',
												'all' => 'holder.iblocks.all()',
												'group' => 'type'
											));
										?>
									</div>
								</div>

								<div class="row" ng-show="holder.iblock().id">
									<div class="label">Выбрать: </div>
									<div class="value">
										<?
											$view->showSelect(array(
												'model' => 'holder.iblock().selectType',
												'all' => 'holder.iblock().selectTypes.all()',
											));
										?>
									</div>
								</div>

								<div
									ng-if="holder.iblock().selectType"
									ng-repeat="holdersName in ['compare', 'update']"
								>
									<div
										class="holders"
										data-attribute-holders="holder.iblock().relations.byType(holdersName)"
										data-iblock="holder.iblock()"
										data-iblock-holders="holders"
									></div>
								</div>

							</div>

							<div class="header">
								Инфоблоки:
							</div>
							<div class="row">
								<div class="label"></div>
								<div class="value">
									<input
										type="button"
										ng-click="iblockHolders.addHolder()"
										value="Добавить"
										tabindex="-1"
									/>
								</div>
							</div>

							<div ng-if="example.avaliable()">
								<div class="header">Пример выборки</div>
								<div class="row">
									<div class="label">Действия</div>
									<div class="value">
										<input
											type="button"
											ng-click="example.update()"
											value="Показать пример"
										/>
									</div>

								</div>

								<div class="table" ng-if="example.any()">
									<div ng-repeat="table in example.getTables()" ng-init="iblock = iblockHolders.iblockById(table.iblock)">
										<div class="header h-3">{{ table.name }}</div>
										
										<div ng-if="iblock.attributes.any()">
										
											<div class="row">
												<div class="label">Всего элементов:</div>
												<div class="value">{{ table.getTotalElements() }}</div>
											</div>
											<div class="row">
												<div class="label">Столбцы</div>
												<div class="value">
													<input
														type="button"
														ng-click="iblock.selects.push(null)"
														value="Добавить"
													>
												</div>
											</div>

											<div class="row" ng-repeat="select in iblock.selects track by $index">
												<div class="label">Значение №{{ $index + 1 }}</div>
												<div class="value">

													<?
														$view->showSelect(array(
															'model' => 'iblock.selects[$index]',
															'all' => 'iblock.attributes.all()',
															'change' => 'example.update()',
															'getset' => false
														));
													?>

													<input
														type="button"
														ng-click="iblock.selects.splice($index, 1)"
														value="Удалить"
													>
												</div>
											</div>

										</div>

										<table class="example">
											<thead>
												<tr>
													<td>
														№
													</td>
													<td ng-repeat="cell in table.getHeads()">
														{{ cell.content() }}
													</td>
												</tr>
											</thead>

											<tbody>
												<tr
													ng-repeat="row in table.getRows()"
												>
													<td>
														{{ $index + ((iblock.page - 1) * 20) + 1}}
													</td>
													<td ng-repeat="cell in row.getCells()">
														{{ cell.content() }}
													</td>
												</tr>
											</tbody>

										</table>

										<div class="row" ng-if="table.getTotal() > 1">
											<div class="label">Страница</div>
											<div class="value">
												<input
													type="button"
													value="<-"
													ng-click="example.pager(iblock, -1)"
													ng-if="iblock.page > 1"
												>
												<input
													type="text"
													ng-model="iblock.page"
													style="width:50px;"
													ng-change="example.update()"
												> из {{ table.getTotal() }} 
												<input
													type="button"
													value="->"
													ng-click="example.pager(iblock, +1)"
													ng-if="iblock.page < table.getTotal()"
												>
											</div>
										</div>
									</div>
								</div>
							</div>
	
							<table>
								<tbody>
						<?
			/*
						use Webprofy\Bitrix\IBlock;
						use Webprofy\Bitrix\Attribute\Attributes;
						use Webprofy\Bitrix\Attribute\PropertyAttribute;
						use Webprofy\Bitrix\Attribute\FieldAttribute;
						use Webprofy\Bitrix\Attribute\Action\Compare\EqualAction;
						use Webprofy\Bitrix\Attribute\Action\Compare\BetweenAction;
						use Webprofy\Bitrix\Attribute\Action\Update\MathAction;

						$attribute = new FieldAttribute('ID');
						$attribute->setAction(new BetweenAction());
						$attribute->setValue(array(275805, 275809));

						/*
							$attribute = new PropertyAttribute(80330);
							$attribute->setAction(new EqualAction('='));
							$attribute->setValue('106-010');
						*//*

						$attributes = new Attributes();
						$attributes->add($attribute);

						$iblock = new IBlock(300);
						try{
							$iblock->elementsByAttributes($attributes, array(
								'object' => true,
								'sel' => 'ID, NAME',
								'each' => function($d, $element){
									$attribute = new FieldAttribute('ID');
									$attribute->setAction(new MathAction('multiply'));
									$attribute->setValue(10);

									$attributes = new Attributes();
									$attributes->add($attribute);

									$element->updateWithAttributes($attributes);
								}
							));
						}
						catch(Exception $exception){
							$module->showMessage('Произошла ошибка', ''.$exception, true);
						}*/
					/*$tabs->BeginNextTab();
						?>
							Вкладка 2.
						<?*/
					/*$tabs->Buttons(array(
						"disabled" => false,
						"back_url" => '/bitrix/admin/'
					));*/
					$tabs->End();
				$module->formEnd();
			?>
		</div>
	<?


	require($mainPath."/include/epilog_admin.php");