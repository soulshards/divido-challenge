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
        // prepare
        $filename   = 'fixtures.json';
        $loaderType = $this->_get_ext($filename);

        // do
        $fileLoader = FileLoaderFactory::create($loaderType);

        // assert
        self::assertInstanceOf(FileLoaderInterface::class, $fileLoader);
    }

    /**
     * @covers App\FileLoaderFactory::create
     */
    public function test_file_loader_factory_throws_when_file_type_is_not_supported(): void
    {
        // prepare
        $filename   = 'fixtures.jsonx';
        $loaderType = $this->_get_ext($filename);

        // assert
        self::expectException(FileLoaderException::class);

        // do
        $fileLoader = FileLoaderFactory::create($loaderType);
    }

    /**
     * @covers App\FileLoaderFactory::registerLoader
     */
    public function test_file_loader_factory_registers_new_file_loader_type(): void
    {
        // prepare
        $loaderType           = array('yaml', 'yml');
        $loaderImplementation = 'YamlFileLoader';

        // do
        $typesRegistered = FileLoaderFactory::registerLoader($loaderType, $loaderImplementation);

        // assert
        self::assertGreaterThan(0, $typesRegistered);
    }

    /**
     * @covers App\FileLoaderFactory::registerLoader
     */
    public function test_file_loader_factory_throws_when_registering_junk(): void
    {
        // prepare
        $loaderType           = array(null);
        $loaderImplementation = 'YamlFileLoader';

        // assert
        self::expectException(FileLoaderException::class);

        // do
        $typesRegistered = FileLoaderFactory::registerLoader($loaderType, $loaderImplementation);
    }

    /**
     * @covers App\JsonFileLoader::loadFile
     * @covers App\JsonFileLoader::getProcessedFileNames
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_successful_json_parse(): void
    {
        // prepare
        $existingFileName = 'fixtures.json';
        $loaderType       = $this->_get_ext($existingFileName);
        $fileLoader       = FileLoaderFactory::create($loaderType);

        // do
        $contents = $fileLoader->loadFile($existingFileName);

        // assert
        self::assertIsArray($contents);
        self::assertContains($existingFileName, $fileLoader->getProcessedFileNames('successful'));
    }

    /**
     * @covers App\JsonFileLoader
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_non_json_file(): void
    {
        // prepare
        $nonJsonFileName = 'fixtures';

        // assert
        self::expectException(FileLoaderException::class);

        //do
        $fileLoader = FileLoaderFactory::create($nonJsonFileName);
    }

    /**
     * @covers App\JsonFileLoader
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_non_readable_file(): void
    {
        // prepare
        $missingFileName = 'missing.json';
        $loaderType      = $this->_get_ext($missingFileName);

        $fileLoader = FileLoaderFactory::create($loaderType);

        // assert
        self::expectException(FileLoaderException::class);

        // do
        $contents = $fileLoader->loadFile($missingFileName);
    }

    /**
     * @covers App\JsonFileLoader
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_empty_file(): void
    {
        // prepare
        $emptyFileName = 'empty.json';
        $loaderType    = $this->_get_ext($emptyFileName);

        $fileLoader = FileLoaderFactory::create($loaderType);

        // do
        $contents = $fileLoader->loadFile($emptyFileName);

        // assert
        self::assertNull($contents);
        self::assertContains($emptyFileName, $fileLoader->getProcessedFileNames('failed'));
    }

    /**
     * @covers App\JsonFileLoader
     * @group single
     * @uses App\FileLoaderFactory::create
     */
    public function test_loader_corrupt_file(): void
    {
        // prepare
        $corruptFileName = 'corrupted.json';
        $loaderType      = $this->_get_ext($corruptFileName);

        $fileLoader = FileLoaderFactory::create($loaderType);

        // do
        $contents = $fileLoader->loadFile($corruptFileName);

        // assert
        self::assertNull($contents);
        self::assertContains($corruptFileName, $fileLoader->getProcessedFileNames('failed'));
    }

    /**
     * @covers App\Config
     * @covers App\JsonFileLoader::loadFile
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_config_loading_a_valid_file_and_getting_a_path(): void
    {
        // prepare
        $existingFileName = 'fixtures.json';
        $loaderType       = $this->_get_ext($existingFileName);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFile($existingFileName);

        // assert
        self::assertNull($config->get('some.path'));
    }

    /**
     * Helper function to extract extension from a file path.
     * @param  string $filePath     File path to extract from.
     * @return string               Returns file extension if found.
     */
    protected function _get_ext(string $filePath): string
    {
        $pInfo = pathinfo($filePath);
        return $pInfo['extension'];
    }
}
