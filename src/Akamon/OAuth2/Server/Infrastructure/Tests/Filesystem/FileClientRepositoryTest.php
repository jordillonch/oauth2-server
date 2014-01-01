<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\Filesystem;

use Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository;
use Akamon\OAuth2\Server\Domain\Tests\Model\Client\ClientRepositoryTestCase;

class FileClientRepositoryTest extends ClientRepositoryTestCase
{
    private $filename;

    protected function createRepository()
    {
        $this->filename = sys_get_temp_dir() . 'not-existent-folder/file-client-repository';

        return new FileClientRepository($this->filename);
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
