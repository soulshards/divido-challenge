<?php

use App\Config;
use PHPUnit\Framework\TestCase;

class FeatureTest extends TestCase
{

    /**
     * @test
     * @covers App\Config
     */
    public function testBasicFeatures(): void
    {
        $existingFileName = 'fixtures';
        $config           = new Config();

        self::assertInstanceOf(Config::class, $config);

        $config->loadFromFile($existingFileName);

        $nonExistentConfig = $config->get('comma.separated.path');
        self::assertNull($nonExistentConfig);
    }
}
