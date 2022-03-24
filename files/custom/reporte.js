var flagBuscar = false;
var flagExcel = false;

$(document).on({
    ajaxStart: function() { 
        if(flagBuscar){
            $('#spinner').show(); 
        }
    },
    ajaxStop: function() {

        if(flagBuscar){
            $('#spinner').hide(); 
            $('.btnBuscar').removeClass('btn-disabled disabled');
            flagBuscar = false;
        }

        if(flagExcel){
            $('.btnExportar').removeClass('btn-disabled disabled');
            flagExcel = false;
        }
        
    }    
});

$(document).ready(function() {
    $('#spinner').hide();

    $('.btnComparar').prop('disabled', true);
    $('.btnComparar').addClass('btn-disabled disabled');
    

    var btnBuscar = $(".btnBuscar");
    //tabla-resultados
    btnBuscar.on('click', function(){

        if (btnBuscar.hasClass('btn-disabled disabled')) {
            return false;
        } else {
            btnBuscar.addClass('btn-disabled disabled');
        }
        flagBuscar = true;

        $('.btnComparar').prop('disabled', true);
        $('.btnComparar').addClass('btn-disabled disabled');

        $("#tabla-resultados").show();
        $("#tabla-detalles").hide();
        $.ajax({
            url: $("#urlReporte").val(),
            type: 'POST',
            data: $("#frmGrupo").serialize(),
            error: function() {
                
            },
            success: function(data) {
                $("#tabla-resultados").html(data);
            }
        });
    });

    var btnExportar = $(".btnExportar");
    //tabla-resultados
    btnExportar.on('click', function(){

        var url_xlsx = $("#urlExportar").val() + "/?" + $("#frmGrupo").serialize();
        console.log(url_xlsx);

        window.open(url_xlsx, '_blank');
        /*
        $.ajax({
            url: $("#urlExportar").val(),
            type: 'POST',
            data: $("#frmGrupo").serialize(),
            error: function() {
                
            },
            success: function(data) {
                $("#tabla-resultados").html(data);
            }
        });*/
    });

      

    

});