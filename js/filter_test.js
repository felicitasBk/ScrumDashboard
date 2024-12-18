// ---- Min/Max und Tabellenwert auslesen und auf Filterbarkeit prüfen (Testcovarage filtern) -----
$.fn.dataTable.ext.search.push(
	function (settings, data, dataIndex) {

		var min = parseInt($('#testMinRange').val(), 10);
		var max = parseInt($('#testMaxRange').val(), 10);
		var estimate = parseFloat(data[4]) || 0;

		if ((isNaN(min) && isNaN(max)) ||
			(isNaN(min) && estimate <= max) ||
			(min <= estimate && isNaN(max)) ||
			(min <= estimate && estimate <= max)) {
			return true;
		}
		return false;
	}
);