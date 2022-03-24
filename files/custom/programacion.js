$(document).on({
    ajaxStart: function() { $('#spinner').show(); },
     ajaxStop: function() {
          $('#spinner').hide(); 
          $('#btnGuardar').removeClass('btn-disabled disabled');

        }    
});

var divPregunta;
var preguntaFecha = [];

$(document).ready(function() {

    divPregunta = $('#div-preguntas');

    

    $('#example-1').Tabledit({
        editButton: false,
        deleteButton: false,
        hideIdentifier: true,
        columns: {
            identifier: [0, 'id'],
            editable: [[2, 'fecha']]
        }
    });

    

    $("#encuesta").change(function () {
        var str = "";
        preguntaFecha = [];
        $( "#encuesta option:selected" ).each(function() {
            var data = $(this).val();
            divPregunta.hide();
            $("#example-1 > tbody").empty();
            if(data != ""){
                
                $.ajax({
                    url: $("#urlProgramacionPre").val()+'/'+data,
                    type: 'post',
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function (data) {

                        $.each(data, function( i, pregu ) {

                            preguntaFecha.push(pregu);
                            var dPregunta = pregu.encuesta_pregunta_nombre;
                            if (dPregunta.length > 50){
                                
                                $('#example-1 > tbody:last-child').append('<tr><td class="tabledit-view-mode"><span data-toggle="tooltip" title="' + dPregunta + '">' + dPregunta.substring(0, 50) + '...</span></td><td class="tabledit-view-mode" style="cursor: pointer;"><span class="tabledit-span">'+pregu.encuesta_pregunta_fecha_inicio+'</span><input class="tabledit-input form-control input-sm" type="date" name="fecha" data-id="'+pregu.encuesta_pregunta_id+'" value="'+pregu.encuesta_pregunta_fecha_inicio+'" style="display: none;"></td>');
                            }else{
                                $('#example-1 > tbody:last-child').append('<tr><td class="tabledit-view-mode">'+pregu.encuesta_pregunta_nombre+'</td><td class="tabledit-view-mode" style="cursor: pointer;"><span class="tabledit-span">'+pregu.encuesta_pregunta_fecha_inicio+'</span><input class="tabledit-input form-control input-sm" type="date" name="fecha" data-id="'+pregu.encuesta_pregunta_id+'" value="'+pregu.encuesta_pregunta_fecha_inicio+'" style="display: none;"></td>');
                            }
                            $('[data-toggle="tooltip"]').tooltip();
                            
                        });
                        divPregunta.show();
                    }
                });
                
            }
            
        });
    }).change();

    
    var elemsmall = document.querySelector('.js-small');
	var switchery = new Switchery(elemsmall, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    function setSwitchery(switchElement, checkedBool) {
        if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
            switchElement.setPosition(true);
            switchElement.handleOnchange(true);
        }
    }

    $('#spinner').hide();
    $("#msgErrorCard").hide();

    $('.btnEditar').on('click',function () {
        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Editar programación');

        divPregunta.hide();
        $("#example-1 > tbody").empty();

        var dataId = $(this).data("id");
        
        $('#encuesta').attr('disabled',true);
        $('#grupo').attr('disabled',true);

        $.ajax({
            type:"POST",
            url: $("#urlProgramacionGet").val()+"/"+dataId,
            success:function (data) {
                console.log("get_data_1", data);

                var result = data.data;

                console.log("get_data", result);
                $('#idProgramacion').val(result.encuesta_programacion_id);
                setSwitchery(switchery, result.encuesta_programacion_estado_id == 2);

                
                $('#encuesta').val(result.encuesta_id);
                $('#grupo').val(result.grupo_id);
                $('#fecha_inicio').val(result.encuesta_programacion_fecha_inicio);
                $('#fecha_fin').val(result.encuesta_programacion_fecha_fin);
                $('#tipo_notifi').val(result.encuesta_programacion_tipo_id);
                $('#nombre_programacion').val(result.encuesta_programacion_nombre);

                $('#mensaje_email').val(result.encuesta_programacion_mensaje_email);
                $('#asunto_email').val(result.encuesta_programacion_asunto_email);
                $('#mensaje_sms').val(result.encuesta_programacion_mensaje_sms);

                $('#hora_job').val(result.encuesta_programacion_hora_job);
                
                var oDias = jQuery.parseJSON(result.encuesta_programacion_json_dias);
                console.log("get_dias", oDias);

                $( "#cLunes").prop('checked', oDias.Lunes);
                $( "#cMartes").prop('checked', oDias.Martes);
                $( "#cMiercoles").prop('checked', oDias.Miercoles);
                $( "#cJueves").prop('checked', oDias.Jueves);
                $( "#cViernes").prop('checked', oDias.Viernes);
                $( "#cSabado").prop('checked', oDias.Sabado);
                $( "#cDomingo").prop('checked', oDias.Domingo);


                preguntaFecha = [];
                $.each(data.data_pregunta, function( i, pregu ) {

                    preguntaFecha.push(pregu);
                    var dPregunta = pregu.encuesta_pregunta_nombre;
                    if (dPregunta.length > 50){
                        
                        $('#example-1 > tbody:last-child').append('<tr><td class="tabledit-view-mode"><span data-toggle="tooltip" title="' + dPregunta + '">' + dPregunta.substring(0, 50) + '...</span></td><td class="tabledit-view-mode" style="cursor: pointer;"><span class="tabledit-span">'+pregu.encuesta_pregunta_fecha_inicio+'</span><input class="tabledit-input form-control input-sm" type="date" name="fecha" data-id="'+pregu.encuesta_pregunta_id+'" value="'+pregu.encuesta_pregunta_fecha_inicio+'" style="display: none;"></td>');
                    }else{
                        $('#example-1 > tbody:last-child').append('<tr><td class="tabledit-view-mode">'+pregu.encuesta_pregunta_nombre+'</td><td class="tabledit-view-mode" style="cursor: pointer;"><span class="tabledit-span">'+pregu.encuesta_pregunta_fecha_inicio+'</span><input class="tabledit-input form-control input-sm" type="date" name="fecha" data-id="'+pregu.encuesta_pregunta_id+'" value="'+pregu.encuesta_pregunta_fecha_inicio+'" style="display: none;"></td>');
                    }
                    $('[data-toggle="tooltip"]').tooltip();
                    
                });
                divPregunta.show();

                $('#modalNuevoEditar').modal({
                    show: true
                })
            }
        }); 

        
    });


    $(".btnSend").on('click',function () {
        var id = $(this).data('id');

        var dPersonas = [];
        var send=false;
        var _idProgramacion = 0;
        $('#tbl-notificacion > tbody tr').each(function(){
            var inputs = $(this).find('input');

            if(inputs.is(':checked')){
                var _idPersona = inputs.data("id");
                _idProgramacion = inputs.data("programacion");
                
                var dataCheck = {
                    'idPersona': _idPersona
                }
                dPersonas.push(dataCheck);
                send=true;
            }

        });

        if(send){

            var dataSendNot = {
                'idProgramacion': _idProgramacion,
                'dataPersona': dPersonas
            }

            $.ajax({
                url: $("#urlProgramacionSend").val()+"/"+id,
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (response) {
                    if(response){
                        location.reload();
                    }
                },
                data: JSON.stringify(dataSendNot)
            });
        }


    });


    $(".btnAbrir").on('click',function () {
        var _id = $(this).data("id");
        
        $.ajax({
            url: $("#urlPersonasNoti").val()+"/"+_id,
            type: 'get',
            success: function (data) {
                //console.log("get_data_1", data);
                
                $("#divBody").html(data);
                //id="divBody"
                $('#modalVer').modal({
                    show: true
                })
            }
        });

        
    });

    $('.btnNuevo').on('click',function () {
        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Nueva programacion');
        $('#idProgramacion').val(0);

        $('#encuesta').val('');
        $('#grupo').val('');

        $('#encuesta').attr('disabled',false);
        $('#grupo').attr('disabled',false);


        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        $('#tipo_notifi').val('');
        $('#nombre_programacion').val('');

        $('#mensaje_email').val('Para nosotros es importante conocer tu nivel de progreso con los micro hábitos en este día:');
        $('#asunto_email').val('Nuevo Micro Hábito');
        $('#mensaje_sms').val('Estimado(a) {{nombre}}, tienes un micro habito pendiente. Link: ');

        $('#hora_job').val('');

        $( "#cLunes").prop('checked', false);
        $( "#cMartes").prop('checked', false);
        $( "#cMiercoles").prop('checked', false);
        $( "#cJueves").prop('checked', false);
        $( "#cViernes").prop('checked', false);
        $( "#cSabado").prop('checked', false);
        $( "#cDomingo").prop('checked', false);

        divPregunta.hide();
        $("#example-1 > tbody").empty();
        
        setSwitchery(switchery, true);

        $('#modalNuevoEditar').modal({
            show: true
        })
    });

    $('.btnEliminar').on('click',function () {
        
        var idProgramacion = $(this).data("id");
        var idGrupo = $(this).data("grupo");
        var idEncuesta = $(this).data("encuesta");
        
        var dataForm = {
            'id_encuesta': idEncuesta,
            'id_grupo': idGrupo,
            'id_programacion': idProgramacion
        }

        swal({
            title: "¿Está seguro que desea eliminar?",
            text: "Eliminará la progamación seleccionada",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, deseo eliminar",
            cancelButtonText: "No, cancelar",
            closeOnConfirm: false
        },
        function(){

            $.ajax({
                url: $("#urlProgramacionDel").val(),
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (data) {
                    console.log("get_data_1", data);
                    location.reload();
                },
                data: JSON.stringify(dataForm)
            });
            
        });

        
    });


    var table = $('#dom-jqry-programacion').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "language": {
            "search": "Buscar:"
          }
    });

    
    var btnGuardar = $('#btnGuardar');

    btnGuardar.on('click', function() {
        
        $("#msgErrorCard").hide();
        
        if (btnGuardar.hasClass('btn-disabled disabled')) {
            return false;
        } else {
            btnGuardar.addClass('btn-disabled disabled');
        }

        var dataString = $("#frmProgramacion").serialize();

        
        $('#example-1').find('input').each(function(){
            var dInput = $(this);
            var idPreguntaInput =  dInput.data("id");

            var iPregunta = preguntaFecha.findIndex(item => item.encuesta_pregunta_id === ''+idPreguntaInput+'');//findWithAttr(preguntaFecha, 'encuesta_pregunta_id', ''+idPreguntaInput+'');
            preguntaFecha[iPregunta].encuesta_pregunta_fecha_inicio = dInput.val();
        });

        $.ajax({
            type:"POST",
            url: $("#urlProgramacionForm").val(),
            data:dataString,
            success:function (data) {

                if(data.estado){

                    var dataPost = {
                        'programacion_id': data.encuesta_programacion_id,
                        'preguntas': preguntaFecha
                    }

                    $.ajax({
                        url: $("#urlProgramacionPreguntaForm").val(),
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function (response) {
                            if(response){
                                location.reload();
                            }
                        },
                        data: JSON.stringify(dataPost)
                    });

                    
                }else{
                    $("#msgCampos").html(data);
                    $("#msgErrorCard").show();
                }
               
            }
        });
    });

       
  })