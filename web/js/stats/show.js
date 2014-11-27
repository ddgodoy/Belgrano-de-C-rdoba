function load_pie_chart_data(id_q) {
    
     $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/question/'+id_q+'/show',
        data: {'pie_chart_data': 1},
        success: function (response) {
            
           build_charts(response, id_q);
        }
    });
}

function load_bar_chart_data(id_q) {
     $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/question/'+id_q+'/show',
        data: {'bar_chart_data': 1},
        success: function (response) {   
           build_bar_charts(response, id_q);
        }
    });   
}

function build_charts(pie_data, id_q) {
   

    var plotObj = $.plot($("#pie-chart-"+id_q), pie_data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 20,
                y: 0
            },
            defaultTheme: false
        },
       
    });

}

function build_bar_charts(bar_data, id_q) {
      Morris.Bar({
        element: 'bar-chart-'+id_q,
        data: bar_data.data,
        xkey: 's',
        ykeys: bar_data.ykeys,
        labels: bar_data.labels,
        //hideHover: 'auto',
        resize: true
    });
}

function load_charts() {
    $('input[name="questions"]').each(function(){
        load_pie_chart_data($(this).val());
        load_bar_chart_data($(this).val());
    });
}

$(document).ready(function() {
    
    load_charts();
    
});