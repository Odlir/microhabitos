<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="row">
	<div class="col-sm-12">
		<!-- DOM/Jquery table start -->
		<div class="card">
			<div class="card-header">
				<h5>Mensaje Email</h5>
			</div>
			<div class="card-block">
                <div class="row">
                    <div class="col-sm-12">
                        <form id="frmMensaje">
                            <input type="hidden" id="urlMensajeForm" value="<?=site_url('ConfiguracionEmail/email_create')?>">
                            <input type="hidden" id="idMensaje" name="idMensaje" value="<?php echo $data->codigo_maestro_id;?>">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Mensaje</label>
                                <div class="col-sm-10">
                                    <textarea rows="5" cols="5" class="form-control" name="textoMensaje" id="textoMensaje" placeholder="Ingresar mensaje de notificación en el email"><?php echo $data->codigo_maestro_descripcion;?></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn waves-effect waves-light btn-primary btn-outline-primary btnGuardar"><i class="icofont icofont-ui-add"></i>Guardar</button>
                    </div>
                </div>

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12">
                        <div class="general-info">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Usuario Creación:</th>
                                                    <td><?php echo $data->codigo_maestro_usuario_creacion_str;?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Fecha Creación</th>
                                                    <td><?php echo $data->codigo_maestro_fecha_creacion;?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Usuario Modificación:</th>
                                                    <td><?php echo $data->codigo_maestro_usuario_modificacion_str;?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Fecha Modificación</th>
                                                    <td><?php echo $data->codigo_maestro_fecha_modificacion;?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- end of row -->
                        </div>
                    </div>
                </div>
				
			</div>
		</div>
	</div>
	<!-- Form components Validation card end -->
</div>
