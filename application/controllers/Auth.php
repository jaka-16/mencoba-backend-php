<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

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
        //$this->email->attach('C:\Users\MACHINE\Downloads\CV(JAKA)');

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
	
	public function register(){
	if(!username_pass_validate_length($_POST["username"])){
            echo json_encode(response_error(203, "Maaf, username harus memiliki panjang minimal 8 karakter, maksimal 20 karakter"));
    } else if(!username_pass_validate_upper($_POST["username"])) {
            echo json_encode(response_error(203, "Maaf, username setidaknya memiliki 1 huruf besar"));
    } else if(!username_pass_validate_lower($_POST["username"])) {
            echo json_encode(response_error(203, "Maaf, username setidaknya memiliki 1 huruf kecil"));
	} else if(!username_pass_validate_length($_POST["password"])){
            echo json_encode(response_error(203, "Maaf, password harus memiliki panjang minimal 12 karakter"));
    } else if(!username_pass_validate_upper($_POST["password"])) {
            echo json_encode(response_error(203, "Maaf, password setidaknya memiliki 1 huruf besar"));
    } else if(!username_pass_validate_lower($_POST["password"])) {
            echo json_encode(response_error(203, "Maaf, password setidaknya memiliki 1 huruf kecil"));
    } else if(!password_validate_number($_POST["password"])) {
            echo json_encode(response_error(203, "Maaf, password setidaknya memiliki 1 angka"));
    } else if(!password_validate_special($_POST["password"])) {
            echo json_encode(response_error(203, "Maaf, password setidaknya memiliki 1 spesial karakter"));
	}else{
		$this->user->record = [
				"user_id" => $_POST["id"],
                "username" => $_POST["username"],
                "email" => $_POST["email"],
                "password" => password_hash($_POST["password"], PASSWORD_BCRYPT),
                "fullname" => $_POST["fullname"]
            ];
		if(!$this->user->create()){
			echo json_encode(response_error(203, "Maaf, akun yang anda buat tidak berhasil terdaftar"));
		}else{
			echo json_encode(response_success("Selamat, akun anda berhasil didaftarkan."));
		}	
	}
	}
	
	public function login(){
		$result = $this->user->check_account($_POST["username_or_email"]);
		
		if(empty($_POST["username_or_email"]) && empty($_POST['password'])){
			echo json_encode(response_error(203, "Maaf, login harus menggunakan username dan password"));
		}else if(empty($_POST["username_or_email"])){
			echo json_encode(response_error(203, "Maaf, login harus menggunakan username"));
		}else if(empty($_POST['password'])){
			echo json_encode(response_error(203, "Maaf, login harus menggunakan password"));		
		}else{
				if(!$result){
					
					echo json_encode(response_error(203, "Maaf, username yang anda masukkan salah"));
					
				}else{
					
					if(password_verify($_POST['password'], $result[0]->password)){
						echo json_encode(response_success("Selamat datang admin."));
						
					}else if(!empty($_POST['reset_password'])){
							
							if(isset($_POST['new_password'])){
								
								$new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
								
								$pesan_email = "Hai, ".$result[0]->username."<br>
								Silahkan klik link di bawah ini untuk mereset password :<br><a href=\"http://localhost/kitab/index.php/Auth/reset_password?email=".$result[0]->email."&new_password=".$new_password."\">Reset Password</a>"; 	
						
								$send_mail = $this->index('pratama.jaka548@gmail.com', 'Reset Password', $pesan_email);
								if(!$send_mail){
									echo json_encode(response_error(203, "Maaf, link reset password gagal dikirim ke email"));
								}else{
									echo json_encode(response_success("link reset password telah dikirm ke email, silahkan dicek"));
								}
							}
					}else{
						echo json_encode(response_error(203, "Maaf, password anda tidak cocok, coba ulangi"));
						
					
					}
					
					
				}	
			
				
			}
	}
	
	public function reset_password(){
		
		if(empty($_GET['new_password'])){
			echo json_encode(response_error(203, "Maaf, parameter password tidak boleh kosong"));
		
		}else{
			
			$this->user->record['password'] = $_GET['new_password'];
			
			$result = $this->user->update($_GET['email']);
			if(!$result){
				echo json_encode(response_error(203, "Maaf, belum berhasil mereset password"));
			}else{
			
				echo json_encode(response_success("Selamat anda berhasil mereset password"));
			}
		}
		
	}



}