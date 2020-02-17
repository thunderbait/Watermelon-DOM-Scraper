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

    function curl_download($Url)
    {
        if (!function_exists('curl_init')) {
            die('cURL is not installed. Install and try again.');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    $profiles = [];

    $file = fopen('uia_testing_real.csv', 'r');
    while (($data = fgetcsv($file)) !== FALSE) {
        array_push($profiles, $data);
    }
    fclose($file);

    for ($i = 57; $i < sizeof($profiles); $i++) {
        $html = curl_download($profiles[$i][0]);
        $dom = str_get_html($html);
        echo $i;
        $content = $dom->find('#content')[0];

        $title = $content->find('.title')[0]->plaintext;
        echo "<hr><h1>" . $title . "</h1>";

        $contactDetailsDiv = $content->find('div[style="float:right;width:400px;clear:right;margin-bottom: 40px;margin-left:40px;margin-top:10px;"]')[0];
        //$contactDetailsTitle = $contactDetailsDiv->find('h2')[0]->plaintext;
        //$contactDetailsText = $contactDetailsDiv->find('p')[0]->plaintext;

        try {
            if ($contactDetailsDiv->find('a[title*="Click to access page"]')[0] !== null) {
                $websiteURL = $contactDetailsDiv->find('a[title*="Click to access page"]')[0]->plaintext;
                echo $websiteURL . "<br>";
            } else {
                throw new Exception('EXCEPTION');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }


        //echo "<h2>" . $contactDetailsTitle . "</h2>";
        //echo $contactDetailsText . "<br>";

        $count = count($content->find('h2'));

        /*try {

            //for ($i = 0; $i < count($content->find('h2')); $i++) {
            while ($i < $count) {
                if ($content->find('h2')[$i] !== null) {
                    $item = $content->find('h2')[$i];
                    $itemTitle = $content->find('h2')[$i]->plaintext;
                    echo $item;

                    //if ($item->next_sibling() !== null) {
                    $itemText = $item->next_sibling()->plaintext;
                    echo $itemText . "<br>";
                    //  } else {
                    //echo 'null';
                    // }
                } else {
                    throw new Exception('EXCEPTION');
                }
                $i++;
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }*/


        if ($content->find('h2')[0] !== null) {
            $contactDetails = $content->find('h2')[0];
            $contactDetailsTitle = $content->find('h2')[0]->plaintext;
            echo "<h2>" . $contactDetailsTitle . "</h2>";

            if ($contactDetails->next_sibling() !== null) {
                $contactDetailsText = $contactDetails->next_sibling()->plaintext;
                echo $contactDetailsText . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[1] !== null) {
            $history = $content->find('h2')[1];
            $historyTitle = $content->find('h2')[1]->plaintext;
            echo $history;

            if ($history->next_sibling() !== null) {
                $historyText = $history->next_sibling()->plaintext;
                echo $historyText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[2] !== null) {
            $aims = $content->find('h2')[2];
            $aimsTitle = $content->find('h2')[2]->plaintext;
            echo $aims;

            if ($aims->next_sibling() !== null) {
                $aimsText = $aims->next_sibling()->plaintext;
                echo $aimsText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[3] !== null) {
            $events = $content->find('h2')[3];
            $eventsTitle = $content->find('h2')[3]->plaintext;
            echo $events;
            if ($events->next_sibling() !== null) {
                $eventsText = $events->next_sibling()->plaintext;
                echo $eventsText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[4] !== null) {
            $structure = $content->find('h2')[4];
            $structureTitle = $content->find('h2')[4]->plaintext;
            echo $structure;

            if ($structure->next_sibling() !== null) {
                $structureText = $structure->next_sibling()->plaintext;
                echo $structureText;
            } else {
                echo 'null';
            }

        }

        if ($content->find('h2')[5] !== null) {
            $financing = $content->find('h2')[5];
            $financingTitle = $content->find('h2')[5]->plaintext;
            echo $financing;

            if ($financing->next_sibling() !== null) {
                $financingText = $financing->next_sibling()->plaintext;
                echo $financingText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[6] !== null) {
            $consultative = $content->find('h2')[6];
            $consultativeTitle = $content->find('h2')[6]->plaintext;
            echo $consultative;

            if ($consultative->next_sibling() !== null) {
                $consultativeText = $consultative->next_sibling()->plaintext;
                echo $consultativeText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[7] !== null) {
            $ngo = $content->find('h2')[7];
            $ngoTitle = $content->find('h2')[7]->plaintext;
            echo $ngo;

            if ($ngo->next_sibling() !== null) {
                $ngoText = $ngo->next_sibling()->plaintext;
                echo $ngoText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[8] !== null) {
            $members = $content->find('h2')[8];
            $membersTitle = $content->find('h2')[8]->plaintext;
            echo $members;

            if ($members->next_sibling() !== null) {
                $membersText = $members->next_sibling()->plaintext;
                echo $membersText;
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[9] !== null) {
            $type_I = $content->find('h2')[9];
            $type_I_Title = $content->find('h2')[9]->plaintext;
            echo $type_I;

            if ($type_I->next_sibling() !== null) {
                $type_I_Text = $type_I->next_sibling()->plaintext;
                echo $type_I_Text . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[10] !== null) {
            $type_II = $content->find('h2')[10];
            $type_II_Title = $content->find('h2')[10]->plaintext;
            echo $type_II;

            if ($type_II->next_sibling() !== null) {
                $type_II_Text = $type_II->next_sibling()->plaintext;
                echo $type_II_Text . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[11] !== null) {
            $type_III = $content->find('h2')[11];
            $type_III_Title = $content->find('h2')[11]->plaintext;
            echo $type_III;

            if ($type_III->next_sibling() !== null) {
                $type_III_Text = $type_III->next_sibling()->plaintext;
                echo $type_III_Text . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[12] !== null) {
            $item12 = $content->find('h2')[12];
            $itemTitle12 = $content->find('h2')[12]->plaintext;
            echo $item12;

            if ($item12->next_sibling() !== null) {
                $itemText12 = $item12->next_sibling()->plaintext;
                echo $itemText12 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[13] !== null) {
            $item13 = $content->find('h2')[13];
            $itemTitle13 = $content->find('h2')[13]->plaintext;
            echo $item13;

            if ($item13->next_sibling() !== null) {
                $itemText13 = $item13->next_sibling()->plaintext;
                echo $itemText13 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[14] !== null) {
            $item14 = $content->find('h2')[14];
            $itemTitle14 = $content->find('h2')[14]->plaintext;
            echo $item14;

            if ($item14->next_sibling() !== null) {
                $itemText14 = $item14->next_sibling()->plaintext;
                echo $itemText14 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[15] !== null) {
            $item15 = $content->find('h2')[15];
            $itemTitle15 = $content->find('h2')[15]->plaintext;
            echo $item15;

            if ($item15->next_sibling() !== null) {
                $itemText15 = $item15->next_sibling()->plaintext;
                echo $itemText15 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[16] !== null) {
            $item16 = $content->find('h2')[16];
            $itemTitle16 = $content->find('h2')[16]->plaintext;
            echo $item16;

            if ($item16->next_sibling() !== null) {
                $itemText16 = $item16->next_sibling()->plaintext;
                echo $itemText16 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[17] !== null) {
            $item17 = $content->find('h2')[17];
            $itemTitle17 = $content->find('h2')[17]->plaintext;
            echo $item17;

            if ($item17->next_sibling() !== null) {
                $itemText17 = $item17->next_sibling()->plaintext;
                echo $itemText17 . "<br>";
            } else {
                echo 'null';
            }
        }

        if ($content->find('h2')[18] !== null) {
            $item18 = $content->find('h2')[18];
            $itemTitle18 = $content->find('h2')[18]->plaintext;
            echo $item18;

            if ($item18->next_sibling() !== null) {
                $itemText18 = $item18->next_sibling()->plaintext;
                echo $itemText18 . "<br>";
            } else {
                echo 'null';
            }
        }

        

		$contactDetailsText = $contactDetailsTitle. '-' . $contactDetailsText;
		$aimsText= $aimsTitle. '-' .  $aimsText;
		$historyText = $historyTitle. '-' . $historyText;
		$eventsText = $eventsTitle. '-' . $eventsText;
		$financingText = $financingTitle. '-' . $financingText;
		$ngoText = $ngoTitle. '-' . $ngoText;
		$membersText = $membersTitle. '-' . $membersText;
		$contactDetailsText = $consultativeTitle. '-' . $contactDetailsText;
		$type_I_Text = $type_I_Title. '-' . $type_I_Text;
		$type_II_Text = $type_II_Title. '-' . $type_II_Text;
		$type_III_Text = $type_III_Title. '-' . $type_III_Text;
		$itemText12 = $itemTitle12. '-' . $itemText12;
		$itemText13 = $itemTitle13. '-' . $itemText13;
		$itemText14 = $itemTitle14. '-' . $itemText14;
		$itemText15 = $itemTitle15. '-' . $itemText15;
		$itemText16 = $itemTitle16. '-' . $itemText16;
		$itemText17 = $itemTitle17. '-' . $itemText17;
		$itemText18 = $itemTitle18. '-' . $itemText18;

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "uia_research";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        echo "Connected successfully" . "<br>";


        $sql = "INSERT INTO assocs (name, contact_det, websiteURL, aims, history, events, financing, consultative_status, ngo_relations, members, type1, 
            type2, activities, structure, languages, staff, igo_relations, subjects, last_news_received)
            VALUES (  '$title', '$contactDetailsText', '$websiteURL',' $aimsText', '$historyText', '$eventsText',' $financingText', '$consultativeText',
            '$ngoText', '$membersText', '$type_I_Text', '$type_II_Text', '$itemText12', '$itemText13', '$itemText14', '$itemText15',
             '$itemText16', '$itemText17', '$itemText18')";
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);


    }

    ?>


</div>

</body>
</html>