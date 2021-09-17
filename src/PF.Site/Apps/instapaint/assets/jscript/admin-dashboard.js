$Ready(function() {

    // The following code takes care of pulling data for charts and plotting the charts:
    if (typeof(google) === "undefined") {
        $.getScript( "https://www.gstatic.com/charts/loader.js", function( data, textStatus, jqxhr ) {

            google.charts.load('current', {packages: ['geochart', 'corechart', 'bar']});

            $.getJSON( "/admin-dashboard/?ajax=stats", function( data ) {
                stats = data;

                google.charts.setOnLoadCallback(drawUsersByCountryMap);
                google.charts.setOnLoadCallback(drawLatestRegistrations7Days);
                google.charts.setOnLoadCallback(drawLatestRegistrations28Days);
            });
        });
    } else {
        if ($('#admin-dashboard-main-page').size()) {
            $.getJSON( "/admin-dashboard/?ajax=stats", function( data ) {
                stats = data;

                google.charts.setOnLoadCallback(drawUsersByCountryMap);
                google.charts.setOnLoadCallback(drawLatestRegistrations7Days);
                google.charts.setOnLoadCallback(drawLatestRegistrations28Days);
            });

        }
    }


    function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
            ['Country', 'Popularity'],
            ['Germany', 200],
            ['United States', 300],
            ['Brazil', 400],
            ['Canada', 500],
            ['France', 600],
            ['RU', 700]
        ]);

        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('users_by_country_map'));

        chart.draw(data, options);
    }

    function drawUsersByCountryMap() {
        var aData = [['Country', 'Users']];

        stats.users_by_country.forEach(function(element) {
            aData.push([element.country_iso, parseInt(element.count)]);
        });

        var data = google.visualization.arrayToDataTable(aData);
        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('users_by_country_map'));

        chart.draw(data, options);
    }

    function drawLatestRegistrations7Days() {

        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'New users');

        var registrations = Object.entries(stats.user_joins_last_7_days);

        for (var i = 0; i < registrations.length; i++) {
            var date = registrations[i][0];
            var count = registrations[i][1];
            var year = parseInt(date.substring(0, 4));
            var month = parseInt(date.substring(5, 7)) - 1;
            var day = parseInt(date.substring(8, 10));

            data.addRow([new Date(year, month, day), count]);
        }

        var chart = new google.visualization.ColumnChart(
            document.getElementById('latest_registrations_7_days'));

        var options = {
            legend: { position: "none" },
        };

        chart.draw(data, options);
    }

    function drawLatestRegistrations28Days() {

        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'New users');

        var registrations = Object.entries(stats.user_joins_last_28_days);

        for (var i = 0; i < registrations.length; i++) {
            var date = registrations[i][0];
            var count = registrations[i][1];
            var year = parseInt(date.substring(0, 4));
            var month = parseInt(date.substring(5, 7)) - 1;
            var day = parseInt(date.substring(8, 10));

            data.addRow([new Date(year, month, day), count]);
        }

        var chart = new google.visualization.ColumnChart(
            document.getElementById('latest_registrations_28_days'));

        var options = {
            legend: { position: "none" },
        };

        chart.draw(data, options);
    }
});

