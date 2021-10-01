<?php

namespace App;

use App\FileLoaderException;
use App\JsonFileLoader;

class FileLoaderFactory
{
    /**
     * Map of file loader types and their implementation.
     * @var array
     */
    protected static $_loaderMap = array(
        'json' => JsonFileLoader::class,
    );

    /**
     * Internal list of file loader instances (one per file type) (aka singletons)
     * @var array
     */
    protected static $_loaders = array();

    /**
     * Assuming the file extension is consistent with the file contents.
     *
     * @param  string $fileExtension    The configuration filename extension to be able to parse.
     *
     * @throws FileLoaderException      If there is no suitable file loader found for the requested file type.
     *
     * @return FileLoaderInterface  Returns a concrete file loader instance.
     */
    public static function create(string $loaderType): FileLoaderInterface
    {
        if (!isset(self::$_loaderMap[$loaderType])) {
            throw new FileLoaderException(sprintf('Could not find suitable file loader for [%s] files!', $loaderType));
        }

        // Enforcing singleton pattern via the factory
        // as direct instantiation is permitted but discouraged.

        if (!isset(self::$_loaders[$loaderType])) {
            self::$_loaders[$loaderType] = new self::$_loaderMap[$loaderType]();
        }

        return self::$_loaders[$loaderType];
    }

    /**
     * The method is introduced to allow for run-time extension of the supported file loader types.
     *
     * @param  array  $types        File loader types (file extensions to match)
     * @param  string $className    Class name used to instantiate the related file loader.
     *
     * @return                      The count of extensions registered.
     */
    public static function registerLoader(array $types, string $className): int
    {
        foreach ($types as $fileExtension) {
            if (!is_string($fileExtension)) {
                throw new FileLoaderException(sprintf('File loader extensions should of type string, [%s] found instead!', gettype($fileExtension)));
            }
            self::$_loaderMap[$fileExtension] = $className;
        }

        return count($types);
    }

}
