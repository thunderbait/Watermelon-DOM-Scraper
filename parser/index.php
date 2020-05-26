<?php
    include_once 'PagesParser.php';

    ini_set('max_execution_time', 60 * 10);

    $pagesParser = new PagesParser();
    $pagesParser->parsePages();

