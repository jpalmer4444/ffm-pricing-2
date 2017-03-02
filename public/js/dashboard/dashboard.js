//Document ready functions here

// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChart);

  $(document).ready(function(){

    $("#" + processedItemsDivId).zfTable(processedItemsUrl);
    $("#" + pendingItemsDivId).zfTable(pendingItemsUrl);
    $("#" + rejectedItemsDivId).zfTable(rejectedItemsUrl);

    drawChart();



    //specific class to body
      $('body').addClass('dashboard');

  });



//Functions

  //pie chart A

  function drawChart() {

        // Create the data table - piechart A.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'B2C or B2B');
        data.addColumn('number', 'Percentage');
        data.addRows([
          ['B2B', 35],
          ['B2C', 65]
        ]);

        // Create the data table - piechart B.
        var data2 = new google.visualization.DataTable();
        data2.addColumn('string', 'B2C or B2B');
        data2.addColumn('number', 'Percentage');
        data2.addRows([
          ['B2B', 48],
          ['B2C', 52]
        ]);


        // Create the data table - piechart C.
        var data3 = new google.visualization.DataTable();
        data3.addColumn('string', 'B2C or B2B');
        data3.addColumn('number', 'Percentage');
        data3.addRows([
          ['B2B', 88],
          ['B2C', 12]
        ]);


        // Create the data table - linechart
        var data4 = google.visualization.arrayToDataTable([
          ['Week', ''],
          ['Week 1',  15],
          ['Week 2',  20],
          ['Week 3',  13],
          ['Week 4',  32]
        ]);



        // Set chart options - piechart A
        var options = {
                       title: 'A.',
                       backgroundColor: 'transparent',
                       chartArea:{
                          left:0,
                          top:20,
                          width:'100%'
                        },
                        legend: 'none',
                        pieSliceTextStyle: {
                          fontSize: 26
                        },
                        width:'100%',
                        height: '360',
                        fontName : 'azo-sans-web',
                        colors: ['#01576e','#669aa8']
                     };


         // Set chart options - piechart B
        var options2 = {
                        title: 'B.',
                        backgroundColor: 'transparent',
                        chartArea:{
                          left:0,
                          top:20,
                          width:'100%'
                        },
                        pieSliceTextStyle: {
                          fontSize: 11
                        },
                        legend: 'none',
                        height:'180',
                        width: '100%',
                        fontName : 'azo-sans-web',
                        colors: ['#01576e','#669aa8']
                     };

        // Set chart options - piechart C
        var options3 = {
                        title: 'C.',
                        backgroundColor: 'transparent',
                        chartArea:{
                          left:0,
                          top:20,
                          width:'100%'
                        },
                        pieSliceTextStyle: {
                          fontSize: 11
                        },
                        height:'180',
                        width: '100%',
                        fontName : 'azo-sans-web',
                        legend: 'none',
                        colors: ['#01576e','#669aa8']
                     };

        // Set chart options - linechart
          var options4 = {
            chartArea:{
                width:'80%'
              },
            legend: 'none'
          };


        // Instantiate and draw chart A, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('piechart_a'));
        chart.draw(data, options);


        // Instantiate and draw our chart, passing in some options.
        var chart2 = new google.visualization.PieChart(document.getElementById('piechart_b'));
        chart2.draw(data2, options2);

        // Instantiate and draw our chart, passing in some options.
        var chart3 = new google.visualization.PieChart(document.getElementById('piechart_c'));
        chart3.draw(data3, options3);


        // Instantiate and draw our chart, passing in some options.
        var chart4 = new google.visualization.LineChart(document.getElementById('line_chart'));
        chart4.draw(data4, options4);

      }








