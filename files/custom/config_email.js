$(document).ready(function() {

    var btnGuardar = $('.btnGuardar');
    btnGuardar.on('click', function() {
        
        if (btnGuardar.hasClass('btn-disabled disabled')) {
            return false;
        } else {
            btnGuardar.addClass('btn-disabled disabled');
        }

        var dataString = $("#frmMensaje").serialize();
        $.ajax({
            type:"POST",
            url: $("#urlMensajeForm").val(),
            data:dataString,
            success:function (data) {
                location.reload();
            }
        }); 
    });
});
