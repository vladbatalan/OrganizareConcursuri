<?php
  $sponsorizari = $conn->select_sponsorizari($concurs_ales->ID_CONCURS);
  $labels = "";
  $values = "";
  $index = 0;
  foreach($sponsorizari as $sponsorizare){
    $labels .= "\"".$sponsorizare->NUME_SPONSORIZARE."\"";
    $values .= $sponsorizare->SUMA_SPONSORIZATA;

    if($index < count($sponsorizari) - 1)
    {
      $labels .= ", ";
      $values .= ", ";
    }
    $index ++;
  }

?>

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Bar Chart Example
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [<?php echo $labels; ?>],
    datasets: [{
      label: "Suma sponsorizata",
      backgroundColor: "rgba(2,117,216,1)",
      borderColor: "rgba(2,117,216,1)",
      data: [<?php echo $labels; ?>],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 6
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 15000,
          maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});
