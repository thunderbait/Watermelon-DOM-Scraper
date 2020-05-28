<?php


class PageInfo
{
    private static $nameMappings = [
        'Address:' => 'location',
        'Telephone:' => 'phone',
        'Service types:' => 'types',
        'Provider:' => 'group',
        'Responsible individual:' => 'contactName',
        'Maximum number of places:' => 'beds',
        'Local authority:' => 'localAuthority'
    ];

    public $location;
    public $phone;
    public $types;
    public $group;
    public $contactName;
    public $beds;
    public $localAuthority;

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