<?php

namespace Tests\Unit;

use App\Traits\HasEnumLongDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\Translator;
use Tests\TestCase;

/**
 * Enum descriptions tests.
 */
class HasEnumLongDescriptionTest extends TestCase
{
    /**
     * Test description retrieval using fallback due to missing translations.
     */
    public function testGetDescriptions(): void
    {
        $class = new class(0) extends Enum {
            use HasEnumLongDescription;
            public const TEST_VALUE = 0;
        };

        $this->assertEquals('Test value', $class::getLongDescription($class::TEST_VALUE));
        $this->assertEquals('Test value', $class::getDescription($class::TEST_VALUE));
    }

    /**
     * Test enum long description retrieval successful.
     */
    public function testLongDescription(): void
    {
        $class = new class(0) extends Enum implements LocalizedEnum {
            use HasEnumLongDescription;
            public const TEST_VALUE = 0;
        };

        $longKey = $class::getLocalizationKey() . '.' . $class::TEST_VALUE . '.long';

        Lang::shouldReceive('has')->withArgs([$longKey])->andReturnTrue();

        $this->app->bind('translator', function () use ($longKey) {
            return $this->partialMock(Translator::class, function ($mock) use ($longKey) {
                $mock->shouldReceive('get')
                    ->withArgs([
                        $longKey,
                        [],
                        null,
                    ])
                    ->andReturn('Fake long description');
            });
        });

        $this->assertEquals('Fake long description', $class::getLongDescription($class::TEST_VALUE));
    }

    /**
     * Test enum short description retrieval successful.
     */
    public function testShortDescription(): void
    {
        $class = new class(0) extends Enum implements LocalizedEnum {
            use HasEnumLongDescription;
            public const TEST_VALUE = 0;
        };

        $longKey = $class::getLocalizationKey() . '.' . $class::TEST_VALUE . '.long';
        $shortKey = $class::getLocalizationKey() . '.' . $class::TEST_VALUE . '.short';

        Lang::shouldReceive('has')->withArgs([$longKey])->andReturnFalse();
        Lang::shouldReceive('has')->withArgs([$shortKey])->andReturnTrue();

        $this->app->bind('translator', function () use ($class) {
            return $this->partialMock(Translator::class, function ($mock) use ($class) {
                $mock->shouldReceive('get')
                    ->withArgs([
                        $class::getLocalizationKey() . '.' . $class::TEST_VALUE . '.short',
                        [],
                        null,
                    ])
                    ->andReturn('Fake short description');
            });
        });

        $this->assertEquals('Fake short description', $class::getDescription($class::TEST_VALUE));
    }
}
