(function () {
	window.tableEditor = function (raw_table, editor_element)
	{
		this.cols = [];
		this.rows = [];
		this.editor = editor_element;
		this.currentToolbar = null;

		this.parse(raw_table);
		this.render();
	};

	tableEditor.prototype.parse = function (src)
	{
		var align = src.match(/\[rltable\s*=\s*([lrc]+)\]/);

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

	tableEditor.prototype.getCell = function (content = '', blank, align = 'left')
	{
		var cell = document.createElement('td');

		var input = document.createElement('input');
		input.type = 'text';
		input.value = content;

		if (blank)
		{
			input.onkeyup = this.splitRow.bind(this);
			cell.colSpan = this.cols.length;
			cell.classList.toggle('blank-cell');
		}
		else
		{
			input.onkeyup = this.reduceRow.bind(this);
			input.onfocus = this.showToolbar.bind(this);
		}

		cell.appendChild(input);
		cell.style.textAlign = align;
		return cell;
	};

	tableEditor.prototype.render = function ()
	{
		for (var i = 0; i < this.rows.length; i++)
		{
			var row = document.createElement('tr');

			if (this.rows[i].length <= 1)
			{
				row.appendChild(this.getCell('', true));
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

	tableEditor.prototype.reduceRow = function (e)
	{
		var row = e.target.parentNode.parentNode;
		var cells = row.querySelectorAll('td > input');

		var str = '';

		for (var i = 0; i < cells.length; i++)
		{
			str += cells[i].value;
		}

		if (str.replace(/^\s+|\s+$/g, '') == '')
		{
			this.closeToolbar();
			while (row.firstChild) {
				row.removeChild(row.firstChild);
			}

			row.appendChild(this.getCell('', true));
			row.firstChild.firstChild.focus();
		}

		this.listenKeys(e);

		return true;
	};

	tableEditor.prototype.splitRow = function (e)
	{
		if (e.target.value != '')
		{
			var cell = e.target.parentNode;
			cell.colSpan = null;
			cell.classList.toggle('header');

			var row = cell.parentNode;

			row.appendChild(this.getCell(e.target.value));
			row.removeChild(cell);

			for (var i = 1; i < this.cols.length; i++)
			{
				row.appendChild(this.getCell('', false, this.cols[i] == 'l' ? 'left' : 'right'));
			}

			row.firstChild.firstChild.focus();
		}

		this.listenKeys(e);
		return true;
	};

	tableEditor.prototype.listenKeys = function (e)
	{
		if (e.key == 'Enter')
		{
			var new_row = document.createElement('tr');
			new_row.appendChild(this.getCell('', true));
			this.editor.insertBefore(new_row, e.target.parentNode.parentNode.nextSibling);
			new_row.firstChild.firstChild.focus();
		}
		else if (e.key == 'Backspace' && e.target.parentNode.classList.contains('blank-cell'))
		{
			if (prev = e.target.parentNode.parentNode.previousSibling.querySelector('input'))
			{
				prev.focus();
			}

			this.closeToolbar();
			this.editor.removeChild(e.target.parentNode.parentNode);
		}
		else if (e.key == 'Backspace' && e.target.value == '')
		{
			if (prev = e.target.parentNode.parentNode.previousSibling.querySelector('input'))
			{
				prev.focus()
			}
		}
	};

	tableEditor.prototype.removeColumn = function (e) {
		if (this.rows == 1)
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
				rows[i].firstChild.colSpan = Math.min(1, rows[i].firstChild.colSpan - 1);
			}
			else
			{
				rows[i].removeChild(rows[i].childNodes[index]);
			}
		}
	};

	tableEditor.prototype.removeRow = function (e) {
		var index = e.target.parentNode.parentNode.cellIndex;
		var row = e.target.parentNode.parentNode.parentNode;

		if (prev = row.previousSibling.childNodes[index].querySelector('input'))
		{
			prev.focus();
		}

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
			if (rows[i].childNodes.length == 1)
			{
				rows[i].firstChild.colSpan = rows[i].firstChild.colSpan + 1;
			}
			else
			{
				rows[i].insertBefore(this.getCell('', false, cell.style.textAlign), rows[i].childNodes[index].nextSibling);
			}
		}
	};

	tableEditor.prototype.addRow = function (e) {
		var row = e.target.parentNode.parentNode.parentNode;

		var new_row = document.createElement('tr');
		new_row.appendChild(this.getCell('', true));

		row.parentNode.insertBefore(new_row, row.nextSibling)
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
	};

	tableEditor.prototype.showToolbar = function (e)
	{
		this.closeToolbar();
		this.currentToolbar = this.getToolbar();
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

	tableEditor.prototype.getToolbar = function ()
	{
		var toolbar = document.createElement('div');
		toolbar.className = 'toolbar';

		toolbar.appendChild(this.getButton('drop-column', 'Supprimer colonne', this.removeColumn));
		toolbar.appendChild(this.getButton('insert-column', 'Ajouter colonne après celle-ci', this.addColumn));
		toolbar.appendChild(this.getButton('drop-row', 'Supprimer ligne', this.removeRow));
		toolbar.appendChild(this.getButton('insert-row', 'Ajouter ligne après celle-ci', this.addRow));
		toolbar.appendChild(this.getButton('header', 'Ligne en gras', this.switchHeader));
		toolbar.appendChild(this.getButton('align-left', 'Aligner colonne à gauche', this.switchLeft));
		toolbar.appendChild(this.getButton('align-right', 'Aligner colonne à droite', this.switchRight));
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