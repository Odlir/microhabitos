<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProgramacionModel extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }

    public function get_mensaje_email(){
        $this->db->select('cm.codigo_maestro_id,
        cm.codigo_maestro_tipo_id,
        cm.codigo_maestro_valor,
        cm.codigo_maestro_descripcion,
        cm.codigo_maestro_estado_id,
        u.usuario_nombre as codigo_maestro_usuario_creacion_str,
        cm.codigo_maestro_fecha_creacion,
        u2.usuario_nombre as codigo_maestro_usuario_modificacion_str,
        cm.codigo_maestro_fecha_modificacion');
        $this->db->from('codigo_maestro cm');
        $this->db->join('usuario u', 'cm.codigo_maestro_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'cm.codigo_maestro_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 10);
        $this->db->where('cm.codigo_maestro_valor', 1);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function codigo_maestro_actualizar($idMensaje, $data){
        $this->db->set('codigo_maestro_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('codigo_maestro_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('codigo_maestro_id', $idMensaje);
        $this->db->update('codigo_maestro', $data);
        return true;
    }

    public function get_mensaje_sms(){
        $this->db->select('cm.codigo_maestro_id,
        cm.codigo_maestro_tipo_id,
        cm.codigo_maestro_valor,
        cm.codigo_maestro_descripcion,
        cm.codigo_maestro_estado_id,
        u.usuario_nombre as codigo_maestro_usuario_creacion_str,
        cm.codigo_maestro_fecha_creacion,
        u2.usuario_nombre as codigo_maestro_usuario_modificacion_str,
        cm.codigo_maestro_fecha_modificacion');
        $this->db->from('codigo_maestro cm');
        $this->db->join('usuario u', 'cm.codigo_maestro_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'cm.codigo_maestro_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 10);
        $this->db->where('cm.codigo_maestro_valor', 2);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }
    

    public function get_programaciones(){

        $estados = array('1', '2');
        $this->db->select('ep2.encuesta_persona_codigo,
        ep.encuesta_programacion_id,
        e.encuesta_id,
        e.encuesta_nombre as encuesta_str,
        g.grupo_id,
        g.grupo_nombre as grupo_str,
        cm2.codigo_maestro_descripcion as encuesta_programacion_tipo_str,
        ep.encuesta_programacion_nombre,
        ep.encuesta_programacion_mensaje_email,
        ep.encuesta_programacion_mensaje_sms,
        ep.encuesta_programacion_fecha_inicio,
        ep.encuesta_programacion_fecha_fin,
        ep.encuesta_programacion_estado_id,
        cm.codigo_maestro_descripcion as encuesta_programacion_estado_str,
        u.usuario_nombre as encuesta_programacion_usuario_creacion_str,
        encuesta_programacion_usuario_creacion_id,
        ep.encuesta_programacion_fecha_creacion,
        u2.usuario_nombre as encuesta_programacion_usuario_modificacion_str,
        ep.encuesta_programacion_fecha_modificacion');
        $this->db->from('encuesta_programacion ep');
        
        $this->db->join('encuesta e', 'ep.encuesta_id = e.encuesta_id', 'inner');
        $this->db->join('grupo g', 'ep.grupo_id = g.grupo_id', 'inner');
        $this->db->join('codigo_maestro cm', 'ep.encuesta_programacion_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('codigo_maestro cm2', 'ep.encuesta_programacion_tipo_id = cm2.codigo_maestro_valor', 'inner');

        $this->db->join('usuario u', 'ep.encuesta_programacion_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'ep.encuesta_programacion_usuario_modificacion_id = u2.usuario_id', 'left');

        $this->db->join('encuesta_persona ep2', 'ep.encuesta_programacion_id = ep2.encuesta_programacion_id and ep2.persona_id = '. get_usuario_persona_id(), 'left');
        

        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('cm2.codigo_maestro_tipo_id', 8);

        if(get_usuario_login_tipo() != "1"){ // no admin
            
            $this->db->where('(ep.encuesta_programacion_usuario_creacion_id = '. get_usuario_login_id().' OR ep2.encuesta_persona_codigo is not null)', null, false);
        }

        $this->db->where_in('ep.encuesta_programacion_estado_id', $estados);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_programacion($id){
        $this->db->select('*');
        $this->db->from('encuesta_programacion');
        $this->db->where('encuesta_programacion_id', $id);
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function programacion_actualizar($id, $data){
        
        $this->db->set('encuesta_programacion_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_programacion_id', $id);
        $this->db->update('encuesta_programacion', $data);
        return true;
    }

    public function programacion_insertar($data){
        
        $this->db->set('encuesta_programacion_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('encuesta_programacion', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function programacion_pregunta_insertar($data){
        
        $this->db->set('encuesta_programacion_pregunta_usuario_creacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_pregunta_fecha_creacion', 'NOW()', FALSE);
        $this->db->insert('encuesta_programacion_pregunta', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function programacion_pregunta_actualizar($id, $data){
        
        $this->db->set('encuesta_programacion_pregunta_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_pregunta_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_programacion_pregunta_id', $id);
        $this->db->update('encuesta_programacion_pregunta', $data);
        return true;
    }

    public function programacion_eliminar($id){
        
        $this->db->set('encuesta_programacion_estado_id', 0);
        $this->db->set('encuesta_programacion_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_programacion_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_programacion_id', $id);
        $this->db->update('encuesta_programacion');
        return true;
    }

    public function programacion_grupo_eliminar($idprogramacion, $idgrupo){
        $estados = array('1', '2');
        $this->db->set('encuesta_grupo_estado_id', 0);
        $this->db->set('encuesta_grupo_usuario_modificacion_id', get_usuario_login_id());
        $this->db->set('encuesta_grupo_fecha_modificacion', 'NOW()', FALSE);
        $this->db->where('encuesta_programacion_id', $idprogramacion);
        $this->db->where('grupo_id', $idgrupo);
        $this->db->where_in('encuesta_grupo_estado_id', $estados);
        $this->db->update('encuesta_grupo');
        return true;
    }
}
?>