$(document).ready(function () {
  showGraph();
});
function showGraph() {
  {
    // in Json encodete Arrays aufrufen 
    $.post("Json/OwnerChartData.php", function ([ownerSprints_data, [ownerCompletion_data,[ownerLeadtime_data,[ownerBurndown_data,[ownerSpi_data]]]]]) {
      

      // für den Velocity Chart benötigte Variablen deklarieren 
      var Sprint_ID = [];
      var Commitment = [];
      var Complete = [];
      // Variablen für Lead Time Chart deklarieren
      var leadtime = []; 
      var Bezeichnung = [];
      // Variablen für den Burndown Chart deklarieren
      var Tag = [];
      var ideal = [];
      var actual = [];
      // Variablen die für den SPI Chart benötigt werden deklarieren
      var SpiTag = [];
      var Value = [];
      var PlannedValue = [];

      // für den Velocity Chart benötigte Variablen deklarieren 
      for (var i in ownerSprints_data) {
        Sprint_ID.push("Sprint " + ownerSprints_data[i].Sprint_ID);
        Commitment.push(ownerSprints_data[i].Commitment);
      }
      for (var i in ownerCompletion_data) {
        Complete.push(ownerCompletion_data[i].Complete);
      }

      // Variablen für Leadtime Chart initialisieren
      for (var i in ownerLeadtime_data){
        leadtime.push(ownerLeadtime_data[i].leadtime);
        Bezeichnung.push(ownerLeadtime_data[i].Bezeichnung);         
      }

      //Variablen für den Burndown Chart initialisieren
      for (var i in ownerBurndown_data) {
        Tag.push("Tag " + ownerBurndown_data[i].Tag);
        ideal.push(ownerBurndown_data[i].ideal);
        actual.push(ownerBurndown_data[i].actual);
      }

      // Variablen für SPI Chart initialisieren
      for (var i in ownerSpi_data){
        SpiTag.push("Tag " + ownerSpi_data[i].Tag);
        Value.push(ownerSpi_data[i].Value);  
        PlannedValue.push(ownerSpi_data[i].PlannedValue);       
      }

      // Chartdaten für dem velocity Chart erstellen 
      var ovelo_chartdata = {
        labels: Sprint_ID ,
        datasets: [
          {
            label: "Commitment",
            backgroundColor: "rgba(33, 33, 33, 0.9)",
            data: Commitment,
          },
          {
            label: "Work Complete",
            backgroundColor: "rgba(0,255,130, 0.9)",
            data: Complete,
          }
        ]
      };

       // Chart Daten für Lead Time Chart erstellen
       var leadtime_chartdata = {
        labels : Bezeichnung,
        datasets:[
            {
              label: 'Lead Time',
              backgroundColor: "#09172e",               
              data: leadtime
            }
        ]
      };

      // Chartdaten für den Burndown Chart erstellen
      var ownerburn_chartdata = {
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
      var ownerSpi_chartdata = {
        labels: SpiTag,
        datasets: [
          {
            label: "SPI",
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

       //Lead Time Chart erstellen
       var ctx10 = $("#lead-time");
       var myChart10 = new Chart (ctx10,{
           type: 'line',
           data: leadtime_chartdata,
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

      // Velocity Chart erstellen 
      var ctx11 = $("#ovelo");
      var myChart11 = new Chart(ctx11, {
        type: "bar",
        data: ovelo_chartdata,
        options: {
          plugins: {
            title: {
              display: true,
              text: "Overall Velocity",
           },
          },
            responsive: true,
            layout: {
              padding: 10,
            },
          },
        });

        //Burndown Chart erstellen
        var ctx12 = $("#ownerburn");
        var myChart12 = new Chart(ctx12, {
          type: 'line',
          data: ownerburn_chartdata,
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
        var ctx13 = $("#ownerSpi");
        var myChart13 = new Chart(ctx13, {
          type: 'line',
          data: ownerSpi_chartdata,
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

