<?php

/**
 * Read the pages from the web, using the CSV that it's provided with
 *
 * Class CsvToUrlPageContentProvider
 */
class CsvToUrlPageContentProvider implements PageContentProvider
{
    protected $csv;

    public function __construct($source)
    {
        $this->csv = $source;
    }

    public function getNextPageContent()
    {
        // TODO: Implement getNextPageContent() method.
    }


}