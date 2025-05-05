 // Weekly Activiy Column Chart
 document.addEventListener("DOMContentLoaded", function () {
    fetch('get_weekly_activity.php')
        .then(response => response.json())
        .then(data => {
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            const deposits = Array(7).fill(0);
            const withdrawals = Array(7).fill(0);

            data.forEach(entry => {
                const index = days.indexOf(entry.day);
                if (index !== -1) {
                    deposits[index] = parseFloat(entry.total_deposit);
                    withdrawals[index] = parseFloat(entry.total_withdraw);
                }
            });

            const options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                title: {
                    text: ' '
                },
                xaxis: {
                    categories: days
                },
                yaxis: {
                    title: {
                        text: 'Amount ($)'
                    }
                },
                series: [
                    {
                        name: 'Deposits',
                        data: deposits
                    },
                    {
                        name: 'Withdrawals',
                        data: withdrawals
                    }
                ],
                colors: ['#706EFF', '#343C6A']
            };

            const chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
});



        // Transaction Type Distribution Pie Chart
        document.addEventListener("DOMContentLoaded", function () {
        fetch('get_transaction_distribution.php')
            .then(response => response.json())
            .then(data => {
                const labels = [];
                const values = [];

                data.forEach(entry => {
                    let label = '';
                    switch (entry.type) {
                        case 'deposit': label = 'Deposit'; break;
                        case 'transfer_out': label = 'Transfer'; break;
                        case 'withdrawal': label = 'Withdraw'; break;
                        case 'loanpayment': label = 'Loan Payment'; break;
                        default: label = entry.type;
                    }
                    labels.push(label);
                    values.push(parseFloat(entry.total));
                });

                if (values.length === 0) {
                    document.querySelector("#pieChart").innerHTML = "<p>No transaction data available.</p>";
                    return;
                }

                const options = {
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    series: values,
                    labels: labels,
                    title: {
                        text: ' '
                    },
                    colors: ['#00B8D9', '#0052CC', '#5243AA', '#16DBCC']
                };

                const pieChart = new ApexCharts(document.querySelector("#pieChart"), options);
                pieChart.render();
            })
            .catch(error => {
                console.error('Error loading pie chart data:', error);
            });
    });


    // Balance Over Time Area Chart
    var options = {
        series: [{
        data: [
        //   data
        ]
      }],
        chart: {
        id: 'area-datetime',
        type: 'area',
        height: 350,
        zoom: {
          autoScaleYaxis: true
        }
      },
      annotations: {
        yaxis: [{
          y: 30,
          borderColor: '#999',
          label: {
            show: true,
            text: 'Support',
            style: {
              color: "#fff",
              background: '#00E396'
            }
          }
        }],
        xaxis: [{
          x: new Date('14 Nov 2012').getTime(),
          borderColor: '#999',
          yAxisIndex: 0,
          label: {
            show: true,
            text: 'Rally',
            style: {
              color: "#fff",
              background: '#775DD0'
            }
          }
        }]
      },
      dataLabels: {
        enabled: false
      },
      markers: {
        size: 0,
        style: 'hollow',
      },
      xaxis: {
        type: 'datetime',
        min: new Date('01 Mar 2012').getTime(),
        tickAmount: 6,
      },
      tooltip: {
        x: {
          format: 'dd MMM yyyy'
        }
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0.9,
          stops: [0, 100]
        }
      },
      };

      var chart = new ApexCharts(document.querySelector("#chart-timeline"), options);
      chart.render();
    
    
      var resetCssClasses = function(activeEl) {
      var els = document.querySelectorAll('button')
      Array.prototype.forEach.call(els, function(el) {
        el.classList.remove('active')
      })
    
      activeEl.target.classList.add('active')
    }
    
    document
      .querySelector('#one_month')
      .addEventListener('click', function(e) {
        resetCssClasses(e)
    
        chart.zoomX(
          new Date('28 Jan 2013').getTime(),
          new Date('27 Feb 2013').getTime()
        )
      })
    
    document
      .querySelector('#six_months')
      .addEventListener('click', function(e) {
        resetCssClasses(e)
    
        chart.zoomX(
          new Date('27 Sep 2012').getTime(),
          new Date('27 Feb 2013').getTime()
        )
      })
    
    document
      .querySelector('#one_year')
      .addEventListener('click', function(e) {
        resetCssClasses(e)
        chart.zoomX(
          new Date('27 Feb 2012').getTime(),
          new Date('27 Feb 2013').getTime()
        )
      })
    
    document.querySelector('#ytd').addEventListener('click', function(e) {
      resetCssClasses(e)
    
      chart.zoomX(
        new Date('01 Jan 2013').getTime(),
        new Date('27 Feb 2013').getTime()
      )
    })
    
    document.querySelector('#all').addEventListener('click', function(e) {
      resetCssClasses(e)
    
      chart.zoomX(
        new Date('23 Jan 2012').getTime(),
        new Date('27 Feb 2013').getTime()
      )
    })