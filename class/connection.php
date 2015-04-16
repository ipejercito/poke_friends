<?php

Class Database{

	protected $connection;
	protected $host = "localhost";
	protected $user = "root";
	protected $password = "";
	protected $database = "poke_friends";

	public function __construct()
	{
		//connection using mysqli
		$this->connection = mysqli_connect($this->host,$this->user,$this->password)
								or die("error encountered on connection". mysqli_error($this->connection));
		mysqli_select_db($this->connection,$this->database)
			or die("Coudn't connect to database");
	}

	public function fetch_all($query)
	{
		$data = array();
		$result = mysqli_query($this->connection, $query);
		while($row = mysqli_fetch_assoc($result))
		{
			$data[] = $row;
		}
		return $data;
	}

	public function fetch_record($query)
	{
		$result = mysqli_query($this->connection, $query);
		return mysqli_fetch_assoc($result);

	}

}

?>