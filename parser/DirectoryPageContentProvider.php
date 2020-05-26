<?php

include_once 'PageContentProvider.php';

class DirectoryPageContentProvider implements PageContentProvider
{

    protected $directory;
    protected $directoryHandle;

    public function __construct($source)
    {
        $this->directory = $source;
        $this->directoryHandle = opendir($this->directory);
    }

    public function getNextPageContent()
    {
        $filename = readdir($this->directoryHandle);
        echo $filename . "<br>";
        if (!$filename) {
            closedir($this->directoryHandle);
            return null;
        }
        else {

            $path = $this->directory . "/$filename";

            // if we are looking at a directory, just go to the next file
            return is_dir($path)
                ? $this->getNextPageContent()
                : file_get_contents($path);
        }
    }
}