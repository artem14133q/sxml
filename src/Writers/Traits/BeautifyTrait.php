<?php

namespace Sxml\Writers\Traits;

trait BeautifyTrait
{
    /**
     * @var bool
     */
    protected bool $beautify = false;

    public function beautify(): self
    {
        $this->beautify = true;

        return $this;
    }
}