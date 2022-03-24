<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoriaModel extends CI_Model{
    
    public function __construct()
    {
        $this->load->database();
    }

    public function get_grupos(){

        $estados = array('1', '2');
        $this->db->select('g.categoria_id,
        g.categoria_nombre,
        cm.codigo_maestro_descripcion as categoria_estado_str,
        g.categoria_estado_id,
        u.usuario_nombre as categoria_usuario_creacion_str,
        g.categoria_fecha_creacion,
        u2.usuario_nombre as categoria_usuario_modificacion_str,
        g.categoria_fecha_modificacion');
        $this->db->from('categoria g');
        $this->db->join('codigo_maestro cm', 'g.categoria_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('usuario u', 'g.categoria_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'g.categoria_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where_in('g.categoria_estado_id', $estados);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_grupos_combo(){

        //$estados = array('1', '2');
        $this->db->select('g.categoria_id,
        g.categoria_nombre,
        cm.codigo_maestro_descripcion as categoria_estado_str,
        g.categoria_estado_id,
        u.usuario_nombre as categoria_usuario_creacion_str,
        g.categoria_fecha_creacion,
        u2.usuario_nombre as categoria_usuario_modificacion_str,
        g.categoria_fecha_modificacion');
        $this->db->from('categoria g');
        $this->db->join('codigo_maestro cm', 'g.categoria_estado_id = cm.codigo_maestro_valor', 'inner');
        $this->db->join('usuario u', 'g.categoria_usuario_creacion_id = u.usuario_id', 'left');
        $this->db->join('usuario u2', 'g.categoria_usuario_modificacion_id = u2.usuario_id', 'left');
        $this->db->where('cm.codigo_maestro_tipo_id', 1);
        $this->db->where('g.categoria_estado_id', 2);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function get_grupo($id){
        $this->db->select('*');
        $this->db->from('categoria');
        $this->db->where('categoria_id', $id);
        $query = $this->db->get();
        $data = $query->result();
        
        return $data;
    }

    public function grupo_actualizar($id, $data){
        
        $this->db->set('categoria_fecha_modificacion', 'NOW()', FALSE);
        $this->db->set('categoria_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('categoria_id', $id);
        $this->db->update('categoria', $data);
        return true;
    }

    public function grupo_insertar($data){
        $this->db->set('categoria_fecha_creacion', 'NOW()', FALSE);
        $this->db->set('categoria_usuario_creacion_id', get_usuario_login_id());
        $this->db->insert('categoria', $data);
        return true;
    }

    public function grupo_eliminar($id){
        
        $this->db->set('categoria_estado_id', 0);
        $this->db->set('categoria_fecha_modificacion', 'NOW()', FALSE);
        $this->db->set('categoria_usuario_modificacion_id', get_usuario_login_id());
        $this->db->where('categoria_id', $id);
        $this->db->update('categoria');
        return true;
    }
}
?>
