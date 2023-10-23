<?php

namespace Sxml\Nodes\Html;

use Exception;
use Sxml\Nodes\SingleNode;
use Sxml\Writers\Html\DoctypeNodeWriter;

class DoctypeNode extends SingleNode
{
    /**
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('DOCTYPE', $attributes);
    }

    /**
     * @return string
     */
    public function writerClass(): string
    {
        return DoctypeNodeWriter::class;
    }
}