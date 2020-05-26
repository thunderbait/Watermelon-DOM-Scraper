<?php

/**
 * Abstracts the process of getting the content for an individual page
 *
 * Interface PageContentProvider
 */
interface PageContentProvider
{
    public function __construct($source);

    public function getNextPageContent();
}