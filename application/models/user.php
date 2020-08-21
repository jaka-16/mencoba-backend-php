<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user extends CI_Model {

	private $table_name = "user_kitab";

    public $record = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
		
    }
	
	public function create(){
		$this->db->insert($this->table_name, $this->record);
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
		
	}
	
	public function check_account($username_or_email){
		$this->db->select('*');
		$query = $this->db->get_where($this->table_name, array('username' => $username_or_email));
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			$this->db->select('*');
			$query = $this->db->get_where($this->table_name, array('email' => $username_or_email));
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}
		
	}
	public function check_email($email){
		$this->db->select('*');
		$query = $this->db->get_where($this->table_name, array('email' => $email));
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
	
		}
	}
	public function update($email){
        //$this->record["updated_at"] = date("Y-m-d h-i-s");
        $this->db->update($this->table_name, $this->record, array('email' => $email));
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    
	}
}