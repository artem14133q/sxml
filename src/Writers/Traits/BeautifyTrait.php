<?php

namespace Sxml\Writers\Traits;

trait BeautifyTrait
{
    /**
     * @var bool
     */
    protected bool $beautify = false;

    /**
     * @return $this
     */
    public function beautify(): self
    {
        $this->beautify = true;

        return $this;
    }
}