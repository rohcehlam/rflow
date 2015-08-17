var to_refresh_icons;
function refresh_icons() {
    $('#icons_load').show();
    $.getJSON('../ajax/json_icons.php', function (data) {
        $('#span_maintenances').html(data.maintenances);
        if (data.maintenances > 0) {
            $('#span_maintenances').removeClass();
            $('#span_maintenances').addClass('label label-warning');
        } else {
            $('#span_maintenances').removeClass();
            $('#span_maintenances').addClass('label label-default');
        }
        $('#span_alarms').html(data.alarms);
        if (data.maintenances > 0) {
            $('#span_alarms').removeClass();
            $('#span_alarms').addClass('label label-danger');
        } else {
            $('#span_alarms').removeClass();
            $('#span_alarms').addClass('label label-default');
        }
        $('#span_mysupport').html(data.my_requests);
        if (data.maintenances > 0) {
            $('#span_mysupport').removeClass();
            $('#span_mysupport').addClass('label label-info');
        } else {
            $('#span_mysupport').removeClass();
            $('#span_mysupport').addClass('label label-default');
        }
        $('#span_unassignedsupport').html(data.support_requests);
        if (data.maintenances > 0) {
            $('#span_unassignedsupport').removeClass();
            $('#span_unassignedsupport').addClass('label label-success');
        } else {
            $('#span_unassignedsupport').removeClass();
            $('#span_unassignedsupport').addClass('label label-default');
        }
        $('#icons_load').hide();
        clearTimeout(to_refresh_icons);
        to_refresh_icons = setTimeout(refresh_icons, 60000);
    });
}
refresh_icons();