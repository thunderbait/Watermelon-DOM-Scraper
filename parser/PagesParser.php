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
        echo $pageInfo->title . "\n";
        //var_dump($pageInfo->types);
        echo $pageInfo->city . "\n";
        echo $pageInfo->phone . "\n";
        echo $pageInfo->group . "\n";
        echo $pageInfo->localAuthority . "\n";
        echo $pageInfo->contactName . "\n";
        echo $pageInfo->beds . "\n";

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
        $location->bind_param('s, s',$pageInfo->city, $pageInfo->localAuthority);
        $location->execute();

        $group = $conn->prepare( "INSERT INTO groups (name) VALUES (?)");
        $group->bind_param('s',$pageInfo->group);
        $group->execute();

        foreach ($pageInfo->types as $type) {
            $type = $conn->prepare( "INSERT INTO types (name) VALUES ($type)");
            $type->bind_param('s',$type);
            $type->execute();
        }

        $carehome =  $conn->prepare( "INSERT INTO care_homes (name, number_beds, location_id, group_id, type_id, notes)
            VALUES (?, ?, ( SELECT location_id FROM locations WHERE locations.id = ? ), 
            ( SELECT group_id FROM groups WHERE groups.id = ? ), ( SELECT type_id FROM types WHERE types.id = ? ), NULL)");
        $carehome->bind_param('s, i, i, i, i, s',$pageInfo->title, $pageInfo->beds,
            $locationId, $groupId, $typeId, $pageInfo->notes);
        $carehome->execute();

        $contact = $conn->prepare( "INSERT INTO contacts (name, role, email, phone, linkedin, carehome_id)
            VALUES (?, NULL, NULL, ?, NULL, ( SELECT carehome_id FROM care_homes WHERE care_homes.id = ? ))");
        $contact->bind_param('s, s, s, s, s, i',$pageInfo->contactName, $pageInfo->role, $pageInfo->email,
            $pageInfo->phone, $pageInfo->linkedin, $carehomeId);
        $contact->execute();

        $specialism = $conn->prepare( "INSERT INTO specialisms (name) VALUES (?)");
        $specialism->bind_param('s',$pageInfo->specialismName);
        $specialism->execute();

        /*if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }*/
        mysqli_close($conn);


    }
}