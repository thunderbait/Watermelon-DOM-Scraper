<?php


class PageInfo
{
    public $title;
    public $acronym;
    public $history;
    public $aims;
    public $orgId;
    public $structure;
    public $languages;
    public $staff;
    public $financing;
    public $consultativeStatus;
    public $ngoRelations;
    public $igoRelations;
    public $type1;
    public $type2;
    public $lastNewsReceived;

    // Collections
    public $goals = [];
    public $subjects = [];
    public $activities = [];
    public $contactDetails = [];
    public $events = [];
    public $members = [];

    private static $nameMappings = [
        'UN Sustainable Development Goals **' => 'goals',
        'UIA Org ID' => 'orgId',
        'Relations with Non-Governmental Organizations' => 'ngoRelations',
        'Relations with Inter-Governmental Organizations' => 'igoRelations',
        'Type I Classification' => 'type1',
        'Type II Classification' => 'type2',
        'Subjects *' => 'subjects',
        'Last News Received' => 'lastNewsReceived',
        'Contact Details' => 'contactDetails'
    ];

    public static function getPropertyFromHeading($headingText)
    {
        $propName = null;

        if (array_key_exists($headingText, static::$nameMappings))
        {
            $propName = static::$nameMappings[$headingText];
        }
        else
        {
            $propName = lcfirst($headingText);
        }

        return $propName;
    }

    public function setPropertyFromSection($section)
    {
        $propertyName = static::getPropertyFromHeading($section['heading']);
        if (property_exists($this, $propertyName))
            $this->{ $propertyName } = $section['content'];
    }

}