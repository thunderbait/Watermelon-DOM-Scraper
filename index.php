<?php 
		include('simple_html_dom.php');
?>
<!DOCTYPE html>
<html>
	<head>
	</head>


	<body>

		<h1>Research UIA scraper</h1>

		<div class="page">
		<?php

			// $url = "https://ybio.brillonline.com/s/or/en/1122269028";
			// // $url = "https://ybio.brillonline.com/ybio/";

			// echo $url;

			// $html = file_get_html($url);
			// echo $html;
			


		?>





			<?php
			function curl_download($Url){
			    if (!function_exists('curl_init')){
			        die('cURL is not installed. Install and try again.');
			    }
			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $Url);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    $output = curl_exec($ch);
			    curl_close($ch);
			    return $output;
			}

			$html = curl_download('https://ybio.brillonline.com/s/or/en/1122269028');


			// echo $html;

			$dom = str_get_html($html);

			$main = $dom->find('#main')[0];

			$events = $main->find('p')[7];

			echo $events;




			?>




		</div>

	</body>
</html>