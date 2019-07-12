/**
 * Class for Web SQL Database
 * @param params
 * @constructor
 */

;
(function (window)
{
	if (window.BX.dataBase) return;

	var BX = window.BX;

	/**
	 * Parameters description:
	 * version - version of the database
	 * name - name of the database
	 * displayName - display name of the database
	 * capacity - size of the database in bytes.
	 * @param params
	 */
	BX.dataBase = function (params)
	{
		this.tableList = [];
		if(typeof window.openDatabase != 'undefined')
			this.dbObject = window.openDatabase(params.name, params.version, params.displayName, params.capacity);
	};


	BX.dataBase.prototype.isTableExists = function (tableName, callback)
	{
		var that = this;
		var tableListCallback = function ()
		{
			var length = that.tableList.length;
			for (var i = 0; i < length; i++)
			{
				if (that.tableList[i].toUpperCase() == tableName.toUpperCase())
				{
					callback(true);
					return;
				}
			}

			callback(false);
		};

		if (this.tableList.length <= 0)
			this.getTableList(tableListCallback);
		else
			tableListCallback();

	};

	/**
	 * Takes the list of existing tables from the database
	 * @param callback The callback handler will be invoked with boolean parameter as a first argument
	 * @example
	 */
	BX.dataBase.prototype.getTableList = function (callback)
	{
		var that = this;
		var callbackFunc = callback;
		this.query(
			{
				query: "SELECT tbl_name from sqlite_master WHERE type = 'table'",
				values: {}
			},
			function (res)
			{
				if (res.count > 0)
				{
					for (var i = 0; i < res.items.length; i++)
						that.tableList[that.tableList.length] = res.items[i].tbl_name;
				}

				if (callbackFunc != null && typeof (callbackFunc) == "function")
					callbackFunc(that.tableList)
			}
		);
	};

	/**
	 * Creates the table in the database
	 * @param params
	 */
	BX.dataBase.prototype.createTable = function (params)
	{
		params.action = "create";
		if (params.success)
		{
			var userSuccessCallback = params.success;
			params.success = function (result)
			{
				userSuccessCallback(result);
				this.getTableList();
			}
		}
		var str = this.getQuery(params);
		this.query(str, params.success, params.fail);
	};

	/**
	 * Drops the table from the database
	 * @param params
	 */
	BX.dataBase.prototype.dropTable = function (params)
	{
		params.action = "drop";
		if(params.success)
		{
			var userSuccessCallback = params.success;
			params.success = function(result)
			{
				userSuccessCallback(result);
				this.getTableList();
			}
		}
		var str = this.getQuery(params);
		this.query(str, params.success, params);
	};

	/**
	 * Drops the table from the database
	 * @param params
	 */
	BX.dataBase.prototype.addRow = function (params)
	{
		params.action = "insert";
		this.query(
			this.getQuery(params),
			params.success,
			params.fail
		);
	};

	/**
	 * Gets the data from the table
	 * @param params
	 */
	BX.dataBase.prototype.getRows = function (params)
	{
		params.action = "select";
		this.query(
			this.getQuery(params),
			params.success,
			params.fail
		);
	};

	/**
	 * Updates the table
	 * @param params
	 */
	BX.dataBase.prototype.updateRows = function (params)
	{
		params.action = "update";
		var queryData = this.getQuery(params);
		this.query(queryData, params.success, params);
	};

	/**
	 * Deletes rows from the table
	 * @param params
	 */
	BX.dataBase.prototype.deleteRows = function (params)
	{
		params.action = "delete";
		var str = this.getQuery(params);
		this.query(str, params.success, params);
	};

	/**
	 * Builds the query string and the set of values.
	 * @param params
	 * @returns {{query: string, values: Array}}
	 */
	BX.dataBase.prototype.getQuery = function (params)
	{
		var values = [];
		var where = params.filter;
		var select = params.fields;
		var insert = params.insertFields;
		var set = params.updateFields;
		var tableName = params.tableName;
		var strQuery = "";

		switch (params.action)
		{
			case "delete":
			{
				strQuery = "DELETE FROM " + tableName.toUpperCase() + " " + this.getFilter(where);
				values = this.getValues([where]);
				break;
			}

			case "update":
			{
				strQuery = "UPDATE " + tableName.toUpperCase() + " " + this.getFieldPair(set, "SET ") + " " + this.getFilter(where);
				values = this.getValues([set, where]);
				break;
			}

			case "create":
			{
				var fieldsString = "";
				if (typeof(select) == "object")
				{
					var field = "";
					for (var j = 0; j < select.length; j++)
					{
						field = "";
						if (typeof(select[j]) == "object")
						{
							if (select[j].name)
							{

								field = select[j].name;
								if (select[j].unique && select[j].unique == true)
									field += " unique";
							}

						}
						else if (typeof(select[j]) == "string" && select[j].length > 0)
							field = select[j];

						if (field.length > 0)
						{

							if (fieldsString.length > 0)
								fieldsString += "," + field.toUpperCase();
							else
								fieldsString = field.toUpperCase();
						}
					}
				}

				strQuery = "CREATE TABLE IF NOT EXISTS " + tableName.toUpperCase() + " (" + fieldsString + ") ";
				break;
			}

			case "drop":
			{
				strQuery = "DROP TABLE IF EXISTS " + tableName.toUpperCase();
				break;
			}
			case "select":
			{
				strQuery = "SELECT " + this.getValueArrayString(select, "*") + " FROM " + tableName.toUpperCase() + " " + this.getFilter(where);
				values = this.getValues([where]);
				break;
			}
			case "insert":
			{
				values = this.getValues([insert]);
				strQuery = "INSERT INTO " + tableName.toUpperCase() + " (" + this.getKeyString(insert) + ") VALUES(%values%)";
				var valueTemplate = "";
				for (var i = 0; i < values.length; i++)
				{
					if (valueTemplate.length > 0)
						valueTemplate += ",?";
					else
						valueTemplate = "?"
				}

				strQuery = strQuery.replace("%values%", valueTemplate);

				break;
			}
		}

		return {
			query: strQuery,
			values: values
		}
	};


	/**
	 * Gets pairs for query string
	 * @param {object} fields The object with set of key-value pairs
	 * @param {string} operator The keyword that will be join on the beginning of the string
	 * @returns {string}
	 */
	BX.dataBase.prototype.getFieldPair = function (fields, operator)
	{
		var pairsRow = "";
		var keyWord = operator || "";

		if (typeof(fields) == "object")
		{
			var i = 0;
			for (var key in fields)
			{
				var pair = ((i > 0) ? ", " : "") + (key.toUpperCase() + "=" + "?");
				if (pairsRow.length == 0 && keyWord.length > 0)
					pairsRow = keyWord;
				pairsRow += pair;
				i++;
			}
		}

		return pairsRow;
	};

	BX.dataBase.prototype.getFilter = function (fields)
	{
		var pairsRow = "";
		var keyWord = "WHERE ";

		if (typeof(fields) == "object")
		{
			var i = 0;
			for (var key in fields)
			{
				var pair = "";
				var count = 1;
				if (typeof(fields[key]) == "object")
					count = fields[key].length;
				for (var j = 0; j < count; j++)
				{
					pair = ((j > 0) ? pair + " OR " : "(") + (key.toUpperCase() + "=" + "?");
					if ((j + 1) == count)
						pair += ")"
				}
				;

				pairsRow += pair;
				i++;
			}
		}
		return "WHERE " + pairsRow;
	};

	/**
	 * Gets the string with keys of fields that have splitted by commas
	 * @param fields
	 * @param defaultResult
	 * @returns {string}
	 */
	BX.dataBase.prototype.getKeyString = function (fields, defaultResult)
	{
		var result = "";
		if (!defaultResult)
			defaultResult = "";
		if (typeof(fields) == "array")
		{
			for (var i = 0; i < valuesItem.length; i++)
			{

				if (result.length > 0)
					result += "," + valuesItem[i].toUpperCase();
				else
					result = valuesItem[i].toUpperCase();
			}
		}
		else if (typeof(fields) == "object")
		{
			for (var key in fields)
			{
				if (result.length > 0)
					result += "," + key.toUpperCase();
				else
					result = key.toUpperCase();
			}
		}

		if (result.length == 0)
			result = defaultResult;

		return result;
	};

	/**
	 * Gets the string with values of the array that have splitted by commas
	 * @param fields
	 * @param defaultResult
	 * @returns {string}
	 */
	BX.dataBase.prototype.getValueArrayString = function (fields, defaultResult)
	{
		var result = "";
		if (!defaultResult)
			defaultResult = "";
		if (typeof(fields) == "object")
		{
			for (var i = 0; i < fields.length; i++)
			{

				if (result.length > 0)
					result += "," + fields[i].toUpperCase();
				else
					result = fields[i].toUpperCase();
			}
		}


		if (result.length == 0)
			result = defaultResult;

		return result;
	};

	/**
	 * Gets the array of values
	 * @param values
	 * @returns {Array}
	 */
	BX.dataBase.prototype.getValues = function (values)
	{
		var resultValues = [];
		for (var j = 0; j < values.length; j++)
		{
			var valuesItem = values[j];

			if (typeof(valuesItem) == "object")
			{
				for (var keyField in valuesItem)
				{
					if (typeof(valuesItem[keyField]) != "object")
						resultValues[resultValues.length] = valuesItem[keyField];
					else
						for (var i = 0; i < valuesItem[keyField].length; i++)
						{
							resultValues[resultValues.length] = valuesItem[keyField][i];
						}
				}
			}
			else if (typeof(valuesItem) == "array")
			{
				for (var i = 0; i < valuesItem.length; i++)
				{
					if (typeof(valuesItem[i]) != "object")
						resultValues[resultValues.length] = valuesItem[i];
				}
			}
		}


		return resultValues;
	};

	/**
	 * Executes the query
	 * @param success The success callback
	 * @param fail The failture callback
	 * @returns {string}
	 * @param query
	 */
	BX.dataBase.prototype.query = function (query, success, fail)
	{
		if (!this.dbObject)
		{
			return;
		}
		// console.log(query);
		if(typeof success =='undefined' || typeof success != 'function')
			success = function(){};
		if (typeof fail == 'undefined' || typeof fail != 'function')
			fail = function(){};
		this.dbObject.transaction(
			function (tx)
			{
				tx.executeSql(
					query.query,
					query.values,
					function (tx, results)
					{

						var result = {
							originalResult: results
						};

						var len = results.rows.length;
						if (len >= 0)
						{
							result.count = len;
							result.items = [];

							for (var i = 0; i < len; i++)
							{
								var item = {};
								var dbItem = results.rows.item(i);
								for (var key in dbItem)
								{
									if (dbItem.hasOwnProperty(key))
									{
										item[key] = dbItem[key];
									}
								}
								result.items.push(item);
							}
						}

						if (success != null)
							success(result, tx);
					},
					function (tx, res)
					{
						if (fail != null)
							fail(res, tx);
					}
				);
			}
		);
	};

	/**
	 * Gets the beautifying result from the query response
	 * @param results
	 * @returns {*}
	 */

	BX.dataBase.prototype.getResponseObject = function (results)
	{

		var len = results.rows.length;

		var result = [];
		for (var i = 0; i < len; i++)
		{
			result[result.length] = results.rows.item(i);
		}

		return result;
	};

})(window);