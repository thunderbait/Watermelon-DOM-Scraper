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
        echo "Care home: " . $pageInfo->title . "<br>";
        foreach ($pageInfo->types as $type)
        {
            echo "Type: " . $type . "<br>";
        }
        //var_dump($pageInfo->types);
        echo "Location: " . $pageInfo->location . "<br>";
        echo "Phone: " . $pageInfo->phone . "<br>";
        echo "Provider: " . $pageInfo->group . "<br>";
        echo "Local Authority: " . $pageInfo->localAuthority . "<br>";
        echo "Contact Name: " . $pageInfo->contactName . "<br>";
        echo "Number of beds: " . $pageInfo->beds . "<br>";
        echo "<hr>";

        $this->insertIntoLocationsTable($this->initConnection(), $pageInfo);
        $this->insertIntoCarehomesTable($this->initConnection(), $pageInfo);
        $this->insertIntoContactsTable($this->initConnection(), $pageInfo);
        $this->insertTypesRelationIntoPivotTable($this->initConnection(), $pageInfo);
        /*
        // Add Care home specialism to Specialisms table
        $specialism = $conn->prepare( "INSERT INTO specialisms (name) VALUES (?)");
        $specialism->bind_param('s',$pageInfo->specialismName);
        $specialism->execute();

        // Add foreign keys to Carehomes Specialisms pivot table
        $carehomeForeignKey = 'SELECT id FROM care_homes WHERE care_homes.name = $pageInfo->title';
        $specialismForeignKey = 'SELECT id FROM specialisms WHERE specialisms.name = $pageInfo->specialism'
        $carehomeSpecialism = $conn->prepare( "INSERT INTO carehome_specialism (carehome_id, specialism_id) 
            VALUES (?, ?)");
        $carehomeSpecialism->bind_param('ii',$carehomeForeignKey, $specialismForeignKey);
        $carehomeSpecialism->execute();
         */
    }

    private function handleAllInfo(array $pageInfos)
    {
        $this->ensureAllTypesExist($pageInfos);
        $this->ensureAllGroupsExist($pageInfos);

        foreach($pageInfos as $pageInfo)
        {
            $this->handlePageInfo($pageInfo);

            // loop over pageInfo->types as  $type
            // locate the type ID that relates to the current type
        }
        $this->closeConnection($this->initConnection());
    }

    private function ensureAllTypesExist(array $pageInfos)
    {
        $uniqueTypes = $this->getUniqueTypes($pageInfos);
        $this->insertUniqueItemsIntoTable($uniqueTypes, $this->initConnection(), 'types', 'name');
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

    private function ensureAllGroupsExist(array $pageInfos)
    {
        $uniqueGroups = $this->getUniqueGroups($pageInfos);
        $this->insertUniqueItemsIntoTable($uniqueGroups, $this->initConnection(), 'groups', 'name');
    }

    private function getUniqueGroups(array $pageInfos)
    {
        $groups = [];
        foreach($pageInfos as $pageInfo)
        {
            if ($pageInfo->group)
            {
                $groups[$pageInfo->group] = true;
            }
        }
        return array_keys($groups);
    }

    private function insertUniqueItemsIntoTable(array $uniqueItems, $connection, $table, $field)
    {
        $sql = "INSERT INTO $table ($field) VALUES (?)";
        foreach ($uniqueItems as $item)
        {
            $prepareStatement = $connection->prepare($sql);
            $prepareStatement->bind_param('s', $item);
            $prepareStatement->execute();
        }
    }

    private function initConnection()
    {
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

        return $conn;
    }

    private function closeConnection($conn)
    {
        mysqli_close($conn);
    }

    // Add Care home location data to Locations table
    private function insertIntoLocationsTable($connection, $pageInfo)
    {
        $sql = "INSERT INTO locations (name, location_authority) VALUES (?, ?)";
        $prepareStatement = $connection->prepare($sql);
        $prepareStatement->bind_param('ss', $pageInfo->location, $pageInfo->localAuthority);
        $prepareStatement->execute();
    }

    // Add Care home to Carehomes table with foreign keys
    private function insertIntoCarehomesTable($connection, $pageInfo)
    {
        $sql = "INSERT INTO care_homes (name, number_beds, location_id, group_id) VALUES (?, ?, ?, ?)";

        $locationForeignKey = 'SELECT id FROM locations WHERE name = $pageInfo->location';
        $groupForeignKey = 'SELECT id FROM groups WHERE name = $pageInfo->group';
        $prepareStatement = $connection->prepare($sql);
        $prepareStatement->bind_param('siii', $pageInfo->title, $pageInfo->beds, $locationForeignKey, $groupForeignKey);
        $prepareStatement->execute();
    }

    // Add Care home contact to the Contacts table with care home foreign key
    private function insertIntoContactsTable($connection, $pageInfo)
    {
        $sql = "INSERT INTO contacts (name, phone, carehome_id) VALUES (?, ?, ?)";

        $carehomeForeignKey = 'SELECT id FROM care_homes WHERE name = $pageInfo->title';

        $prepareStatement = $connection->prepare($sql);
        $prepareStatement->bind_param('ssi', $pageInfo->contactName, $pageInfo->phone, $carehomeForeignKey);
        $prepareStatement->execute();
    }

    private function insertTypesRelationIntoPivotTable($connection, $pageInfo)
    {
        $sql = "INSERT INTO carehome_type (carehome_id, type_id) VALUES (?, ?)";

        $carehomeForeignKey = 'SELECT id FROM care_homes WHERE name = $pageInfo->title';
        foreach ($pageInfo->types as $type) {
            $typeForeignKey = 'SELECT id FROM types WHERE name = $type';
            $prepareStatement = $connection->prepare($sql);
            $prepareStatement->bind_param('ii', $carehomeForeignKey, $typeForeignKey);
            $prepareStatement->execute();
        }
    }
}