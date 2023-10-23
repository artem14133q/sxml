<?php

namespace Sxml\Nodes;

use Sxml\Writers\NodeWriter;
use Exception;

class Node extends AbstractNode
{
    /**
     * @param string $name
     * @param array $attributes
     * @param string|null $value
     * @param array $options
     * @throws Exception
     */
    public function __construct(string $name, array $attributes = [], ?string $value = null, array $options = [])
    {
        parent::__construct(AbstractNode::NODE_TYPE_FULL, $name, $attributes, $value, $options);

        $this->value = $value;
    }

    /**
     * @param AbstractNode $node
     * @return $this
     */
    public function appendChild(AbstractNode $node): self
    {
        $this->children[] = $node;

        return $this;
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        return [
            ...parent::dump(),
            'value' => $this->value,
            'children' => array_map(fn (AbstractNode $node) => $node->dump(), $this->children),
        ];
    }

    /**
     * @return string
     */
    public function writerClass(): string
    {
        return NodeWriter::class;
    }
}