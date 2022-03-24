<?php

$TotalPregunta = $total_preguntas;
$TotalRespuestas =  $total_respondidas->total_respuestas;

$TotalPorResponder = $TotalPregunta - $TotalRespuestas;

$resutado = 100-((($TotalPregunta-$TotalRespuestas)/$TotalPregunta)*100);
$resutado = round($resutado, 2)


?>
<div class="card">
    <div class="card-header">
        <h5>Persona - <?php echo $dataEncuesta->persona_nombre_completo ?></h5>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-12">
                <div class="card app-design">
                    <div class="card-block">
                        <div class="row">
                            <div class="col-sm-12">
                                <h6 class="f-w-400 text-muted"><?php echo $dataEncuesta->encuesta_nombre ?></h6>
                                <p class="text-c-blue f-w-400"><?php echo $dataEncuesta->grupo_nombre ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="chart_Donut" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                
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
                                                        <th>Pregunta</th>
                                                        <th>Respuesta</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($dataPreguntas as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->encuesta_pregunta_nombre; ?></td>
                                                        <td><?php echo $d->encuesta_pregunta_respuesta; ?></td>
                                                        <?php if($d->encuesta_pregunta_estado_id == 2){?>
                                                            <td><label class="label badge-success">Respondida</label></td>
                                                        <?php } else {?>
                                                            <td><label class="label badge-danger">No respondida</label></td>
                                                        <?php }?>
                                                        
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
        </div>
    </div>
</div>





<script>
$(document).ready(function(){
    
    $('#reporte_personas').dataTable( {"ordering": false, "searching": false, "lengthChange": false, "info": false});
    //Donut chart
    google.charts.load("current", { packages: ["corechart"] });
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

});

</script>