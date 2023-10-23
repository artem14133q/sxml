<?php

namespace Sxml\Writers\Html;

use Sxml\Nodes\Html\DoctypeNode;
use Sxml\Writers\AbstractWriter;

class DoctypeNodeWriter extends AbstractWriter
{
    /**
     * @param DoctypeNode $node
     * @param int $depth
     * @param bool $beautify
     */
    public function __construct(DoctypeNode $node, int $depth = 0, bool $beautify = false)
    {
        parent::__construct($node, $depth, $beautify);
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return implode(["<!", $this->getFullName(), $this->attributesAsText(), ">"]);
    }
}