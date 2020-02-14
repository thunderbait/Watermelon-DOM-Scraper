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

    for ($i = 0; $i < sizeof($profiles); $i++) {
        $html = curl_download($profiles[$i][0]);
        $dom = str_get_html($html);

        $content = $dom->find('#content')[0];

        $title = $content->find('.title')[0]->plaintext;
        echo "<hr><h1>" . $title . "</h1>";

        $contactDetailsDiv = $content->find('div[style="float:right;width:400px;clear:right;margin-bottom: 40px;margin-left:40px;margin-top:10px;"]')[0];
        //$contactDetailsTitle = $contactDetailsDiv->find('h2')[0]->plaintext;
        //$contactDetailsText = $contactDetailsDiv->find('p')[0]->plaintext;

        if ($contactDetailsDiv->find('a[title*="Click to access page"]')[0] !== null) {
            $websiteURL = $contactDetailsDiv->find('a[title*="Click to access page"]')[0]->plaintext;
            echo $websiteURL . "<br>";
        }

        //echo "<h2>" . $contactDetailsTitle . "</h2>";
        //echo $contactDetailsText . "<br>";

        // echo count($content->find('h2'));

        /*for ($i = 0; $i < count($content->find('h2')); $i++) {
            if ($content->find('h2')[$i] !== null) {
                $item = $content->find('h2')[$i];
                $itemTitle = $content->find('h2')[$i]->plaintext;
                echo $item;

                if ($item->next_sibling() !== null) {
                    $itemText = $item->next_sibling()->plaintext;
                    echo $itemText . "<br>";
                } else {
                    echo 'null';
                }
            }

        }*/

        // Contact Details
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

        // History
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

        //aims
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

        //events
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

        // structure
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

        //financing
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

        //Consultative Status
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

        //Relationships with NGOs
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

        //Members
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

        //Type I Classification
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

        //Type II Classification
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

        //Type III Classification
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
    }

    ?>


</div>

</body>
</html>