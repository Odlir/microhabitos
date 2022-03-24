$(document).on({
    ajaxStart: function() { $('#spinner').show(); },
     ajaxStop: function() {
          $('#spinner').hide(); 
          
          $('#btnGuardar').removeClass('btn-disabled disabled');

        }    
});

$(document).ready(function() {

    var mensajeRespuestaDefault = "Se guardaron los datos de la encuesta correctamente.";
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    var defaultData = '<p><strong>Selecciona con que frecuencia lograste alcanzar tu micro hábito</strong></p><p>donde 1 no cumpliste el micro hábito y {{maximo}} lograste cumplir todo el micro hábito</p>';

    var tableEncuesta = $('#dom-jqry-encuesta').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "language": {
            "search": "Buscar:"
          }
    });

    var valor_insert = 0; //0-> insert - 1-> update

    var elemsmallEncuesta = document.querySelector('#estadoEncuesta');
    var switcheryEncuesta = new Switchery(elemsmallEncuesta, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    var elemsmallPregunta = document.querySelector('#estadoPregunta');
	var switcheryPregunta = new Switchery(elemsmallPregunta, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    var elemsmallRespuesta = document.querySelector('#estadoRespuesta');
	var switcheryRespuesta = new Switchery(elemsmallRespuesta, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    var elemsmallCelRespuesta = document.querySelector('#celebracionRespuesta');
	var switcheryCelRespuesta = new Switchery(elemsmallCelRespuesta, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    var elemsmallMaxRespuesta = document.querySelector('#maximaRespuesta');
	var switcheryMaxRespuesta = new Switchery(elemsmallMaxRespuesta, { color: '#4099ff', jackColor: '#fff', size: 'small' });

    

    function setSwitchery(switchElement, checkedBool) {
        if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
            switchElement.setPosition(true);
            switchElement.handleOnchange(true);
        }
    }

    $('#spinner').hide();
    $("#msgErrorCard").hide();

    $("#tipoPreguntaDiv").hide();

    $('.btnEditar').on('click',function () {
        
        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Editar micro hábito');
        $('#idEncuesta').val(0);
        
        $('#nombre_pregunta').val('');
        $('#pregunta_inicio').val(today);
        editor.setData(defaultData);
        $('#tipo_pregunta').val('2');
        $('#escala_respuesta').val('');
        setSwitchery(switcheryPregunta, true);
        $('.btnAddPregunta').show();
        $('.btnGuardarPregunta').hide();
        $('.btnCancelarPregunta').hide();

        var tPre = $('#dom-jqry-pregunta').DataTable();
        tPre.clear().draw();

        $('#encuesta_grupo').val('');
        $("#encuesta_grupo > option").each(function() {
            $(this).show();
        });
        var tGrupo = $('#dom-jqry-grupo').DataTable();
        tGrupo.clear().draw();

        $('#nombre_respuesta').val('');
        $('#mensaje_respuesta').val(mensajeRespuestaDefault);
        
        setSwitchery(switcheryRespuesta, true);
        setSwitchery(switcheryCelRespuesta, true);
        setSwitchery(switcheryMaxRespuesta, false);
        
        
        $(".btnAddRespuesta").show();
        $(".btnGuardarRespuesta").hide();
        $(".btnCancelarRespuesta").hide();
        var tRes = $('#dom-jqry-respuesta').DataTable();
        tRes.clear().draw();
        
        
        dPreguntas = [];
        tblPreguntarowI = 0;
        tblPreguntadataR = 0;

        indexPregunta = 0;
        dGrupos = [];

        valor_insert = 1;

        
        var dataId = $(this).data("id");
        
        $.ajax({
            type:"POST",
            url: $("#urlEncuestaGet").val()+"/"+dataId,
            success:function (data) {
                var result = data;
                

                $('#idEncuesta').val(result.encuesta.encuesta_id);
                $('#categoria').val(result.encuesta.categoria_id);
                $('#nombre_encuesta').val(result.encuesta.encuesta_nombre);
                $('#tiempo_alerta').val(result.encuesta.encuesta_tiempo_alerta_horas);
                setSwitchery(switcheryEncuesta, result.encuesta.encuesta_estado_id == 2);

                $('#fecha_inicio').val(result.encuesta.encuesta_fecha_inicio_alerta);
                $('#fecha_fin').val(result.encuesta.encuesta_fecha_fin_alerta);
                $('#tipo_notifi').val(result.encuesta.encuesta_tipo_alerta_id);
               

                $.each(result.preguntas, function( i, pregu ) {
                    
                    var objPregunta = {
                        'idEncuestaPregunta': pregu.encuesta_pregunta_id,
                        'nombrePregunta': pregu.encuesta_pregunta_nombre,
                        'fechaInicioPregunta': pregu.encuesta_pregunta_fecha_inicio,
                        'descripcionPregunta': pregu.encuesta_pregunta_descripcion_html,
                        'tipoPreguntaId': pregu.encuesta_pregunta_tipo_id,
                        'estadoPreguntaId': pregu.encuesta_pregunta_estado_id,
                        'fechaCreaStr': pregu.encuesta_pregunta_fecha_creacion,
                        'fechaModStr': pregu.encuesta_pregunta_fecha_modificacion,
                        'aRespuestas': [],
                        'flagEliminado': 0
                    };
                    dPreguntas.push(objPregunta);
                    
                    var t = $('#dom-jqry-pregunta').DataTable();
                    t.row.add( [
                        pregu.encuesta_pregunta_id,
                        pregu.encuesta_pregunta_nombre,
                        pregu.encuesta_pregunta_fecha_inicio,
                        pregu.encuesta_pregunta_estado_str,
                        pregu.encuesta_pregunta_fecha_creacion,
                        pregu.encuesta_pregunta_fecha_modificacion
                    ] ).draw( false );
                    
                    $.each(pregu.respuestas, function( key, resp ) {
                       
                        var sendData = {
                            'idPreguntaRespuesta': resp.encuesta_pregunta_respuesta_id,
                            'nombreRespuesta': resp.encuesta_pregunta_respuesta_nombre,
                            'mensajeRespuesta': resp.encuesta_pregunta_respuesta_mensaje,
                            'estadoRespuestaStr': resp.encuesta_pregunta_respuesta_estado_str,
                            'estadoRespuestaId': resp.encuesta_pregunta_respuesta_estado_id,
                            'celebracionRespuesta': resp.encuesta_pregunta_respuesta_flag_celebracion,
                            'maximaRespuesta': resp.encuesta_pregunta_respuesta_flag_maxima,
                            'fechaCreaStr': resp.encuesta_pregunta_respuesta_fecha_creacion,
                            'fechaModStr': resp.encuesta_pregunta_respuesta_fecha_modificacion,
                            'flagEliminado': 0
                        };
            
                        dPreguntas[i].aRespuestas.push(sendData);
                        
                        var t = $('#dom-jqry-respuesta').DataTable();
                        t.row.add( [
                            resp.encuesta_pregunta_respuesta_id,
                            resp.encuesta_pregunta_respuesta_nombre,
                            resp.encuesta_pregunta_respuesta_mensaje,
                            resp.encuesta_pregunta_respuesta_estado_str,
                            resp.encuesta_pregunta_respuesta_flag_celebracion,
                            resp.encuesta_pregunta_respuesta_flag_maxima,
                            resp.encuesta_pregunta_respuesta_fecha_creacion,
                            resp.encuesta_pregunta_respuesta_fecha_modificacion
                        ] ).draw( false );
                        
                    })
                    
                });

                /*$.each(result.grupos, function( i, grup ) {
                    var sendGrupos = {
                        'idEncuestaGrupo': grup.encuesta_grupo_id,
                        'GrupoID': grup.grupo_id,
                        'GrupoNombre': grup.grupo_nombre,
                        'fechaCreaStr': grup.encuesta_grupo_fecha_creacion,
                        'flagEliminado': 0
                    };
                    dGrupos.push(sendGrupos);
        
                    $('#encuesta_grupo option:contains("'+grup.grupo_nombre+'")').hide();
                    
                    var t = $('#dom-jqry-grupo').DataTable();
                    t.row.add( [
                        grup.encuesta_grupo_id,
                        grup.grupo_nombre,
                        grup.encuesta_grupo_fecha_creacion
                    ] ).draw( false );
                });*/


                $('#modalNuevoEditar').modal({
                    show: true
                })
            }
        }); 
    });

    
    $(document).on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    
    $('#modalVer').on('hidden.bs.modal', function (e) {
        if ($('.modal:visible').length) { 
            $('body').addClass('modal-open');
        }
    });
    
    
    
    $('.btnNuevo').on('click',function () {

        

        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Nuevo micro hábito');
        $('#idEncuesta').val(0);
        
        $('#nombre_encuesta').val('');
        $('#tiempo_alerta').val('');
        setSwitchery(switcheryEncuesta, true);

        $('#nombre_pregunta').val('');
        $('#pregunta_inicio').val(today);
        editor.setData(defaultData);
        $('#escala_respuesta').val('');
        $('#tipo_pregunta').val('2');
        $('#tipo_notifi').val('1');
        setSwitchery(switcheryPregunta, true);
        $('.btnAddPregunta').show();
        $('.btnGuardarPregunta').hide();
        $('.btnCancelarPregunta').hide();

        var tPre = $('#dom-jqry-pregunta').DataTable();
        tPre.clear().draw();

        $('#encuesta_grupo').val('');
        $("#encuesta_grupo > option").each(function() {
            $(this).show();
        });
        var tGrupo = $('#dom-jqry-grupo').DataTable();
        tGrupo.clear().draw();

        $('#nombre_respuesta').val('');
        $('#mensaje_respuesta').val(mensajeRespuestaDefault);
        setSwitchery(switcheryRespuesta, true);
        setSwitchery(switcheryCelRespuesta, true);
        setSwitchery(switcheryMaxRespuesta, false);
        $(".btnAddRespuesta").show();
        $(".btnGuardarRespuesta").hide();
        $(".btnCancelarRespuesta").hide();
        var tRes = $('#dom-jqry-respuesta').DataTable();
        tRes.clear().draw();
        
        
        dPreguntas = [];
        tblPreguntarowI = 0;
        tblPreguntadataR = 0;

        indexPregunta = 0;
        dGrupos = [];
        valor_insert = 0;

        $('#modalNuevoEditar').modal({
            show: true
        })
    });

    $('.btnEliminar').on('click',function () {
        var dataId = $(this).data("id");
        var dataNombre = $(this).data("nombre");
        
        swal({
            title: "¿Está seguro que desea eliminar?",
            text: "Eliminará el micro hábito con nombre " + dataNombre,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, deseo eliminar",
            cancelButtonText: "No, cancelar",
            closeOnConfirm: false
        },
        function(){
            $.ajax({
                type:"POST",
                url: $("#urlEncuestaDel").val()+"/"+dataId,
                success:function (data) {
                    
                    location.reload();
                }
            });
            
        });

        
    });

    var btnGuardar = $('#btnGuardar');

    btnGuardar.on('click', function() {
        
        $("#msgErrorCard").hide();
        
        if (btnGuardar.hasClass('btn-disabled disabled')) {
            return false;
        } else {
            btnGuardar.addClass('btn-disabled disabled');
        }


        //Cabecera
        var pDias = { 
            'Lunes': $("#cLunes").is(':checked'),
            'Martes': $("#cMartes").is(':checked'),
            'Miercoles': $("#cMiercoles").is(':checked'),
            'Jueves': $("#cJueves").is(':checked'),
            'Viernes': $("#cViernes").is(':checked'),
            'Sabado': $("#cSabado").is(':checked'),
            'Domingo': $("#cDomingo").is(':checked')
        }

        var dCabecera = {
            'idEncuesta': $("#idEncuesta").val(),
            'categoria': $("#categoria").val(),
            'nombreEncuesta': $("#nombre_encuesta").val(),
            'alertaHoras': 0,
            'estadoEncuesta': elemsmallEncuesta.checked ? 2 : 1,
            'fechaInicio':  $("#fecha_inicio").val(),
            'fechaFin': $("#fecha_fin").val(),
            'tipoProgramacion': $("#tipo_notifi").val(),
            'jsonDiasProgramacion': pDias
        }

        
        
        var dataForm = {
            'encuesta': dCabecera,
            'pregunta': dPreguntas,
            'grupo': dGrupos,
            'insert': valor_insert
        }
        
        $.ajax({
            url: $("#urlEncuestaForm").val(),
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if(data.error == false){
                    $('#modalNuevoEditar').modal('toggle');
                    location.reload();
                }
            },
            data: JSON.stringify(dataForm)
        });
    });

    //#region Pregunta
    var tablePregunta = $('#dom-jqry-pregunta').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "columnDefs": [
            { "targets": 0, visible: false},
            { "targets": 1, 
              "render": function (data, type, full, meta) {
                  if (data.length > 20){
                    return '<span data-toggle="tooltip" title="' + data + '">' + data.substring(0, 20) + '...</span>';
                  }else{
                    return data;
                  }
                
              } 
            },
            { "targets": "_all", visible: true},
            {
                "targets": -1,
                "data": null,
                "defaultContent": "<div class='icon-btn'><button class='btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnEditarRespuesta'><i class='icofont icofont-presentation'></i></button><button class='btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnEditarPregunta'><i class='icofont icofont-ui-edit'></i></button><button class='btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon btnEliminarPregunta'><i class='icofont icofont-ui-delete'></i></button></div>"
            }
        ],
        "language": {
            "search": "Buscar:"
          }
    });

    $('#dom-jqry-pregunta').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $("#dom-jqry-pregunta").removeAttr("style");

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    var dPreguntas = [];
    var tblPreguntarowI = 0;
    var tblPreguntadataR = 0;

    $('.btnAddPregunta').on('click',function () {
                

        var escala = $('#escala_respuesta').val(); 
        var nombrePregunta = $('#nombre_pregunta').val();
        var fechaInicioPregunta = $('#pregunta_inicio').val();
        var descripcionPregunta = editor.getData();
        var tipoPreguntaId = "2"; //$('#tipo_pregunta').val();
        var tipoPreguntaStr = "RadioButton";//$("#tipo_pregunta option:selected").text();
        var estadoPreguntaId = elemsmallPregunta.checked ? 2 : 1;
        var estadoPreguntaStr = elemsmallPregunta.checked ? 'Activo' : 'Inactivo';
        
        var fechaCrea = new Date(Date.now());
        var fechaCreaStr = fechaCrea.getFullYear() + "-" + pad(fechaCrea.getMonth()+1, 2) + "-" + pad(fechaCrea.getDay(), 2) + ' '+ pad(fechaCrea.getHours(), 2) + ":" + pad(fechaCrea.getMinutes(), 2) + ":" + pad(fechaCrea.getSeconds(), 2);


        if(tipoPreguntaId == ''){
            return;
        }
        
        if (escala < 3 || escala > 10){
            return;
        }

        var dataAIF = dPreguntas.find(x => x.nombrePregunta === nombrePregunta && x.flagEliminado == 0);

        if(!dataAIF){
           //my object
            var sendData = {
                'idEncuestaPregunta': 0,
                'nombrePregunta':nombrePregunta,
                'fechaInicioPregunta':fechaInicioPregunta,
                'descripcionPregunta':descripcionPregunta,
                'tipoPreguntaId':tipoPreguntaId,
                'estadoPreguntaId':estadoPreguntaId,
                'fechaCreaStr':fechaCreaStr,
                'fechaModStr':'',
                'aRespuestas': [],
                'flagEliminado': 0
            };
            dPreguntas.push(sendData);


            iPregunta = dPreguntas.findIndex(x => x.nombrePregunta === nombrePregunta && x.flagEliminado == 0);

            for(var i = 1; i <= escala; i++){
                var sRespuesta = {
                    'idPreguntaRespuesta': 0,
                    'nombreRespuesta': i,
                    'mensajeRespuesta': mensajeRespuestaDefault,
                    'estadoRespuestaStr': 'Activo',
                    'estadoRespuestaId': 2,
                    'celebracionRespuesta': 1,
                    'maximaRespuesta': 0,
                    'fechaCreaStr':fechaCreaStr,
                    'fechaModStr':'',
                    'flagEliminado': 0
                };
                dPreguntas[iPregunta].aRespuestas.push(sRespuesta);
            }
            


            var t = $('#dom-jqry-pregunta').DataTable();
            t.row.add( [
                0,
                nombrePregunta,
                fechaInicioPregunta,
                estadoPreguntaStr,
                fechaCreaStr,
                ''
            ] ).draw( false );
            

            $('#nombre_pregunta').val('');
            $('#pregunta_inicio').val(today);
            editor.setData(defaultData);
            $('#escala_respuesta').val('');
            $('#tipo_pregunta').val('2');
            setSwitchery(switcheryPregunta, true);
        }else{
            alert('Pregunta ya existe')
        }

        
    });

    

    $('.btnGuardarPregunta').on('click',function () {

        var nombrePregunta = $('#nombre_pregunta').val();
        var fechaInicioPregunta = $('#pregunta_inicio').val();
        var descripcionPregunta = editor.getData();

        var tipoPreguntaId = "2";//$('#tipo_pregunta').val();
        var tipoPreguntaStr = "RadioButton";//$("#tipo_pregunta option:selected").text();
        var estadoPreguntaId = elemsmallPregunta.checked ? 2 : 1;
        var estadoPreguntaStr = elemsmallPregunta.checked ? 'Activo' : 'Inactivo';
        
        var fechaCrea = new Date(Date.now());
        var fechaModStr = fechaCrea.getFullYear() + "-" + pad(fechaCrea.getMonth()+1, 2) + "-" + pad(fechaCrea.getDay(), 2) + ' '+ pad(fechaCrea.getHours(), 2) + ":" + pad(fechaCrea.getMinutes(), 2) + ":" + pad(fechaCrea.getSeconds(), 2);

        var cTable = $('#dom-jqry-pregunta').DataTable();
        someId = tblPreguntarowI ; //first row
        
        //newData = [ tblPreguntadataR[0], nombrePregunta, tipoPreguntaStr, estadoPreguntaStr, tblPreguntadataR[4], fechaModStr ];
        newData = [ tblPreguntadataR[0], nombrePregunta, fechaInicioPregunta, estadoPreguntaStr, tblPreguntadataR[4], fechaModStr ]; //Array, data here must match structure of table data
        cTable.row(someId).data(newData).draw();

        //Find index of specific object using findIndex method.    
        var objIndex = dPreguntas.findIndex((x => x.nombrePregunta === tblPreguntadataR[1]));

        //Update object's name property.
        dPreguntas[objIndex].nombrePregunta = nombrePregunta
        dPreguntas[objIndex].fechaInicioPregunta = fechaInicioPregunta
        dPreguntas[objIndex].descripcionPregunta = descripcionPregunta
        dPreguntas[objIndex].tipoPreguntaId = tipoPreguntaId
        dPreguntas[objIndex].estadoPreguntaId = estadoPreguntaId
        dPreguntas[objIndex].fechaModStr = fechaModStr

        //Ocultar botones
        $(".btnGuardarPregunta").hide();
        $(".btnCancelarPregunta").hide();
        $(".btnAddPregunta").show();

        //Limpiar campos
        $('#nombre_pregunta').val('');
        $('#pregunta_inicio').val(today);
        editor.setData(defaultData);
        $('#escala_respuesta').val('');
        $('#tipo_pregunta').val('2');
        setSwitchery(switcheryPregunta, true);

        
    });

    $('.btnCancelarPregunta').on('click',function () {
        //Ocultar botones
        $(".btnGuardarPregunta").hide();
        $(".btnCancelarPregunta").hide();
        $(".btnAddPregunta").show();

        //Limpiar campos
        $('#nombre_pregunta').val('');
        $('#pregunta_inicio').val(today);
        editor.setData(defaultData);
        $('#escala_respuesta').val('');
        $('#tipo_pregunta').val('2');
        setSwitchery(switcheryPregunta, true);
    });

    $('#dom-jqry-pregunta tbody').on( 'click', '.btnEditarPregunta', function () {
        var table = $('#dom-jqry-pregunta').DataTable();  
        tblPreguntadataR = table.row( $(this).parents('tr') ).data();
        tblPreguntarowI = table.row( $(this).parents('tr') ).index();

        var objIndex = dPreguntas.findIndex((x => x.nombrePregunta === tblPreguntadataR[1]));

        $('#nombre_pregunta').val(dPreguntas[objIndex].nombrePregunta);
        $('#pregunta_inicio').val(dPreguntas[objIndex].fechaInicioPregunta);
        editor.setData(dPreguntas[objIndex].descripcionPregunta);
        //var tipoPreguntaStr = tblPreguntadataR[2];
        var estadoPreguntaStr = tblPreguntadataR[3];

        /*$.each($("#tipo_pregunta option"), function(){
            if($(this).text() == tipoPreguntaStr){
                $('#tipo_pregunta').val($(this).val());
            }
        });*/

        setSwitchery(switcheryPregunta, estadoPreguntaStr == 'Activo' ? true : false);

        //Ocultar botones
        $(".btnGuardarPregunta").show();
        $(".btnCancelarPregunta").show();
        $(".btnAddPregunta").hide();
    } );

    $('#dom-jqry-pregunta tbody').on( 'click', '.btnEliminarPregunta', function () {
        var table = $('#dom-jqry-pregunta').DataTable();  
        var data = table.row( $(this).parents('tr') ).data();
        var dataA = dPreguntas.find(x => x.nombrePregunta === data[1] && x.flagEliminado == 0);
        
        var objIndex = dPreguntas.findIndex((x => x.nombrePregunta === data[1] && x.flagEliminado == 0));
        dPreguntas[objIndex].flagEliminado = 1;
      
        table.row($(this).parents('tr')).remove().draw();
        
    } );
    //#endregion Pregunta
   
    //#region Respuesta

    var tableRespuesta = $('#dom-jqry-respuesta').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "columnDefs": [
            { "targets": 0, visible: false},
            { "targets": [1,2], 
              "render": function (data, type, full, meta) {
                  if (data.length > 20){
                    return '<span data-toggle="tooltip" title="' + data + '">' + data.substring(0, 20) + '...</span>';
                  }else{
                    return data;
                  }
                
              } 
            },
            { "targets": [4,5], 
              "render": function (data, type, full, meta) {
                  if (data == 1){
                    return 'Si';
                  }else{
                    return 'No';
                  }
                
              } 
            },
            { "targets": "_all", visible: true},
            {
                "targets": -1,
                "data": null,
                "defaultContent": "<div class='icon-btn'><button class='btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnModificarRespuesta'><i class='icofont icofont-ui-edit'></i></button><button class='btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon btnEliminarRespuesta'><i class='icofont icofont-ui-delete'></i></button></div>"
            }
        ],
        "language": {
            "search": "Buscar:"
          }
    });
    $("#dom-jqry-respuesta").removeAttr("style");


    $('#dom-jqry-respuesta').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    var indexPregunta = 0;
    $('#dom-jqry-pregunta tbody').on( 'click', '.btnEditarRespuesta', function () {

        var table = $('#dom-jqry-pregunta').DataTable();  
        var data = table.row( $(this).parents('tr') ).data();
        
        indexPregunta = dPreguntas.findIndex(x => x.nombrePregunta === data[1] && x.flagEliminado == 0);
        
        var tRes = $('#dom-jqry-respuesta').DataTable();
        tRes.clear().draw();

        $.each(dPreguntas[indexPregunta].aRespuestas, function( key, value ) {
           
            if(value.flagEliminado == 0){
                var t = $('#dom-jqry-respuesta').DataTable();
                t.row.add( [
                    value.idPreguntaRespuesta,
                    value.nombreRespuesta,
                    value.mensajeRespuesta,
                    value.estadoRespuestaStr,
                    value.celebracionRespuesta,
                    value.maximaRespuesta,
                    value.fechaCreaStr,
                    value.fechaModStr
                ] ).draw( false );
            }
            
        });

        $(".btnGuardarRespuesta").hide();
        $(".btnCancelarRespuesta").hide();
        $(".btnAddRespuesta").show();

        $('#modalVer').modal('show');
    } );

    
    $('.btnAddRespuesta').on('click',function () {
      

        var nombreRespuesta = $('#nombre_respuesta').val();
        var mensajeRespuesta = $('#mensaje_respuesta').val();
        var estadoRespuestaId = elemsmallRespuesta.checked ? 2 : 1;
        var estadoRespuestaStr = elemsmallRespuesta.checked ? 'Activo' : 'Inactivo';
        var celebracionRespuesta = elemsmallCelRespuesta.checked ? 1 : 0;
        var maximaRespuesta = elemsmallMaxRespuesta.checked ? 1 : 0;
        
        
        var fechaCrea = new Date(Date.now());
        var fechaCreaStr = fechaCrea.getFullYear() + "-" + pad(fechaCrea.getMonth()+1, 2) + "-" + pad(fechaCrea.getDay(), 2) + ' '+ pad(fechaCrea.getHours(), 2) + ":" + pad(fechaCrea.getMinutes(), 2) + ":" + pad(fechaCrea.getSeconds(), 2);

        var dataAIF = dPreguntas[indexPregunta].aRespuestas.find(x => x.nombreRespuesta === nombreRespuesta && x.flagEliminado == 0);

        if(!dataAIF){
           //my object
            var sendData = {
                'idPreguntaRespuesta': 0,
                'nombreRespuesta':nombreRespuesta,
                'mensajeRespuesta':mensajeRespuesta,
                'estadoRespuestaStr': estadoRespuestaStr,
                'estadoRespuestaId':estadoRespuestaId,
                'celebracionRespuesta':celebracionRespuesta,
                'maximaRespuesta': maximaRespuesta,
                'fechaCreaStr':fechaCreaStr,
                'fechaModStr':'',
                'flagEliminado': 0
            };

            dPreguntas[indexPregunta].aRespuestas.push(sendData);
            
            var t = $('#dom-jqry-respuesta').DataTable();
            t.row.add( [
                0,
                nombreRespuesta,
                mensajeRespuesta,
                estadoRespuestaStr,
                celebracionRespuesta,
                maximaRespuesta,
                fechaCreaStr,
                ''
            ] ).draw( false );
            

            $('#nombre_respuesta').val('');
            $('#mensaje_respuesta').val(mensajeRespuestaDefault);
            setSwitchery(switcheryRespuesta, true);
            setSwitchery(switcheryCelRespuesta, true);
            setSwitchery(switcheryMaxRespuesta, false);
            
        }else{
            alert('Respuesta ya existe')
        }

        
    });

    $('#dom-jqry-respuesta tbody').on( 'click', '.btnEliminarRespuesta', function () {
        

        var table = $('#dom-jqry-respuesta').DataTable();  
        var data = table.row( $(this).parents('tr') ).data();
        
        var objIndex = dPreguntas[indexPregunta].aRespuestas.findIndex((x => x.nombreRespuesta === data[1] && x.flagEliminado == 0));
        dPreguntas[indexPregunta].aRespuestas[objIndex].flagEliminado = 1;
      
        table.row($(this).parents('tr')).remove().draw();
        

        
    } );

    $('#dom-jqry-respuesta tbody').on( 'click', '.btnModificarRespuesta', function () {
        

        var table = $('#dom-jqry-respuesta').DataTable();  
        tblRespuestadataR = table.row( $(this).parents('tr') ).data();
        tblRespuestarowI = table.row( $(this).parents('tr') ).index();
       
        $('#nombre_respuesta').val(tblRespuestadataR[1]);
        $('#mensaje_respuesta').val(tblRespuestadataR[2]);
        var estadoRespuestaStr = tblRespuestadataR[3];
        var celebracionRespuesta = tblRespuestadataR[4];
        var maximaRespuesta = tblRespuestadataR[5];
        


        setSwitchery(switcheryRespuesta, estadoRespuestaStr == 'Activo' ? true : false);
        setSwitchery(switcheryCelRespuesta, celebracionRespuesta == '1' ? true : false);
        setSwitchery(switcheryMaxRespuesta, maximaRespuesta == '1' ? true : false);
        
        //Ocultar botones
        $(".btnGuardarRespuesta").show();
        $(".btnCancelarRespuesta").show();
        $(".btnAddRespuesta").hide();
        
    } );

    $('.btnGuardarRespuesta').on('click',function () {

        var nombreRespuesta = $('#nombre_respuesta').val();
        var mensajeRespuesta = $('#mensaje_respuesta').val();
        var estadoRespuestaId = elemsmallRespuesta.checked ? 2 : 1;
        var estadoRespuestaStr = elemsmallRespuesta.checked ? 'Activo' : 'Inactivo';
        var celebracionRespuesta = elemsmallCelRespuesta.checked ? 1 : 0;
        var maximaRespuesta = elemsmallMaxRespuesta.checked ? 1 : 0;

        var fechaCrea = new Date(Date.now());
        var fechaModStr = fechaCrea.getFullYear() + "-" + pad(fechaCrea.getMonth()+1, 2) + "-" + pad(fechaCrea.getDay(), 2) + ' '+ pad(fechaCrea.getHours(), 2) + ":" + pad(fechaCrea.getMinutes(), 2) + ":" + pad(fechaCrea.getSeconds(), 2);

        var cTable = $('#dom-jqry-respuesta').DataTable();
        someId = tblRespuestarowI ; //first row
        
        newData = [ tblRespuestadataR[0], nombreRespuesta, mensajeRespuesta, estadoRespuestaStr, celebracionRespuesta, maximaRespuesta, tblRespuestadataR[6], fechaModStr ]; //Array, data here must match structure of table data
        cTable.row(someId).data(newData).draw();

        //Find index of specific object using findIndex method.    
        var objIndex = dPreguntas[indexPregunta].aRespuestas.findIndex((x => x.nombreRespuesta === tblRespuestadataR[1]));
  
        //Update object's name property.
        dPreguntas[indexPregunta].aRespuestas[objIndex].nombreRespuesta = nombreRespuesta
        dPreguntas[indexPregunta].aRespuestas[objIndex].mensajeRespuesta = mensajeRespuesta
        dPreguntas[indexPregunta].aRespuestas[objIndex].estadoRespuestaId = estadoRespuestaId
        dPreguntas[indexPregunta].aRespuestas[objIndex].celebracionRespuesta = celebracionRespuesta
        dPreguntas[indexPregunta].aRespuestas[objIndex].maximaRespuesta = maximaRespuesta;
        dPreguntas[indexPregunta].aRespuestas[objIndex].fechaModStr = fechaModStr

        //Ocultar botones
        $(".btnGuardarRespuesta").hide();
        $(".btnCancelarRespuesta").hide();
        $(".btnAddRespuesta").show();

        //Limpiar campos
        $('#nombre_respuesta').val('');
        $('#mensaje_respuesta').val(mensajeRespuestaDefault);
        setSwitchery(switcheryRespuesta, true);
        setSwitchery(switcheryCelRespuesta, true);
        
    });

    //#endregion Respuesta

    //#region Grupo

    /*var tableGrupo = $('#dom-jqry-grupo').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "columnDefs": [
            { "targets": 0, visible: false},
            { "targets": "_all", visible: true},
            {
                "targets": -1,
                "data": null,
                "defaultContent": "<div class='icon-btn'><button class='btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon btnEliminarGrupo' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='icofont icofont-ui-delete'></i></button></div>"
            }
        ],
        "language": {
            "search": "Buscar:"
          }
    });
*/

    //$("#dom-jqry-grupo").removeAttr("style");

    var dGrupos = [];
    /*$('.btnAddGrupo').on('click',function () {

        var iGrupo = $("#encuesta_grupo").val();
        var iGrupoText = $("#encuesta_grupo option:selected").text();

        if(iGrupo != ''){
            var fechaCreaG = new Date(Date.now());
            var fechaCreaStrG = fechaCreaG.getFullYear() + "-" + pad(fechaCreaG.getMonth()+1, 2) + "-" + pad(fechaCreaG.getDay(), 2) + ' '+ pad(fechaCreaG.getHours(), 2) + ":" + pad(fechaCreaG.getMinutes(), 2) + ":" + pad(fechaCreaG.getSeconds(), 2);
    
            var sendGrupos = {
                'idEncuestaGrupo': 0,
                'GrupoID': iGrupo,
                'GrupoNombre': iGrupoText,
                'fechaCreaStr': fechaCreaStrG,
                'flagEliminado': 0
            };
            dGrupos.push(sendGrupos);

            $("#encuesta_grupo option:selected").hide();
            $("#encuesta_grupo").val('');
            var t = $('#dom-jqry-grupo').DataTable();
            t.row.add( [
                0,
                iGrupoText,
                fechaCreaStrG
            ] ).draw( false );
        }
    });*/

    /*$('#dom-jqry-grupo tbody').on( 'click', '.btnEliminarGrupo', function () {
        

        var table = $('#dom-jqry-grupo').DataTable();  
        var data = table.row( $(this).parents('tr') ).data();
        var dataA = dGrupos.find(x => x.GrupoNombre === data[1] && x.flagEliminado == 0);
        $('#encuesta_grupo option:contains("'+data[1]+'")').show();
        
        var objIndex = dGrupos.findIndex((x => x.GrupoNombre === data[1] && x.flagEliminado == 0));
        dGrupos[objIndex].flagEliminado = 1;
      
        table.row($(this).parents('tr')).remove().draw();
        

        
    } );*/
    //#endregion Grupo

    
  });
