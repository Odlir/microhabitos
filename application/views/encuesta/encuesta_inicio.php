<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="row">
	<div class="col-sm-12">
		<!-- DOM/Jquery table start -->
		<div class="card">
			<div class="card-header">
				<h5>Lista de micro hábitos</h5>
			</div>
			<div class="card-block">

                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn waves-effect waves-light btn-primary btn-outline-primary btnNuevo"><i class="icofont icofont-ui-add"></i>Nuevo micro hábito</button>
						<input type="hidden" id="urlEncuestaForm" value="<?=site_url('encuesta/encuesta_create')?>">
						<input type="hidden" id="urlEncuestaGet" value="<?=site_url('encuesta/encuesta_get')?>">
						<input type="hidden" id="urlEncuestaDel" value="<?=site_url('encuesta/encuesta_eliminar')?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive dt-responsive">
                            <table id="dom-jqry-encuesta" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
										<th>Micro hábito</th>
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
										<td><?php echo $d->encuesta_nombre; ?></td>
										<td><?php echo $d->encuesta_estado_str; ?></td>
										<td><?php echo $d->encuesta_usuario_creacion_str; ?></td>
                                        <td><?php echo $d->encuesta_fecha_creacion; ?></td>
                                        <td><?php echo $d->encuesta_usuario_modificacion_str; ?></td>
                                        <td><?php echo $d->encuesta_fecha_modificacion; ?></td>
                                        <td>
											<div class="icon-btn">
											<?php if (can_edited($d->encuesta_usuario_creacion_id)){ ?>
												<button class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnEditar" data-id="<?php echo $d->encuesta_id; ?>"><i class="icofont icofont-ui-edit"></i></button>
												<button class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon btnEliminar" data-nombre="<?php echo $d->encuesta_nombre; ?>" data-id="<?php echo $d->encuesta_id; ?>"><i class="icofont icofont-ui-delete"></i></button>
											<?php } ?>
												
												
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

	<!--Nuevo / Editar-->
	<div class="modal fade" id="modalNuevoEditar">
		<div class="modal-dialog modal-xlg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="modalTitulo">Modal</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="container"></div>
				<div class="modal-body">
					<!-- Row start -->
					<div class="row">
						<div class="col-sm-12">
							<!-- Other card start -->
							<div class="card">
								<div class="card-header">
									<!-- <h5>Micro hábito</h5> -->
								</div>
								<div class="card-block">
									<div class="row">
										<div class="col-sm-12">
											<form id="frmEncuesta">
												<input type="hidden" id="idEncuesta" name="idEncuesta">
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Nombre</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" name="nombre_encuesta" id="nombre_encuesta" placeholder="Ingresar el nombre">
													</div>
												</div>

												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Categoria</label>
													<div class="col-sm-9">
														<select class="form-control" name="categoria" id="categoria">
															<option value="">- Seleccionar -</option>
															<?php foreach ($comboCategoria as $d) { ?>
																<option value="<?php echo $d->categoria_id; ?>"><?php echo $d->categoria_nombre; ?></option>
															<?php }?>
														</select>
													</div>
												</div>
												
												<div class="form-group row" style="display:none;">
													<label class="col-sm-3 col-form-label">Tiempo alerta (Horas)</label>
													<div class="col-sm-9">
														<input type="hidden" class="form-control" name="tiempo_alerta" id="tiempo_alerta" placeholder="Ingresar tiempo de alerta en horas">
													</div>
												</div>
												
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Estado</label>
													<div class="col-sm-9">
														<input type="checkbox" id="estadoEncuesta" name="estadoEncuesta" class="js-small" checked/>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Other card end -->
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<!-- Other card start -->
							<div class="card">
								<div class="card-header">
									<h5></h5>
								</div>
								<div class="card-block">
									
									<div class="row">
										<div class="col-sm-12">
											<form id="frmPregunta">
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Micro hábito</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" name="nombre_pregunta" id="nombre_pregunta" placeholder="Ingresar el micro hábito">
													</div>
												</div>

												<div class="form-group row d-none">
													<label class="col-sm-3 col-form-label">Fecha de inicio</label>
													<div class="col-sm-9">
														<input type="date" class="form-control" name="pregunta_inicio" id="pregunta_inicio">
													</div>
												</div>


												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Descripción</label>
													<div class="col-sm-9">
														<!--<input type="text" class="form-control" name="nombre_pregunta" id="nombre_pregunta" placeholder="Ingresar la pregunta">-->

														<textarea class="form-control" name="pregunta_descripcion" id="pregunta_descripcion" >
														</textarea>
													</div>
												</div>

												<div id="tipoPreguntaDiv" class="form-group row">
													<label class="col-sm-3 col-form-label">Tipo de pregunta</label>
													<div class="col-sm-9">
														<select class="form-control" name="tipo_pregunta" id="tipo_pregunta">
															<option value="">- Seleccionar -</option>
															<?php foreach ($comboRespuesta as $d) { ?>
																<option value="<?php echo $d->valor; ?>" <?php echo $d->valor=='2'?'selected="selected"':''; ?>><?php echo $d->texto; ?></option>
															<?php }?>
														</select>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Escala de respuestas</label>
													<div class="col-sm-9">
														<input type="number" step="1" min="3" max="10" class="form-control" name="escala_respuesta" id="escala_respuesta" value = "3" placeholder="Ingresar la escala entre 3 a 10">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-3 col-form-label">Estado</label>
													<div class="col-sm-9">
														<input type="checkbox" id="estadoPregunta" name="estadoPregunta" class="js-small" checked/>
													</div>
												</div>
											</form>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<button class="btn waves-effect waves-light btn-primary btn-outline-primary btnAddPregunta"><i class="icofont icofont-ui-add"></i>Agregar micro hábito</button>
											<button class="btn waves-effect waves-light btn-success btn-outline-success btnGuardarPregunta"><i class="icofont icofont-save"></i>Guardar</button>
											<button class="btn waves-effect waves-light btn-inverse btn-outline-inverse btnCancelarPregunta"><i class="icofont icofont-save"></i>Cancelar</button>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="table-responsive dt-responsive">
												<table id="dom-jqry-pregunta" class="table table-striped table-bordered nowrap">
													<thead>
														<tr>
															<th>Id</th>
															<th>Pregunta</th>
															<th>Fecha Inicio</th>
															<th>Estado</th>
															<th>Fecha de Creación</th>
															<th>Fecha de Modificación</th>
															<th>Acciones</th>
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
							<!-- Other card end -->
						</div>
					</div>

					<!-- Row end -->
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
</div>
<!--Respuesta-->
<div class="modal fade" id="modalVer">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Respuestas</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="container"></div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<form id="frmRespuesta">
							
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Respuesta</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="nombre_respuesta" id="nombre_respuesta" placeholder="Ingresar la respuesta">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Mensaje</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="mensaje_respuesta" id="mensaje_respuesta" placeholder="Ingresar el mensaje">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Estado</label>
								<div class="col-sm-9">
									<input type="checkbox" id="estadoRespuesta" name="estadoRespuesta" class="js-small" checked/>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Celebración</label>
								<div class="col-sm-9">
									<input type="checkbox" id="celebracionRespuesta" name="celebracionRespuesta" class="js-small" checked/>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Super Celebración</label>
								<div class="col-sm-9">
									<input type="checkbox" id="maximaRespuesta" name="maximaRespuesta" class="js-small" checked/>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<button class="btn waves-effect waves-light btn-primary btn-outline-primary btnAddRespuesta"><i class="icofont icofont-ui-add"></i>Agregar Respuesta</button>
						<button class="btn waves-effect waves-light btn-success btn-outline-success btnGuardarRespuesta"><i class="icofont icofont-save"></i>Guardar</button>
						<button class="btn waves-effect waves-light btn-inverse btn-outline-inverse btnCancelarRespuesta"><i class="icofont icofont-save"></i>Cancelar</button>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="table-responsive dt-responsive">
							<table id="dom-jqry-respuesta" class="table table-striped table-bordered nowrap">
								<thead>
									<tr>
										<th>Id</th>
										<th>Respuesta</th>
										<th>Mensaje</th>
										<th>Estado</th>
										<th>Celebración</th>
										<th>Super Celebración</th>
										<th>Fecha de Creación</th>
										<th>Fecha de Modificación</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn">Cerrar</a>
			</div>
		</div>
	</div>
</div>
