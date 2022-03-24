<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
				<h5>Reporte de encuesta</h5>
			</div>
			<div class="card-block">

                <div class="row">
                    <div class="col-sm-12">
                        <input type="hidden" id="urlReporte" value="<?=site_url('reporte/getResultados')?>">
                        <input type="hidden" id="urlExportar" value="<?=site_url('reporte/getExcel')?>">
                        <form id="frmGrupo">
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Persona</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombrePersona" id="nombrePersona" placeholder="Ingresar nombre de la persona">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Grupo</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="nombreGrupo" id="nombreGrupo">
                                        <option value="">- Seleccionar -</option>
                                        <?php foreach ($comboGrupo as $d) { ?>
                                            <option value="<?php echo $d->valor; ?>"><?php echo $d->texto; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Micro hábito</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="nombreEncuesta" id="nombreEncuesta">
                                        <option value="">- Seleccionar -</option>
                                        <?php foreach ($comboEncuesta as $d) { ?>
                                            <option value="<?php echo $d->valor; ?>"><?php echo $d->texto; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha desde</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="fechaDesde" id="fechaDesde" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha hasta</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="fechaHasta" id="fechaHasta" >
                                </div>
                            </div>
                            
                        </form>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn waves-effect waves-light btn-primary btnBuscar"><i class="icofont icofont-ui-search"></i>Buscar</button>
                        <button class="btn waves-effect waves-light btn-success btnExportar"><i class="icofont icofont-file-text"></i>Exportar XLSX</button>
                        <button class="btn waves-effect waves-light btn-warning btnComparar"><i class="icofont icofont-chart-line-alt"></i>Comparar</button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <div id="spinner" class="preloader3 loader-block">
                            <div class="circ1 loader-default"></div>
                            <div class="circ2 loader-default"></div>
                            <div class="circ3 loader-default"></div>
                            <div class="circ4 loader-default"></div>
                        </div>
                    </div>
                </div>
				
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-sm-12" id="tabla-resultados">
        
    </div>
</div>

<div class="row">
    <div class="col-sm-12" id="tabla-detalles">
        
    </div>
</div>