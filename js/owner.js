// DataTables initalisierung (Sprint-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter Extention

$(document).ready(function () {
  $('#sprint-tbl').DataTable({
    responsive: true,
	"language": {
		"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
	},
    initComplete: function () {
      this.api().columns([0,1,2,3,4,5]).every(function () {
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

// DataTables initalisierung (Backlog-Tabelle)
//	+ Responive Plugin
//	+ Language (German) Plugin
//  + Dropdown Data Filter

  $('#backlog-table').DataTable({
    responsive: true,
	"language": {
		"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
	},
    initComplete: function () {
      this.api().columns([0,1,2,3,4]).every(function () {
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


// ---------------- Switch Tables -----------------------------

// von Sprints- zu Product-Backlog-Table
function switchToProducts() {
  document.querySelector("#products").style.display = "inline";
  document.querySelector("#sprints").style.display = "none";
}

// von Product-Backlog- zu Sprints-Table
function switchToSprints() {
  document.querySelector("#products").style.display = "none";
  document.querySelector("#sprints").style.display = "inline";
}

// ----------------- Toggle show input popup------------------

let newStory = document.querySelector(".add-story");
function toggleNewStory(){

	newStory.classList.toggle("show");
}

// ------------ Beendete Story akzeptieren/ablehnen und Popup zeigen ---------
function setInputAndToggleRateStory(sl_id, sp_id){
	document.querySelector("#rateSL").value = sl_id;
	document.querySelector("#rateSP").value = sp_id;

	toggleRateStory()
}

var rateStory = document.querySelector(".rate-story");
function toggleRateStory() {
	rateStory.classList.toggle("show");
}


// -------------------------- Story bearbeiten und Popup zeigen------------------------
function setInputAndToggleEditStory(sp_id, bezeichnung, prioritaet, businessValue){
	document.querySelector("#edit_SP_ID").value = sp_id;
	document.querySelector("#edit_bezeichnung").value = bezeichnung;
	document.querySelector("#editselectPrio").children[0].value = prioritaet;
	document.querySelector("#editselectPrio").children[0].innerHTML = "aktuell: " + prioritaet;
	document.querySelector("#edit-business-value").value = businessValue;
	toggleEditStory();
}

var editStory = document.querySelector(".edit-story");

function toggleEditStory(){
	editStory.classList.toggle("show");
}

// ---------- Popup für die Bewertung der Kommunikationsintensität zeigen ------------
var rateKomm = document.querySelector(".rate-komm");

function toggleRateKomm(){
	rateKomm.classList.toggle("show");
}

// ---------- Popup für die Bewertung der NPS zeigen ------------
var rateNPS = document.querySelector(".rate-nps");

function toggleRateNPS(){
	rateNPS.classList.toggle("show");
}

// ------------------ Story löschen + Popup  --------------------------
var checkDeleteStory = document.querySelector(".check-delete-story");

let spDeleteHidden;

function toggleCheckDeleteStory(sp_id){
	toggleDeleteStory()
	spDeleteHidden = sp_id;
}

function toggleDeleteStory(){
	checkDeleteStory.classList.toggle("show");
}

function deleteStory() {
	$.ajax({
		type: "POST", //type of method
		url: "php/input_owner.php", //your page
		data: {deleteSP:spDeleteHidden }, // passing the values
		success: function (response) {
			toggleCheckDeleteStory();
			setTimeout(reload, 500);
		},
	});
}

// ----------- Neu laden der seite erzwingen --------------
function reload(){
	location.reload();
}
