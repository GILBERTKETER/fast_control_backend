<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

	public function index()
	{

        echo "welcome to the signup page";
    //   echo json_encode($this->request->body);

		//$this->load->view('login');
	}
    public function create(){
        function filterError($field){
            return $field['msg'] != "";
        }
        $errorArray = [];
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $comfirmPassword = $_POST['comfirmPassword'];
        array_push($errorArray,['field'=>"name","msg" => (empty($name) ? "Name is required" :"") ]);
        array_push($errorArray,['field'=>"email","msg" => (empty($email) ? "Email is required" :"") ]);
     //   array_push($errorArray,['field'=>"email","msg" => (!filter_var($email,FILTER_VALIDATE_EMAIL) ? "Invalid Email Address" :"") ]);
        array_push($errorArray,['field'=>"password","msg" => (empty($password) ? "Password is required" :"") ]);
        if(!empty($password)){
            array_push($errorArray,['field'=>"password","msg" => (($password != $comfirmPassword) ? "Password mismatched!" :"") ]);
        }

        $errors = array_filter($errorArray,'filterError');
        if(count($errors) == 0){
            // do the insert here 
            $userId = uniqid().time();
            $password = password_hash($password,PASSWORD_DEFAULT);
            $post_data = array('username'=> $name,'user_id'=>$userId,'email'=>$email,'password'=>$password);
            $this->db->insert('user',$post_data);
            $this->db->insert_id();
            $access_token = password_hash($userId,PASSWORD_BCRYPT);
            echo json_encode (
                ['success'=>true,'message'=>"user successfully created", 'data' =>
                 ['name'=> $name,'email' =>$email,'userId' =>$userId]]
            );
        }else{
            echo json_encode($errors);
        }


     }

    }
}
//$value = $this->input->post($fieldName);