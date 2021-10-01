<?php

use App\Config;
use App\FileLoaderException;
use App\FileLoaderFactory;
use App\FileLoaderInterface;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{

    /**
     * @covers App\FileLoaderFactory::create
     */
    public function test_file_loader_factory_works(): void
    {
        $filename = 'fixtures.json';

        $fileLoader = FileLoaderFactory::create($filename);

        self::assertInstanceOf(FileLoaderInterface::class, $fileLoader);
    }

    /**
     * @covers App\FileLoaderFactory::create
     */
    public function test_file_loader_factory_throws_when_file_type_is_not_supported(): void
    {
        $filename = 'fixtures.jsonx';

        self::expectException(FileLoaderException::class);

        $fileLoader = FileLoaderFactory::create($filename);
    }

    /**
     * @covers App\FileLoaderFactory::registerLoader
     */
    public function test_file_loader_factory_registers_new_file_loader_type(): void
    {
        $loaderType           = array('yaml', 'yml');
        $loaderImplementation = 'YamlFileLoader';

        $typesRegistered = FileLoaderFactory::registerLoader($loaderType, $loaderImplementation);
        self::assertGreaterThan(0, $typesRegistered);
    }

    /**
     * @covers App\FileLoaderFactory::registerLoader
     */
    public function test_file_loader_factory_throws_when_registering_junk(): void
    {
        $loaderType           = array(null);
        $loaderImplementation = 'YamlFileLoader';

        self::expectException(FileLoaderException::class);

        $typesRegistered = FileLoaderFactory::registerLoader($loaderType, $loaderImplementation);
    }

    /**
     * @covers App\JsonFileLoader::loadFile
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_successful_json_parse(): void
    {
        $existingFileName = 'fixtures.json';
        $fileLoader       = FileLoaderFactory::create($existingFileName);

        $contents = $fileLoader->loadFile($existingFileName);
        self::assertIsArray($contents);
    }

    /**
     * @covers App\JsonFileLoader
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_non_json_file(): void
    {
        $nonJsonFileName = 'fixtures';
        self::expectException(FileLoaderException::class);
        $fileLoader = FileLoaderFactory::create($nonJsonFileName);
    }

    /**
     * @covers App\JsonFileLoader
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_non_readable_file(): void
    {
        $missingFileName = 'missing.json';

        $fileLoader = FileLoaderFactory::create($missingFileName);

        self::expectException(FileLoaderException::class);
        $contents = $fileLoader->loadFile($missingFileName);
    }

    /**
     * @covers App\Config
     * @covers App\JsonFileLoader::loadFile
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_config_loading_a_valid_file_and_getting_a_path(): void
    {
        $existingFileName = 'fixtures.json';
        $fileLoader       = FileLoaderFactory::create($existingFileName);
        $config           = new Config($fileLoader);
        $config->loadFromFile($existingFileName);

        self::assertNull($config->get('some.path'));
    }

}
