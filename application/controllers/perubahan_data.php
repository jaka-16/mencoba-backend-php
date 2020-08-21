<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class perubahan_data extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('validation_helper');
        $this->load->helper('response_helper');
        $this->load->model('user');
		$this->load->model('kitab_model');
		$this->load->model('pesanan_model');
    }
	
	public function insert_kitab_new(){
		$result = $this->user->check_account($_POST["username_or_email"]);
		if(!$result){ 
			echo json_encode(response_error(203, "Maaf, insert tidak dapat dilakukan karena login belum berhasil, cek kembali username anda"));
		}else{
			if(password_verify($_POST['password'], $result[0]->password)){
				$this->kitab_model->record = [
				"id" => $_POST["id_buku"],
				"judul_kitab" => $_POST["judul_kitab"],
				"pengarang" => $_POST["penyusun"],
				"penerbit" => $_POST["penerbit"],
				"cetakan" => $_POST["cetakan"],
				"harga" => $_POST["harga"]];
				$insert = $this->kitab_model->create();
				if(!$insert){
					echo json_encode(response_error(203, "Maaf, insert kitab terbaru belum sukses"));
				}else{
					echo json_encode(response_success("Selamat, insert kitab terbaru berhasil."));
				}			
			}else{
				echo json_encode(response_error(203, "Maaf, insert tidak dapat dilakukan karena login belum berhasil, cek kembali password anda"));	
			}
		
	}
	}
	public function update_kitab_new(){
		$result = $this->user->check_account($_POST["username_or_email"]);
		if(!$result){
		
			echo json_encode(response_error(203, "Maaf, update belum berhasil, cek kembali username atau email anda"));
			
		}else{	
			
			if(password_verify($_POST['password'], $result[0]->password))){
				$this->kitab_model->record = [
				'judul_kitab' => $_POST['judul_kitab'],
				'pengarang' => $_POST['penyusun'],
				'penerbit' => $_POST['penerbit'],
				'cetakan' => $_POST['cetakan'],
				'harga' => $_POST['harga']];
				$insert = $this->kitab_model->update($_POST['id_buku']);
				if(!$insert){
					echo json_encode(response_error(203, "Maaf, tidak ada data yang diupdate"));
				}else{
					echo json_encode(response_success("Selamat, meng-update kitab terbaru telah berhasil."));
				}			
			
			}else{
				
				echo json_encode(response_error(203, "Maaf, insert tidak dapat dilakukan karena login belum berhasil, cek kembali password anda"));
				
			}
		}
		
	}
	
	public function update_data_user(){
		$result = $this->user->check_account($_POST["username_or_email"]);
		if(!$result){ 
			echo json_encode(response_error(203, "Maaf, update belum berhasil, cek kembali username atau email anda"));
		}else{
		
			if(password_verify($_POST['password'], $result[0]->password)){
				$this->pesanan_model->record = [
				'status' => $_POST['status']];
			
				$insert = $this->pesanan_model->update($_POST['id_user'], $_POST['id_buku']);
				if(!$insert){
					echo json_encode(response_error(203, "Maaf, tidak ada data yang diupdate"));
				}else{
					echo json_encode(response_success("Selamat, update status telah berhasil."));
				}			
			
			}else{
				
				echo json_encode(response_error(203, "Maaf, update belum berhasil, cek kembali password anda"));

			}
		
		}
	}
	
	public function check_data_user(){
		$result = $this->user->check_account($_POST["username_or_email"]);
		if($result && password_verify($_POST['password'], $result[0]->password)){
			
			$select = $this->pesanan_model->check_status_user($_POST['status'], $_POST['periode']);
			if(!$select){
				echo json_encode(response_error(203, "Maaf, tidak ada data tersimpan"));
			}else{
				for($i=0; $i < count($select); $i++){
				
				echo json_encode($select[$i])."\n";
				
				}
			}			
			
		}else{
				
			echo json_encode(response_error(203, "Maaf, update belum berhasil, cek kembali user dan password anda`"));
			
		}
		
	}
	
}