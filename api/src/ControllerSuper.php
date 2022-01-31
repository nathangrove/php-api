<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller {
    
    var $body;
    var $user;
    
    # start the controller method
    public function __construct($process = true){
       
        $this->mysql = $GLOBALS['mysql'];

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        # Init the JSON body
        $this->body = new stdClass();
        if ($method == 'post' || $method == 'put' || $method == 'patch'){
            $body = file_get_contents('php://input');
            $this->body = json_decode($body);

            # clean these up. They are set by the system
            unset($this->body->id);
            unset($this->body->created);
        }

        if ($process) $this->_process();
    }

    # Process the request
    private function _process(){
       
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        # init auth
        if (!$this->skip_auth) $this->_auth();

        # check if the route is callable
        if (is_callable(array($this, $method))){
            $this->$method();
        } else {
            $this->notFound();
        }

    }
    
    # Quick access 404
    public function notFound(){
        http_response_code(404);
        echo "404 - Not Found";
        exit;
    }
    
    # send JSON to the client
    public function json($var){
        header("content-type: application/json");
        echo json_encode($var);
        exit;
    }
    
    # quick access error response
    public function error($code, $message){
        http_response_code($code);
        print $message;
        exit;
    }

    # Get the ID for get/delete requests
    public function id(){
        return intval(explode('?',array_pop(explode('/',$_SERVER['REQUEST_URI'])))[0]);
    }

    # Verify auth
    private function _auth(){

        try {

            $headers = apache_request_headers();
            $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
            $jwt = explode(" ", $jwt)[1];

            $decoded = JWT::decode($jwt, new Key($GLOBALS['key'], 'HS256'));

            $this->user = $decoded;
            
        } catch (Exception $e){

            $this->error(403, "403 - Not Authorized");

        }

        #$jwt = JWT::encode($payload, $key, 'HS256');
        #$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        
    }


    # Perform login
    public function login(){
        
        if ($this->body->username && $this->body->password){


            $user = $this->body->username;
            $pass = $this->body->password;

            $stmt = $this->mysql->prepare("SELECT * FROM user WHERE username=?");
			$stmt->bind_param('s', $user);
			$stmt->execute();
			$results = $stmt->get_result();

            # spit out the resutl if we found it
            if ($row = $results->fetch_assoc()){

                if (password_verify($this->body->password, $row['password'])){

                    $payload = array(
                        "iss" => "http://php-api.example.org",
                        "aud" => "http://php-api.example.com",
                        "iat" => time(),
                        "nbf" => time() - 1000,
                        "exp" => time() + (8 * 60 *60),
                        "uid" => $row['id']
                    );

                    $jwt = JWT::encode($payload, $GLOBALS['key'], 'HS256');

                    $this->json(array("jwt" => $jwt));

                } else {
                    $this->error(403, "403 - Invalid username and password");
                }

            } else {
                $this->error(403, "403 - Invalid username and password");
            }

        } else {
            $this->error(403, "403 - Username and password required");
        }


    }
}