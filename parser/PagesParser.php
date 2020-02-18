<?php

include_once 'DirectoryPageContentProvider.php';
include_once 'PageParser.php';

/**
 * Responsible for trawling through each of the pages in the set, and parsing each one
 *
 * Class PagesParser
 */
class PagesParser
{

    protected $pagesContentProvider;

    public function __construct()
    {
        $this->pagesContentProvider = new DirectoryPageContentProvider('pages');
    }

    public function parsePages()
    {
        while ($html = $this->pagesContentProvider->getNextPageContent())
        {
            $pageParser = new PageParser($html);
            $pageInfo = $pageParser->parse();

            if ($pageInfo)
                $this->handlePageInfo($pageInfo);
            else
                $this->handleError($html);
        }
    }


    private function handleError($html)
    {
        echo 'Error!!';
    }

    /**
     * Ingests the page info containing the info about a particular org. This method
     * is responsible for inserting into the DB.
     *
     * @param $pageInfo
     */
    private function handlePageInfo($pageInfo)
    {
        echo $pageInfo->title . " -- " . $pageInfo->acronym . "\n";
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


        $sql =  $conn->prepare( "INSERT INTO assocs (name, contact_det, websiteURL, aims, history, events, 
            financing, consultative_status, ngo_relations, members, type1, type2, activities, structure, 
            languages, staff, igo_relations, subjects, last_news_received, other)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $sql->bind_param('ssssssssssssssssssss',$title, $contactDetails, $websiteURL, $aims,
            $history, $events, $financing, $consultativeStatus, $ngoRelations, $members, $type1, $type2, $activities,
            $structure, $languages, $staff, $igoRelations, $subjects, $lastNewsReceived, $goals );

        $sql->execute();

        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);


    }
}