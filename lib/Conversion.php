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

namespace RQuadling\ClassFileConversion;

use Composer\Autoload\ClassLoader;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerClassToFile;
use Phpactor\ClassFileConverter\Adapter\Composer\ComposerFileToClass;
use Phpactor\ClassFileConverter\Domain\ClassName;
use Phpactor\ClassFileConverter\Domain\FilePath;
use RQuadling\Environment\Environment;
use Throwable;

class Conversion
{
    private static ClassLoader $autoLoader;
    private static ComposerClassToFile $composerClassToFile;
    private static ComposerFileToClass $composerFileToClass;

    private static function initAutoLoader(): void
    {
        if (empty(static::$autoLoader)) {
            static::$autoLoader = require Environment::getRoot().'/vendor/autoload.php';
        }
    }

    /**
     * Return the class name based upon Composer's current information.
     */
    public static function getClassNameFromFilename(string $filename): ?ClassName
    {
        if (empty(static::$composerFileToClass)) {
            static::initAutoLoader();
            static::$composerFileToClass = new ComposerFileToClass(static::$autoLoader);
        }

        try {
            return static::$composerFileToClass
                ->fileToClassCandidates(FilePath::fromString($filename))
                ->best();
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Return the filename based upon Composer's current information.
     */
    public static function getFilenameFromClassName(string $classname): ?FilePath
    {
        if (empty(static::$composerClassToFile)) {
            static::initAutoLoader();
            static::$composerClassToFile = new ComposerClassToFile(static::$autoLoader);
        }

        try {
            return static::$composerClassToFile
                ->classToFileCandidates(ClassName::fromString($classname))
                ->best();
        } catch (Throwable $exception) {
            return null;
        }
    }
}
