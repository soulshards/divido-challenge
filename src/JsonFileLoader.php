<?php

namespace App;

use App\FileLoaderException;
use App\FileLoaderInterface;

/**
 * The class is not implementing the Singleton pattern, to allow for
 * potential multiple instances used for different modules.
 */
class JsonFileLoader implements FileLoaderInterface
{
    /**
     * Attempts reading and parsing a single JSON file.
     *
     * @throws FileLoaderException  When the requested file is not readable.
     *
     * @param  string $filePath     File path to read from.
     *
     * @return array|null           Returns an array if the parse was successful, null otherwise.
     */
    public function loadFile(string $filePath):  ? array
    {
        $result = null;

        if (!is_readable($filePath)) {
            // File not being readable is a rather unrecoverable error for the class,
            // as it cannot assure the client code calling it that the file can be
            // parsed or not ( as with returning null if the file is corrupted ).
            // So in this case it bubbles up an exception to the client code to let it
            // deal with the error (if possible).
            throw new FileLoaderException(sprintf("Not a readable file %s", $filePath));
        }

        $contents = file_get_contents($filePath);

        // decode into an associative array for later merging
        if ($conf = json_decode($contents, true)) {

            $result = $conf;
        }

        return $result;
    }
}
