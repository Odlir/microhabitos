<?php

$TotalPregunta01 = $total_preguntas01;
$TotalRespuestas01 =  $total_respondidas01;

$TotalPorResponder01 = $TotalPregunta01 - $TotalRespuestas01;

$resutado01 = 100-((($TotalPregunta01 - $TotalRespuestas01) / $TotalPregunta01)*100);
$resutado01 = round($resutado01, 2);


$TotalPregunta02 = $total_preguntas02;
$TotalRespuestas02 =  $total_respondidas02;

$TotalPorResponder02 = $TotalPregunta02 - $TotalRespuestas02;

$resutado02 = 100-((($TotalPregunta02 - $TotalRespuestas02)/$TotalPregunta02)*100);
$resutado02 = round($resutado02, 2);


?>
<div class="card">
    <div class="card-header">
        <h5>Comparativo</h5>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-12">
                <div class="card app-design">
                    <div class="card-block">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="text-right">
                                    <button class="btn waves-effect waves-light btn-warning btnRegresar"><i class="icofont icofont-arrow-left"></i>Regresar</button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="f-w-400 text-muted"><?php echo $dataEncuesta01->encuesta_nombre ?></h6>
                                <p class="text-c-blue f-w-400"><?php echo $dataEncuesta01->grupo_nombre ?></p>
                            </div>

                            <div class="col-sm-6">
                                <h6 class="f-w-400 text-muted"><?php echo $dataEncuesta02->encuesta_nombre ?></h6>
                                <p class="text-c-blue f-w-400"><?php echo $dataEncuesta02->grupo_nombre ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="chart_Donut01" style="width: 100%; height: 300px;"></div>
                            </div>

                            <div class="col-sm-6">
                                <div id="chart_Donut02" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-sm-12">
                                        <div class="progress-box">
                                            <p class="d-inline-block m-r-20 f-w-400">Progreso</p>
                                            <div class="progress d-inline-block">
                                                <div class="progress-bar bg-c-blue" style="width:<?php echo $resutado01?>% "><label><?php echo $resutado01?>%</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive dt-responsive">
                                            <table id="reporte_personas1" class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Persona</th>
                                                        <th>Total respondidas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($persona_respondidas01 as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
                                                    </tr>
                                                    <?php } ?>

                                                    <?php foreach ($persona_sin_responder01 as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-sm-6">
                                
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-sm-12">
                                        <div class="progress-box">
                                            <p class="d-inline-block m-r-20 f-w-400">Progreso</p>
                                            <div class="progress d-inline-block">
                                                <div class="progress-bar bg-c-blue" style="width:<?php echo $resutado02?>% "><label><?php echo $resutado02?>%</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive dt-responsive">
                                            <table id="reporte_personas2" class="table table-striped table-bordered nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Persona</th>
                                                        <th>Total respondidas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($persona_respondidas02 as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
                                                    </tr>
                                                    <?php } ?>

                                                    <?php foreach ($persona_sin_responder02 as $d) { ?>
                                                    <tr>
                                                        <td><?php echo $d->persona_nombre_completo; ?></td>
                                                        <td><?php echo $d->total_preguntas; ?></td>
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
    $('#reporte_personas1').dataTable( {"ordering": false, "searching": false, "lengthChange": false, "info": false});
    $('#reporte_personas2').dataTable( {"ordering": false, "searching": false, "lengthChange": false, "info": false});
    //Donut chart
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut01);
    google.charts.setOnLoadCallback(drawChartDonut02);

    function drawChartDonut01() {
        var dataDonut = google.visualization.arrayToDataTable([
            ['Tareas', 'Cantidades'],
            ['Respondidas', <?php echo $TotalRespuestas01; ?>],
            ['No respondidas', <?php echo $TotalPorResponder01; ?>]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: ['#11c15b', '#448aff']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut01'));
        chart.draw(dataDonut, optionsDonut);
    }

    function drawChartDonut02() {
        var dataDonut = google.visualization.arrayToDataTable([
            ['Tareas', 'Cantidades'],
            ['Respondidas', <?php echo $TotalRespuestas02; ?>],
            ['No respondidas', <?php echo $TotalPorResponder02; ?>]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: ['#11c15b', '#448aff']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut02'));
        chart.draw(dataDonut, optionsDonut);
    }

    $(".btnRegresar").on('click', function(){
        $("#tabla-resultados").show();
        $("#tabla-detalles").hide();

    });
    

});

</script>