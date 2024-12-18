$(document).ready(function () {
  showGraph();
});
function showGraph() {
  {
    // in Json encodete Arrays aufrufen 
    $.post("Json/DevChartData.php", function ([devSprints_data,[devCompletion_data,[devBurndown_data]]]) {
      
      
      // für den velocity Chart benötigte Variablen deklarieren 
      var Sprint_ID = [];
      var Commitment = [];
      var Complete = [];
      // Variablen für den Burndown Chart deklarieren
      var Tag = [];
      var ideal = [];
      var actual = [];

      // für den velocity Chart benötigte Variablen initialisieren 
      for (var i in devSprints_data) {
        Commitment.push(devSprints_data[i].Commitment);
        Sprint_ID.push("Sprint " + devSprints_data[i].Sprint_ID);
      }
      for (var i in devCompletion_data) {
        Complete.push(devCompletion_data[i].Complete);
      }

      // Variablen für den Burndown Chart initialisieren
      for (var i in devBurndown_data){
        Tag.push("Tag " + devBurndown_data[i].Tag);
        ideal.push(devBurndown_data[i].ideal);
        actual.push(devBurndown_data[i].actual);
      }

      // Chartdaten für dem velocity Chart erstellen 
      var velodev_chartdata = {
        labels: Sprint_ID,
        datasets: [
          {
            label: "Commitment",
            backgroundColor: "rgba(33, 33, 33, 0.9)",
            data: Commitment
          },
          {
            label: "Work Complete",
            backgroundColor: "rgba(34, 197, 255, 0.9)",
            data: Complete
          }
        ]
      };

      // Chartdaten für den Burndown Chart erstellen
      var devburn_chartdata = {
        labels: Tag,
        datasets: [
          {
            label: "Ideal",
            backgroundColor: "rgba(33, 33, 33, 0.9)",
            data: ideal
          },
          {
            label: "Burndown",
            backgroundColor: "rgba(0,255,130, 0.9)",
            data: actual
          }
        ]
      };

      // Velocity Chart erstellen 
      var ctx1 = $("#velodev");
      var myChart1 = new Chart(ctx1, {
        type: 'bar',
        data: velodev_chartdata,
        options: {
          plugins: {
            title: {
              display: true,
              text: "Teams Velocity",
            },
          },
          responsive: true,
        },
      });

      // Burndown Chart erstellen
      var ctx2 = $("#burndev");
      var myChart2 = new Chart(ctx2, {
        type: 'line',
        data: devburn_chartdata,
        options: {
          plugins: {
            title: {
              display : true,
              text: "Burndown",
            },
          },
          responsive: true,
          layout: {
            padding: 10,
          },
        },
      });
    });
  }
};

