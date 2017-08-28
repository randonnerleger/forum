(function () {
	window.tableEditor = function (raw_table, editor_element)
	{
		this.cols = [];
		this.rows = [];
		this.editor = editor_element;
		this.currentToolbar = null;
		this.alignments = {'l': 'left', 'r': 'right', 'c': 'center'};

		this.parse(raw_table);
		this.render();
	};

	tableEditor.prototype.parse = function (src)
	{
		var align = src.match(/\[rltable\s*=\s*([lrc]+)\]/);

		src = src.replace(/\s*\[\/rltable\]\s*/, '');

		if (!align)
		{
			return;
		}

		this.cols = align[1].split('');

		src = src.substr(src.indexOf(align[0])+align[0].length);

		this.rows = src.replace(/\r/g, '').replace(/^\s+|\s+$/g,'').split("\n");

		for (var i = 0; i < this.rows.length; i++)
		{
			this.rows[i] = this.rows[i].split('|');

			for (var j = 0; j < this.rows[i].length; j++)
			{
				// trim
				this.rows[i][j] = this.rows[i][j].replace(/^\s+|\s+$/g, '');
			}
		}
	};

	tableEditor.prototype.getCell = function (content = '', merged, align = 'l')
	{
		var cell = document.createElement('td');

		var input = document.createElement('input');
		input.type = 'text';
		input.value = content;
		input.onkeyup = this.listenKeys.bind(this);
		input.onfocus = this.showToolbar.bind(this);

		if (merged)
		{
			cell.colSpan = this.cols.length;
			cell.classList.add(content != '' ? 'mono-cell' : 'blank-cell');
		}
		else
		{
			cell.style.textAlign = this.alignments[align.substr(0, 1)];
		}

		cell.appendChild(input);
		return cell;
	};

	tableEditor.prototype.render = function ()
	{
		for (var i = 0; i < this.rows.length; i++)
		{
			var row = document.createElement('tr');

			if (this.rows[i].length <= 1)
			{
				row.appendChild(this.getCell(this.rows[i][0], true));
			}
			else
			{
				for (var j = 0; j < this.cols.length; j++)
				{
					var cell_value = this.rows[i][j];

					if (j == 0 && cell_value.substr(0, 1) == '^')
					{
						row.classList.toggle('header');
						cell_value = cell_value.substr(1).replace(/^\s+/g, '');
					}

					row.appendChild(this.getCell(cell_value, false, this.cols[j] == 'l' ? 'left' : 'right'));
				}
			}

			this.editor.appendChild(row);
		}

		// Free some memory
		this.rows = null;
	};

	tableEditor.prototype.export = function ()
	{
		var out = [];

		out.push('[rltable=' + this.cols.join('') + ']');

		var rows = this.editor.rows;

		for (var i = 0; i < rows.length; i++)
		{
			var cols = [];
			var line = '';

			for (var j = 0; j < rows[i].cells.length; j++)
			{
				cols.push(rows[i].cells[j].firstChild.value);
			}

			if (rows[i].classList.contains('header'))
			{
				line += '^ ';
			}

			line += cols.join(' | ');

			out.push(line);
		}

		out.push('[/rltable]');

		return out.join("\n");
	};

	tableEditor.prototype.listenKeys = function (e)
	{
		var input = e.target;
		var cell = input.parentNode;
		var row = cell.parentNode;
		var column_index = cell.cellIndex;

		if (e.key == 'Enter')
		{
			// Add a new row on hitting enter
			return this.addRow(cell);
		}
		else if (e.key == "Down" || e.key == "ArrowDown" || e.key == "Up" || e.key == "ArrowUp")
		{
			// Switch to next/previous row if possible
			var next_row = (e.key == "Down" || e.key == "ArrowDown") ? row.nextSibling : row.previousSibling;

			if (!next_row)
			{
				return false;
			}

			var new_index = (next_row.cells.length > column_index) ? column_index : 0;
			next_row.cells[new_index].firstChild.focus();
			return false;
		}
		else if (e.key == 'Backspace' && input.value == '')
		{
			if (cell.classList.contains('mono-cell'))
			{
				// Switch to blank cell
				cell.classList.add('blank-cell');
				cell.classList.remove('mono-cell');
				return false;
			}

			// Delete current row if all cells are empty
			if (cell.colSpan == 1)
			{
				var inputs = row.querySelectorAll('input[type=text]');

				for (var i = 0; i < inputs.length; i++)
				{
					if (inputs[i].value != '')
					{
						// If one column is not empty, don't delete the row
						return false;
					}
				}
			}

			// Delete row
			this.removeRow(cell);

			return false;
		}
		else if (cell.colSpan > 1)
		{
			if (input.value != '')
			{
				cell.classList.remove('blank-cell');
				cell.classList.add('mono-cell');
			}
			else
			{
				cell.classList.add('blank-cell');
				cell.classList.remove('mono-cell');
			}
		}
	};

	tableEditor.prototype.removeColumn = function (e) {
		if (this.cols.length == 1)
		{
			return !alert("Ne peut pas supprimer la dernière colonne !");
		}

		var cell = e.target.parentNode.parentNode;
		var index = cell.cellIndex;

		if (index > 0 && (prev = cell.parentNode.childNodes[index-1].querySelector('input')))
		{
			prev.focus();
		}

		var rows = this.editor.querySelectorAll('tr');

		// Update cols list
		this.cols.splice(index, 1);

		for (var i = 0; i < rows.length; i++)
		{
			if (rows[i].childNodes.length == 1)
			{
				rows[i].firstChild.colSpan = this.cols.length;
			}
			else
			{
				rows[i].removeChild(rows[i].childNodes[index]);
			}
		}
	};

	tableEditor.prototype.removeRow = function (cell) {
		var index = cell.cellIndex;
		var row = cell.parentNode;

		var next_row = row.previousSibling || row.nextSibling;

		if (!next_row)
		{
			// You can't delete the last row of the table!
			return false;
		}

		var new_index = (next_row.cells.length > index) ? index : 0;
		next_row.cells[new_index].firstChild.focus();

		row.parentNode.removeChild(row);
	};

	tableEditor.prototype.addColumn = function (e) {
		var cell = e.target.parentNode.parentNode;
		var index = cell.cellIndex;

		var rows = this.editor.querySelectorAll('tr');

		// Update cols list
		this.cols.splice(index, 0, cell.style.textAlign.substr(0, 1));

		for (var i = 0; i < rows.length; i++)
		{
			if (rows[i].firstChild.classList.contains('blank-cell') || rows[i].firstChild.classList.contains('mono-cell'))
			{
				rows[i].firstChild.colSpan = rows[i].firstChild.colSpan + 1;
			}
			else
			{
				rows[i].insertBefore(this.getCell('', false, cell.style.textAlign), rows[i].childNodes[index].nextSibling);
			}
		}
	};

	tableEditor.prototype.addRow = function (cell) {
		var row = cell.parentNode;

		var new_row = document.createElement('tr');

		for (var i = 0; i < this.cols.length; i++)
		{
			new_row.appendChild(this.getCell('', false, this.cols[i]));
		}

		row.parentNode.insertBefore(new_row, row.nextSibling)
		new_row.cells[cell.cellIndex].firstChild.focus();
	};

	tableEditor.prototype.mergeRow = function (e) {
		var cell = e.target.parentNode.parentNode;
		var row = cell.parentNode;
		var cells = row.querySelectorAll('td > input');

		var str = '';

		for (var i = 0; i < cells.length; i++)
		{
			str += ' ' + cells[i].value;
			row.removeChild(cells[i].parentNode);
		}

		str = str.replace(/^\s+|\s+$/g, '');

		var new_cell = this.getCell(str, true);
		row.appendChild(new_cell);
		new_cell.firstChild.focus();
	};

	tableEditor.prototype.splitRow = function (e) {
		var cell = e.target.parentNode.parentNode;
		var row = cell.parentNode;
		cell.colSpan = 1;
		cell.classList.remove('mono-cell', 'blank-cell');

		row.appendChild(this.getCell(cell.firstChild.value, false, this.cols[0]));
		row.removeChild(cell);

		for (var i = 1; i < this.cols.length; i++)
		{
			row.appendChild(this.getCell('', false, this.cols[i]));
		}

		row.firstChild.firstChild.focus();
	};

	tableEditor.prototype.switchHeader = function (e) {
		var row = e.target.parentNode.parentNode.parentNode;
		row.classList.toggle('header');
	};

	tableEditor.prototype.switchLeft = function (e) {
		return this.switchAlign('left', e);
	};

	tableEditor.prototype.switchRight = function (e) {
		return this.switchAlign('right', e);
	};

	tableEditor.prototype.switchCenter = function (e) {
		return this.switchAlign('center', e);
	};

	tableEditor.prototype.switchAlign = function (align, e) {
		var index = e.target.parentNode.parentNode.cellIndex;

		var rows = this.editor.querySelectorAll('tr');

		for (var i = 0; i < rows.length; i++)
		{
			if (rows[i].childNodes.length > index)
			{
				rows[i].childNodes[index].style.textAlign = align;
			}
		}

		this.cols[index] = align.substr(0, 1);
		return false;
	};

	tableEditor.prototype.showToolbar = function (e)
	{
		this.closeToolbar();
		this.currentToolbar = this.getToolbar(e.target.parentNode);
		e.target.parentNode.appendChild(this.currentToolbar);
	};

	tableEditor.prototype.closeToolbar = function (e)
	{
		if (this.currentToolbar)
		{
			this.currentToolbar.parentNode.removeChild(this.currentToolbar);
			this.currentToolbar = null;
		}
	};

	tableEditor.prototype.getToolbar = function (cell)
	{
		var toolbar = document.createElement('div');
		toolbar.className = 'toolbar';
		var merged_row = cell.classList.contains('mono-cell') || cell.classList.contains('blank-cell');

		if (merged_row)
		{
			toolbar.appendChild(this.getButton('split-row', 'Séparer cellule', this.splitRow));
		}
		else
		{
			toolbar.appendChild(this.getButton('merge-row', 'Fusionner cellules de la ligne', this.mergeRow));
		}

		toolbar.appendChild(this.getButton('insert-row', 'Ajouter ligne après celle-ci', function (e) { return this.addRow(e.target.parentNode.parentNode); }));
		toolbar.appendChild(this.getButton('drop-row', 'Supprimer ligne', function (e) { return this.removeRow(e.target.parentNode.parentNode); }));

		if (!merged_row)
		{
			toolbar.appendChild(this.getButton('insert-column', 'Ajouter colonne après celle-ci', this.addColumn));
			toolbar.appendChild(this.getButton('drop-column', 'Supprimer colonne', this.removeColumn));
			toolbar.appendChild(this.getButton('header', 'Ligne en gras', this.switchHeader));
			toolbar.appendChild(this.getButton('align-left', 'Aligner colonne à gauche', this.switchLeft));
			toolbar.appendChild(this.getButton('align-center', 'Aligner colonne au centre', this.switchCenter));
			toolbar.appendChild(this.getButton('align-right', 'Aligner colonne à droite', this.switchRight));
		}

		return toolbar;
	};

	tableEditor.prototype.getButton = function (class_name, title, callback)
	{
		var btn = document.createElement('input');
		btn.type = 'button';
		btn.title = btn.value = title;
		btn.className = class_name;
		btn.onclick = callback.bind(this);
		btn.tabIndex = -1;
		return btn;
	};
})();