var jsCSESettings = {
	arData: null,
	arDefaultFields: null,
	$arRows: {},
	obForm: null,
	$table: null,
	$info: null,

	init: function(arFieldData, arDefaultFields, $table, $info) 
	{
		BX.loadCSS('/bitrix/components/citrus/realty.send_event/templates/.default/style.css');
		BX.loadScript('/bitrix/components/citrus/realty.send_event/settings/jquery.tablednd_0_5.js');
	
		this.arData = arFieldData;
		this.arDefaultFields = arDefaultFields;
		this.obForm = document.forms['bx_popup_form_cse_form'];
		this.obForm.onsubmit = this.__saveChanges;
		
		this.$table = $table;
		this.$info= $info;
		
		var idx = 0;
		for (var field in this.arData)
		{
			arField = this.arData[field];
			$table.append(
				'<tr>' +
					'<td class="bx-popup-label" title="' + field + '">' + this.arDefaultFields[field].ORIGINAL_TITLE + ': </td>' +
					'<td><input type="text" size="20" value="' + BX.util.htmlspecialchars(arField.TITLE ? arField.TITLE : '') + '" class="title" /></td>' +
					'<td align="center"><input type="checkbox" value="1" class="required"' + (arField.IS_REQUIRED ? ' checked="checked"' : '') + ' /></td>' +
					'<td><input type="text" size="25" value="' + BX.util.htmlspecialchars(arField.TOOLTIP ? arField.TOOLTIP : '') + '" class="tooltip" /></td>' +
					'<td align="center"><input type="checkbox" value="1" class="email"' + (arField.IS_EMAIL ? ' checked="checked"' : '') + ' /></td>' +
					'<td align="center"><a href="javascript:void(0);" class="cse-link-delete" onclick="jsCSESettings.deleteField(this); return false;">' + BX.message('cse_delete') + '</a></td>' +
				'</tr>'
			);
			this.$arRows[field] = $table.find('tr:last');
			if (arField.ACTIVE === false)
				this.$arRows[field].hide();
			this.$arRows[field].data({
				'arField' : arField,
				'field' : field
			});
		}
		this.arData = null;
		
		this.__updateAddLink();
	},
	
	__getAvailableFields: function()
	{
		var _this = this;
		var _availableFields = {};
		this.$table.find('tr').each(function (index, value) {
			$row = $(value);
			data = $row.data();
			if (data.field && data.arField && data.arField.ACTIVE === false)
				_availableFields[data.field] = $row;
		});
		return _availableFields;
	},
	
	__updateAddLink: function()
	{
		var bFieldsAvailable = false, _availableFields = this.__getAvailableFields();
		for (var propertyName in _availableFields) {
			bFieldsAvailable = true;
			break;
		}
		if (bFieldsAvailable)
		{
			this.$info.html(
				'<dl class="cse-dropdown">' +
					'<dt><a href="javascript:void(0);" class="cse-action" onclick="jsCSESettings.showAddFields(); return false;">' + BX.message('cse_add_field') + '</a></dt>' +
					'<dd><ul style="display: none;"></ul></dd>' + 
				'</dl>' 
			);
			this.$info.find(".cse-dropdown dt a").click(function() {
				$(".cse-dropdown dd ul").toggle();
			});
			
			// fill list
			$ul = $(".cse-dropdown dd ul");
			for (field in _availableFields)
			{
				$ul.append('<li class="' + field + '"><a href="javascript:void(0);" onclick="jsCSESettings.addField(\'' + field + '\')" class="cse-action">' + this.arDefaultFields[field].ORIGINAL_TITLE + '</a></li>');
			}
		}			
		else
			this.$info.html('<p>' + BX.message('cse_no_fields_to_add') + '</p>');
	},
	
	showAddFields: function()
	{
		var _availableFields = this.__getAvailableFields(), _fields = {};
		for (var field in _availableFields)
			_fields[field] = this.arDefaultFields[field].ORIGINAL_TITLE;
//		console.log(_fields);
		return false;
	},
	
	deleteField: function(obj)
	{
		$row = $(obj).parents('tr'); 
		data = $row.data('arField');
		data.ACTIVE = false;
		// update data and remove DOM element
		$row.data('arField', data).hide();
		this.__updateAddLink();
	},
	
	addField: function(field)
	{
		var _availableFields = this.__getAvailableFields();
		if ($row = _availableFields[field])
		{
			$contentInner = this.$table.parents('.content-inner');
			
			data = $row.data('arField');
			data.ACTIVE = true;
			// update data and remove DOM element
			$row.data('arField', data).show();
			// add row to the bottom of the table
			this.$table.append($row);

			// hide add field link
			this.__updateAddLink();
			/*
			this.$info.find('li.' + field).remove();
			
			$contentInner = this.$table.parents('.bx-core-dialog-content');
			$contentInner.scrollTop($contentInner[0].scrollHeight);
			
			// remove link if there is no more fields
			var cnt = 0;
			for (var field in _availableFields)
				++cnt;
			if (cnt == 1)
				this.__updateAddLink();*/
		}
	},

	__serialize: function(obj)
	{
		if (typeof(obj) == 'object')
		{
			var str = '', cnt = 0;
			for (var i in obj)
			{
				//if (this.__checkValidKey(i))
				//{
					++cnt;
					str += this.__serialize(i) + this.__serialize(obj[i]);
				//}
			}
			
			str = "a:" + cnt + ":{" + str + "}";
			
			return str;
		}
		else if (typeof(obj) == 'boolean')
		{
			return 'b:' + (obj ? 1 : 0) + ';';
		}
		else if (null == obj)
		{
			return 'N;'
		}
		else if (Number(obj) == obj && obj != '' && obj != ' ')
		{
			if (Math.floor(obj) == obj)
				return 'i:' + obj + ';';
			else
				return 'd:' + obj + ';';
		}
		else if(typeof(obj) == 'string')
		{
			obj = obj.replace(/\r\n/g, "\n");
			obj = obj.replace(/\n/g, "###RN###");

			var offset = 0;
			if (window._global_BX_UTF)
			{
				for (var q = 0, cnt = obj.length; q < cnt; q++)
				{
					if (obj.charCodeAt(q) > 127) offset++;
				}
			}
			
			return 's:' + (obj.length + offset) + ':"' + obj + '";';
		}
	},

	__saveChanges: function()
	{
		var _this = this;
		this.arData = {};
		this.$table.find('tr').each(function (index, value) {
			$row = $(value);
			data = $row.data();
			if (data.field && data.arField)
			{
				data.arField.TITLE = $row.find('.title').val();
				data.arField.TOOLTIP = $row.find('.tooltip').val();
				data.arField.IS_REQUIRED = $row.find('.required:checked').val() !== undefined;
				data.arField.IS_EMAIL = $row.find('.email:checked').val() !== undefined;
				 
				if (data.arField.ACTIVE !== false)
					_this.arData[data.field] = data.arField;
			}
		});
		window.jsCSESettingsOpener.saveData(this.__serialize(this.arData));
		return false;
	}
}
