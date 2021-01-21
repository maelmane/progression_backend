/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

var editor;

function betterTab(cm) {
	if (cm.somethingSelected()) {
		cm.indentSelection("add");
	} else {
		cm.replaceSelection(
			cm.getOption("indentWithTabs")
			? "\t"
			: Array(cm.getOption("indentUnit") + 1).join(" "),
			"end",
			"+input"
		);
	}
}

function créerÉditeur(textarea, lang_id) {
	if (document.getElementById("incode") != null) {
		editor = CodeMirror.fromTextArea(textarea, {
			matchBrackets: true,
			lineNumbers: true,
			readOnly: false,
			indentUnit: 4,
			scrollbarStyle: null,
			extraKeys: { Tab: betterTab },
			mode: getMode(lang_id),
			gutters: ["CodeMirror-linenumbers", "gutter-ro"],
		});
		editor.setSize("100%", "100%");

		//Si on a au moins un TODO
		if(textarea.value.indexOf("TODO") > -1){
			désactiverHorsTodo(textarea.value);
		}

		//Si on a au moins un VISIBLE
		if(textarea.value.indexOf("VISIBLE") > -1){
			cacherHorsVisible(textarea.value);
		}
	}
}
function désactiverHorsTodo(doc) {
	posDébut = 0;
	posFin = 0;
	while (posDébut > -1) {
		posFin = doc.indexOf("+TODO", posDébut);
		if (posFin == -1) {
			posFin = doc.length;
		}

		ligneDébut = editor.doc.posFromIndex(posDébut);
		ligneFin = editor.doc.posFromIndex(posFin);

		//Rend immuable
		editor.doc.markText(
			{ line: ligneDébut.line, ch: 0 },
			{ line: ligneFin.line, ch: 999 },
			{ readOnly: true, inclusiveRight: true }
		);

		//Style des lignes immuables
		for (i = ligneDébut.line; i < ligneFin.line; i++) {
			editor.doc.addLineClass(i, "background", "ligne-ro");
		}
		
		//Cache la ligne -TODO
		editor.doc.markText(
			{ line: ligneDébut.line, ch: 0 },
			{ line: ligneDébut.line + 1, ch: 0 },
			{ collapsed: "true" }
		);
		//Cache la ligne +TODO
		editor.doc.markText(
			{ line: ligneFin.line, ch: 0 },
			{ line: ligneFin.line + 1, ch: 0 },
			{ collapsed: "true" }
		);

		posDébut = doc.indexOf("-TODO", posFin);
	}
}

function cacherHorsVisible(doc) {
	posDébut = 0;
	posFin = 0;
	while (posDébut > -1) {
		posFin = doc.indexOf("+VISIBLE", posDébut);
		if (posFin == -1) {
			posFin = doc.length;
		}

		ligneDébut = editor.doc.posFromIndex(posDébut);
		ligneFin = editor.doc.posFromIndex(posFin);

		//Cache toute la section non visible
		editor.doc.markText(
			{ line: ligneDébut.line, ch: 0 },
			{ line: ligneFin.line + 1, ch: 0 },
			{
				collapsed: "true",
			}
		);

		posDébut = doc.indexOf("-VISIBLE", posFin);
	}
}

function getMode(lang_id) {
	var mode = "c";
	switch (lang_id) {
		case 1:
			mode = "python";
			break;
		case 2:
			mode = "ruby";
			break;
		case 4:
			mode = "php";
			break;
		case 7:
			mode = "go";
			break;
		case 8:
		case 9:
			mode = "c";
			break;
		case 10:
			mode = "text/x-java";
			break;
		case 11:
			mode = "shell";
			break;
		case 12:
			mode = "perl";
			break;
	}

	return mode;
}
