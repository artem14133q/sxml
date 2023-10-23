<?php

namespace Sxml\Writers;

use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\Node;

class NodeWriter extends AbstractWriter
{
    /**
     * @param Node $node
     * @param int $depth
     * @param bool $beautify
     */
    public function __construct(Node $node, int $depth = 0, bool $beautify = false)
    {
        parent::__construct($node, $depth, $beautify);
    }

    /**
     * @return string
     */
    function asText(): string
    {
        [$tabs, $newLine] = [$this->tabs(), $this->newLine()];

        $value = $this->node->getValue();

        $content = $value ? ($tabs . $this->tab() . $value) : implode($newLine, array_map(
            fn (AbstractNode $node) => $this->getChildWriter($node)->asText(), $this->node->getChildren()
        ));

        return implode([
            $tabs . "<", $this->getFullName(), $this->attributesAsText(), ">", $newLine,
            $content, $newLine,
            $tabs, "</", $this->getFullName(), ">"
        ]);
    }
}