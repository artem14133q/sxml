<?php

namespace Sxml\Writers\Xml;

use Sxml\Nodes\Xml\XmlNode;
use Sxml\Writers\AbstractWriter;

class XmlNodeWriter extends AbstractWriter
{
    /**
     * @param XmlNode $node
     * @param int $depth
     * @param bool $beautify
     */
    public function __construct(XmlNode $node, int $depth = 0, bool $beautify = false)
    {
        parent::__construct($node, $depth, $beautify);
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        return implode(["<?", $this->getFullName(), $this->attributesAsText(), "?>"]);
    }
}