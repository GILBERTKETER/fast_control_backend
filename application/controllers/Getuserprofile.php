<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetUserProfile extends CI_Controller {

	public function index()
	{
        echo "welcome to the users page";
	}
    public function find(){
        function filterError($field){
            return $field['msg'] != "";
        }
        $errorArray = [];
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$id = $_POST['id']; // User Id for Token Verification.....
		$userid = $_POST['user_id'];
		$token = $_POST['token'];
        array_push($errorArray,['field'=>"token","msg" => (empty($token) ? "Token is required" :"") ]);
        array_push($errorArray,['field'=>"userid","msg" => (empty($userid) ? "User_ID is required" :"") ]);
		array_push($errorArray,['field'=>"id","msg" => (empty($id) ? "ID is required" :"") ]);
        $errors = array_filter($errorArray,'filterError');
        if(count($errors) == 0){
            // do the get here.. here 
            $auth = password_verify($id, strval($token));
	    if ($auth){
		    $sql = "SELECT start_time, finish_time FROM user WHERE user_id = '$id' ";
		    $results = $this->db->query($sql);
		    if($results){
				foreach ($results->result() as $row) {
					$start = $row -> start_time;
					$finish = $row -> finish_time;
				}
				$time = time();
				if($time < $finish){
					$info = array();
					$sql = "SELECT * FROM user where user_id = '$userid' ";
					$results = $this->db->query($sql);
					if($results){
						foreach ($results->result() as $row) {
							$id = $row -> user_id;
							$name = $row -> username;
							$email = $row -> email;
							array_push($info, array('user_id'=> $id, 'name'=> $name,'email' => $email, 'status'=> 'active'));
						}
						echo json_encode($info);
					}
				else{
					array_push($errorArray,['field'=>"token","msg" => "Token Expired, please login again."]);
					$errors = array_filter($errorArray,'filterError');
				}
		    } 
	    }
		else{
			array_push($errorArray,['field'=>"token","msg" => "You do not have access to this token" ]);
        	$errors = array_filter($errorArray,'filterError');
			echo json_encode($errors);
		}
        }else{
            echo json_encode($errors);
        }

        }
    
    }

}}
