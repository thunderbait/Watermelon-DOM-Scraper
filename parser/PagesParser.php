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
        $this->pagesContentProvider = new DirectoryPageContentProvider('pages/Wales');
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
        echo $pageInfo->title . "<br>";
        //var_dump($pageInfo->types);
        //echo $pageInfo->location . "\n";
        //echo $pageInfo->phone . "\n";
        //echo $pageInfo->group . "\n";
        //echo $pageInfo->localAuthority . "\n";
        //echo $pageInfo->contactName . "\n";
        //echo $pageInfo->beds . "\n";

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "carehomes";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        echo "Connected successfully" . "<br>";


        $location =  $conn->prepare( "INSERT INTO locations (name, location_authority) VALUES (?, ?)");
        $location->bind_param('ss',$pageInfo->location, $pageInfo->localAuthority);
        $location->execute();

        $group = $conn->prepare( "INSERT INTO groups (name) VALUES (?)");
        $group->bind_param('s',$pageInfo->group);
        $group->execute();

        foreach ((array) $pageInfo->types as $type) {
            $type = $conn->prepare("INSERT INTO types (name) VALUES ($type)");
            $type->bind_param('s', $type);
            $type->execute();
        }

        $carehome =  $conn->prepare( "INSERT INTO care_homes (name, number_beds, location_id, group_id, type_id, notes)
            VALUES (?, ?, ( SELECT MAX id FROM locations ), ( SELECT MAX id FROM groups ), ( SELECT MAX id FROM types ), NULL)");
        $carehome->bind_param('siiiis',$pageInfo->title, $pageInfo->beds,
            $location_id, $group_id, $type_id, $pageInfo->notes);
        $carehome->execute();

        $contact = $conn->prepare( "INSERT INTO contacts (name, role, email, phone, linkedin, carehome_id)
            VALUES (?, NULL, NULL, ?, NULL, ( SELECT MAX id FROM care_homes ))");
        $contact->bind_param('sssssi',$pageInfo->contactName, $pageInfo->role, $pageInfo->email,
            $pageInfo->phone, $pageInfo->linkedin, $carehome_id);
        $contact->execute();

        $specialism = $conn->prepare( "INSERT INTO specialisms (name) VALUES (?)");
        $specialism->bind_param('s',$pageInfo->specialismName);
        $specialism->execute();

        $carehomeSpecialism = $conn->prepare( "INSERT INTO carehome_specialism (carehome_id, specialism_id) 
            VALUES ((SELECT MAX id FROM care_homes), (SELECT MAX id FROM specialisms))");
        $specialism->bind_param('ii',$carehome_id, $specialism_id);
        $specialism->execute();

        /*if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }*/
        mysqli_close($conn);


    }
}