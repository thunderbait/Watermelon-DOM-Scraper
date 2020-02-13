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

			//Consultative Status
			$consultative = $content->find('h2')[6];
			$consultativeTitle = $content->find('h2')[6]->plaintext;
			$consultativeText = $consultative->next_sibling()->plaintext;

			//Relationships with NGO's
			$ngo = $content->find('h2')[7];
			$ngoTitle = $content->find('h2')[7]->plaintext;
			$ngoText = $ngo->next_sibling()->plaintext;

			//Members
			$members = $content->find('h2')[8];
			$membersTitle = $content->find('h2')[8]->plaintext;
			$membersText = $members->next_sibling()->plaintext;

			//Type I Classification
			$type_I = $content->find('h2')[9];
			$type_I_Title = $content->find('h2')[9]->plaintext;
			$type_I_Text = $type_I->next_sibling()->plaintext;

			//Type II Classification
			$type_II = $content->find('h2')[10];
			$type_II_Title = $content->find('h2')[10]->plaintext;
			$type_II_Text = $type_II->next_sibling()->plaintext;

			//Type III Classification
			$type_III = $content->find('h2')[11];
			$type_III_Title = $content->find('h2')[11]->plaintext;
			$type_III_Text = $type_III->next_sibling()->plaintext;

			echo $title . "<br>";
			echo $history ;
			echo $historyText . "<br>";
			echo $aims ;
			echo $aimsText . "<br>";
			echo $events; 
			echo $eventsText . "<br>";
			echo $financing;
			echo $financingText . "<br>";
			echo $consultative;
			echo $consultativeText . "<br>";
			echo $ngo ;
			echo $ngoText . "<br>";
			echo $members ;
			echo $membersText . "<br>";
			echo $type ;
			echo $typeText . "<br>";
			echo $type_I ;
			echo $type_I_Text . "<br>";
			echo $type_II ;
			echo $type_II_Text . "<br>";
			echo $type_III ;
			echo $type_III_Text . "<br>";




			?>




		</div>

	</body>
</html>