	String.prototype.matchAll = function(regexp) {
		var matches = [];
		this.replace(regexp, function() {
			var arr = ([]).slice.call(arguments, 0);
			var extras = arr.splice(-2);
			arr.index = extras[0];
			arr.input = extras[1];
			matches.push(arr);
		});
		return matches.length ? matches : null;
	};

(function () {
	var demo_new_table = "[rltable=llr]^Portage  |    |  1400\nSac à dos  |  Lowe Alpine 40L  |  1400\n\n^Couchage  |    |  3001\nTente (2 pers.)  |  Hubba Hubba NX  |  1700\nMatelas  |  Sea to Summit Ultralight Regular  |  392";

	var textarea = window.opener.document.querySelector('textarea');
	var selector = document.querySelector('select');
	var editor_element = document.querySelector('table');

	var current_table = null;
	var current_table_editor = null;

	initForm();

	document.querySelector('select').onchange = function (e) {
		var selected_idx = e.target.value;

		if (selected_idx == "null")
		{
			return false;
		}

		if (current_table != "null" && window.confirm("Valider les changements du tableau modifié avant de changer de tableau ?"))
		{
			saveCurrentTable();
		}

		editor_element.innerHTML = '';

		current_table = null;
		current_table_editor = null;

		if (selected_idx == "new")
		{
			createNewTable();
		}
		else if (selected_idx == "import")
		{
			showImportModal();
		}
		else
		{
			current_table = selected_idx;
			current_table_editor = new tableEditor(findTables()[selected_idx].content, editor_element);
		}
	};

	document.querySelector('input#saveBtn').onclick = function () {
		saveCurrentTable();
		self.close();
	};

	document.querySelector('input#cancelBtn').onclick = function () { self.close(); };

	document.getElementById('importModalBtn').onclick = function() {
		var txt = document.getElementById('importText').value;
		txt = txt.replace(/\t/g, ' | ');
		var nb_columns = 1;

		if (txt.match(/\|/))
		{
			var lines = txt.split("\n");

			// Find the maximum number of columns on a line
			for (var i = 0; i < lines.length; i++)
			{
				if (lines[i].match(/\|/))
				{
					nb_columns = Math.max(nb_columns, lines[i].match(/\|/g).length);
				}
			}
		}

		txt = "[rltable=" + "l".repeat(nb_columns) + "]\n" + txt + "\n[/rltable]";

		current_table = "new";
		current_table_editor = new tableEditor(txt, editor_element);

		document.getElementById('importModal').classList.add('hidden');
	};

	function initForm()
	{
		var tables = findTables();

		if (tables.length == 0)
		{
			current_table = "new";
		}
		// Try to use the table where the cursor is
		else if ((textarea.selectionStart || textarea.selectionStart === 0) && textarea.selectionStart == textarea.selectionEnd)
		{
			// Check if the cursor is in a table
			var prev_start = textarea.value.substr(0, textarea.selectionStart).lastIndexOf('[rltable');
			var prev_end = textarea.value.substr(0, textarea.selectionStart).lastIndexOf('[/rltable');

			var next_end = textarea.value.substr(textarea.selectionEnd).indexOf('[/rltable');
			var next_start = textarea.value.substr(textarea.selectionEnd).indexOf('[rltable');

			// There should be an opening tag before, but not followed by a closing tag
			// and a closing tag after, but not preceded by an opening tag
			if (prev_start >= 0 && prev_end < prev_start && next_end >= 0 && (next_start > next_end || next_start < 0))
			{
				next_end += textarea.selectionEnd + '[/rltable]'.length;

				for (var i = 0; i < tables.length; i++)
				{
					var t = tables[i];

					if (t.start == prev_start && t.end == next_end)
					{
						current_table = i;
						current_table_editor = new tableEditor(t.content, editor_element);
						break;
					}
				}
			}
		}
		// Try to get the currently selected table
		else if (textarea.selectionStart || textarea.selectionStart === 0)
		{
			var c = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
			var start = c.indexOf('[rltable');
			var end = c.indexOf('[/rltable]');

			if (start >= 0 && end >= 0)
			{
				start += textarea.selectionStart;
				end += textarea.selectionStart + '[/rltable]'.length;

				for (var i = 0; i < tables.length; i++)
				{
					var t = tables[i];

					if (t.start == start && t.end == end)
					{
						current_table = i;
						current_table_editor = new tableEditor(t.content, editor_element);
						break;
					}
				}
			}
		}

		var option = document.createElement('option');
		option.value = 'import';
		option.innerHTML = "Créer un nouveau tableau (copié/collé à partir d'un tableur)";
		selector.appendChild(option);

		for (var i = 0; i < tables.length; i++)
		{
			var option = document.createElement('option');
			option.value = i;
			option.selected = (i == current_table);
			option.innerHTML = "Éditer tableau n°" + (i + 1);
			selector.appendChild(option);
		}

		// If it fails and there's existing tables, let the user choose using the selector
		if (tables.length > 0)
		{
			return true;
		}

		// If there is no existing table, we probably want to create a new one
		createNewTable();
		selector.selectedIndex = 1;
	}

	function showImportModal()
	{
		document.getElementById('importModal').classList.remove('hidden');
	}

	function findTables()
	{
		var re = /\[rltable[\s\S]*?\[\/rltable\]/igm;
		var tables = [];
		var matches = textarea.value.matchAll(re);

		if (!matches)
		{
			return tables;
		}

		for (var i = 0; i < matches.length; i++)
		{
			var m = matches[i];
			tables.push({
				'start': m.index,
				'end': m.index + m[0].length,
				'content': m[0]
			})
		}
		
		return tables;
	}

	function findCurrentTable()
	{
		return findTables()[current_table];
	}

	function saveCurrentTable()
	{
		if (current_table == "new")
		{
			// Append at end
			textarea.value += "\n\n" + current_table_editor.export();
		}
		else
		{
			var table = findCurrentTable();
			textarea.value = textarea.value.substr(0, table.start) + current_table_editor.export() + textarea.value.substr(table.end);
		}

		current_table = null;
		current_table_editor = null;
	}

	function createNewTable()
	{
		current_table = "new";
		current_table_editor = new tableEditor(demo_new_table, editor_element);
	}
})();