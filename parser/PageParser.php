<?php

include_once '../simple_html_dom.php';
include_once 'PageInfo.php';

/**
 * Responsible for parsing a single page
 *
 * Class PageParser
 */
class PageParser
{
    protected $html;
    protected $pageInfo;

    protected static $EXTRACTORS;
    protected static $CONTENT_TAGS = [
        'div',
        'ul',
        'p'
    ];

    public static function getExtractors()
    {
        if (!static::$EXTRACTORS)
            static::$EXTRACTORS = [

                'location' => function ($contentElement) {
                    $address = $contentElement->find('.address-line1', 0);
                    $city = $contentElement->find('.address-line2', 0);
                    $postcode = $contentElement->find('li', 3);
                    $location = $address . ", " . $city . ", " . $postcode;
                    return $location
                        ? trim($location->plaintext)
                        : null;
                },

                'phone' => function ($contentElement) {
                    $listElement = $contentElement->find('li', 0);
                    $phone = $listElement->find('span', 0);
                    return $phone
                        ? trim($phone->plaintext)
                        : null;
                },

                'types' => function ($contentElement) {
                    $elements = $contentElement->find('div');
                    $types = [];

                    foreach ($elements as $element) {
                        if (!$element->hasChildNodes()) {
                            $types = trim($element->plaintext);
                        }
                    }
                    return $types;
                },

                'group' => function ($contentElement) {
                    $group = $contentElement->find('span', 0);
                    return $group
                        ? trim($group->plaintext)
                        : null;
                },

                'contactName' => function ($contentElement) {
                    $contactName = $contentElement->nextSibling();
                    return $contactName
                        ? trim($contactName->plaintext)
                        : null;
                },

                'beds' => function ($contentElement) {
                    $beds = $contentElement->nextSibling();
                    return $beds
                        ? trim($beds->plaintext)
                        : null;
                },

                'localAuthority' => function ($contentElement) {
                    $localAuthority = $contentElement->nextSibling();
                    return $localAuthority
                        ? trim($localAuthority->plaintext)
                        : null;
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

            $sections = $this->buildSections($content);
            $this->loadSectionData($sections, $this->pageInfo);

            if ($this->infoIsOk($this->pageInfo))
                $result = $this->pageInfo;
        }
        return $result;
    }


    private function getContentNode($dom)
    {
        return $dom->find('.layout-content', 0);
    }

    private function getTitle($content)
    {
        // Get title element
        $titleDiv = $content->find('.content__title', 0);
        $titleElement = $titleDiv->find('span', 0);
        return $titleElement
            ? trim($titleElement->plaintext)
            : null;
    }

    private function infoIsOk(PageInfo $pageInfo)
    {
        return $pageInfo->title;
    }

    /**
     * @param simple_html_dom_node $content
     * @return array
     */
    private function buildSections($content)
    {
        $sections = [];
        // Find data fields html elements
        $sectionElems = $content->find("[class^='info-']");
        //$sectionHeadings = $content->find('.info-location', 0, '.info-tel', 0, 'info-services');
        //$sectionHeadings = $content->find('.info-')

        /** @var simple_html_dom_node $sectionHeading */
        foreach ($sectionElems as $section) {

            /** @var simple_html_dom_node $sectionContent */
            $heading = $section->find('h1, h2, h3, h4, h5, .type');
            $content = $section->find('.col-lg-7');

            if (count($heading) && count($content)) {


                $headingString = $heading[0]->plaintext;
                $contentData = $this->extractContent($headingString, $content[0]);

                $sections[] = [
                    'heading' => $headingString,
                    'content' => $contentData
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

    /**
     * Determines if the given tag is an acceptable holder for content in the page
     *
     * @param string $tag
     * @return bool
     */
    private function tagCanHoldContent($tag)
    {
        return in_array($tag, static::$CONTENT_TAGS);
    }
}