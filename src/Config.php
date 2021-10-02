<?php

namespace App;

class Config
{

    /**
     * Container for the configuration data loaded from the files.
     * @var array
     */
    protected $_data = array();

    /**
     * Container for the configuration data loaded from the files.
     * @var array
     */
    protected $_configCollection = array();

    /**
     * List of file names processed
     * @todo: Current implementation allows for multiple passes of the same file
     *         overwriting the outcome.
     * @var array
     */
    protected $_filesProcessed = array();

    /**
     * @param FileLoader $fileLoader
     */
    public function __construct(FileLoaderInterface $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    /**
     * Attempts to load configuration from a given set of files.
     *
     * @param  array $configFileNames   The filenames to lookup.
     * @return void
     */
    public function loadFromFiles(array $configFileNames): void
    {
        $parsedData = array();

        foreach ($configFileNames as $configFileName) {
            try {

                $contents = $this->fileLoader->loadFile($configFileName);

                if (!is_null($contents)) {

                    $parsedData[] = $contents;

                    $this->_filesProcessed['successful'][$configFileName] = 'Config file read successfully.';
                } else {

                    $this->_filesProcessed['failed'][$configFileName] = 'Config file contents invalid!';
                }
            } catch (FileLoaderException $e) {

                $this->_filesProcessed['failed'][$configFileName] = $e->getMessage();
            }
        }

        $this->_mergeState($parsedData);
    }

    /**
     * Get a piece of configuration by it's key
     * @param  string $path     String denoting the path to a configuration in dot notation.
     * @return mixed            Returns configuration data (could be a scalar or compound value)
     */
    public function getByPath(string $path)
    {
        $result = null;

        $segments = explode(".", $path);

        $d = $this->_data;

        foreach ($segments as $segment) {

            if (isset($d[$segment])) {

                $result = $d[$segment];
                $d      = $result;
            } else {
                $result = null;
            }
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
     * @return array            Returns an array of filename => status message mappings, if any, else empty array.
     */
    public function getProcessingStats(string $outcome): array
    {
        $result = array();
        if (in_array($outcome, array_keys($this->_filesProcessed))) {
            $result = $this->_filesProcessed[$outcome];
        }
        return $result;
    }

    /**
     * Helper method for merging new into existing state.
     *
     * @param  array  $parsedData The new state to be merged.
     *
     * @return void
     */
    protected function _mergeState(array $parsedData): void
    {
        if (count($parsedData) > 0) {

            // be sure to account for already present state.
            array_unshift($parsedData, $this->_data);

            // merge the new state into the current, in order
            $this->_data = call_user_func_array('array_replace_recursive', $parsedData);
        }
    }
}
