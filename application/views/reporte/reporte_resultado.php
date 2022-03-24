<div class="card">
    <div class="card-header">
        <h5>Resultados</h5>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive dt-responsive">
                    <table id="dom-jqry-reporte" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Comparar</th>
                                <th>Programación</th>
                                <?php if($esPersona){ ?>
                                <th>Persona</th>
                                <?php } ?>
                                <th>Micro hábito</th>
                                <th>Grupo</th>
                                <th>Estado</th>
                                <th>Fecha inicio</th>
                                <th>Fecha fin</th>
                                <th>Fecha creación</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $d) { ?>
                            <tr>
                                <td><input type="checkbox" name="chkComparar" data-id="<?php echo $d->encuesta_programacion_id; ?>"></td>
                                <td><?php echo $d->encuesta_programacion_nombre; ?></td>
                                <?php if($esPersona){ ?>
                                <td><?php echo $d->persona_nombre_completo; ?></td>
                                <?php } ?>
                                <td><?php echo $d->encuesta_nombre; ?></td>
                                <td><?php echo $d->grupo_nombre; ?></td>
                                <td><?php echo $d->encuesta_programacion_estado_str; ?></td>
                                <td><?php echo $d->encuesta_programacion_fecha_inicio; ?></td>
                                <td><?php echo $d->encuesta_programacion_fecha_fin; ?></td>
                                <td><?php echo $d->encuesta_programacion_fecha_creacion; ?></td>
                                <td>
                                    <div class="icon-btn">
                                    <?php if($esPersona){ ?>
                                        <button class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon btnAbrir" data-toggle="tooltip" title="Ver"  data-id="<?php echo $d->encuesta_programacion_id; ?>" data-persona="<?php echo $d->persona_id; ?>"><i class="icofont icofont-eye"></i></button>
                                    <?php } else { ?>
                                        <button class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon btnAbrir" data-toggle="tooltip" title="Ver"  data-id="<?php echo $d->encuesta_programacion_id; ?>" data-persona="0"><i class="icofont icofont-eye"></i></button>
                                    <?php }?>
                                    <button class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon btnExcelDetalle" data-toggle="tooltip" title="Detalles Excel"  data-id="<?php echo $d->encuesta_programacion_id; ?>" data-persona="0"><i class="icofont icofont-file-excel"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function ($) {

    $(".btnAbrir").on('click', function(){

        var id_programacion = $(this).data("id");
        var id_persona = $(this).data("persona");

        console.log("id_programacion", id_programacion);
        console.log("id_persona", id_persona);

        $("#tabla-resultados").hide();

        if(id_persona != 0){
            $.ajax({
                url: "<?=site_url('reporte/getDetallePersona')?>",
                type: 'POST',
                data: "persona_id="+id_persona+"&programacion_id="+id_programacion,
                error: function() {
                    $("#tabla-resultados").show();
                },
                success: function(data) {
                    $("#tabla-detalles").html(data);
                    $("#tabla-detalles").show();
                }
            });
        }else{
            $.ajax({
                url: "<?=site_url('reporte/getDetalleEncuesta')?>",
                type: 'POST',
                data: "programacion_id="+id_programacion,
                error: function() {
                    $("#tabla-resultados").show();
                },
                success: function(data) {
                    $("#tabla-detalles").html(data);
                    $("#tabla-detalles").show();
                }
            });
        }
    });

    $(".btnExcelDetalle").on('click', function(){

        var id_programacion = $(this).data("id");
        var id_persona = $(this).data("persona");

        console.log("id_programacion", id_programacion);
        console.log("id_persona", id_persona);

        var url_xlsx = "<?=site_url('reporte/getDetalleExcel')?>"+"/"+id_programacion;
        console.log(url_xlsx);

        window.open(url_xlsx, '_blank');

    });

    var table = $('#dom-jqry-reporte').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "language": {
            "search": "Buscar:"
        }
    });

    var contador = 0;
    var dataCompara = [];
    $('#tabla-resultados').on('click', 'input[type="checkbox"]', function() {
        var $checkbox = $(this);
        var dataID = $checkbox.data('id');

        if ($checkbox.is(":checked")) {
            if(contador == 2){
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            dataCompara.push(dataID);
            contador++;
        } else {
            dataCompara.splice( $.inArray(dataID, dataCompara), 1 );
            contador--;
        }
        
        if(contador == 2){
            $('.btnComparar').prop('disabled', false);
            $('.btnComparar').removeClass('btn-disabled disabled');
        }else{
            $('.btnComparar').prop('disabled', true);
            $('.btnComparar').addClass('btn-disabled disabled');
        }
        
    }); 

    $('input[type="checkbox"]').click(function(event) {

        
    });

    $(".btnComparar").on('click', function(){

        $("#tabla-resultados").hide();
        
        $.ajax({
            url: "<?=site_url('reporte/getComparar')?>",
            type: 'POST',
            data: "id_primero="+dataCompara[0]+"&id_segundo="+dataCompara[1],
            error: function() {
                $("#tabla-resultados").show();
            },
            success: function(data) {
                $("#tabla-detalles").html(data);
                $("#tabla-detalles").show();
            }
        });
        
    });

    
});
</script>