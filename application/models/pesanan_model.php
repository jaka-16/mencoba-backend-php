<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pesanan_model extends CI_Model {

	private $table_name = "pelanggan_kitab";

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
	public function update($user_id, $id_kitab){
        //$this->record["updated_at"] = date("Y-m-d h-i-s");
        $this->db->update($this->table_name, $this->record, array('id_pelanggan' => $user_id, 'id_buku' => $id_kitab));
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    
	}
	
	public function select_id($user_id, $id_buku){
        $this->db->select('*');
        $query = $this->db->get_where($this->table_name, array('id_pelanggan' => $user_id, 'id_buku' => $id_buku ));
        if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
    }
	
	public function check_total($id){
		$this->db->select('id_pelanggan, nama, id_buku, nama_buku, harga_buku, jumlah_pesanan, tanggal_pemesanan, total_harga, status, kode_promo');
        $query = $this->db->get_where($this->table_name, array("id_pelanggan" => $id));
		if($query->num_rows() > 0){
			return $query->result();
			
		}else{
		$this->db->select('id_pelanggan, nama, id_buku, nama_buku, harga_buku, jumlah_pesanan, tanggal_pemesanan, total_harga, status, kode_promo');	
		$query = $this->db->get_where($this->table_name, array("nama" => $id));
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		
		}
		}
		
	}
	public function check_status($user_id, $nama){
        $this->db->select('id_buku, nama_buku, harga_buku, jumlah_pesanan, total_harga, status');
        $query = $this->db->get_where($this->table_name, array('id_pelanggan' => $user_id, 'nama' => $nama ));
        if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
    }
	
	public function deleting($user_id, $id_kitab){
        $this->db->delete($this->table_name, array('id_pelanggan' => $user_id, 'id_buku' => $id_kitab));
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
		}
	}
	public function check_status_user($status, $periode){
        $this->db->select('id_pelanggan, nama, alamat, id_buku, total_harga, status');
        $query = $this->db->get_where($this->table_name, array('status' => $status, 'tanggal_pemesanan' => $periode));
        if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
		
	public function check_kode_promo($nama){
        $this->db->select('*');
        $query = $this->db->get_where($this->table_name, array('nama' => $nama, 'kode_promo' => 'Terpakai Disc. 15%'));
        if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}	
    }
}