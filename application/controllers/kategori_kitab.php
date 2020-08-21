<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

class kategori_kitab extends CI_Controller {       

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
	private $response = [];
	
	 
    public function __construct()
    {
        parent::__construct();
		$this->load->helper('reformat_helper');
        $this->load->helper('response_helper');
        $this->load->model('kitab_model');
		$this->load->model('pesanan_model');
    }
	
	public function index($to_mail, $subject, $message){
	
        $config = [
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_user' => 'pratama.jaka16@gmail.com',  // Email gmail
            'smtp_pass'   => 'K3ramasan',  // Password gmail
            'smtp_crypto' => 'ssl',
            'smtp_port'   => 465,
            'crlf'    => "\r\n",
            'newline' => "\r\n"
        ];
		$this->load->library('email', $config);

        // Email dan nama pengirim
        $this->email->from('no-reply@gmail.com', 'Jual Kitab');

        // Email penerima
        $this->email->to($to_mail); // Ganti dengan email tujuan

        // Lampiran email, isi dengan url/path file
        $this->email->attach('C:\Users\MACHINE\Downloads\CV(JAKA)');

        // Subject email
        $this->email->subject($subject);

        // Isi email
        $this->email->message($message);
		
		
		//$this->email->message($message1);
        // Tampilkan pesan sukses atau error
        if($this->email->send()){
			return true;
			// echo "sukses, mengirimkan email";
			
        }else{
            return false;
			
			echo $this->email->print_debugger();
        }
    }
	
	
	public function summ(){
		$result = $this->kitab_model->check_kitab($_POST['nama_kitab_or_id']);
		if(empty($_POST['nama_kitab_or_id']) && empty($_POST['jumlah']) && empty($_POST['id'])){
			echo "tidak boleh kosong";
		 
		}else if(empty($_POST['nama_kitab_or_id']) && empty($_POST['jumlah'])){
			
			$this->response["message"] = "maaf, untuk pemesanan nama kitab dan jumlah tidak boleh kosong";
			echo json_encode($this->response);
			
		}else if(empty($_POST['id'])){
			
			$this->response["message"] = "maaf, id pelanggan tidak boleh kosong";
			echo json_encode($this->response);
			
		}else if(preg_match('/,/', $_POST['nama_kitab_or_id']) && preg_match('/,/', $_POST['jumlah'])){
				
				if(strpos($_POST['nama_kitab_or_id'], ', ') && strpos($_POST['jumlah'], ', ')){
					
					$check_id = $this->pesanan_model->check_total($_POST['id']);
					if(!$check_id){
						
					$id = explode(", ", $_POST['nama_kitab_or_id']);
					$jumlah_buku = explode(", ", $_POST['jumlah']);
					 
					$total_belanja = 0;
					
					for($i=0; $i < count($id); $i++){
						$result = $this->kitab_model->check_kitab($id[$i]);
						if($result){
							
							if($_POST['kode_promo'] == 75){
								
								$kode_promo = "";
								$check_kode = $this->pesanan_model->check_kode_promo($_POST['nama']);
								if(!$check_kode){
							
									$disc = ($result[0]->harga * 15)/100;
							
									$nilai = $result[0]->harga - $disc;
									$total_harga = $nilai * $jumlah_buku[$i];
							
									echo json_encode(response_success("selamat, anda mendapatkan potongan 15% untuk buku yang pertama"));
									$kode_promo .= "Terpakai Disc. 15%";
								}else{
								
									$kode_promo .= "Sudah Terpakai Kode Disc. 15%";
									$harga = $result[0]->harga;
									$total_harga = $harga * $jumlah_buku[$i];
									echo json_encode(response_error(203, "Maaf, anda sudah menggunakan kode promo"));
								
								
								}
					
							}else{
								
								$harga = $result[0]->harga;
								$total_harga = $harga * $jumlah_buku[$i];
								echo json_encode(response_error(203, "Maaf, anda tidak mendapatkan potongan harga, coba gunakan kode promo 75"));
								$kode_promo = "Belum Terpakai";
							}
							
							//$total_harga = $result[0]->harga * $jumlah_buku[$i];
							
							$total_belanja += $total_harga;
							
							if(is_numeric($id[$i])){
								
								$this->pesanan_model->record = [
								"id_pelanggan" => $_POST['id'],
								"nama" => $_POST['nama'],
								"alamat" => $_POST['alamat'],
								"id_buku" => $id[$i], 
								"nama_buku" => $result[0]->judul_kitab, 
								"harga_buku" => $result[0]->harga,							
								"jumlah_pesanan" => $jumlah_buku[$i],
								"total_harga" => $total_harga,
								"tanggal_pemesanan" => date("Y-m-d"),
								"status" => "On Proses",
								"kode_promo" => $kode_promo];
							}else{
								
								$this->pesanan_model->record = [
								"id_pelanggan" => $_POST['id'],
								"nama" => $_POST['nama'],
								"alamat" => $_POST['alamat'],
								"id_buku" => $result[0]->id,
								"nama_buku" => $id[$i], 
								"harga_buku" => $result[0]->harga,							
								"jumlah_pesanan" => $jumlah_buku[$i],
								"total_harga" => $total_harga,
								"tanggal_pemesanan" => date("Y-m-d"),
								"status" => "On Proses",
								"kode_promo" => $kode_promo];
							}
							
							$result = $this->pesanan_model->create();
							if($result){
								//$message = "selamat, data anda berhasil diinsert, masuk ketahap pemrrosesan";
								echo json_encode($this->pesanan_model->record);
							}else{
								
								echo json_encode(response_error(203, "Maaf, insert tidak berhasil"));
						}
							
						}else{
							$this->pesanan_model->record['pesan penting'] = "maaf, judul buku atau id $id[$i] belum tersedia"; 
							echo json_encode($this->pesanan_model->record);
						}
						}
						
						$this->response['total_belanja'] = $total_belanja;
						echo json_encode($this->response)."\n";
						
						
					
					$id_buku = "";
					$nama_buku = "";
					$jumlah_pesanan = "";
					$harga_buku = "";
					$total_harga = "";
					
					$message = $this->pesanan_model->check_total($_POST['id']);
					for($i=0; $i < count($message); $i++){
						
						if($i < count($message)-1){
							
							$id_buku .= $message[$i]->id_buku.", ";
							$nama_buku .= $message[$i]->nama_buku.", ";
							$jumlah_pesanan .= $message[$i]->jumlah_pesanan.", ";
							$harga_buku .= $message[$i]->harga_buku.", ";
							$total_harga .= $message[$i]->total_harga.", ";
							
						}else{
							
							$id_buku .= $message[$i]->id_buku;
							$nama_buku .= $message[$i]->nama_buku;
							$jumlah_pesanan .= $message[$i]->jumlah_pesanan;
							$harga_buku .= $message[$i]->harga_buku;
							$total_harga .= $message[$i]->total_harga;
						}
						
						
					}
						
					$pesan_email = "Assalamu'alaikum <b>".$message[0]->nama."</b>,<br>
					<b>Berikut Detail Pesanan Kamu</b><br>
					Id Pelanggan : ".$message[0]->id_pelanggan."<br>
					Id Buku : ".$id_buku."<br>
					Nama Buku : ".$nama_buku."<br>
					Tanggal Pemesanan : ".reformat_date($message[0]->tanggal_pemesanan)."<br>
					Status : ".$message[0]->status."<br>
					Kode Promo : ".$message[0]->kode_promo."<br>
					<h5 align=\"left\">Rincian Pesanan</h5>
					<table style=\"border-radius: 10px; border: 1px solid #48D1CC;\" width=\"800px\" bgcolor=\"#E0FFFF\" cellpadding=\"4\">
					<tr>
						<th>Nama Buku</th>
						<th>Jumlah Pesanan</th>
						<th>Harga</th>
						<th>Total Harga Per Item</th>
					</tr>
					<tr>
						<td><b>".$nama_buku."</b></td>
						<td>".$jumlah_pesanan."</td>
						<td>".$harga_buku."</td>
						<td>".$total_harga."</td>
					</tr>
					<tr>
						<td colspan=\"3\"><b>Total Harga</b></td>
						<td><b>Rp ".$total_belanja."</b></td>
					</tr>
					</table>";
					
					$email = $this->index('pratama.jaka548@gmail.com', 'Total harga yang harus dibayar',$pesan_email);
					if(!$email){
						
						$message = "Tidak berhasil mengirim rincian belanja ke email";
						echo json_encode(response_error(203, $message));
						
					}else{
						$message = "Berhasil mengirimkan rincian belanja ke email anda";
						echo json_encode(response_success($message));
						
					}
						
						
					}else{
						$id = rand(0,1000);
						
						do{
							if($this->pesanan_model->check_total($id)){
						
								$id = rand(0,1000);
								echo json_encode(response_error(203, "Maaf, id pelanggan sudah ada, coba gunakan id : $id"));
								break;
						
							}else{
								
								echo json_encode(response_error(203, "Maaf, id pelanggan sudah ada, coba gunakan id : $id"));
							}
							}while($this->pesanan_model->check_total($id));
							
						
					}
				}else{
					
					$this->response['message_error'] = "maaf, anda salah memasukkan delimiter untuk pesanan varian kitab. ex : nama_kitab1, nama_kitab2 dan jumlah1, jumlah2";
					echo json_encode($this->response);
					
				}
				
						
		}else if($result){
				if(!empty($_POST['jumlah']) && preg_match('/,/', $_POST['jumlah'])){
					$this->response["message"] = "maaf, pemesanan satu kitab masukkan jumlah yang diinginkan tanpa menambahkan tanda dibelakangnya";
					echo json_encode($this->response);
					
				}else if(!empty($_POST['nama_kitab_or_id']) && preg_match('/,/', $_POST['nama_kitab_or_id'])){
					$this->response["message"] = "maaf, pemesanan satu kitab masukkan nama kitab or id yang diinginkan tanpa menambahkan tanda dibelakangnya";
					echo json_encode($this->response);
					
				}else if(empty($_POST['nama_kitab_or_id'])){
					
					$this->response["message"] = "maaf, pemesanan nama kitab tidak boleh kosong";
					echo json_encode($this->response);
					
				}else if(empty($_POST['jumlah'])){
					
					$this->response["message"] = "maaf, pemesanan jumlah kitab tidak boleh kosong";
					echo json_encode($this->response);
					
				}else{ 
					
					$check_id = $this->pesanan_model->check_total($_POST['id']);
					if(!$check_id){
						
						if($_POST['kode_promo'] == 75){
							
							$kode_promo = "";
							$check_kode = $this->pesanan_model->check_kode_promo($_POST['nama']);
							if(!$check_kode){
								
								$disc = ($result[0]->harga * 15)/100;
							
								$nilai = $result[0]->harga - $disc;
								$total_harga = $nilai * $_POST['jumlah'];
							
								echo json_encode(response_success("selamat, anda mendapatkan potongan 15%"));
								$kode_promo .= "Terpakai Disc. 15%";
								
							}else{
								
								$kode_promo .= "Sudah Terpakai Kode Disc. 15%";
								$harga = $result[0]->harga;
								$total_harga = $harga * $_POST['jumlah'];
								echo json_encode(response_error(203, "Maaf, anda sudah menggunakan kode promo"));
								
							}
					
						}else{
							
							$kode_promo = "Belum Terpakai";
							$harga = $result[0]->harga;
							$total_harga = $harga * $_POST['jumlah'];
							echo json_encode(response_error(203, "Maaf, anda tidak mendapatkan potongan harga gunakan kode promo 75"));
						
						}
					
					if(is_numeric($_POST['nama_kitab_or_id'])){
								
							$this->pesanan_model->record = [
							"id_pelanggan" => $_POST['id'],
							"nama" => $_POST['nama'],
							"alamat" => $_POST['alamat'],
							"id_buku" => $_POST['nama_kitab_or_id'], 
							"nama_buku" => $result[0]->judul_kitab, 
							"harga_buku" => $result[0]->harga,							
							"jumlah_pesanan" => $_POST['jumlah'],
							"total_harga" => $total_harga,
							"tanggal_pemesanan" => date("Y-m-d"),
							"status" => "On Proses",
							"kode_promo" => $kode_promo
							];
						}else{
								
							$this->pesanan_model->record = [
							"id_pelanggan" => $_POST['id'],
							"nama" => $_POST['nama'],
							"alamat" => $_POST['alamat'],
							"id_buku" => $result[0]->id,
							"nama_buku" => $_POST['nama_kitab_or_id'], 
							"harga_buku" => $result[0]->harga,							
							"jumlah_pesanan" => $_POST['jumlah'],
							"total_harga" => $total_harga,
							"tanggal_pemesanan" => date("Y-m-d"),
							"status" => "On Proses",
							"kode_promo" => $kode_promo];
							}
							
					$result = $this->pesanan_model->create();
					if($result){
						//$message = "selamat, data anda berhasil diinsert, masuk ketahap pemrrosesan";
						echo json_encode($this->pesanan_model->record);
					}else{
								
						echo json_encode(response_error(203, "Maaf, insert tidak berhasil"));
					}		
					
					$this->response['total_belanja'] = $total_harga;
					echo json_encode($this->response);
					
					$message = $this->pesanan_model->check_total($_POST['id']);
					
					$pesan_email = "Assalmu'alaikum <b>".$message[0]->nama."</b>,<br>
					<b>Berikut Detail Pesanan Kamu</b><br>
					Id Pelanggan : ".$message[0]->id_pelanggan."<br>
					Id Buku : ".$message[0]->id_buku."<br>
					Nama Buku : ".$message[0]->nama_buku."<br>
					Tanggal Pemesanan : ".reformat_date($message[0]->tanggal_pemesanan)."<br>
					Status : ".$message[0]->status."<br>
					Kode Promo : ".$message[0]->kode_promo."<br>
					<h5 align=\"left\">Rincian Pesanan</h5>
					<table style=\"border-radius: 10px; border: 1px solid #48D1CC;\" width=\"800px\" bgcolor=\"#E0FFFF\" cellpadding=\"4\">
					<tr>
						<th>Nama Buku</th>
						<th>Jumlah Pesanan</th>
						<th>Harga</th>
					</tr>
					<tr>
						<td><b>".$message[0]->nama_buku."</b></td>
						<td>".$message[0]->jumlah_pesanan."</td>
						<td>".$message[0]->harga_buku."</td>
					</tr>
					<tr>
						<td colspan=\"2\"><b>Total Harga</b></td>
						<td><b>Rp ".$total_harga."</b></td>
					</tr>
					</table>"; 
					
					$email = $this->index('pratama.jaka548@gmail.com', 'Total harga yang harus dibayar',$pesan_email);
					if(!$email){
						
						$message = "Tidak Berhasil mengirim rincian ke email";
						echo json_encode(response_error(203, $message));
						
					}else{
						$message = "Berhasil Mengirimkan rincian belanja ke email anda";
						echo json_encode(response_success($message));
						
					}
					
					}else{
						$id = rand(0,1000);
						
						do{
						if($this->pesanan_model->check_total($id)){
						
							$id = rand(0,1000);
							echo json_encode(response_error(203, "Maaf, id pelanggan sudah ada, coba gunakan id : $id"));
							break;
						
						}else{
							echo json_encode(response_error(203, "Maaf, id pelanggan sudah ada, coba gunakan id : $id"));
							
						}
						
						}while($this->pesanan_model->check_total($id));
						
					}					
			
				}
					
		}else{	
			echo "data tidak ada";
			
		}
		
}

