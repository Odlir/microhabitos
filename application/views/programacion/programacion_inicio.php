<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="row">
	<div class="col-sm-12">
		<!-- DOM/Jquery table start -->
		<div class="card">
			<div class="card-header">
				<h5>Lista de programaciones</h5>
			</div>
			<div class="card-block">

                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn waves-effect waves-light btn-primary btn-outline-primary btnNuevo"><i class="icofont icofont-ui-add"></i>Nueva programación</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive dt-responsive">
                            <table id="dom-jqry-programacion" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Programación</th>
                                        <th>Encuesta</th>
                                        <th>Grupo</th>
                                        <th>Tipo</th>
                                        <th>Fecha de inicio</th>
                                        <th>Fecha de fin</th>
                                        <th>Estado</th>
                                        <th>Usuario Creación</th>
                                        <th>Fecha de Creación</th>
                                        <th>Usuario Modificación</th>
                                        <th>Fecha de Modificación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $d) { ?>
                                    <tr>
                                        <td><?php echo $d->encuesta_programacion_nombre; ?></td>
                                        <td><?php echo $d->encuesta_str; ?></td>
                                        <td><?php echo $d->grupo_str; ?></td>
                                        <td><?php echo $d->encuesta_programacion_tipo_str; ?></td>
                                        <td><?php echo $d->encuesta_programacion_fecha_inicio; ?></td>
                                        <td><?php echo $d->encuesta_programacion_fecha_fin; ?></td>
                                        <td><?php echo $d->encuesta_programacion_estado_str; ?></td>
                                        <td><?php echo $d->encuesta_programacion_usuario_creacion_str; ?></td>
                                        <td><?php echo $d->encuesta_programacion_fecha_creacion; ?></td>
                                        <td><?php echo $d->encuesta_programacion_usuario_modificacion_str; ?></td>
                                        <td><?php echo $d->encuesta_programacion_fecha_modificacion; ?></td>
                                        <td>
                                        <div class="icon-btn">
                                            
                                        <?php if (can_edited($d->encuesta_programacion_usuario_creacion_id)){ ?>
                                            <button data-toggle="tooltip" title="Notificaciones" class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon btnAbrir" data-id="<?php echo $d->encuesta_programacion_id; ?>"><i class="icofont icofont-notification"></i></button>
                                            <button data-toggle="tooltip" title="Editar" class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnEditar" data-id="<?php echo $d->encuesta_programacion_id; ?>"><i class="icofont icofont-ui-edit"></i></button>
                                            <button data-toggle="tooltip" title="Eliminar" class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon btnEliminar" data-id="<?php echo $d->encuesta_programacion_id; ?>" data-encuesta="<?php echo $d->encuesta_id; ?>" data-grupo="<?php echo $d->grupo_id; ?>"><i class="icofont icofont-ui-delete"></i></button>
                                        <?php }?>
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
	</div>
	<!-- Form components Validation card end -->
</div>

<!--Nuevo / Editar-->
<div class="modal fade" id="modalNuevoEditar">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalTitulo">Modal</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="container"></div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <input type="hidden" id="urlProgramacionForm" value="<?=site_url('programacion/programacion_create')?>">
                        <input type="hidden" id="urlProgramacionGet" value="<?=site_url('programacion/programacion_get')?>">
                        <input type="hidden" id="urlProgramacionDel" value="<?=site_url('programacion/programacion_eliminar')?>">
                        <input type="hidden" id="urlProgramacionPre" value="<?=site_url('programacion/programacion_get_pregunta')?>">
                        <input type="hidden" id="urlProgramacionPreguntaForm" value="<?=site_url('programacion/programacion_pregunta_create')?>">
                        <form id="frmProgramacion">
                            <input type="hidden" id="idProgramacion" name="idProgramacion">

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nombre</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre_programacion" id="nombre_programacion" placeholder="Ingrese el nombre de la programación">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Asunto Email</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="asunto_email" id="asunto_email" placeholder="Ingrese el asunto de notificación de correo"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Mensaje Email</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="mensaje_email" id="mensaje_email" placeholder="Ingrese el mensaje de notificación de correo"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Mensaje SMS</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="mensaje_sms" id="mensaje_sms" placeholder="Ingrese el mensaje de notificación de sms"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Micro hábito</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="encuesta" id="encuesta">
                                        <option value="">- Seleccionar -</option>
                                        <?php foreach ($comboEncuesta as $d) { ?>
                                            <option value="<?php echo $d->valor; ?>"><?php echo $d->texto; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div id="div-preguntas" class="form-group row">
                                <div class="col-sm-12">

                                    <div class="card">
                                        <div class="card-header">
                                            <ul>
                                                <li><small>* clic para editar la fecha</small></li>
                                                <li><small>* enter para guardar la fecha</small></li>
                                            </ul>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" id="example-1">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Listado de micro hábitos</th>
                                                            <th>Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Grupo</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="grupo" id="grupo">
                                        <option value="">- Seleccionar -</option>
                                        <?php foreach ($comboGrupo as $d) { ?>
                                            <option value="<?php echo $d->valor; ?>"><?php echo $d->texto; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Hora de ejecución</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="hora_job" id="hora_job">
                                        <option value="">- Seleccionar -</option>
                                        
                                        <?php for ($i=0; $i < 24; $i++){
                                            $val = str_pad($i, 2, '0', STR_PAD_LEFT).":00";
                                            ?>
                                            <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha inicio</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha fin</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tipo notificación</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="tipo_notifi" id="tipo_notifi">
                                        <option value="">- Seleccionar -</option>
                                        <?php foreach ($comboProgramacion as $d) { ?>
                                            <option value="<?php echo $d->valor; ?>"><?php echo $d->texto; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Repetición</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cLunes" name="cLunes" >
                                        <label class="form-check-label" for="cLunes">Lunes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cMartes" name="cMartes">
                                        <label class="form-check-label" for="cMartes">Martes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cMiercoles" name="cMiercoles">
                                        <label class="form-check-label" for="cMiercoles">Miércoles</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cJueves" name="cJueves">
                                        <label class="form-check-label" for="cJueves">Jueves</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cViernes" name="cViernes">
                                        <label class="form-check-label" for="cViernes">Viernes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cSabado" name="cSabado">
                                        <label class="form-check-label" for="cSabado">Sábado</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cDomingo" name="cDomingo">
                                        <label class="form-check-label" for="cDomingo">Domingo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Estado</label>
                                <div class="col-sm-10">
                                    <input type="checkbox" id="state" name="state" class="js-small" checked/>
                                </div>
                            </div>
                        </form>
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
                        <div id="msgErrorCard" class="card text-white card-danger">
                            <div class="card-header">Error</div>
                            <div class="card-body">
                                <p class="card-text" id="msgCampos"></p>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn">Cancelar</a>
                <button id="btnGuardar" class="btn btn-primary">Guardar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalVer">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Enviar notificación</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="container">
                <input type="hidden" id="urlPersonasNoti" value="<?=site_url('programacion/getPersonasEncuesta')?>">
                <input type="hidden" id="urlProgramacionSend" value="<?=site_url('programacion/enviar_notificacion')?>">
                
            </div>
			<div class="modal-body" >
                
                <div class="row">
                    <div class="col-sm-12">
                        <div id="divBody"> </div>
                    </div>
                </div>
                <div class="row">
					<div class="col-sm-12">
						<button class="btn waves-effect waves-light btn-primary btn-outline-primary btnSend" data-id="2"><i class="icofont icofont-ui-add"></i>Enviar SMS</button>
						<button class="btn waves-effect waves-light btn-success btn-outline-success btnSend" data-id="1"><i class="icofont icofont-save"></i>Enviar Email</button>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn">Cerrar</a>
			</div>
		</div>
	</div>
</div>
