<?php

namespace Sxml\Writers;

use Sxml\Nodes\SingleNode;

class SingleNodeWriter extends AbstractWriter
{
    public function __construct(SingleNode $node, int $depth = 0, bool $beautify = false)
    {
        parent::__construct($node, $depth, $beautify);
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return implode([$this->tabs(), "<", $this->getFullName(), $this->attributesAsText(), "/>"]);
    }
}