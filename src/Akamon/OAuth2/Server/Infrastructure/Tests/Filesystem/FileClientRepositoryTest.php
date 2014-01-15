<?php

namespace Akamon\OAuth2\Server\Infrastructure\Tests\Filesystem;

use Akamon\OAuth2\Server\Infrastructure\Filesystem\FileClientRepository;
use Akamon\OAuth2\Server\Domain\Tests\Model\Client\ClientRepositoryTestCase;

class FileClientRepositoryTest extends ClientRepositoryTestCase
{
    private $file;

    protected function createRepository()
    {
        $this->file = sys_get_temp_dir() . '/not-existent-folder/file-client-repository';

        return new FileClientRepository($this->file);
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}
