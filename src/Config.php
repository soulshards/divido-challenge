<?php

namespace App;

class Config
{

    /**
     * Container for the configuration data loaded from the files.
     * @var array
     */
    protected $_data;

    /**
     * @param FileLoader $fileLoader
     */
    public function __construct(FileLoaderInterface $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    /**
     * Attempts to load configuration from a given file.
     *
     * @param  string $configFilename   The filename to lookup.
     * @return void
     */
    public function loadFromFile(string $configFilename): void
    {
        $this->_data = $this->fileLoader->loadFile($configFilename);
    }

    /**
     * Get a configuration by it's key
     * @param  string $path     String denoting the path to a configuration in dot notation.
     * @return mixed            Returns configuration data (Could be a scalar or compound value)
     */
    public function get(string $path)
    {
        $result = null;

        return $result;
    }
}
