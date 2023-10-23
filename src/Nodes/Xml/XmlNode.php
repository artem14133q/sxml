<?php

namespace Sxml\Nodes\Xml;

use Exception;
use Sxml\Nodes\SingleNode;
use Sxml\Writers\Xml\XmlNodeWriter;

class XmlNode extends SingleNode
{
    /**
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('xml', $attributes);
    }

    /**
     * @return string
     */
    public function writerClass(): string
    {
        return XmlNodeWriter::class;
    }
}