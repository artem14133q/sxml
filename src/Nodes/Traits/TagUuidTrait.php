<?php

namespace Sxml\Nodes\Traits;

use Exception;

trait TagUuidTrait
{
    /**
     * @var string
     */
    protected string $uuid;

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function createUuid()
    {
        $this->uuid = uuid();
    }
}
