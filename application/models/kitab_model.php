<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class kitab_model extends CI_Model {

	private $table_name = "spek_kitab";

    public $record = [];

	 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


	public function check_all_kitab(){
		$this->db->select('id, judul_kitab, harga');
        $query = $this->db->get($this->table_name);
		return $query->result();
		}
		
	public function check_kitab($nama_or_id){
		$this->db->select('*');
        $query = $this->db->get_where($this->table_name, array('id' => $nama_or_id));
		if($query->num_rows() > 0){
			return $query->result();
			
		}else{
		$this->db->select('*');
        $query = $this->db->get_where($this->table_name, array('judul_kitab' => $nama_or_id));
		if($query->num_rows() > 0){
			return $query->result();
			
		}else{
			return false;
		}	
	}
		
	} 	
	
	public function update($id_kitab){
        //$this->record["updated_at"] = date("Y-m-d h-i-s");
        $this->db->update($this->table_name, $this->record, array('id' => $id_kitab));
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    
	}
	public function create(){
		$this->db->insert($this->table_name, $this->record);
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
		
	}
		
	}