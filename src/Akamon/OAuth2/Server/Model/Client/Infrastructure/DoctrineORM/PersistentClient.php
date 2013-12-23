<?php

namespace Akamon\OAuth2\Server\Model\Client\Infrastructure\DoctrineORM;

class PersistentClient
{
    private $id;
    private $name;
    private $secret;
    private $allowedGrantTypes;
    private $allowedScopes;
    private $defaultScope;

    public function __construct(array $params)
    {
        $this->setParams($params);
    }

    public function setParams(array $params)
    {
        foreach ($params as $name => $param) {
            $this->$name = $param;
        }
    }

    public function getParams()
    {
        $ref = new \ReflectionClass($this);

        $params = [];
        foreach ($ref->getProperties() as $p) {
            $name = $p->getName();
            $params[$name] = $this->$name;
        }

        return $params;
    }
}
