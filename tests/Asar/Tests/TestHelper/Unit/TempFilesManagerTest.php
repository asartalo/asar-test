<?php
/**
 * This file is part of the Asar Test Library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\TestHelper\Unit;

use Asar\TestHelper\TempFilesManager;

/**
 * Specifications for Asar\Tests\TempFilesManager
 */
class TempFilesManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->tempdirParent = dirname(ASAR_TESTHELPER_TEMPDIRECTORY);
        $this->tempdir = ASAR_TESTHELPER_TEMPDIRECTORY;
        $this->tempFilesManager = new TempFilesManager($this->tempdir);
        $this->recursiveDelete($this->tempdir, false);
    }

    /**
     * Teardown
     */
    public function tearDown()
    {
        $this->recursiveDelete($this->tempdir, false);
    }

    private function recursiveDelete($folderPath, $thisToo = true)
    {
        $folderPath = $this->getOsPath($folderPath);
        if (file_exists($folderPath) && is_dir($folderPath)) {
            $contents = scandir($folderPath);
            foreach ($contents as $value) {
                if ($value != "." && $value != ".." && $value != '.svn') {
                    $value = $folderPath . "/" . $value;
                    if (is_dir($value)) {
                        $this->recursiveDelete($value);
                    } elseif (is_file($value)) {
                        @unlink($value);
                    }
                }
            }
            if ($thisToo) {
                return rmdir($folderPath);
            }
        } else {
             return false;
        }
    }

    /**
     * Throws exception when temporary directory does not exist
     */
    public function testInstatiationThrowsErrorWhenTempDirDoesNotExist()
    {
        $dir = 'foo_dir';
        $this->setExpectedException(
            'Asar\TestHelper\TempFilesManagerException',
            "The directory specified ($dir) as temporary directory does not exist."
        );
        $tempFilesManager = new TempFilesManager($dir);
    }

    /**
     * Adds files to temporary directory
     */
    public function testAddingFilesToTemp()
    {
        $this->tempFilesManager->newFile('foo.txt', 'bar');
        $fileFullPath = $this->getFilePath('foo.txt');
        $this->assertFileExists($fileFullPath);
        $this->assertEquals('bar', file_get_contents($fileFullPath));
    }

    /**
     * Adds files with directory paths
     */
    public function testAddingFilesWithDirectoryPaths()
    {
        $file = 'foo/bar/baz.txt';
        $this->tempFilesManager->newFile($file, 'foo bar baz');
        $this->assertFileExists($this->getFilePath($file));
        $this->assertEquals(
            'foo bar baz', file_get_contents($this->getFilePath($file))
        );
    }

    /**
     * Creates directories in temporary directory
     */
    public function testCreatingDirectories()
    {
        $dir = 'foo/bar/baz';
        $this->tempFilesManager->newDir($dir);
        $this->assertFileExists($this->getFilePath($dir));
    }

    /**
     * Can get full file path
     */
    public function testGettingFullFilePath()
    {
        $files = array('foo.txt' => 'foo', 'bar/baz.txt' => 'bar baz');
        foreach ($files as $file => $contents) {
            $this->tempFilesManager->newFile($file, $contents);
            $this->assertEquals(
                $this->getFilePath($file), $this->tempFilesManager->getPath($file)
            );
        }
    }

    /**
     * Can remove files in temporary directory
     */
    public function testRemovingFilesInTemp()
    {
        $this->tempFilesManager->newFile('file1', 'test');
        $this->tempFilesManager->removeFile('file1');
        $this->assertFileNotExists($this->getFilePath('file1'));
    }

    /**
     * Clears temporary directory
     *
     * @param array $files list of files to be created and deleted
     *
     * @dataProvider clearingTempDirTestData
     */
    public function testClearingTempDirectory($files)
    {
        foreach ($files as $file => $contents) {
            $this->tempFilesManager->newFile($file, $contents);
            $this->assertFileExists($this->getFilePath($file));
        }
        $this->tempFilesManager->clearTempDirectory();
        foreach (array_keys($files) as $file) {
            $this->assertFileNotExists($this->getFilePath($file));
        }
    }

    /**
     * A list of files to be created and deleted
     *
     * @return array a list of files
     */
    public function clearingTempDirTestData()
    {
        return array(
            array(
                array('file1' => 'test1', 'file2' => 'test2', 'file3' => 'test3')
            ),
            array(
                array(
                    'foo/file1' => 'test1',
                    'bar/file2' => 'test2',
                    'foo/baz/file3' => 'test3'
                )
            )
        );
    }

    /**
     * Can obtain temporary directory path
     */
    public function testGettingTempDirectory()
    {
        $this->assertEquals($this->tempdir, $this->tempFilesManager->getTempDirectory());
    }

    private function getOsPath($path)
    {
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        return $path;
    }

    private function getFilePath($file)
    {
        return $this->tempdir . DIRECTORY_SEPARATOR . $this->getOsPath($file);
    }
}
