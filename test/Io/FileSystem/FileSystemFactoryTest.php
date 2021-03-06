<?php

/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

namespace Phing\Test\Io\FileSystem;

use Phing\Io\FileSystem\FileSystemFactory;
use Phing\Phing;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Unit test for FileSystem
 *
 * @package phing.system.io
 */
class FileSystemFactoryTest extends PHPUnit_Framework_TestCase
{

    private $oldFsType = "";

    public function setUp()
    {
        $this->oldFsType = Phing::getProperty('host.fstype');
    }

    public function tearDown()
    {
        Phing::setProperty('host.fstype', $this->oldFsType);
        $this->_resetFileSystem();
    }

    protected function _resetFileSystem()
    {
        $refClass = new ReflectionClass('Phing\\Io\\FileSystem\\FileSystemFactory');
        $refProperty = $refClass->getProperty('fs');
        $refProperty->setAccessible(true);
        $refProperty->setValue(null);
    }

    /**
     * @expectedException Phing\Io\IOException
     */
    public function testGetFileSystemWithUnknownTypeKeyThrowsException()
    {
        $this->_resetFileSystem();

        Phing::setProperty('host.fstype', 'UNRECOGNISED');

        FileSystemFactory::getFileSystem();
    }

    /**
     * @dataProvider fileSystemMappingsDataProvider
     */
    public function testGetFileSystemReturnsCorrect($expectedFileSystemClass, $fsTypeKey)
    {
        $this->_resetFileSystem();

        Phing::setProperty('host.fstype', $fsTypeKey);

        $system = FileSystemFactory::getFileSystem();

        $this->assertInstanceOf($expectedFileSystemClass, $system);
    }

    public function fileSystemMappingsDataProvider()
    {
        return array(
            array('Phing\\Io\\FileSystem\\UnixFileSystem', 'UNIX'),
            array('Phing\\Io\\FileSystem\\Win32FileSystem', 'WIN32'),
            array('Phing\\Io\\FileSystem\\Win32FileSystem', 'WINNT')
        );
    }
}
