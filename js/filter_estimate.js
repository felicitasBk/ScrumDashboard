// ---- Min/Max und Tabellenwert auslesen und auf Filterbarkeit prüfen (Geschätzter Aufwand filtern) -----
$.fn.dataTable.ext.search.push(
	function (settings, data, dataIndex) {

		var min = parseInt($('#estimatedMinRange').val(), 10);
		var max = parseInt($('#estimatedMaxRange').val(), 10);
		var estimate = parseFloat(data[2]) || 0;


		if ((isNaN(min) && isNaN(max)) ||
			(isNaN(min) && estimate <= max) ||
			(min <= estimate && isNaN(max)) ||
			(min <= estimate && estimate <= max)) {
			return true;
		}
		return false;
	}
);