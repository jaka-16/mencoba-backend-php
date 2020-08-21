

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class sendMail extends CI_Controller {

    /**
     * Kirim email dengan SMTP Gmail.
     *
     */
	//public $message = []; 
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('kitab_model');
    }
	
	
	
    public function index(){
	

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
        $this->email->from('pratama.jaka16@gmail.com', 'jual kitab');

        // Email penerima
        $this->email->to(); // Ganti dengan email tujuan

        // Lampiran email, isi dengan url/path file
        //$this->email->attach('https://masrud.com/content/images/20181215150137-codeigniter-smtp-gmail.png');

        // Subject email
        $this->email->subject($subject);

        // Isi email
        $this->email->message($message);

        // Tampilkan pesan sukses atau error
        if($this->email->send()){
			return true;
			echo "sukses, mengirimkan email";
			
        }else{
            return false;
			
			echo $this->email->print_debugger();
        }
    }
}