	public function check_in(){
		$result = $this->kitab_model->check_kitab($_POST['nama_kitab_or_id']);
		if($result){
			for($i=0; $i < count($result); $i++){
				foreach($result[$i] as $key => $output){
				echo $key." : ".$output."\n";}
			}
		}else{
			echo "data tidak ada";
		}
		
	} 
	public function check_kitab(){
		$result = $this->kitab_model->check_all_kitab();
		if(count($result) > 0){
			for($i=0; $i < count($result); $i++){
				echo json_encode($result[$i])."\n";
			}
		}else{
			echo "data tidak ada";
		}
	}

	public function update_kitab(){
		$this->pesanan_model->record = [
							"id_pelanggan" => $_POST['id'],
							"nama" => $_POST['nama'],
							"alamat" => $_POST['alamat'],
							"id_buku" => $_POST['id_buku'], 						
							"jumlah_pesanan" => $_POST['jumlah']];
									
		$result = $this->kitab_model->check_kitab($_POST['id_buku']);
		
		if($result){
			$harga = $result[0]->harga;
			$total_harga = $harga * $_POST['jumlah'];
			$this->pesanan_model->record['total_harga'] = $total_harga;	
		
		// $this->pesanan_model->record['total_belanja'] = $total_harga;
		if($this->pesanan_model->select_id($_POST['id'], $_POST['id_buku'])){
			$result = $this->pesanan_model->update($_POST['id'], $_POST['id_buku']);
			if($result){
			
				$message = "selamat, data pembelian anda berhasil diupdate";
			}else{
				
				$message = "tidak ada data yang diupodate";	
			}		
		}else{
						
			$message = "data tidak ada";
						
		}
		
						
		if($result){
			echo json_encode(response_success($message));
		}else{
			echo json_encode(response_error(203, $message));
						
		}
		
		}else{
			
		$message = "maaf, data belum diupdate";
		echo json_encode(response_error(203, $message));
		
		}
		
		$hasil = $this->pesanan_model->check_total($_POST['id']);

		$harga_semua = 0;
		for($i=0; $i < count($hasil); $i++){
			$harga_semua += $hasil[$i]->total_harga;
			
		}
			
		$this->pesanan_model->record = [ "data update" => $hasil, "total belanja" => $harga_semua ];
		echo json_encode($this->pesanan_model->record);
		
}

	public function check_status(){
		$result = $this->pesanan_model->check_status($_POST['id'], $_POST['nama']);
		if($result){
			
			$harga_semua = 0;
			for($i=0; $i < count($result); $i++){
				
			$harga_semua += $result[$i]->total_harga;
			
			}
			
			$this->response = [ 
			"check_status" => $result,
			"check_total_belanja" => $harga_semua
			];
			
			echo json_encode($this->response);
			
		}else{
			
			$this->response['message_error'] = "maaf, tidak ada pesanan oleh nama atau id yang anda masukkan";
			echo json_encode($this->response);
		}
		
	}
	
	public function delete_data(){
		$result = $this->pesanan_model->deleting($_POST['id_user'], $_POST['id_buku']);
		if(empty($_POST['id_user']) && empty($_POST['id_buku'])){
			$message = "maaf, kolom id user dan id buku tidak boleh kosong";
			echo json_encode(response_error(203, $message));
		}else{
			if(!$result){
				$message = "maaf, data belum berhasil dihapus";
				echo json_encode(response_error(203, $message));
			}else{
				$message = "selamat, data yang tidak dikehendaki berhasil dihapus";
				echo json_encode(response_success($message));
		}		
		}	
	}
	
	
}	