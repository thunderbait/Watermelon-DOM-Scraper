<?php


class PageInfo
{
    public $title;
    public $acronym;
    public $contactDetails;
    public $history;
    public $aims;
    public $orgId;
    public $structure;

    // Collections
    public $events = [];
    public $goals = [];

    private static $nameMappings = [
        'UN Sustainable Development Goals **' => 'goals',
        'UIA Org ID' => 'orgId',
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