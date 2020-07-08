<?php

declare(strict_types=1);

/**
 * RQuadling/ClassFileConversion
 *
 * LICENSE
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
 * as a compiled binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
 * interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
 * detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
 * all present and future rights to this software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <https://unlicense.org>
 *
 */

namespace RQuadlingTests\ClassFileConversion;

use PHPUnit\Framework\TestCase;
use RQuadling\ClassFileConversion\Conversion;
use RQuadling\Environment\Environment;
use RQuadlingTests\ClassFileConversion\Fixtures\Deeply\Nested\DeeplyNestedFixture;

class ConversionTest extends TestCase
{
    /**
     * @dataProvider provideFilenamesAndClasses
     */
    public function testClassNameCanBeRetrievedForValidFilename(string $file, string $expectation = null): void
    {
        $this->assertEquals($expectation, Conversion::getClassNameFromFilename($file));
    }

    /**
     * @dataProvider provideClassnamesAndFilenames
     */
    public function testGetFilenameFromClass(string $classname, string $expectation = null): void
    {
        $this->assertEquals($expectation, Conversion::getFilenameFromClassName($classname));
    }

    /**
     * @return array<string, array<int, string|null>>
     */
    public function provideFilenamesAndClasses(): array
    {
        return [
            // Valid filenames.
            'Current class' => [__FILE__, __CLASS__],
            'Made up class in a valid namespace' => [__DIR__.'/MadeUpName.php', __NAMESPACE__.'\\MadeUpName'],
            'Deeply nested class' => [Environment::getRoot().'/tests/Fixtures/Deeply/Nested/DeeplyNestedFixture.php', DeeplyNestedFixture::class],

            // Invalid
            'Completely non-existent file' => ['/this/is/not/valid.php', null],
            'Outside of the document root' => [\dirname(Environment::getRoot()).'/not-found.php', null],
            'Inside the document root but invalid file' => [Environment::getRoot().'/not-found.php', null],
            'Inside the document root but invalid path and file' => [Environment::getRoot().'/not-found/not-found.php', null],
        ];
    }

    /**
     * @return array<string, array<int, string|null>>
     */
    public function provideClassnamesAndFilenames(): array
    {
        return [
            // Valid namespaces.
            'Current file' => [__CLASS__, __FILE__],
            'Made up class in a valid namespace' => [__NAMESPACE__.'\\MadeUpName', __DIR__.'/MadeUpName.php'],
            'Deeply nested class' => [DeeplyNestedFixture::class, Environment::getRoot().'/tests/Fixtures/Deeply/Nested/DeeplyNestedFixture.php'],

            // Invalid classnames
            'Completely non-existent class' => ['\\InvalidNamespace\\InvalidClass', null],
        ];
    }
}
