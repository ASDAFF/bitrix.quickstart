function JCCatTblEdit(arParams)
{
	if (!arParams) return;
	
	this.intERROR = 0;
	this.PREFIX = arParams.PREFIX;
	this.PREFIX_TR = this.PREFIX+'ROW_';
	this.PROP_COUNT_ID = arParams.PROP_COUNT_ID;
	this.TABLE_PROP_ID = arParams.TABLE_PROP_ID;
	
	this.CELLS = [];
	this.CELL_CENT = [];

	BX.ready(BX.delegate(this.Init,this));
};

JCCatTblEdit.prototype.Init = function()
{
	this.PROP_TBL = BX(this.TABLE_PROP_ID);
	if (!this.PROP_TBL)
	{
		this.intERROR = -1;
		return;
	}
	this.PROP_COUNT = BX(this.PROP_COUNT_ID);
	if (!this.PROP_COUNT)
	{
		this.intERROR = -1;
		return;
	}
};

JCCatTblEdit.prototype.SetCells = function(arCells,arCenter)
{
	if (0 > this.intERROR)
		return;

	if (arCells)
		this.CELLS = BX.clone(arCells,true);
	for (var i = 0; i < this.CELLS.length; i++)
	{
		this.CELLS[i] = this.CELLS[i].replace(/PREFIX/ig, this.PREFIX);
	}
	if (arCenter)
		this.CELL_CENT = BX.clone(arCenter,true);
};

JCCatTblEdit.prototype.addRow = function()
{
	if (0 > this.intERROR)
		return;
	var i = 0;
	var id = parseInt(this.PROP_COUNT.value);

	var newRow = this.PROP_TBL.insertRow(this.PROP_TBL.rows.length);
	newRow.id = this.PREFIX_TR+id;
	for (i = 0; i < this.CELLS.length; i++)
	{
		var oCell = newRow.insertCell(-1);
		var typeHtml = this.CELLS[i];
		typeHtml = typeHtml.replace(/tmp_xxx/ig, id);
		oCell.innerHTML = typeHtml;
	}
	for (i = 0; i < this.CELL_CENT.length; i++)
	{
		var needCell = newRow.cells[this.CELL_CENT[i]-1];
		if (needCell)
		{
			needCell.setAttribute('align','center');
		}
	}

	this.PROP_COUNT.value = id + 1;
};