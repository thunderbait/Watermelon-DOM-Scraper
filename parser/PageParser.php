<?php

include_once '../simple_html_dom.php';
include_once 'PageInfo.php';

/**
 * Responsible for parsing a single page
 *
 * TODO Some content paragraphs have a 'view more'.
 *
 * Class PageParser
 */
class PageParser
{
    protected $html;
    protected $pageInfo;

    protected static $EXTRACTORS;

    public static function getExtractors()
    {
        if (!static::$EXTRACTORS)
            static::$EXTRACTORS = [
                'goals' => function ($contentElement) {
                    $links = $contentElement->find('a');
                    $goals = [];
                    foreach ($links as $link) {
                        $href = $link->href;
                        $token = 'UN';
                        if ($pos = strrpos($href, $token)) {
                            $numString = substr($href, $pos + strlen($token));
                            $num = intval($numString);
                            if ($num)
                                $goals[] = $num;
                        }
                    }
                    return $goals;
                },

                'subjects' => function ($contentElement) {
                    $unorderedLists = $contentElement->find('ul');
                    $listItems = $unorderedLists->find('li');
                    $subjects = [];
                    foreach ($listItems as $listItem) {
                        $subject = $listItem->plaintext;
                        if ($subject)
                            $subjects[] = $subject;
                    }
                    return $subjects;
                },

                'activities' => function ($contentElement) {
                    $activitiesContent = $contentElement->innertext;
                    $activities = [];
                    $delimiters = [";", ",", "."];

                    if ($activitiesContent)
                    {
                        for ($i = 0; $i <= 2; $i++)
                        {
                            $activityItems = explode($delimiters[$i], $activitiesContent);
                            if (count($activityItems) >= 2)
                            {
                                for ($i = 0; $i < count($activityItems); $i++)
                                $activities[] = $activityItems[$i];
                            }
                        }
                    }
                    return $activities;
                },

                'contactDetails' => function ($contentElement)
                {
                    $contactDetails = [];
                    $contactContent = $contentElement->innertext;
                    if ($contactContent)
                    {
                        $contactItems = explode('<br />', $contactContent);
                        for ($i = 0; $i < count($contactItems) - 1; $i++)
                        {
                            $contactDetails[] = $contactItems[$i];
                        }
                    }
                    return $contactDetails;
                },

                'events' => function ($contentElement)
                {
                    $events = [];
                    $eventsContent = $contentElement->innertext;
                    if ($eventsContent)
                    {
                        $eventItems = explode('<br />', $eventsContent);
                        for ($i = 0; $i < count($eventItems); $i++)
                        {
                            $events[] = $eventItems[$i];
                        }
                    }
                    return $events;
                },

                'members' => function ($contentElement)
                {
                    $members = [];
                    $membersItems = $contentElement->find('a')->plaintext;
                    foreach ($membersItems as $membersItem)
                    {
                        $members[] = $membersItem;
                    }
                    return $members;
                },

                // TODO: Finish Implementation
                'websiteURL' => function ($contentElement)
                {
                    $websiteURL = "";
                    return websiteURL;
                },

                // the generic simple extractor
                '_default' => function ($contentElement) {
                    return trim($contentElement->plaintext);
                }
            ];

        return static::$EXTRACTORS;
    }

    public static function getExtractorForProperty($propertyName)
    {
        $extractors = static::getExtractors();
        $key = array_key_exists($propertyName, $extractors)
            ? $propertyName
            : '_default';

        return $extractors[$key];
    }


    public function __construct($html)
    {
        $this->html = $html;
        $this->pageInfo = new PageInfo();
    }

    /**
     * @return PageInfo|null
     */
    public function parse()
    {
        $result = null;

        /** @var simple_html_dom $dom */
        $dom = str_get_html($this->html);
        /** @var simple_html_dom_node $content */
        if ($content = $this->getContentNode($dom)) {
            $this->pageInfo->title = $this->getTitle($content);
            $this->splitTitleForAcronym($this->pageInfo);

            $sections = $this->buildSections($content);
            $this->loadSectionData($sections, $this->pageInfo);

            if ($this->infoIsOk($this->pageInfo))
                $result = $this->pageInfo;
        }
        return $result;
    }


    private function getContentNode($dom)
    {
        return $dom->find('#content', 0);
    }

    private function getTitle($content)
    {
        $titleElement = $content->find('.title', 0);
        return $titleElement
            ? trim($titleElement->plaintext)
            : null;
    }

    private function infoIsOk(PageInfo $pageInfo)
    {
        return $pageInfo->title;
    }

    private function splitTitleForAcronym(PageInfo $pageInfo)
    {
        if ($pageInfo->title) {
            $startPos = strrpos($pageInfo->title, '(');
            if ($startPos) {
                $startPos = $startPos + 1;
                $endPos = strpos($pageInfo->title, ')', $startPos);
                if ($endPos) {
                    $pageInfo->acronym = substr($pageInfo->title, $startPos, $endPos - $startPos);
                    $pageInfo->title = trim(substr($pageInfo->title, 0, $startPos - 1));
                }
            }
        }
    }

    /**
     * @param simple_html_dom_node $content
     * @return array
     */
    private function buildSections($content)
    {
        $sections = [];
        $sectionHeadings = $content->find('h2');
        /** @var simple_html_dom_node $sectionHeading */
        foreach ($sectionHeadings as $sectionHeading) {
            /** @var simple_html_dom_node $sectionContent */
            $sectionContent = $sectionHeading->nextSibling();
            if ($sectionContent && $sectionContent->tag == 'p') {
                $heading = trim($sectionHeading->plaintext);
                $content = $this->extractContent($heading, $sectionContent);

                $sections[] = [
                    'heading' => $heading,
                    'content' => $content
                ];
            }
        }

        return $sections;
    }


    private function loadSectionData(array $sections, PageInfo $pageInfo)
    {
        foreach ($sections as $section) {
            $pageInfo->setPropertyFromSection($section);
        }
    }

    private function extractContent($sectionHeading, simple_html_dom_node $sectionContent)
    {
        $propertyName = PageInfo::getPropertyFromHeading($sectionHeading);
        $extractor = static::getExtractorForProperty($propertyName);
        return $extractor($sectionContent);
    }
}