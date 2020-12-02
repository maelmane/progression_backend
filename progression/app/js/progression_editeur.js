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
			gutters: ["CodeMirror-linenumbers", "gutter-ro"],
		});
		changerMode(lang_id);
		editor.setSize("100%", "100%");

		//Si on a au moins un TODO
		if(textarea.value.indexOf("TODO")){
			désactiverHorsTodo(textarea.value);
		}

		//Si on a au moins un VISIBLE
		if(textarea.value.indexOf("VISIBLE")){
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

function changerMode(lang_id) {
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
			mode = "java";
			break;
		case 11:
			mode = "shell";
			break;
		case 12:
			mode = "perl";
			break;
	}

	editor.setOption("mode", "text/x-" + mode);
}
