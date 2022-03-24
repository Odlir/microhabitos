<?php
defined('BASEPATH') OR exit('No direct script access allowed');


?>


<div class="row">
	<div class="col-sm-12">
		<!-- DOM/Jquery table start -->
		<div class="card">
			<div class="card-header">
				<h5>Lista de Micro hábitos pendientes</h5>
			</div>
			<div class="card-block">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive dt-responsive">
                            <table id="dom-jqry-mi-lista" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Micro hábito</th>
                                        <th>Grupo</th>
                                        <th>Fecha</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $d) { ?>
                                    <tr>
                                        <td><?php echo $d->encuesta_nombre; ?></td>
                                        <td><?php echo $d->grupo_nombre; ?></td>
                                        <td><?php echo $d->encuesta_persona_fecha_creacion; ?></td>
                                        <td>
                                        <div class="icon-btn">
                                            <button class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon btnVer" data-url="<?=site_url('formulario/index')?>/<?php echo $d->encuesta_persona_codigo; ?>"><i class="icofont icofont-eye-alt"></i></button>
                                            
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