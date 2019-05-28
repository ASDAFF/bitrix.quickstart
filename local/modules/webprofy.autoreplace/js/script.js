(function($){

	function createClass(a, b){
		var parent,
			current;

		switch(arguments.length){
			case 1:
				current = a;
				break;

			case 2:
				parent = a._object_data;
				current = b;
				break;

			default:
				return;
		}

		if(parent){
			parent = $.extend({}, parent);
			current = $.extend({}, parent, current);
			current._parent = parent;
		}

		var result = function(){
			$.extend(this, current);
			current._init && current._init.apply(this, arguments);
		};

		result._object_data = current;

		return result;
	}

	angular
		.module('WPAutoreplaceApp', ['ui.select', 'ngSanitize'])
		.filter('propsFilter', function() {
		  return function(items, props) {
		    var out = [];

		    if (angular.isArray(items)) {
		      items.forEach(function(item) {
		        var itemMatches = false;

		        var keys = Object.keys(props);
		        for (var i = 0; i < keys.length; i++) {
		          var prop = keys[i];
		          var text = props[prop].toLowerCase();
		          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
		            itemMatches = true;
		            break;
		          }
		        }

		        if (itemMatches) {
		          out.push(item);
		        }
		      });
		    } else {
		      // Let the output be the input untouched
		      out = items;
		    }

		    return out;
		  };
		})
		.factory('RecursionHelper', ['$compile', function($compile){
		    return {
		        compile: function($element, $link){
		            if(angular.isFunction($link)){
		                $link = { post: $link };
		            }

		            var contents = $element.contents().remove(),
		            	compiledContents;

		            return {
		                pre: ($link && $link.pre) ? $link.pre : null,
		                post: function(scope, $element){
		                    if(!compiledContents){
		                        compiledContents = $compile(contents);
		                    }
		                    compiledContents(scope, function(clone){
		                        $element.append(clone);
		                    });

		                    if($link && $link.post){
		                        $link.post.apply(null, arguments);
		                    }
		                }
		            };
		        }
		    };
		}])
		.directive('editContainer', [function(){
			return {
				restrict : 'A',
				scope : {
					container : '=editContainer',
					type : '@',
				},
				template : '\
					<input\
						type="button"\
						value="Добавить"\
						ng-click="container.addByType(type)"\
					>\
					<input\
						type="button"\
						value="Удалить все"\
						ng-show="container.any()"\
						ng-click="container.clear()"\
					>\
				'
			}
		}])
		.directive('attributeHolders', ['RecursionHelper', function(RecursionHelper){
			return {
				restrict : 'A',
				scope : {
					holders : '=attributeHolders',
					parentIndex : '=',
					iblock : '=',
					iblockHolders : '='
				},
				controller : function($scope){
					$.extend($scope, {
						addHolder : function(holder){
							holder.holders.add(holder.holders.generate('single'));
						},
					});
				},
				compile : function($element){
					return RecursionHelper.compile($element);
				},
				template : $('.autoreplace-root .templates .attributes').html()
			}
		}])
		.controller('MainController', [
			'$scope',
			'$http',
			'$timeout',
			function($scope, $http, $timeout){
				var c = {};
				c.Container = createClass({
					_init : function(){
						this._all = [];
					},
					remove : function(one){
						var me = this;
						this.each(function(one_, i){
							if(one == one_){
								me._all.splice(i, 1);
								return false;
							}
						});
					},
					get : function(index, value){
						var result = null;
						this.each(function(one){
							if(one[index] == value){
								result = one;
								return false;
							}
						});
						return result;
					},
					addByType : function(type){
						this.add(new c[type]);
						return this;
					},
					getAll : function(){
						return this._all;
					},
					all : function(){
						return this._all;
					},
					clear : function(){
						this._all = [];
						return this;
					},
					add : function(one){
						this._all.push(one);
						return this;
					},
					each : function(callback){
						$.each(this._all, function(i, one){
							return callback(one, i);
						});
					},
					map : function(callback){
						return $.map(this._all, callback);
					},
					first : function(){
						return this._all[0] || null;
					},
					onload : null,
					fill : function(data){
						var me = this;
						ajax($.extend({
							_success : function(response){
								me.clear();
								var a = response[data.ajax.get];
								if(!a || !a.length){
									return;
								}
								$.each(a, function(i, one){
									me.add(new c[data.type](one));
								});
								me.onload && me.onload(me);
							}
						}, data.ajax || {}));
					},

					// bool
					any : function(){
						return !this.empty();
					},
					many : function(){
						return this._all.length > 1;
					},
					empty : function(){
						return this._all.length == 0;
					},
				});

				c.IBlocks = createClass(c.Container, {
					_init : function(){
						this._parent._init();
						this.fill({
							ajax : {
								get : 'iblocks',
							},
							type : 'IBlock'
						});
					}
				});

				c.SelectTypes = createClass(c.Container, {
					_init : function(){
						this._parent._init.apply(this, arguments);

						this
							.add(new c.SelectType({
								id : 'elements',
								name : 'Элементы',
							}))
							.add(new c.SelectType({
								id : 'sections',
								name : 'Разделы',
							}));
					}
				});

				c.SelectType = createClass({
					_init : function(data){
						$.extend(this, data);
					}
				})

				c.IBlock = createClass({
					_init : function(data){
						$.extend(this, data);
						this.relations = new c.AttributeHoldersList(this);
					},
					selectTypes : new c.SelectTypes(),
					_selectType : null,
					page : 1,
					selectType : function(selectType){
						if(arguments.length == 0){
							return this._selectType;
						}
						this._selectType = selectType;
						this.relations.fillHolders();
					},
					setType : function(type){
						this.type = type;
						return this;
					},
					type : null,
					getOptionName : function(){
						return this.name + ' [' + this.id + ']';
					},
					relations : null,
					getLoadData : function(){
						return {
							id : this.id,
							of : this.selectType() && this.selectType().id
						};
					},
					selects : [],
					setAttributes : function(){
						this.attributes = new c.CompareAttributes(this);
						this.attributes.load();
					},
					getAjaxData : function(){
						var result = {
							iblock : this.getLoadData(),
							selectType : this.selectType.id,
							page : this.page,
							selects : $.map(this.selects, function(attribute){
								return (attribute && attribute.getAjaxData()) || null;
							})
						};

						this.relations.each(function(holders){ // AttributeHolders
							result[holders.getType()] = holders.getAjaxData();
						});

						return result;
					},
					canSend : function(){
						var can = true;

						this.relations.each(function(holders){ // AttributeHolders
							if(!holders.canSend()){
								can = false;
								return false;
							}
						});

						return can;
					}
				});

				c.IBlockHolder = createClass({
					_init : function(){
						$.extend(this, {
							iblock : function(iblock){
								if(arguments.length == 0){
									return this._iblock;
								}

								if(iblock){
									iblock.setType(this._type);
									iblock.selectType(iblock.selectTypes.first());
									iblock.relations.fillHolders();
									iblock.setAttributes();
								}

								this._iblock = iblock;
							},
							iblocks : new c.IBlocks(),
						});


						var me = this;
						this.iblocks.onload = function(){
							me.iblock(me.iblocks.get('def', true));
						};
					}
				});

				c.AttributeHoldersList = createClass(c.Container, { // AttributeHolders
					_init : function(iblock){
						this._parent._init.apply(this, Array.prototype.slice.call(arguments, 1));
						this.iblock = iblock;
					},
					fillHolders : function(){
						this.clear();
						var iblock = this.iblock,
							compareHolders = new c.CompareAttributeHolders(iblock),
							updateHolders = new c.UpdateAttributeHolders(iblock),
							holder = null;

							holder = compareHolders.generate('and');
							holder.conditions.limit(['and', 'or']);
							holder.holders.add(
								compareHolders.generate('single')
							);
							compareHolders.add(holder);
						this.add(compareHolders);

							holder = updateHolders.generate('and');
							updateHolders.add(holder);
						this.add(updateHolders);
					},
					byType : function(type){
						var result = null;
						this.each(function(holders){
							if(holders.getType() == type){
								result = holders;
								return false;
							}
						});
						return result;
					}
				})

				c.AttributeHolders = createClass(c.Container, { // AttributeHolder
					iblock : null,
					_init : function(iblock){
						this._parent._parent._init.apply(this, Array.prototype.slice.call(arguments, 1));
						this.iblock = iblock;
					},
					getAjaxData : function(){
						return this.map(function(holder){
							return holder.getAjaxData();
						});
					},
					canSend : function(){
						var can = true;
						this.each(function(holder){
							if(!holder.canSend()){
								can = false;
								return false;
							}
						});
						return can;
					},
					getType : function(){}
				});

				c.UpdateAttributeHolders = createClass(c.AttributeHolders, {
					generate : function(conditionID){
						return new c.UpdateAttributeHolder(this.iblock, conditionID);
					},
					getType : function(){
						return 'update';
					},
					_type : 'update',
					lang : {
						no : 'Нет ни одной замены',
						one : 'Замена',
						many : 'Замены'
					}
				});

				c.CompareAttributeHolders = createClass(c.AttributeHolders, {
					generate : function(conditionID){
						return new c.CompareAttributeHolder(this.iblock, conditionID);
					},
					getType : function(){
						return 'compare';
					},
					lang : {
						no : 'Нет ни одного условия',
						one : 'Условие',
						many : 'Условия'
					}
				});

				c.AttributeHolder = createClass({
					iblock : null,
					_attribute : null,
					_init : function(iblock, conditionID){
						this.iblock = iblock;
						$.extend(this, {
							name : '',
							_condition : null,
							condition : function(condition){
								if(arguments.length == 0){
									return this._condition;
								}

								if(condition.isSingle()){
									this._attribute = null;
									this.attributes = this.getAttributes().load();
									var me = this;
									this.attributes.onload = function(){
										me.attribute(me.attributes.first());
									}
									delete this.holders;
								}
								else{
									delete this.attributes;
									this.holders = this.getHolders();
								}
								this._condition = condition;
							},
							conditions : new c.Conditions()
						});

						conditionID && this.setCondition(conditionID);
					},
					setCondition : function(id){
						this.condition(this.conditions.get('id', id));
						return this;
					},
					get : function(type, more){
						switch(type){
							case 'attribute':
								return this.attribute() || null;
								
							case 'action':
								var o = this.get('attribute');
								if(!o){
									return null;
								}
								return o.action();
								
							case 'values':
								var o = this.get('action');
								if(!o){
									return null;
								}
								return o.values;
						}
					},
					attribute : function(attribute){
						if(arguments.length == 0){
							return this._attribute;
						}

						this._attribute = attribute;
						attribute
							.setIBlock(this.iblock)
							.loadActions();
					},
					getAjaxData : function(){
						var condition = this.condition(),
							result = {
								condition : condition.id
							};

						if(condition.isSingle()){
							result.attribute = this.attribute().getAjaxData();
						}
						else{
							result.holders = this.holders.getAjaxData();
						}

						return result;
					},
					canSend : function(){
						var can = true;

						if(this.condition().isSingle()){
							if(!this.attribute().canSend()){
								can = false;
							}
						}
						else{
							if(!this.holders.canSend()){
								can = false;
							}
						}

						return can;
					}
				});

				c.UpdateAttributeHolder = createClass(c.AttributeHolder, {
					getHolders : function(){
						return new c.UpdateAttributeHolders(this.iblock);
					},
					getAttributes : function(){
						return new c.UpdateAttributes(this.iblock);
					}
				});

				c.CompareAttributeHolder = createClass(c.AttributeHolder, {
					getHolders : function(){
						return new c.CompareAttributeHolders(this.iblock);
					},
					getAttributes : function(){
						return new c.CompareAttributes(this.iblock);
					}
				});

				c.IBlockHolders = createClass(c.Container /* IBlockHolder */, {
					sendQuery : function(data, success){
						ajax($.extend({
							get : 'example',
							iblocks : this.map(function(iblockHolder){
								return iblockHolder.iblock().getAjaxData()
							}),
							_success : success
						}, data));
					},
					addHolder : function(){
						this.add(new c.IBlockHolder());
					},
					canSend : function(){
						var can = true;
						try{
							this.each(function(iblockHolder){
								if(!iblockHolder.iblock().canSend()){
									can = false;
									return false;
								}
							});
						}
						catch(e){
							return false;
						}
						return can;
					},
					iblockById : function(id){
						var result;
						this.each(function(holder){
							var iblock = holder.iblock();
							if(iblock && iblock.id == id){
								result = iblock;
								return false;
							}
						});
						return result;
					}
				});

				c.Conditions = createClass(c.Container, {
					_init : function(onlySingle){
						this._parent._init.apply(this, arguments);

						this
							.add(new c.Condition({
								id : 'single',
								name : 'Условие'
							}));

						if(onlySingle){
							return;
						}
						
						this.add(new c.Condition({
								id : 'and',
								name : 'Все условия'
							}))
							.add(new c.Condition({
								id : 'or',
								name : 'Хотя бы одно условие'
							}))
					},
					limit : function(ids){
						var me = this;
						me.each(function(condition){
							if(!condition || $.inArray(condition.id, ids) != -1){
								return;
							}
							me.remove(condition);
						});
					}
				});

				c.Condition = createClass({
					_init : function(data){
						$.extend(this, data);
					},
					notSingle : function(){
						return !this.isSingle();
					},
					isSingle : function(){
						return this.id == 'single';
					}
				})

				c.Attributes = createClass(c.Container, {
					iblock : null,
					_init : function(iblock){
						this._parent._parent._init.apply(this, Array.prototype.slice.call(arguments, 1))
						this.iblock = iblock;
					},
					load : function(type, _class){
						this.fill({
							ajax : {
								get : 'attributes',
								type : type,
								iblock : this.iblock.getLoadData()
							},
							type : _class,
						});
						return this;
					},
					shorten : function(example){
						example.query({
							get : 'shortened',
							
						});
					}
				});

				c.CompareAttributes = createClass(c.Attributes, {
					load : function(){
						this._parent.load.call(
							this,
							'compare',
							'CompareAttribute'
						);
						return this;
					},
				});

				c.UpdateAttributes = createClass(c.Attributes, {
					load : function(){
						this._parent.load.call(
							this,
							'update',
							'UpdateAttribute'
						);
						return this;
					}
				});


				c.Attribute = createClass({
					iblock : null,
					_init : function(data){
						var me = this;
						$.extend(this, data, {
							_action : null,
							action : function(action){
								if(arguments.length == 0){
									return this._action;
								}
								action
									.setAttribute(me)
									.activate();
								this._action = action;
							},
							actions : new c.Actions()
						});
					},
					setIBlock : function(iblock){
						this.iblock = iblock;
						return this;
					},
					loadActions : function(){
						var me = this;

						me.actions.load(this.iblock, this);
							
						me.actions.onload = function(){
							me.action(me.actions.first());
						};
					},
					getAjaxData : function(surface){
						var result = {
							id : this.id,
							type : this.type,
						};
						if(!surface){
							var action = this.action();
							if(action){
								result.action = action.getAjaxData();
							}
						}
						return result;
					},
					canSend : function(){
						return this.action().canSend();
					}
				});

				c.Actions = createClass(c.Container, {
					load : function(iblock, attribute){
						this.fill({
							ajax : {
								get : 'actions',
								attribute : {
									type : attribute.type,
									id : attribute.id
								},
								type : attribute.generalType,
								iblock : iblock.getLoadData()
							},
							type : 'Action'
						});
					}
				});

				c.ListValue = createClass({
					_init : function(data){
						$.extend(this, data);
					}
				});

				c.ListValues = createClass(c.Container, {
					values : null,
					setValues : function(values){
						this.values = values;
						return this;
					},
					fillValues : function(){
						this.fill({
							ajax : {
								iblock : this.values.action.attribute.iblock.getLoadData(),
								get : 'values',
								attribute : this.values.action.attribute.getAjaxData(true)
							},
							type : 'ListValue'
						});
					}
				});

				c.Values = createClass(c.Container, {
					action : null,
					init : function(data){
						$.extend(this, data);

						if(this.action.attribute.valueType == 'list'){
							this.type = 'list';
							this.list = new c.ListValues();
							this.list
								.setValues(this)
								.fillValues();
							var value = new c.Value(this);
							value.type = 'list';
							this.add(value);
							return;
						}
						this.type = this.type || 'string';

						if(this.limit){
							this.type = 'limit';
							var me = this;
							$.each(this.limit, function(i, info){
								var value = new c.Value(me);
								value.value = info.value;
								value.type = info.type || me.type;
								value.label = info.name;
								me.add(value);
							});
							return;
						}

						var n = this.many() ? 2 : 1;
						for(var i=0; i<n; i++){
							var value = new c.Value(this);
							value.type = 'string';
							this.add(value);
						}
					},
					setAction : function(action){
						this.action = action;
						return this;
					},
					canAnything : function(){
						return !this.limit && (this.canOther || this.canMany);
					},
					addOne : function(){
						if(this.limit){
							return;
						}
						var value = new c.Value(this);
						value.type = this.type;
						this.add(value);
					},
					many : function(many){
						if(arguments.length == 0){
							return this._many;
						}
						if(many){
							this.values = [null, null]
						}
						this._many = many;
					},					
					other : function(other){
						if(arguments.length == 0){
							return this._other;
						}
						if(other){
							this.prevType = this.type;
							this.type = 'other';
							this.each(function(value){
								value.prevType = value.type;
								value.type = 'other'
							});
						}
						else{
							this.type = this.prevType;
							this.each(function(value){
								value.type = value.prevType;
							});
						}
						this._other = other;
					},
					getAjaxData : function(){
						var other = this.other();
						return {
							values : this.map(function(value){
								if(other){
									return value.value.getAjaxData(true);
								}
								return value.value;
							}),
							many : this.many(),
							other : other,
							type : this.type
						};
					}
				});

				c.Value = createClass({
					values : null,
					_init : function(values){
						this.values = values;
					},
					label : null,
					getLabel : function($index){
						if(this.label){
							return this.label;
						}
						var s = 'Занчение';
						if(!this.values.many()){
							return s;
						}
						return s + ' №' + ($index + 1);
					},
					type : null,
					value : null
				});

				c.Action = createClass({
					attribute : null,
					data : null,
					_init : function(data){
						$.extend(this, data, {
							values : new c.Values()
						});
						this.data = data;
					},
					setAttribute : function(attribute){
						this.attribute = attribute;
						return this;
					},
					activate : function(){
						this.values
							.setAction(this)
							.init(this.data.value);
					},
					getAjaxData : function(){
						return {
							id : this.id,
							name : this.name,
							values : this.values.getAjaxData()
						};
					},
					canSend : function(){
						return true
					}
				});

				c.CompareAttribute = createClass(c.Attribute, {
					generalType : 'compare'
				});

				c.UpdateAttribute = createClass(c.Attribute, {
					generalType : 'update'
				});

				c.Example = createClass({
					_init : function(holders){
						this.holders = holders; // IBlockHolders
					},
					pager : function(iblock, delta){
						delta = parseInt(delta);
						iblock.page += delta;
						this.update();
					},
					update : function(){
						var me = this;
						me.query({
							get : 'example'
						}, function(result){
							var tables = [];
							$.each(result.example.tables, function(i, table){
								tables.push(new c.ExampleTable(table))
							});
							me.tables = tables;
						});
					},
					query : function(data, callback){
						this.holders.sendQuery(data, callback);
					},
					avaliable : function(){
						return this.holders.canSend();
					},
					any : function(){
						return (this.tables && this.tables.length);
					},
					getTables : function(){
						return this.tables;
					},
					_amountTimeout : null,
					updateAmount : function(){
						clearTimeout(this._amountTimeout);
						var me = this;
						this._amountTimeout = $timeout(function(){
							me.query({
								get : 'amount'
							}, function(result){
								me.amount = result.amount;
							})
						}, 500);
					},
					amount : 0
				});

				c.ExampleTable = createClass({
					_init : function(data){
						$.extend(this, data);

						var elements = [];
						$.each(this.elements, function(i, element){
							elements.push(new c.ExampleTableRow(element));
						})
						this.elements = elements;

						var rows = [];
						$.each(this.rows, function(i, cell){
							rows.push(new c.ExampleTableCell(cell.name));
						});
						this.rows = rows;
					},
					getTotalElements : function(){
						return this.totalElements;
					},
					getRows : function(){
						return this.elements;
					},
					getHeads : function(){
						return this.rows;
					},
					getTotal : function(){
						return this.total;
					}
				})

				c.ExampleTableRow = createClass({
					_init : function(data){
						var cells = [];
						$.each(data, function(i, cell){
							cells.push(new c.ExampleTableCell(cell));
						});
						this.cells = cells;
					},
					getCells : function(){
						return this.cells;
					}
				})

				c.ExampleTableCell = createClass({
					_init : function(data){
						this._content = data;
					},
					content : function(){
						return this._content;
					}
				})

				var ajax = function(data){
					$scope.loading = true;
					var success;
					if(data._success){
						success = data._success;
						delete data._success;
					}

					$http
						.post('?ajax=1', data)
						.success(function(response){
							$scope.loading = false;
							console.log(response);
							success && success(response);
						});
				};

				$scope.gs = {
					getterSetter: true
				};

				$scope.iblockHolders = new c.IBlockHolders();
				$scope.iblockHolders.addHolder();
				$scope.example = (new c.Example($scope.iblockHolders));

				$(window).keydown(function(e){
					if(!e.ctrlKey){
						return;
					}

					switch(e.keyCode){
						case 37: // left
						case 39: // right
							$scope.iblockHolders.each(function(holder){
								$scope.example.pager(holder.iblock(), e.keyCode == 37 ? -1 : 1);
							});
							break;

						case 13: // enter
							$scope.example.update();
							break;

						default:
							return;
					}
					e.preventDefault();
				})
			}
		]);
})(jQuery);