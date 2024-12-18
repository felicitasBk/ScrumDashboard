$(document).ready(function(){
  showGraph();
});
function showGraph(){
  {
      // in Json encodete Arrays aufrufen 
      $.post("Json/LeadChartData.php", function([leadForecastData, epicBurndown_data]) {
          

          // Variablen für Forecast Chart deklarieren
          var Sprint_ID = []; 
          var geplanteDauerInTagen = [];
          var realeDauer = [];

          //Variablen für Epic-Burndown Chart deklarieren
          var current = [];
          var Sprint = [];
          var completed = [];
          var added = [];    

          //Variablen für Forecast Chart initialisieren
          for (var i in leadForecastData){
          Sprint_ID.push("Sprint " + leadForecastData[i].Sprint_ID); 
          geplanteDauerInTagen.push(leadForecastData[i].geplanteDauerInTagen);
          realeDauer.push(leadForecastData[i].realeDauer);
          }

          //Variablen für Epic-Burndown Chart initialisieren
          for (var i in epicBurndown_data) {
            current.push(epicBurndown_data[i].current);
            Sprint.push(epicBurndown_data[i].Sprint);
            completed.push(epicBurndown_data[i].completed);
            added.push(epicBurndown_data[i].added);
          }
          
          //Chartdaten für Forecast Chart erstellen
          var forecast_chartdata = {
          labels : Sprint_ID,
          datasets:[
                {
                  label: 'Forecast',
                  backgroundColor: 'rgba(33, 33, 33, 0.9)',                       
                  data: geplanteDauerInTagen
                }, 
                {
                  label: 'Actual Time',
                  backgroundColor: 'rgba(0,255,130, 0.9)',                   
                  data: realeDauer
                }
            ]
          };

          //Chartdaten für 
          var epic_chartdata = {
            labels: Sprint,
            datasets: [
              {
                label: "Work remaining",
                backgroundColor: "rgba(33, 33, 33, 0.9)",
                data: current,
              },
              {
                label: "completed",
                backgroundColor: "rgba(0,255,130, 0.9)",
                data: completed,
              },
              {
                label: "added",
                backgroundColor: "blue",
                data: added
              }
            ],
          };
          
          //Forecast Chart erstellen
        	var ctx8 = $("#forecast");
          var myChart8 = new Chart (ctx8,{
            type: 'bar',
            data: forecast_chartdata,
            options: {
              plugins: {
                title: {
                  display: true,
                  text: "Forecast Consistency",
                },
              },
              responsive: true,
           },
        });

        // Epic Burndown Chart erstellen
        var ctx9 = $("#epicburn");
        var myChart9 = new Chart(ctx9, {
          type: 'bar',
          data: epic_chartdata,
          options: {
            plugins: {
              title: {
                display: true,
                text: "Epic Burndown Chart",
              },
            },
            responsive: true,
            scales: {
              x: {
                stacked: true,
              },
              y: {
                stacked: true,
              },
            },
          },
        });
    });
  }    
};

