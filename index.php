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

			$content = $dom->find('#content')[0];

			$title = $content->find('.title')[0]->plaintext;

			// History
			$history = $content->find('h2')[1];
			$historyTitle = $content->find('h2')[1]->plaintext;
			$historyText = $history->next_sibling()->plaintext;

			//aims
			$aims = $content->find('h2')[2];
			$aimsTitle = $content->find('h2')[2]->plaintext;
			$aimsText = $aims->next_sibling()->plaintext;

			//events
			$events = $content->find('h2')[3];
			$eventsTitle = $content->find('h2')[3]->plaintext;
			$eventsText = $events->next_sibling()->plaintext;

			// structure
			$structure = $content->find('h2')[4];
			$structureTitle = $content->find('h2')[4]->plaintext;
			$structureText = $structure->next_sibling()->plaintext;

			//financing
			$financing = $content->find('h2')[5];
			$financingTitle = $content->find('h2')[5]->plaintext;
			$financingText = $financing->next_sibling()->plaintext;




			echo $title . "<br>";
			echo $history . "<br>";
			echo $historyText . "<br>";
			echo $aims . "<br>";
			echo $aimsText . "<br>";
			echo $events . "<br>";
			echo $eventsText . "<br>";
			echo $financing . "<br>";
			echo $financingText . "<br>";


			?>




		</div>

	</body>
</html>