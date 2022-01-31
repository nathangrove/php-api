<?php

/**
 * Name: TodoController
 * Description: Allows CRUD on todo objects
 * 
 * SQL: sql/todo.sql
 * 
 */

 # the ToDo class structure
 class ToDo {
	 private $_id;  	   # (int) The id of the task
	 private $_created;    # (string) timestamp the task was created
	 public $description;  # (string) short description
	 public $due;          # (string) a due date
	 public $done;         # (bool) determine if done
 }

# The route controller
class TodoController extends Controller {

	# require auth
	var $skip_auth = false;
	
	# Get the tasks/task
	public function get(){

		# was there an ID of a specific element to fetch?
		if ($this->id()){
			
			# Create the SQL statement
			$stmt = $this->mysql->prepare('select * from todo where id=? and user=?');
			$stmt->bind_param('ii', intval($this->id()), $this->user->uid);
			$stmt->execute();
			$results = $stmt->get_result();

			# spit out the resutl if we found it
			if ($row = $results->fetch_assoc()){
				$this->json($row);

			# error out
			} else {
				$this->notFound();
			}

		# Get all the tasks
		} else {
			
			$stmt = $this->mysql->prepare('select * from todo where user=?');
			$stmt->bind_param('i', $this->user->uid);
			$stmt->execute();
			$results = $stmt->get_result();
			
			$result = [];
			while ($row = $results->fetch_assoc()){
				$row['id'] = intval($row['id']);
				$result[] = $row;
			}
			$this->json($result);
		}
	}

	# Create a task
	public function post(){

		# validate the data
		if (!$this->body->description || !$this->body->due)
			$this->error(400, "A description (string) and due date (YYYY-MM-DD) are required.");

		# insert todo
		$stmt = $this->mysql->prepare('INSERT INTO todo (`description`, `due`, `user`) VALUES (?,?,?)');
		$stmt->bind_param('ssi', $this->body->description, $this->body->due, $this->user->uid);
		if ($stmt->execute()){
			$this->body->id = $stmt->insert_id;
			$this->json($this->body);
		} else {
			$this->error(500, "Insert failed");
		}
	}

	# Update a task
	public function patch(){
		$id = $this->id();

		if (!$id){
			$this->error(400, "An ID is required");
		}

		if (!$this->body->description && !$this->body->due){
			$this->error(400, "A description (string) or due date (YYYY-MM-DD) are required");
		}

		$stmt = $this->mysql->prepare("UPDATE todo set ".implode(',', array_map( 
			function($a) { 
				return "`$a`=?"; 
			}, array_keys(get_object_vars($this->body))))." where id=?");


		$params = array_values(get_object_vars($this->body));
		$params[] = $id;

		$stmt->bind_param(implode("", array_map( 
			function($a){ 
				return "s"; 
			}, get_object_vars($this->body)
		)).'i', ...$params);

		if ($stmt->execute()){
			$this->json($this->body);
		} else {
			$this->error(400, "Update failed");
		}
	}

	# Delete a task
	public function delete(){

		if ($this->id()){
			$stmt = $this->mysql->prepare('delete from todo where id=?');
			$stmt->bind_param('i', intval($this->id()));
			$stmt->execute();
			if ($stmt->affected_rows > 0){
				http_response_code(204);
				$this->json(array("status" => "success"));
			} else {
				$this->notFound();
			}
		} else {
			$this->error(400, "Invalid request.");
		}
	}


}