<?php

use App\Config;
use App\FileLoaderFactory;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function setUp(): void
    {
        Config::setBaseDir(__DIR__ . '/fixtures');
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_a_valid_file_and_getting_a_valid_path(): void
    {
        // prepare
        $existingFileNames = array('fixtures.json', 'fixtures2.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);

        // assert
        self::assertIsString($config->getByPath('environment'));
        self::assertIsArray($config->getByPath('database'));
        self::assertIsString($config->getByPath('database.host'));
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_a_valid_file_reverse_and_getting_a_valid_path(): void
    {
        // prepare
        $existingFileNames = array('fixtures2.json', 'fixtures.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);

        // assert
        self::assertEquals('production', $config->getByPath('environment'));
        self::assertIsArray($config->getByPath('database'));
        self::assertEquals('mysql', $config->getByPath('database.host'));
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_a_valid_file_reverse_and_getting_invalid_paths(): void
    {
        // prepare
        $existingFileNames = array('fixtures2.json', 'fixtures.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);

        // assert
        self::assertNull($config->getByPath(''));
        self::assertNull($config->getByPath(' .'));
        self::assertNull($config->getByPath(' . '));
        self::assertNull($config->getByPath('!'));
        self::assertNull($config->getByPath('.'));
        self::assertNull($config->getByPath('\.\\'));
        self::assertNull($config->getByPath('..'));
        self::assertNull($config->getByPath('database.'));
        self::assertNull($config->getByPath('.database.'));
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_a_valid_file_and_getting_an_invalid_path(): void
    {
        // prepare
        $existingFileNames = array('fixtures.json', 'fixtures2.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);

        // assert
        self::assertNull($config->getByPath('invalid.path'));
        self::assertIsString($config->getByPath('database.host'));
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_an_empty_file_and_getting_a_valid_path(): void
    {
        // prepare
        $existingFileNames = array('empty.json', 'corrupted.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);
        $parsedFilesSuccess = $config->getProcessingStats('successful');
        $parsedFilesFail    = $config->getProcessingStats('failed');

        // assert
        self::assertNull($config->getByPath('database.host'));

        self::assertIsArray($parsedFilesSuccess);
        self::assertCount(0, $parsedFilesSuccess);

        self::assertIsArray($parsedFilesFail);
        self::assertCount(count($existingFileNames), $parsedFilesFail);

    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_an_invalid_file(): void
    {
        // prepare
        $existingFileNames = array('fixtures_missing.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);
        $parsedFilesSuccess = $config->getProcessingStats('successful');
        $parsedFilesFail    = $config->getProcessingStats('failed');

        // assert
        self::assertNull($config->getByPath('environment'));

        self::assertIsArray($parsedFilesSuccess);
        self::assertCount(0, $parsedFilesSuccess);

        self::assertIsArray($parsedFilesFail);
        self::assertCount(count($existingFileNames), $parsedFilesFail);
    }

    /**
     * @covers App\Config
     *
     * @uses App\FileLoaderFactory::create
     * @uses App\JsonFileLoader::loadFile
     */
    public function test_config_loading_an_invalid_file_and_a_valid_one(): void
    {
        // prepare
        $existingFileNames = array('fixtures.json', 'fixtures_missing.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);
        $parsedFilesSuccess = $config->getProcessingStats('successful');
        $parsedFilesFail    = $config->getProcessingStats('failed');

        // assert
        self::assertIsString($config->getByPath('environment'));

        self::assertIsArray($parsedFilesSuccess);
        self::assertCount(1, $parsedFilesSuccess);

        self::assertIsArray($parsedFilesFail);
        self::assertCount(1, $parsedFilesFail);
    }

    /**
     * @covers App\Config
     * @covers App\JsonFileLoader::loadFile
     *
     * @uses App\FileLoaderFactory::create
     */
    public function test_config_loading_a_valid_file_and_return_statistics(): void
    {
        // prepare
        $existingFileNames = array('fixtures.json', 'fixtures2.json');
        $loaderType        = $this->_get_ext($existingFileNames[0]);

        $fileLoader = FileLoaderFactory::create($loaderType);
        $config     = new Config($fileLoader);

        // do
        $config->loadFromFiles($existingFileNames);

        $parsedFilesSuccess = $config->getProcessingStats('successful');
        $parsedFilesFail    = $config->getProcessingStats('failed');

        // assert
        self::assertIsArray($parsedFilesSuccess);
        self::assertCount(count($existingFileNames), $parsedFilesSuccess);

        self::assertIsArray($parsedFilesFail);
        self::assertCount(0, $parsedFilesFail);
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
