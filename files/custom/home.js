$(document).ready(function() {

    var table = $('#dom-jqry-mi-lista').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "ordering": false,
        "language": {
            "search": "Buscar:"
          }
    });

    $(".btnVer").on('click',function () {
        var _url = $(this).data("url");
        window.open(_url, '_blank');
    });
});

