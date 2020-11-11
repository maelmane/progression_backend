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

function créerÉditeur(textarea) {
	if (document.getElementById("incode") != null) {
		var editor = CodeMirror.fromTextArea(textarea, {
			matchBrackets: true,
			lineNumbers: true,
			readOnly: false,
			indentUnit: 4,
			scrollbarStyle: null,
			extraKeys: { Tab: betterTab },
		});
		editor.setSize("100%", "100%");
		désactiverHorsTodo(textarea.value, editor);
		cacherHorsVisible(textarea.value, editor);
	}
}
function désactiverHorsTodo(doc, editor) {
	posDébut = 0;
	posFin = 0;
	while (posDébut > -1) {
		posDébut += posFin;
		posFin = doc.substr(posDébut).search("[+]TODO");
		if (posFin == -1) {
			posFin = doc.length;
		} else {
			posFin += posDébut;
		}
		ligneDébut = editor.doc.posFromIndex(posDébut);
		ligneFin = editor.doc.posFromIndex(posFin + 6);

		//Rend immuable
		editor.doc.markText(
			{ line: ligneDébut.line, ch: 0 },
			{ line: ligneFin.line, ch: 0 },
			{ readOnly: true }
		);
		//Cache la ligne -TODO
		editor.doc.markText(
			{ line: ligneDébut.line, ch: 0 },
			{ line: ligneDébut.line + 1, ch: 0 },
			{ collapsed: "true" }
		);
		//Cache la ligne +TODO
		editor.doc.markText(
			{ line: ligneFin.line - 1, ch: 0 },
			{ line: ligneFin.line, ch: 0 },
			{ collapsed: "true" }
		);

		posDébut = doc.substr(posFin).search("[-]TODO");
	}
}

function cacherHorsVisible(doc, editor) {
	posDébut = 0;
	posFin = 0;
	while (posDébut > -1) {
		posDébut += posFin;
		posFin = doc.substr(posDébut).search("[+]VISIBLE") + 8;
		if (posFin == 7) {
			posFin = doc.length;
		} else {
			posFin += posDébut;
		}
		ligneDébut = editor.doc.posFromIndex(posDébut);
		ligneFin = editor.doc.posFromIndex(posFin + 1);

		editor.doc.markText({ line: ligneDébut.line, ch: 0 }, ligneFin, {
			collapsed: "true",
		});

		posDébut = doc.substr(posFin).search("[-]VISIBLE");
	}
}
