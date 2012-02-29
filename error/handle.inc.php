<?php
		$live=FALSE;
		function my_error_handler($e_number,$e_message,$e_file,$e_line,$e_vars) {
				global $live;
				$message="An error occurred in script '$e_file' on line $e_line:$e_message\n";
				$message .=print_r($e_vars,1);
				if($live) {
						echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div><br>';
				} else {
						echo '<div class="error">' . $message . '</div><br>';
				}
		}
		set_error_handler('my_error_handler');
?>

