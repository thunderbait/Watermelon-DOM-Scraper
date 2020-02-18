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
    }
}