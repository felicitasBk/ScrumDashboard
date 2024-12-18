// DataTables initalisierung (Sprint-Table && Backlog-Table)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter
$(document).ready(function () {
	$('#backlog-table').DataTable({
		responsive: true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
		},
		initComplete: function () {
			this.api().columns().every(function () {
				var column = this;
				var select = $('<select><option value=""></option></select>')
					.appendTo($(column.footer()).empty())
					.on('change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);

						column
							.search(val ? '^' + val + '$' : '', true, false)
							.draw();
					});

				column.data().unique().sort().each(function (d, j) {
					select.append('<option value="' + d + '">' + d + '</option>')
				});
			});
		}
	});

	var table = $('#todo-table').DataTable({
		responsive: true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
		},
		initComplete: function () {
			this.api().columns([0]).every(function () {
				var column = this;
				var select = $('<select><option value=""></option></select>')
					.appendTo($(column.footer()).empty())
					.on('change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);

						column
							.search(val ? '^' + val + '$' : '', true, false)
							.draw();
					});

				column.data().unique().sort().each(function (d, j) {
					select.append('<option value="' + d + '">' + d + '</option>')
				});
			});

			this.api().columns([1]).every(function () {
				var column = this;
				var select = $('<select><option value=""></option></select>')
					.appendTo($(column.footer()).empty())
					.on('change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);

						column
							.search(val ? '^' + val + '$' : '', true, false)
							.draw();
					});

				select.append('<option value="zugewiesen">Zugewiesen</option>')
				select.append('<option value="inBearbeitung">In Bearbeitung</option>')
				select.append('<option value="InReview">In Review</option>')
			});
		}
	});

	//  + Min/Max Data Filter
	//    .. Geschätzter Aufwand
	//    .. Realer Aufwand
	//    .. Testcoverage


	$('#estimatedMinRange, #estimatedMaxRange').on('input keyup', function () {
		table.draw();
	});

	$('#realMinRange, #realMaxRange').on('input keyup', function () {
		table.draw();
	});

	$('#testMinRange, #testMaxRange').on('input keyup', function () {
		table.draw();
	});

});


// ---------------- Switch Table -----------------------------

// von Backlog- zu Todo-Table
function switchToTodo() {
	document.querySelector("#backlog").style.display = "none";
	document.querySelector("#todo").style.display = "inline";

}

// von Todo- zu Backlog-Table
function switchToBacklog() {
	document.querySelector("#backlog").style.display = "inline";
	document.querySelector("#todo").style.display = "none";


}

//------------------- Mood Eingabe-----------------------

// In Review Option freischalten wenn Test-Coverage == 100%
function checkTest(value, parentOfParent) {
	if (value == 100) {
		parentOfParent.children[2].children[0].children[1].disabled = false;
	}
}

/*  Wenn der Status einer Story in der Todo-Table == zugewiesen ist, muss nunächst 
	der geschätzter Aufwand zunächst eingegeben werden. Erst dann kann man die
    Story auf inBearbeitung setzen bzw. andere Daten eingeben */
document.onload = setTimeout(function () {
	let sts = document.querySelector("#todo-body").children;
	for (const x of sts) {
		let statusSelectValue = x.children[2].children[0].value;

		if(statusSelectValue == "zugewiesen"){
			x.children[2].children[0].disabled = true;
			x.children[4].children[1].disabled = true;
			x.children[5].children[0].disabled = true;
		}
	}
}, 0);


/* Wenn In Review ausgewählt, Mood-Eingabe anzeigen und Select deaktivieren, Übermittlung von StoryPoint ID, 
neuem Status neue Testcoverage & Zeitstempel an Datenbank */
function saveStory(id) {
	document.cookie = "slidCookie = " + id;
	let elem = document.getElementById(id);
	let x = elem.parentElement.parentElement;

	if (elem.value == "inReview") {
		elem.disabled = true;
		x.children[4].children[1].disabled = true;
		x.children[5].children[0].disabled = true;
		toggleMood();
	}

	if (elem.value == "inBearbeitung") {
		x.children[3].children[1].disabled = true;
		x.children[4].children[1].disabled = false;
		x.children[5].children[0].disabled = false;

	}
}

/*  Versenden den gewählten Mood-Wert und die Mitarbeiter-ID nach Auswahl des Moods(AJAX -> php) 
    + ausblenden des PopUps + Popup  */
let mood = document.querySelector(".mood");

function toggleMood() {
	mood.classList.toggle("show");
}

function calcMood(mood, mid) {
	$.ajax({
		type: "POST", //type of method
		url: "php/input_dev.php", //your page
		data: { mood: mood, mid: mid }, // passing the values
		success: function (response) {
		
		},
	});

	toggleMood();
}

// ----------------------------- Dev zu Story zuweisen ------------------------

function assignDev(sessionId, assign_slid) {
	$.ajax({
		type: "POST", //type of method
		url: "php/input_dev.php", //your page
		data: { sessionId: sessionId, assign_slid: assign_slid }, // passing the values
		success: function (response) {
			setTimeout(reload, 500);
		},
	});
}

// ----------- Neu laden der seite erzwingen ---------------
function reload(){
	location.reload();
}
