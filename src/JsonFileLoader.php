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
    /**
     * List of file names processed
     * @todo: Move to an abstract super class when developing new concrete implementations
     * @var array
     */
    protected $_files_processed = array();

    public function loadFile(string $filePath):  ? array
    {
        $result = null;

        if (!is_readable($filePath)) {
            throw new FileLoaderException(sprintf("Not a readable file %s", $filePath));
        }

        $contents = file_get_contents($filePath);
        if ($conf = json_decode($contents, true)) {

            $result = $conf;

            $this->_files_processed['successful'][] = $filePath;
        } else {
            $this->_files_processed['failed'][] = $filePath;
        }

        return $result;
    }

    /**
     * Helper method for statistics on processed files with specific outcome.
     *
     * @todo: Good candidate to move to an abstraction class when developing new file loaders.
     *
     * @param  string $outcome  One of [successful|failed]
     *
     * @return array            Returns an array of filename strings, if any, else empty array.
     */
    public function getProcessedFileNames(string $outcome) : array
    {
        $result = array();
        if (in_array($outcome, array_keys($this->_files_processed))) {
            $result = $this->_files_processed[$outcome];
        }
        return $result;
    }

}
