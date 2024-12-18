// DataTables initalisierung (Sprints-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter

$(document).ready(function () {
	$('#sm-table').DataTable({
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
});

// DataTables initalisierung (Create Sprint Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter
//  + Select Functionality for selecting Stories

var oAll =[];

$(document).ready(function () {
	var oTable = $('#select-backlog-table').DataTable({
		responsive: true,
		select: true,
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

	$('#select-backlog-table tbody').on( 'click', 'tr', function () {
		$(this).toggleClass('selected');
		var pos = oTable.row(this).index();
		var row = oTable.row(pos).data();
	} );

	$("#select-backlog-table").on("click",function(){
		oAll =[];
		$('#select-backlog-table tbody tr.selected').each(function(){
			var pos = oTable.row(this).index();
			var row = oTable.row(pos).data();
			oAll.push(row[Object.keys(row)[0]]);
		});
   });
});

// ----------- Neu laden der seite erzwingen ---------------
function reload(){
	location.reload();
}

// ----------- Ajax Request für das Anlegen eines neuen Sprints --------------
function createSprint() {
	let startDate = document.querySelector("#start-date").value;
	let startTime = document.querySelector("#start-time").value;
	let endDate = document.querySelector("#end-date").value;
	let endTime = document.querySelector("#end-time").value;
	let teamNewSprint = document.querySelector("#team-new-sprint").value;

	$.ajax({
		type: "POST", //type of method
		url: "php/input_master.php", //your page
		data: {startDate:startDate, startTime:startTime, endDate:endDate, endTime:endTime, teamNewSprint:teamNewSprint, selectedSP:oAll}, // passing the values
		success: function (response) {
			setTimeout(reload, 500);
		},
	});
	
}

// ------------ Popup für das Anlegen eines neuen Sprints --------------
let newSprint = document.querySelector(".new-sprint");

function toggleNewSprint() {
	newSprint.classList.toggle("show");
}

// ------------ Popup für das beenden Sprints --------------
let checkEndSprint = document.querySelector(".check-end-sprint");

function toggleCheckEndSprint() {
	checkEndSprint.classList.toggle("show");
}


// ------------ Bewerten der Prozesseinhaltung und der Teamzufriedenheit + Popup --------------
let proz = document.querySelector(".proz");

function toggleProz() {
	toggleCheckEndSprint()
	proz.classList.toggle("show");
}

let hiddentSprintVal = document.querySelector("#hiddenSprintNumber");
let selectSprintselectSprintValue = document.querySelector("#selectSprint").value;
hiddentSprintVal.value = selectSprintselectSprintValue;

let prozEinhaltungInput = document.querySelector("#prozEinhaltung");
let prozEinhaltungLabel = document.querySelector("#prozEinhaltungVal");
prozEinhaltungLabel.innerHTML = prozEinhaltungInput.value;

function setProzEinhaltungLabel(value){
	prozEinhaltungLabel.innerHTML = prozEinhaltungInput.value;
}

let teamBewertungInput = document.querySelector("#teamBewertung");
let teamBewertungLabel = document.querySelector("#teamBewertungVal");
teamBewertungLabel.innerHTML = teamBewertungInput.value;

function setZufriedenheitLabel(value){
	teamBewertungLabel.innerHTML = teamBewertungInput.value;
}


