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
        $pageInfos = [];
        $errors = [];
        while ($html = $this->pagesContentProvider->getNextPageContent())
        {
            $pageParser = new PageParser($html);
            $pageInfo = $pageParser->parse();
            if ($pageInfo)
                $pageInfos[] = $pageInfo;
            else
                $errors[] = 'Failed to parse: ' . $html;
        }

        if (!count($errors))
        {
            // all parsed ok!
            $this->handleAllInfo($pageInfos);
        }
        else
        {
            print_r($errors);
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
        var_dump($pageInfo->types);
        echo $pageInfo->location . "<br>";
        echo $pageInfo->phone . "<br>";
        echo $pageInfo->group . "<br>";
        echo $pageInfo->localAuthority . "<br>";
        echo $pageInfo->contactName . "<br>";
        echo $pageInfo->beds . "<br>";

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

        // Add Care home location to Locations table
        $location =  $conn->prepare( "INSERT INTO locations (name, location_authority) VALUES (?, ?)");
        $location->bind_param('ss',$pageInfo->location, $pageInfo->localAuthority);
        $location->execute();

        // Add Care home provider to Groups table
        //$group = $conn->prepare( "INSERT INTO groups (name) VALUES (?)");
        //$group->bind_param('s',$pageInfo->group);
       // $group->execute();

        // Add Care home service types to Types table
        foreach ((array) $pageInfo->types as $type) {
            $type = $conn->prepare("INSERT INTO types (name) VALUES (?)");
            $type->bind_param('s', $type);
            $type->execute();
        }

        // Add Care home to Carehomes table with foreign keys
        $locationForeignKey = 'SELECT MAX id FROM locations';
        $groupForeignKey = 'SELECT MAX id FROM groups';
        $typeForeignKey = 'SELECT MAX id FROM types';
        $carehome =  $conn->prepare( "INSERT INTO care_homes (name, number_beds, location_id, group_id, type_id, notes)
            VALUES (?, ?, ($locationForeignKey ), ( $groupForeignKey ), ( $typeForeignKey ), NULL)");
        $carehome->bind_param('siiiis',$pageInfo->title, $pageInfo->beds, $location_id, $group_id,
            $type_id, $pageInfo->notes);
        $carehome->execute();

        // Add Care home contact to the Contacts table with care home foreign key
        $contact = $conn->prepare( "INSERT INTO contacts (name, role, email, phone, linkedin, carehome_id)
            VALUES (?, NULL, NULL, ?, NULL, ( SELECT MAX id FROM care_homes ))");
        $contact->bind_param('sssssi',$pageInfo->contactName, $pageInfo->role, $pageInfo->email,
            $pageInfo->phone, $pageInfo->linkedin, $carehome_id);
        $contact->execute();

        /*
        // Add Care home specialism to Specialisms table
        $specialism = $conn->prepare( "INSERT INTO specialisms (name) VALUES (?)");
        $specialism->bind_param('s',$pageInfo->specialismName);
        $specialism->execute();

        // Add foreign keys to Carehomes Specialisms pivot table
        $carehomeSpecialism = $conn->prepare( "INSERT INTO carehome_specialism (carehome_id, specialism_id) 
            VALUES ((SELECT MAX id FROM care_homes), (SELECT MAX id FROM specialisms))");
        $carehomeSpecialism->bind_param('ii',$carehome_id, $specialism_id);
        $carehomeSpecialism->execute();
         */

        mysqli_close($conn);
    }

    private function handleAllInfo(array $pageInfos)
    {
        $this->ensureAllTypesExist($pageInfos);

        foreach($pageInfos as $pageInfo)
        {
            $this->handlePageInfo($pageInfo);

            // loop over pageInfo->types as  $type
            // locate the type ID that relates to the current type
        }
    }

    private function ensureAllTypesExist(array $pageInfos)
    {
        $uniqueTypes = $this->getUniqueTypes($pageInfos);
        $this->insertIntoTable('types', $uniqueTypes);
    }

    private function getUniqueTypes(array $pageInfos)
    {
        $types = [];
        foreach($pageInfos as $pageInfo)
        {
            if ($pageInfo->types)
            {
                foreach($pageInfo->types as $type)
                    $types[$type] = true;
            }
        }
        return array_keys($types);
    }

    private function insertIntoTable($string, array $uniqueTypes)
    {
//        $sql = "INSERT into types values (?)";
//        $prepareStatement = $conn->
    }
}