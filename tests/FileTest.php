<?php

namespace STS\ZipStream\Tests;

use Orchestra\Testbench\TestCase;
use STS\ZipStream\Models\File;
use STS\ZipStream\Models\LocalFile;
use STS\ZipStream\Models\S3File;
use STS\ZipStream\Models\TempFile;

class FileTest extends TestCase
{
    public function testMake()
    {
        $this->assertInstanceOf(S3File::class, File::make('s3://bucket/key'));
        $this->assertInstanceOf(LocalFile::class, File::make('/dev/null'));
        $this->assertInstanceOf(LocalFile::class, File::make('/tmp/foobar'));
        $this->assertInstanceOf(TempFile::class, File::make("raw contents", "filename.txt"));
    }

    public function testLocalFile()
    {
        $filename = md5(microtime());
        file_put_contents("/tmp/$filename", "hi there");

        $file = new LocalFile("/tmp/$filename", "test.txt");

        $this->assertEquals(8, $file->getFilesize());
        $this->assertEquals("hi there", $file->getReadableStream()->getContents());
        $this->assertEquals("test.txt", $file->getZipPath());
    }

    public function testTempFile()
    {
        $file = new TempFile("hi there", "test.txt");

        $this->assertEquals(8, $file->getFilesize());
        $this->assertEquals("hi there", $file->getReadableStream()->getContents());
        $this->assertEquals("test.txt", $file->getZipPath());
    }
}