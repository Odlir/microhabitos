<div class="table-responsive dt-responsive">
    <table id="tbl-notificacion" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th><input type="checkbox" id="chkTodos"/> Todos</th>
                <th>Persona</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Cantidad Envío Email</th>
                <th>Cantidad Envío SMS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d) { ?>
            <tr>
                <td><input type="checkbox" data-programacion ="<?php echo $d->encuesta_programacion_id; ?>" data-id="<?php echo $d->persona_id; ?>" /></td>
                <td><?php echo $d->persona_nombre_completo; ?></td>
                <td><?php echo $d->persona_email; ?></td>
                <td><?php echo $d->persona_celular; ?></td>
                <td><?php echo $d->encuesta_persona_cantidad_email; ?></td>
                <td><?php echo $d->encuesta_persona_cantidad_sms; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {

    var table = $('#tbl-notificacion').dataTable({
        "bInfo" : false,
        "lengthChange": false,
        "ordering": false,
        "autoWidth": false,
        "scrollCollapse": true,
        "paging": false,
        "language": {
            "search": "Buscar:"
          }
    });

    $('#chkTodos').change(function() {
        var stado = $(this).is(':checked');
        console.log("estado", stado);
        $('#tbl-notificacion > tbody tr').each(function(){
            var inputs = $(this).find('input');
            inputs.attr('checked', stado);
            inputs.prop("checked", stado);
        });    
    });
});

</script>