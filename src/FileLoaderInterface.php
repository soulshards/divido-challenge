<?php

namespace App;

interface FileLoaderInterface
{
    public function loadFile(string $filePath);
    public function getProcessedFileNames(string $outcome);
}
