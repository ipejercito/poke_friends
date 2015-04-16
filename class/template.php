<?php

Class Template{

	public function error_success_after()
	{
		return "</strong></div>";
	}

	public function success_error_htm($status)
	{
		if($status == TRUE)
			$htm_status = "<div class='alert alert-success col-md-6 col-md-6 col-md-offset-3 text-center'><strong>";
		else
			$htm_status = "<div class='alert alert-danger col-md-6 col-md-6 col-md-offset-3 text-center'><strong>";

		return $htm_status;
	}

}

?>

