<?php

namespace Sxml\Writers;

interface WriterInterface
{
    /**
     * @return string
     */
    public function asText(): string;

    /**
     * @return $this
     */
    public function beautify(): self;
}