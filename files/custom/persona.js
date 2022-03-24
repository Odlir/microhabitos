$(document).on({
    ajaxStart: function() { $('#spinner').show(); },
     ajaxStop: function() {
          $('#spinner').hide(); 
          
          $('#btnGuardar').removeClass('btn-disabled disabled');

        }    
});

$(document).ready(function() {

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

    
    $('.btnPlantilla').on('click',function () {
        //e.preventDefault();  //stop the browser from following
        window.location.href = $("#urlDescarga").val();
    });
    
    $('.btnImportar').on('click',function () {

        $('#spinner1').hide();
        $("#msgErrorCard1").hide();

        $('#modalImportar').modal({
            show: true
        })
    });

    $('.btnEditar').on('click',function () {
        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Editar persona');
        $("#usuario").attr('disabled', 'disabled');
        var dataId = $(this).data("id");
        
        $.ajax({
            type:"POST",
            url: $("#urlPersonaGet").val()+"/"+dataId,
            success:function (data) {
                console.log("get_data_1", data);
                var result = data.data[0];

                console.log("get_data", result);
                $('#idPersona').val(result.persona_id);
                $('#nombres').val(result.persona_nombre);
                $('#apellidoPaterno').val(result.persona_apellido_paterno);
                $('#apellidoMaterno').val(result.persona_apellido_materno);
                $('#tipoDocumento').val(result.persona_tipo_documento_id);
                $('#nroDocumento').val(result.persona_numero_documento);
                $('#estadoCivil').val(result.persona_estado_civil_id);
                $('#fechaNacimiento').val(result.persona_fecha_nacimiento);
                $('#lugarNacimiento').val(result.persona_lugar_nacimiento);
                $('#genero').val(result.persona_genero_id);
                $('#email').val(result.persona_email);
                $('#telefono').val(result.persona_telefono);
                $('#celular').val(result.persona_celular);
                $('#grupo').val(result.grupo_id);

                $('#usuario').val(result.usuario_nombre);
                $('#contrasena').val(result.usuario_contrasena);
                $('#tipoUsuario').val(result.usuario_tipo_id);
                
                setSwitchery(switchery, result.persona_estado_id == 2);

                $('#modalNuevoEditar').modal({
                    show: true
                })
            }
        }); 

        
    });


    $('.btnNuevo').on('click',function () {
        $('#usuario').removeAttr('disabled');
        $("#msgErrorCard").hide();
        $('#modalTitulo').html('Nueva persona');
        $('#idPersona').val(0);
        $('#nombres').val('');
        $('#apellidoPaterno').val('');
        $('#apellidoMaterno').val('');
        $('#tipoDocumento').val('');
        $('#nroDocumento').val('');
        $('#estadoCivil').val('');
        $('#fechaNacimiento').val('');
        $('#lugarNacimiento').val('');
        $('#genero').val('');
        $('#email').val('');
        $('#telefono').val('');
        $('#celular').val('');
        $('#grupo').val('');

        $('#usuario').val('');
        $('#contrasena').val('');
        $('#tipoUsuario').val('');

        setSwitchery(switchery, true);

        $('#modalNuevoEditar').modal({
            show: true
        })
    });

    $('.btnEliminar').on('click',function () {
        var dataId = $(this).data("id");
        var dataNombre = $(this).data("nombre");
        
        swal({
            title: "¿Está seguro que desea eliminar?",
            text: "Eliminará la persona con nombre " + dataNombre,
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
                url: $("#urlPersonaDel").val()+"/"+dataId,
                success:function (data) {
                    console.log("get_data_1", data);
                    location.reload();
                }
            });
            
        });

        
    });


    var table = $('#dom-jqry-persona').dataTable({
        "bInfo" : false,
        "lengthChange": true,
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros"
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

        var dataString = $("#frmPersona").serialize();
        console.log(dataString)
        $.ajax({
            type:"POST",
            url: $("#urlPersonaForm").val(),
            data:dataString,
            success:function (data) {
                if(data == "1"){
                    $('#modalNuevoEditar').modal('toggle');
                    location.reload();
                }else{
                    $("#msgCampos").html(data);
                    $("#msgErrorCard").show();
                }
               
            }
        }); 
    });


    //Example 1
    $("#filer_input_persona").filer({
        limit: null,
        maxSize: null,
        extensions: ['xlsx', 'xls'],
        changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Arrastra y suelta archivos aquí</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn btn btn-primary waves-effect waves-light">Buscar archivos</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                            <span class="jFiler-item-others">{{fi-size2}}</span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        uploadFile: {
            url: $("#urlCarga").val(),
            data: null,
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        },
        addMore: false,
        clipBoardPaste: true,
        excludeName: null,
        beforeRender: null,
        afterRender: null,
        beforeShow: null,
        beforeSelect: null,
        onSelect: null,
        afterShow: null,
        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
            //var file = file.name;
            //$.post('../files/assets/pages/filer/php/ajax_remove_file.php', {file: file});
        },
        onEmpty: null,
        options: null,
        captions: {
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Solo esta permitido archivos xlsx o xls.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
       
  })