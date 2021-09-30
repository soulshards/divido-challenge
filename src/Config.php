<?php

namespace App;

class Config
{
    /**
     * Attempts to load configuration from a given file.
     * @param  string $configFilename The filename to lookup.
     * @return void
     */
    public function loadFromFile(string $configFilename): void
    {

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
