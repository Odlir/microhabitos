<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EncuestaModel extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }

    public function get_encuestas(){

        $estados = array('1', '2');
        $this->db->select('e2.encuesta_id,
        e2.encuesta_nombre,
        e2.encuesta_tiempo_alerta_horas,
        cm.codigo_maestro_descripcion as encuesta_estado_str,
        e2.encuesta_estado_id,
        e2.encuesta_fecha_notificacion,
        u.usuario_nombre as encuesta_usuario_creacion_str,
        encuesta_usuario_creacion_id,
        e2.encuesta_fecha_creacion,
        u2.usuario_nombre as encuesta_usuario_modificacion_str,
        e2.encuesta_fecha_modificacion');
        $this->db->from('encuesta e2');
        $this->db->join('codigo_maestro cm', 'e2.encuesta_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('usuario u', 'e2.encuesta_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'e2.encuesta_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);

        if(get_usuario_login_tipo() != "1"){ // no admin
            
            $this->db->where('e2.encuesta_usuario_creacion_id', get_usuario_login_id());
        }

        $this->db->where_in('e2.encuesta_estado_id', $estados);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_pregunta_programacion_nuevo($idEncuesta){
        $this->db->select('1 es_insert, encuesta_pregunta_id, encuesta_pregunta_nombre, encuesta_pregunta_fecha_inicio');
        $this->db->from('encuesta_pregunta');
        $this->db->where('encuesta_id', $idEncuesta);
        $this->db->where('encuesta_pregunta_estado_id', 2);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_pregunta_programacion_editar($idProgramacion){
        $this->db->select('(case when epp.encuesta_programacion_pregunta_id is null then 1 else 0 end) as es_insert,
        IFNULL(epp.encuesta_programacion_pregunta_id, ep.encuesta_pregunta_id) as encuesta_pregunta_id,
        ep.encuesta_pregunta_nombre,
        IFNULL(epp.encuesta_programacion_pregunta_fecha_inicio , ep.encuesta_pregunta_fecha_inicio) as encuesta_pregunta_fecha_inicio');
        $this->db->from('encuesta_pregunta ep');
        $this->db->join('encuesta_programacion ep2', 'ep.encuesta_id = ep2.encuesta_id', 'inner');
        $this->db->join('encuesta_programacion_pregunta epp', 'ep.encuesta_pregunta_id = epp.encuesta_pregunta_id and epp.encuesta_programacion_id = ep2.encuesta_programacion_id', 'left');
        $this->db->where('ep2.encuesta_programacion_id', $idProgramacion);
        $this->db->where('ep.encuesta_pregunta_estado_id', 2);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    /*Lista Modificacion*/
    public function get_encuestaCombo(){
        $this->db->select('encuesta_id as valor, encuesta_nombre as texto');
        $this->db->from('encuesta');
        $this->db->where('encuesta_estado_id', 2);
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    /*Lista Modificacion*/
    public function get_encuesta($id){
        $this->db->select('*');
        $this->db->from('encuesta');
        $this->db->where('encuesta_id', $id);
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function get_encuesta_grupo($id){
        
        $this->db->select('eg.encuesta_grupo_id,
        eg.encuesta_id,
        eg.grupo_id,
        g2.grupo_nombre,
        eg.encuesta_grupo_estado_id,
        cm.codigo_maestro_descripcion as encuesta_grupo_estado_str,
        eg.encuesta_grupo_fecha_creacion,
        eg.encuesta_grupo_fecha_modificacion');
        $this->db->from('encuesta_grupo eg');
        $this->db->join('codigo_maestro cm', 'eg.encuesta_grupo_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('grupo g2', 'eg.grupo_id = g2.grupo_id', 'inner');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('eg.encuesta_id', $id);
        $this->db->where('eg.encuesta_grupo_estado_id', 2);
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function get_encuesta_pregunta($id, $activos = false){
        $estados = array('1', '2');
        $this->db->select('ep.encuesta_pregunta_id,
        ep.encuesta_id,
        ep.encuesta_pregunta_nombre,
        ep.encuesta_pregunta_valores,
        ep.encuesta_pregunta_tipo_id,
        cm2.codigo_maestro_descripcion as encuesta_pregunta_tipo_str,
        ep.encuesta_pregunta_estado_id,
        cm.codigo_maestro_descripcion as encuesta_pregunta_estado_str,
        ep.encuesta_pregunta_fecha_creacion,
        ep.encuesta_pregunta_fecha_modificacion,
        ep.encuesta_pregunta_fecha_inicio,
        ep.encuesta_pregunta_descripcion_html');
        $this->db->from('encuesta_pregunta ep');
        $this->db->join('codigo_maestro cm', 'ep.encuesta_pregunta_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('codigo_maestro cm2', 'ep.encuesta_pregunta_tipo_id = cm2.codigo_maestro_valor', 'inner');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('cm2.codigo_maestro_tipo_id', 7);
        $this->db->where('ep.encuesta_id', $id);

        if($activos){
            $this->db->where('ep.encuesta_pregunta_estado_id', 2);
        }else{
            $this->db->where_in('ep.encuesta_pregunta_estado_id', $estados);
        }
        
        
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function get_encuesta_pregunta_formulario($id, $activos = false){
        $estados = array('1', '2');
        $this->db->select('ep.encuesta_pregunta_id,
        ep.encuesta_id,
        ep.encuesta_pregunta_nombre,
        ep.encuesta_pregunta_valores,
        ep.encuesta_pregunta_tipo_id,
        cm2.codigo_maestro_descripcion as encuesta_pregunta_tipo_str,
        ep.encuesta_pregunta_estado_id,
        cm.codigo_maestro_descripcion as encuesta_pregunta_estado_str,
        ep.encuesta_pregunta_fecha_creacion,
        ep.encuesta_pregunta_fecha_modificacion,
        ep.encuesta_pregunta_fecha_inicio,
        ep.encuesta_pregunta_descripcion_html');
        $this->db->from('encuesta_pregunta ep');
        
        $this->db->join('codigo_maestro cm', 'ep.encuesta_pregunta_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('codigo_maestro cm2', 'ep.encuesta_pregunta_tipo_id = cm2.codigo_maestro_valor', 'inner');

        $this->db->join('encuesta_programacion ep2', 'ep.encuesta_id = ep2.encuesta_id', 'inner');
        $this->db->join('encuesta_programacion_pregunta epp', 'ep.encuesta_pregunta_id = epp.encuesta_pregunta_id and epp.encuesta_programacion_id = ep2.encuesta_programacion_id', 'left');
        
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('cm2.codigo_maestro_tipo_id', 7);
        $this->db->where('DATE_FORMAT(IFNULL(epp.encuesta_programacion_pregunta_fecha_inicio , ep.encuesta_pregunta_fecha_inicio) ,"%Y-%m-%d") <= DATE_FORMAT(NOW(),"%Y-%m-%d")', null, false);
        $this->db->where('ep2.encuesta_programacion_id', $id);
        $this->db->where('ep.encuesta_pregunta_estado_id', 2);

        if($activos){
            $this->db->where('ep.encuesta_pregunta_estado_id', 2);
        }else{
            $this->db->where_in('ep.encuesta_pregunta_estado_id', $estados);
        }
        
        
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function get_encuesta_pregunta_respuesta($id, $activos = false){
        $estados = array('1', '2');
        $this->db->select('epr.encuesta_pregunta_respuesta_id,
        epr.encuesta_pregunta_id,
        epr.encuesta_pregunta_respuesta_nombre,
        epr.encuesta_pregunta_respuesta_mensaje,
        epr.encuesta_pregunta_respuesta_flag_celebracion,
        epr.encuesta_pregunta_respuesta_flag_maxima,
        epr.encuesta_pregunta_respuesta_estado_id,
        cm.codigo_maestro_descripcion as encuesta_pregunta_respuesta_estado_str,
        epr.encuesta_pregunta_respuesta_fecha_creacion,
        epr.encuesta_pregunta_respuesta_fecha_modificacion');
        $this->db->from('encuesta_pregunta_respuesta epr');
        $this->db->join('codigo_maestro cm', 'epr.encuesta_pregunta_respuesta_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('epr.encuesta_pregunta_id', $id);
        
        if($activos){
            $this->db->where('epr.encuesta_pregunta_respuesta_estado_id', 2);
        }else{
            $this->db->where_in('epr.encuesta_pregunta_respuesta_estado_id', $estados);
        }
        
        $this->db->order_by('epr.encuesta_pregunta_respuesta_id', 'ASC');
        
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function get_encuesta_pregunta_respuesta_new($id, $idpersona_encuesta, $activos = false){
        $estados = array('1', '2');
        $this->db->select('IFNULL(eprp.encuesta_pregunta_respuesta_persona_id, 0) encuesta_pregunta_respuesta_persona_id,
        epr.encuesta_pregunta_respuesta_id,
        epr.encuesta_pregunta_id,
        epr.encuesta_pregunta_respuesta_nombre,
        epr.encuesta_pregunta_respuesta_mensaje,
        epr.encuesta_pregunta_respuesta_flag_celebracion,
        epr.encuesta_pregunta_respuesta_flag_maxima,
        epr.encuesta_pregunta_respuesta_estado_id,
        cm.codigo_maestro_descripcion as encuesta_pregunta_respuesta_estado_str,
        epr.encuesta_pregunta_respuesta_fecha_creacion,
        epr.encuesta_pregunta_respuesta_fecha_modificacion');
        $this->db->from('encuesta_pregunta_respuesta epr');
        $this->db->join('codigo_maestro cm', 'epr.encuesta_pregunta_respuesta_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('encuesta_pregunta_respuesta_persona eprp', 'epr.encuesta_pregunta_respuesta_id = eprp.encuesta_pregunta_respuesta_id and eprp.encuesta_pregunta_respuesta_persona_estado_id = 2 and eprp.encuesta_persona_id = '. $idpersona_encuesta, 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('epr.encuesta_pregunta_id', $id);
        
        if($activos){
            $this->db->where('epr.encuesta_pregunta_respuesta_estado_id', 2);
        }else{
            $this->db->where_in('epr.encuesta_pregunta_respuesta_estado_id', $estados);
        }
        
        $this->db->order_by('epr.encuesta_pregunta_respuesta_id', 'ASC');
        
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    /*Registro*/
    public function encuesta_insertar($data){
        
        $this->db->set('encuesta_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('encuesta', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function encuesta_grupo_insertar($data){
        
        $this->db->set('encuesta_grupo_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_grupo_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('encuesta_grupo', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function encuesta_pregunta_insertar($data){
        
        //$this->db->set('grupo_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('encuesta_pregunta_usuario_creacion_id', get_usuario_login_id());
        $this->db->insert('encuesta_pregunta', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function encuesta_pregunta_respuesta_insertar($data){
        
        //$this->db->set('grupo_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('encuesta_pregunta_respuesta_usuario_creacion_id', get_usuario_login_id());
        $this->db->insert('encuesta_pregunta_respuesta', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*Modificacion*/
    public function encuesta_actualizar($id, $data){
        
        $this->db->set('encuesta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->set('encuesta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('encuesta_id', $id);
        $this->db->update('encuesta', $data);
        return true;
    }

    public function encuesta_pregunta_actualizar($id, $data){
        
        $this->db->set('encuesta_pregunta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('encuesta_pregunta_id', $id);
        $this->db->update('encuesta_pregunta', $data);
        return true;
    }

    public function encuesta_programacion_pregunta_actualizar($id, $data){
        
        $this->db->set('encuesta_programacion_pregunta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('encuesta_pregunta_id', $id);
        $this->db->update('encuesta_programacion_pregunta', $data);
        return true;
    }

    public function encuesta_pregunta_respuesta_actualizar($id, $data){
        
        $this->db->set('encuesta_pregunta_respuesta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('encuesta_pregunta_respuesta_id', $id);
        $this->db->update('encuesta_pregunta_respuesta', $data);
        return true;
    }

    /*Eliminado*/
    public function encuesta_eliminar($id){
        
        $this->db->set('encuesta_estado_id', 0);
        $this->db->set('encuesta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_id', $id);
        $this->db->update('encuesta');
        return true;
    }

    public function encuesta_pregunta_eliminar($id){
        
        $this->db->set('encuesta_pregunta_estado_id', 0);
        $this->db->set('encuesta_pregunta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_pregunta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_pregunta_id', $id);
        $this->db->update('encuesta_pregunta');
        return true;
    }

    public function encuesta_programacion_pregunta_eliminar($id){
        
        $this->db->set('encuesta_programacion_pregunta_estado_id', 0);
        $this->db->set('encuesta_programacion_pregunta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_pregunta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_pregunta_id', $id);
        $this->db->update('encuesta_programacion_pregunta');
        return true;
    }

    public function encuesta_pregunta_respuesta_eliminar($id){
        
        $this->db->set('encuesta_pregunta_respuesta_estado_id', 0);
        $this->db->set('encuesta_pregunta_respuesta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_pregunta_respuesta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_pregunta_respuesta_id', $id);
        $this->db->update('encuesta_pregunta_respuesta');
        return true;
    }

    public function encuesta_grupo_eliminar($id){
        
        $this->db->set('encuesta_grupo_estado_id', 0);
        $this->db->set('encuesta_grupo_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_grupo_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_grupo_id', $id);
        $this->db->update('encuesta_grupo');
        return true;
    }

    //Encuesta persona
    public function get_encuesta_persona_byId($id){
        
        $this->db->select('e.encuesta_id,
        eg.grupo_id,
        p.persona_id,
        p.persona_email,
        p.persona_apellido_paterno,
        p.persona_apellido_materno,
        p.persona_nombre,
        p.persona_celular,
        e.encuesta_nombre,
        ep2.encuesta_programacion_tipo_id,
        ep2.encuesta_programacion_mensaje_email,
        ep2.encuesta_programacion_mensaje_sms,
        ep2.encuesta_programacion_asunto_email,
        IFNULL(ep.encuesta_persona_id, 0) as encuesta_persona_id');
        $this->db->from('persona p');
        $this->db->join('encuesta_grupo eg', 'eg.grupo_id = p.grupo_id and eg.encuesta_grupo_estado_id <> 0', 'inner');
        $this->db->join('encuesta_programacion ep2', 'eg.encuesta_id = ep2.encuesta_id and eg.grupo_id = ep2.grupo_id and ep2.encuesta_programacion_estado_id <> 0', 'inner');
        $this->db->join('encuesta e', 'e.encuesta_id = eg.encuesta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'ep.persona_id = p.persona_id and ep2.encuesta_programacion_id = ep.encuesta_programacion_id', 'left');
        //$this->db->where('IFNULL(ep.encuesta_persona_id, 0) = 0', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id = 2', null, false);
        $this->db->where('p.persona_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_id', $id);
        $this->db->where('eg.encuesta_programacion_id = ep2.encuesta_programacion_id', null, false);
        $this->db->where('DATE_FORMAT(NOW(),"%Y-%m-%d") between ep2.encuesta_programacion_fecha_inicio and ep2.encuesta_programacion_fecha_fin', null, false);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }
 
    public function get_lista_persona_programacion($id){
        
        $this->db->select('CASE WHEN p.persona_apellido_paterno = "" and p.persona_apellido_materno = "" THEN p.persona_nombre  ELSE CONCAT(p.persona_nombre, " ",p.persona_apellido_paterno, " ", p.persona_apellido_materno) END as persona_nombre_completo,
        p.persona_id,
        p.persona_email,
        p.persona_celular,
        ep2.encuesta_programacion_id,
        ep2.encuesta_programacion_mensaje_email,
        ep2.encuesta_programacion_mensaje_sms,
        IFNULL(ep.encuesta_persona_cantidad_sms, 0) as encuesta_persona_cantidad_sms,
        IFNULL(ep.encuesta_persona_cantidad_email, 0) as encuesta_persona_cantidad_email,
        IFNULL(ep.encuesta_persona_id, 0) as encuesta_persona_id');
        $this->db->from('persona p');
        $this->db->join('encuesta_grupo eg', 'eg.grupo_id = p.grupo_id and eg.encuesta_grupo_estado_id <> 0', 'inner');
        $this->db->join('encuesta_programacion ep2', 'eg.encuesta_id = ep2.encuesta_id and eg.grupo_id = ep2.grupo_id and ep2.encuesta_programacion_estado_id <> 0', 'inner');
        $this->db->join('encuesta e', 'e.encuesta_id = eg.encuesta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'ep.persona_id = p.persona_id and ep2.encuesta_programacion_id = ep.encuesta_programacion_id', 'left');
        //$this->db->where('IFNULL(ep.encuesta_persona_id, 0) = 0', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id = 2', null, false);
        $this->db->where('p.persona_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_id', $id);
        $this->db->where('eg.encuesta_programacion_id = ep2.encuesta_programacion_id', null, false);
        //$this->db->where('DATE_FORMAT(NOW(),"%Y-%m-%d") between ep2.encuesta_programacion_fecha_inicio and ep2.encuesta_programacion_fecha_fin', null, false);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_encuesta_programacion_by_persona($idProgramacion, $idpersona){
        
        $this->db->select('e.encuesta_id,
        eg.grupo_id,
        p.persona_id,
        p.persona_email,
        p.persona_apellido_paterno,
        p.persona_apellido_materno,
        p.persona_nombre,
        p.persona_celular,
        e.encuesta_nombre,
        ep2.encuesta_programacion_tipo_id,
        ep2.encuesta_programacion_mensaje_email,
        ep2.encuesta_programacion_mensaje_sms,
        ep2.encuesta_programacion_asunto_email,
        IFNULL(ep.encuesta_persona_codigo, 0) as encuesta_persona_codigo,
        IFNULL(ep.encuesta_persona_id, 0) as encuesta_persona_id');
        $this->db->from('persona p');
        $this->db->join('encuesta_grupo eg', 'eg.grupo_id = p.grupo_id and eg.encuesta_grupo_estado_id <> 0', 'inner');
        $this->db->join('encuesta_programacion ep2', 'eg.encuesta_id = ep2.encuesta_id and eg.grupo_id = ep2.grupo_id and ep2.encuesta_programacion_estado_id <> 0', 'inner');
        $this->db->join('encuesta e', 'e.encuesta_id = eg.encuesta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'ep.persona_id = p.persona_id and ep2.encuesta_programacion_id = ep.encuesta_programacion_id', 'left');
        //$this->db->where('IFNULL(ep.encuesta_persona_id, 0) = 0', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id = 2', null, false);
        $this->db->where('p.persona_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_id', $idProgramacion);
        $this->db->where('p.persona_id', $idpersona);
        $this->db->where('eg.encuesta_programacion_id = ep2.encuesta_programacion_id', null, false);
        //$this->db->where('DATE_FORMAT(NOW(),"%Y-%m-%d") between ep2.encuesta_programacion_fecha_inicio and ep2.encuesta_programacion_fecha_fin', null, false);
        $query = $this->db->get();
        $data = $query->result();
        return $data[0];
    }
    

    public function get_encuesta_persona(){
        
        $this->db->distinct();
        $this->db->select('IFNULL(ep.encuesta_persona_id, 0) as encuesta_persona_id,
        IFNULL(ep.encuesta_persona_codigo, "") as encuesta_persona_codigo,
        e.encuesta_id,
        eg.grupo_id,
        p.persona_id,
        p.persona_email,
        p.persona_apellido_paterno,
        p.persona_apellido_materno,
        p.persona_nombre,
        p.persona_celular,
        e.encuesta_nombre,
        ep2.encuesta_programacion_tipo_id,
        ep2.encuesta_programacion_id,
        ep2.encuesta_programacion_fecha_inicio,
        ep2.encuesta_programacion_fecha_fin,
        ep2.encuesta_programacion_json_dias,
        ep2.encuesta_programacion_hora_job,
        ep2.encuesta_programacion_mensaje_email,
        ep2.encuesta_programacion_mensaje_sms,
        IFNULL(ep2.encuesta_programacion_asunto_email, "") as encuesta_programacion_asunto_email,
        IFNULL(ep.encuesta_persona_fecha_notificacion, "") as encuesta_persona_fecha_notificacion');
        $this->db->from('persona p');
        $this->db->join('encuesta_grupo eg', 'eg.grupo_id = p.grupo_id and eg.encuesta_grupo_estado_id <> 0', 'inner');
        $this->db->join('encuesta_programacion ep2', 'eg.encuesta_id = ep2.encuesta_id and eg.grupo_id = ep2.grupo_id and ep2.encuesta_programacion_estado_id <> 0', 'inner');
        $this->db->join('encuesta e', 'e.encuesta_id = eg.encuesta_id', 'inner');
        $this->db->join('encuesta_persona ep', 'ep.persona_id = p.persona_id and ep2.encuesta_programacion_id = ep.encuesta_programacion_id', 'left');
        //$this->db->where('IFNULL(ep.encuesta_persona_id, 0) = 0', null, false);
        $this->db->where('e.encuesta_estado_id = 2', null, false);
        $this->db->where('p.persona_estado_id = 2', null, false);
        $this->db->where('ep2.encuesta_programacion_estado_id = 2', null, false);
        $this->db->where('DATE_FORMAT(NOW(),"%Y-%m-%d") between ep2.encuesta_programacion_fecha_inicio and ep2.encuesta_programacion_fecha_fin', null, false);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    //Encuesta persona
    public function get_encuesta_persona_byCodigo($codigo){
        
        $this->db->select('p2.persona_apellido_paterno,
        p2.persona_apellido_materno,
        p2.persona_nombre,
        ep2.encuesta_id,
        ep2.encuesta_programacion_id,
        ep.encuesta_persona_id');
        $this->db->from('encuesta_persona ep');
        $this->db->join('persona p2', 'ep.persona_id = p2.persona_id', 'inner');
        $this->db->join('encuesta_programacion ep2', 'ep.encuesta_programacion_id = ep2.encuesta_programacion_id', 'inner');
        $this->db->where('ep.encuesta_persona_codigo', $codigo);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function encuesta_persona_insertar($data){
        
        $this->db->set('encuesta_persona_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_persona_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('encuesta_persona_estado_id', 1, FALSE);
        $this->db->insert('encuesta_persona', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function encuesta_persona_noti_sms($id){
        $query = $this->db->query('update encuesta_persona set encuesta_persona_cantidad_sms = encuesta_persona_cantidad_sms + 1 where encuesta_persona_id = '.$id);
    }
    public function encuesta_persona_noti_email($id){
        $query = $this->db->query('update encuesta_persona set encuesta_persona_cantidad_email = encuesta_persona_cantidad_email + 1 where encuesta_persona_id = '.$id);
    }

    public function encuesta_persona_respuesta_insertar($data){

        $this->db->set('encuesta_pregunta_respuesta_persona_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_pregunta_respuesta_persona_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('encuesta_pregunta_respuesta_persona', $data);
        
        return true;
    }

    public function encuesta_persona_respuesta_actualizar($id){

        $this->db->set('encuesta_pregunta_respuesta_persona_estado_id', 0);
        $this->db->set('encuesta_pregunta_respuesta_persona_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_pregunta_respuesta_persona_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_pregunta_respuesta_persona_id', $id);
        $this->db->update('encuesta_pregunta_respuesta_persona');
       
        return true;
    }

    public function encuesta_persona_insertar_job($data){
        
        $this->db->set('encuesta_persona_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_persona_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('encuesta_persona_estado_id', 1, FALSE);
        $this->db->insert('encuesta_persona', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function encuesta_persona_notificacion_actualizar($fecha, $id){

        $this->db->set('encuesta_persona_fecha_notificacion', $fecha);
        $this->db->where('encuesta_persona_id', $id);
        $this->db->update('encuesta_persona');
       
        return true;
    }
}
?>
