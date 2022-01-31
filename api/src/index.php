<?php

########################
# CONFIG
########################
$DB_HOST = $_ENV['DB_HOST'] | 'mysql';
$DB_USER = $_ENV['DB_USER'] | 'root';
$DB_PASS = $_ENV['DB_PASS'] | 'toor';
$DB_NAME = $_ENV['DB_NAME'] | 'api';
########################

########################
# DB SETUP
########################
$mysql = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

# Load composer libraries
require __DIR__ . '/../vendor/autoload.php';

# This is the JWT Key
$key = $_ENV['AUTH_KEY'] | "example_key";


# The router/controller management class
final class Router {

	public function __construct(){

		# try it!
		try {

			# include the super class
			require_once "ControllerSuper.php";

			# try to find the name of the requested controller
			$uri = str_replace('/api/','/',$_SERVER['REQUEST_URI']);
			$controllerName = ucwords(explode("/", $uri)[1]) . 'Controller';

			# check if the requested controller exists
			if (file_exists("controllers/$controllerName.php")){

				# call the controller and init it
				require_once "controllers/$controllerName.php";
				$controller = new $controllerName();


			# check if they are trying to login
			} else if ($uri == '/login'){
				$controller = new Controller(false);
				$controller->login();


			# if the controller doesn't exist...then use the super...it will know what to do...hopefully
			} else {
				$controller = new Controller();
			}

		# Catch it!
		} catch (Exception $e) {
			http_response_code(500);
			print "500 - Oops. Something went wrong.";
		}


	}

}


# Do work son!
$routes = new Router();

