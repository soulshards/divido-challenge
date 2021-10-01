<?php

namespace App;

use App\FileLoaderException;
use App\FileLoaderInterface;

/**
 * The class is not implementing the Singleton pattern, to allow for
 * potential multiple instances used for different modules. The
 */
class JsonFileLoader implements FileLoaderInterface
{
    public function loadFile(string $filePath):  ? array
    {
        $result = null;

        if (!is_readable($filePath)) {
            throw new FileLoaderException(sprintf("Not a readable file %s", $filePath));
        }

        $contents = file_get_contents($filePath);
        if ($conf = json_decode($contents, true)) {
            $result = $conf;
        }

        return $result;
    }
}
