<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/Firebase/JWT/JWT.php';
use \Firebase\JWT\JWT;

class Api_pcs extends REST_Controller {

    private $scret_key = "igfqwhrouighbaifhegnjjsg";
	public function index()
	{
		echo "hello";
	}

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_admin');
    }
    
    public function index_get()
    {
        $this->load->model('M_admin');
        $data= $this->M_admin->getData();

        $result = array(
            "success" => true,
            "message" => "data di temukan",
            "data" => $data
        );

        echo json_encode($result);
    }

    public function index_post()
    {
        $this->load->model('M_admin');
        $data = array(
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            'nama' => $this->post('nama')
        );
        $insert = $this->M_admin->insertData($data);

        if($insert){
            $this->response($data, 200);
        } else{
            $this->response($data, 502);
        }
    }

    public function admin_put(){
        $this->load->model('M_admn');
        $data = array(
            email =>$this->put("email"),
            password =>md5($this->put("password")),
            nama =>$this->put("nama")

        );

        $id = $this->put("id");
        $result = $this->M_admin->updateAdmin($data,$id);
        $data_json = array(
            "success" => true,
            "message" => "update iso",
            "data" => array(
                "admin" => $result
            )
            );

            $this->response($data_json,REST_Controller::HTTP_OK);
    }

    public function admin_delate(){
        $this->load->model('M_admin');
        $id = $this->delete("id");
        $result = $this->M_admin->deleteAdmin($id);

        if(empty($result)){
            $data_json = array(
                "SUCCESS" => false,
                "message" =>"id tidak valid",
                "nama" =>null
            );

            $this->response($data_json,REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "SUCCESS" => true,
            "message" =>"Delete Berhasil",
            "data" => array(
                "admin" => $result
            )
            );

            $this->response($data_json,REST_Controller::HTTP_OK);
    }

    public function Login_post(){
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password"))
        );
        $resut = $this->M_admin->cekLoginAdmin($data);

        if(empty($result)){
            $data_json = array(
                "success" => false,
                "message" => "email dan password tidak valid",
                "error_code"=>1308,
                "data" => null
            );

            $this->response($data_json,REST_Controller::HTTP_OK);
            $this->output->display();
            exit();
        }else{
            $date = new Datetime();

            $payload["id"] = $result["id"];
            $payload["email"] = $result["email"];
            $payload["iat"] = $date->getTimestamp();
            $payload["exp"] = $date->getTimestamp() + 3600;

            $data_json = array(
                "SUCCESS" => true,
                "message" =>"Otentifikasi Berhasil",
                "data" => array(
                    "admin" => $result,
                    "token" => JWT::encode($payload,$this->secret_key)
                )
                );
    
                $this->response($data_json,REST_Controller::HTTP_OK);
        }
    }

}
