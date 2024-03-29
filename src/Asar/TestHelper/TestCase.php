<?php
/**
 * This file is part of the Asar Test Library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\TestHelper;

/**
 * A helper class to wrap common test setups in one class for easier testing.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    protected function quickMock($class, array $methods = array())
    {
        return $this->getMock($class, $methods, array(), '', false);
    }

    protected function getTempDir()
    {
        return ASAR_TESTHELPER_TEMPDIRECTORY;
    }

    /**
     * Returns the temp files manager
     *
     * @return TempFilesManager the temp files manager
     */
    public function getTfm()
    {
        if (!isset($this->tempFilesManager)) {
            $this->tempFilesManager = new TempFilesManager($this->getTempDir());
        }

        return $this->tempFilesManager;
    }

    protected function clearTestTempDirectory()
    {
        $this->getTFM()->clearTempDirectory();
    }

    protected function createClassDefinitionStr(
        $fullClassName, $extends = '', $body = ''
    )
    {
        $splitname = $this->splitFullClassName($fullClassName);
        $extends = $extends ? " extends $extends" : '';

        return "
            namespace {$splitname['namespace']} {
                class {$splitname['class']}$extends {
                    $body
                }
            }
        ";
    }

    protected function createClassDefinition(
        $fullClassName, $extends = '', $body = ''
    )
    {
        $classDefinition = $this->createClassDefinitionStr(
            $fullClassName, $extends, $body
        );
        eval ($classDefinition);
    }

    protected function splitFullClassName($fullClassName)
    {
        $all = explode('\\', $fullClassName);
        $return['class'] = array_pop($all);
        $return['namespace'] = implode('\\', $all);

        return $return;
    }

    protected function getOsPath($path)
    {
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        return $path;
    }

}
