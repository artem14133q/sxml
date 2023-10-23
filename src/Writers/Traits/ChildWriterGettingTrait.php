<?php

namespace Sxml\Writers\Traits;

use Sxml\Nodes\AbstractNode;
use Sxml\Writers\AbstractWriter;

trait ChildWriterGettingTrait
{
    /**
     * @param AbstractNode $node
     * @return AbstractWriter
     */
    protected function getChildWriter(AbstractNode $node): AbstractWriter
    {
        $class = $node->writerClass();

        return new $class($node, $this->depth + 1, $this->beautify);
    }
}