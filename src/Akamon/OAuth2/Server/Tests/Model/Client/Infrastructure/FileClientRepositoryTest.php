<?php

namespace Akamon\OAuth2\Server\Tests\Model\Client\Infrastructure;

use Akamon\OAuth2\Server\Model\Client\Infrastructure\FileClientRepository;
use Akamon\OAuth2\Server\Tests\Model\Client\ClientRepositoryTestCase;

class FileClientRepositoryTest extends \Akamon\OAuth2\Server\Tests\Model\Client\ClientRepositoryTestCase
{
    protected function createRepository()
    {
        $file = tempnam(sys_get_temp_dir(), 'FileClientRepository');

        return new \Akamon\OAuth2\Server\Model\Client\Infrastructure\FileClientRepository($file);
    }
}
