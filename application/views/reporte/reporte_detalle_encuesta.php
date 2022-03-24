<?php

$TotalPregunta = $total_preguntas;
$TotalRespuestas =  $total_respondidas;

$TotalPorResponder = $TotalPregunta - $TotalRespuestas;

$resutado = 100-((($TotalPregunta-$TotalRespuestas)/$TotalPregunta)*100);
$resutado = round($resutado, 2)


?>
<div class="card">
    <div class="card-header">
        <h5>Detalles</h5>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-12">
                <div class="card app-design">
                    <div class="card-header">
                        <h5>General</h5>
                    </div>
                    <div class="card-block">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="text-right">
                                    <button class="btn waves-effect waves-light btn-warning btnRegresar"><i class="icofont icofont-arrow-left"></i>Regresar</button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <p class="text-c-blue f-w-400"><span class="font-weight-bold">Programación: </span> <?php echo $dataEncuesta->encuesta_programacion_nombre ?></p>    
                                <p class="f-w-400 text-muted"><span class="font-weight-bold">Micro hábito: </span> <?php echo $dataEncuesta->encuesta_nombre ?></p>
                                <p class="f-w-400 text-muted"><span class="font-weight-bold">Grupo: </span><?php echo $dataEncuesta->grupo_nombre ?></p>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="chart3"></div>
                            </div>
                            <div class="col-sm-6">
                                
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-sm-12">
                                        <div class="progress-box">
                                            <p class="d-inline-block m-r-20 f-w-400">Progreso</p>
                                            <div class="progress d-inline-block">
                                                <div class="progress-bar bg-c-blue" style="width:<?php echo $resutado?>% "><label><?php echo $resutado?>%</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive dt-responsive">
                                            <table id="reporte_personas" class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Persona</th>
                                                        <th>Respondidas</th>
                                                        <th>No respondidas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($persona_respondidas as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
                                                        <td><?php echo $total_preguntas_pro-$d->total_preguntas; ?></td>
                                                    </tr>
                                                    <?php } ?>

                                                    <?php foreach ($persona_sin_responder as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
                                                        <td><?php echo $total_preguntas_pro-$d->total_preguntas; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="col-sm-12">
                <div class="card app-design">
                    <div class="card-header">
                        <h5>Preguntas</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <?php 
                            $strPregunta = "";
                            foreach($detalle_por_preguntas as $row_pregunta){

                                if($strPregunta != $row_pregunta->encuesta_pregunta_nombre){?>
                                    <div class="col-sm-6">
                                        <p class="f-w-400 text-center"><?php echo $row_pregunta->encuesta_pregunta_nombre; ?></p>
                                        <div id="pregunta_<?php echo $row_pregunta->encuesta_pregunta_id; ?>" ></div>
                                    </div>
                                <?php 
                                $strPregunta = $row_pregunta->encuesta_pregunta_nombre;
                                }

                            } ?>
                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card app-design">
                    <div class="card-header">
                        <h5>Persona</h5>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div id="chart_bar" style="width: 100%; height: 400px;"></div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<script>
$(document).ready(function(){
    $('#reporte_personas').dataTable( {"ordering": false, "searching": false, "lengthChange": false, "info": false});
    //Donut chart
    /*google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);

    function drawChartDonut() {
        var dataDonut = google.visualization.arrayToDataTable([
            ['Tareas', 'Cantidades'],
            ['Respondidas', <?php echo $TotalRespuestas; ?>],
            ['No respondidas', <?php echo $TotalPorResponder; ?>]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: ['#11c15b', '#448aff']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut'));
        chart.draw(dataDonut, optionsDonut);
    }
*/
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawStacked);

    //cols.push(["Banana: 10", 10]);
    //console.log(cols);

    var cols_persona = [['Respuestas', 'Respuesta 1', 'Respuesta 2', 'Respuesta 3', 'Respuesta 4', 'Respuesta 5', 'No aplica']];

    <?php
    foreach($detalle_por_persona as $valor){
    ?>

        cols_persona.push(['<?php echo $valor["persona"]; ?>', <?php echo $valor["respuesta_1"]; ?>, <?php echo $valor["respuesta_2"]; ?>, <?php echo $valor["respuesta_3"]; ?>, <?php echo $valor["respuesta_4"]; ?>, <?php echo $valor["respuesta_5"]; ?>, <?php echo $valor["no_aplica"]; ?>]);
    <?php
    }
    ?>

    console.log(cols_persona);

    function drawStacked() {
        var data = google.visualization.arrayToDataTable(cols_persona);

        var options = {
            chartArea: { width: '50%' },
            isStacked: true,
            vAxis: {
                title: 'Personas'
            },
            colors: ['#cb4331', '#884ea1', '#2471a1', '#17a581', '#229951', '#d4ac01', '#ca6f11', '#2e4051']
        };
        var chart = new google.visualization.BarChart(document.getElementById('chart_bar'));
        chart.draw(data, options);
    }

    var cols_general = [
        ['Respondidas: '+ <?php echo $TotalRespuestas; ?>, <?php echo $TotalRespuestas; ?>],
        ['No respondidas: '+ <?php echo $TotalPorResponder; ?>, <?php echo $TotalPorResponder; ?>],
    ];
    //cols.push(["Banana: 10", 10]);
    //console.log(cols);
    var chart_c3 = c3.generate({
        bindto: '#chart3',
        data: {
            columns: cols_general,
            type : 'pie'
        },
        legend: {
            position: 'right'
        },
        color: {
            pattern: ['#11c15b', '#448aff']
        }
    });

    var cols_pregunta = [];

    <?php 
    $strPregunta = "";
    $intPregunta = 0;

    if(sizeof($detalle_por_preguntas)>0){
        $strPregunta = $detalle_por_preguntas[0]->encuesta_pregunta_nombre;
        $intPregunta = $detalle_por_preguntas[0]->encuesta_pregunta_id;
    }
    

    for ($i = 0; $i < sizeof($detalle_por_preguntas); $i++){
        
        if($strPregunta == $detalle_por_preguntas[$i]->encuesta_pregunta_nombre){    
    ?>


        cols_pregunta.push(['<?php echo $detalle_por_preguntas[$i]->encuesta_pregunta_respuesta_nombre; ?>'+' [Total: '+'<?php echo $detalle_por_preguntas[$i]->total_personas; ?>'+ ']', '<?php echo $detalle_por_preguntas[$i]->total_personas; ?>']);
    
    <?php
    }
        if($strPregunta != $detalle_por_preguntas[$i]->encuesta_pregunta_nombre){?>

            //console.log(cols_pregunta);
            var chart_c1 = c3.generate({
                bindto: '#pregunta_'+'<?php echo $intPregunta; ?>',
                data: {
                    // iris data from R
                    columns: cols_pregunta,
                    type : 'pie'
                },
                legend: {
                    position: 'right'
                },
                color: {
                    pattern: ['#cb4335', '#884ea0', '#2471a3', '#17a589', '#229954', '#d4ac0d', '#ca6f1e', '#2e4053']
                }
            });

            cols_pregunta = [];
            cols_pregunta.push(['<?php echo $detalle_por_preguntas[$i]->encuesta_pregunta_respuesta_nombre; ?>'+' [Total: '+'<?php echo $detalle_por_preguntas[$i]->total_personas; ?>'+ ']', '<?php echo $detalle_por_preguntas[$i]->total_personas; ?>']);
        <?php 
        $strPregunta = $detalle_por_preguntas[$i]->encuesta_pregunta_nombre;
        $intPregunta = $detalle_por_preguntas[$i]->encuesta_pregunta_id;
        }

        if($i == sizeof($detalle_por_preguntas)-1){?>

            var chart_c1 = c3.generate({
                bindto: '#pregunta_'+'<?php echo $detalle_por_preguntas[$i]->encuesta_pregunta_id; ?>',
                data: {
                    // iris data from R
                    columns: cols_pregunta,
                    type : 'pie'
                },
                legend: {
                    position: 'right'
                },
                color: {
                    pattern: ['#cb4335', '#884ea0', '#2471a3', '#17a589', '#229954', '#d4ac0d', '#ca6f1e', '#2e4053']
                }
            });

        <?php
        }
    } ?>
    

    $(".btnRegresar").on('click', function(){
        $("#tabla-resultados").show();
        $("#tabla-detalles").hide();

    });
    

});

</script>