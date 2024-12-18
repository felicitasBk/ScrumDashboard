// DataTables initalisierung (Nutzer-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter

$(document).ready(function () {
	$('#user-table').DataTable({
		responsive: true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
		},
		initComplete: function () {
			this.api().columns([1,2,3,4,5,6,7,8,9,10,11]).every(function () {
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

// DataTables initalisierung (Teams-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter
$(document).ready(function () {
	$('#teams-table').DataTable({
		responsive: true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
		},
		initComplete: function () {
			this.api().columns([1,2,3]).every(function () {
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

// DataTables initalisierung (Initativen-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter
$(document).ready(function () {
	$('#init-table').DataTable({
		responsive: true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
		},
		initComplete: function () {
			this.api().columns([1,2,3,4]).every(function () {
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

// ---------- Nutzer hinzufügen Popup -------------
let addUser = document.querySelector(".add-user-pop");

function toggleNutzerverwaltung() {
	addUser.classList.toggle("show-add-user-pop");
}


// ---------- Nutzer hinzufügen Popup -------------
let addTeam = document.querySelector(".add-team-pop");

function toggleTeam() {
	addTeam.classList.toggle("show-add-team-pop");
}


// ---------- Nutzer hinzufügen Popup -------------
let addInitative = document.querySelector(".add-initiative-pop");

function toggleInitativen() {
	addInitative.classList.toggle("show-add-initiative-pop");
}