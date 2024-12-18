$(document).ready(function(){
  showGraph();
});
function showGraph(){

  {
       // in Json encodete Arrays aufrufen 
      $.post("Json/MasterChartData.php", function([masterDiversity_data,[masterLeadtime_data,[masterSprints_data,[masterCompletion_data,[masterBurndown_data,[masterSpi_data]]]]]]) {
         

          // Variablen für Diversity Chart deklarieren
          var Fachgebiet = [];
          var Fachgebiet_count = []; 
          // Variablen für Lead Time Chart deklarieren
          var leadtime = []; 
          var Bezeichnung = [];
          // Variablen für Velocity Chart deklarieren
          var Sprint_ID = [];
          var Commitment = [];
          var Complete = [];
           // Variablen für den Burndown Chart deklarieren
          var Tag = [];
          var ideal = [];
          var actual = [];
          // Variablen die für den SPI Chart benötigt werden deklarieren
          var SpiTag = [];
          var Value = [];
          var PlannedValue = [];

          // Variablen für Velocity Chart initialisiseren
          for (var i in masterSprints_data) {
            Commitment.push(masterSprints_data[i].Commitment);
            Sprint_ID.push("Sprint " + masterSprints_data[i].Sprint_ID);
          }

          for (var i in masterCompletion_data) {
            Complete.push(masterCompletion_data[i].Complete);
          }

          // Variablen für Leadtime Chart initialisieren
          for (var i in masterLeadtime_data){
            leadtime.push(masterLeadtime_data[i].leadtime);
            Bezeichnung.push(masterLeadtime_data[i].Bezeichnung);         
          }
          
          //Variablen für Diversity Chart initialisieren
          for (var i in masterDiversity_data){
            Fachgebiet.push(masterDiversity_data[i].Fachgebiet);
            Fachgebiet_count.push(masterDiversity_data[i].Anzahl);
          }

          //Variablen für den Burndown Chart initialisieren
          for (var i in masterBurndown_data) {
            Tag.push("Tag " + masterBurndown_data[i].Tag);
            ideal.push(masterBurndown_data[i].ideal);
            actual.push(masterBurndown_data[i].actual);
          }

          // Variablen für SPI Chart initialisieren
          for (var i in masterSpi_data){
            SpiTag.push("Tag " + masterSpi_data[i].Tag);
            Value.push(masterSpi_data[i].Value);  
            PlannedValue.push(masterSpi_data[i].plannedValue);       
          }

          // Chart Daten für Lead Time Chart erstellen
          var leadTimeMaster_chartdata = {
            labels : Bezeichnung,
            datasets:[
                {
                  label: 'Lead Time',
                  backgroundColor: "#09172e",               
                  data: leadtime
                }
            ]
          };

           // Chart Daten für Team Diversity Chart erstellen
          var teamDiversity_chartdata = {
          labels : Fachgebiet,
          datasets:[
                {
                  backgroundColor: ["#003f5c", "#2f4b7c", "#665191", "#a05195", "#d45087", "#f95d6a", "#ff7c43", "#ffa600" ] ,                                 
                  data: Fachgebiet_count,
                }
            ]
          };
          
           // Chart Daten für Team Velocity Chart erstellen
          var veloma_chartdata = {
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
          var masterburn_chartdata = {
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

          //Chartdaten für SPI Chart erstellen
          var masterSpi_chartdata = {
            labels: SpiTag,
            datasets: [
              {
                label: "Actual Value",
                backgroundColor: "rgba(255, 180, 36, 1)",
                data: Value
              },
              {
                label: "Planned Value",
                backgroundColor: "rgba(27, 105, 176, 1)",
                data: PlannedValue
              }
            ]
          };
          
        // Diversity Chart erstellen
        var ctx3 = $("#team-diversity");
        var myChart3 = new Chart (ctx3,{
            type: 'doughnut',
            data: teamDiversity_chartdata,
            options: {
              plugins: {       
                title: {
                  display: true,
                  text: 'Team Diversity',
                },  
              },      
              layout: {
                padding: 10,
              }
            },
        });

      //Lead Time Chart erstellen
      var ctx4 = $("#lead-time-master");
      var myChart4 = new Chart (ctx4,{
          type: 'line',
          data: leadTimeMaster_chartdata,
          options: { 
            scales: {
              x: {
                max: 9 
              }
            },
            plugins:{         
              title: {
                display: true,
                text: "Lead Time",
              },  
            },      
            layout: {
              padding: 10,
            }
          },
      });
      
      //Velocity Chart erstellen
      var ctx5 = $("#veloma");
      var myChart5 = new Chart (ctx5,{
          type: 'bar',
          data: veloma_chartdata,
          options: {
            plugins: {       
              title: {
                display: true,
                text: 'Team Velocity',
              },  
            },      
            layout: {
              padding: 10,
            }
          },
      });
       //Burndown Chart erstellen
       var ctx6 = $("#masterburn");
       var myChart6 = new Chart(ctx6, {
         type: 'line',
         data: masterburn_chartdata,
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

       //Spi Chart erstellen
       var ctx7 = $("#spi-master");
       var myChart7 = new Chart(ctx7, {
         type: 'line',
         data: masterSpi_chartdata,
         options: {
           plugins: {
             title: {
               display : true,
               text: "Schedule Performance Index",
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
