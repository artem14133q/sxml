<?php

namespace Sxml\Nodes;

use Sxml\Nodes\Enums\NodeType;
use Sxml\Writers\SingleNodeWriter;
use Exception;

class SingleNode extends AbstractNode
{
    /**
     * @param string $name
     * @param array $attributes
     * @param array $options
     * @throws Exception
     */
    public function __construct(string $name, array $attributes = [], array $options = [])
    {
        parent::__construct(NodeType::Single, $name, $attributes, null, $options);
    }

    /**
     * @param array $nodes
     * @return $this
     * @throws Exception
     */
    public function setChildren(array $nodes): self
    {
        throw new Exception('Cannot set children nodes to single tag');
    }

    /**
     * @param AbstractNode $node
     * @return AbstractNode
     * @throws Exception
     */
    public function appendChild(AbstractNode $node): AbstractNode
    {
        throw new Exception('Cannot add child node to single tag');
    }

    /**
     * @param string|null $value
     * @return AbstractNode
     * @throws Exception
     */
    public function setValue(?string $value): AbstractNode
    {
        throw new Exception('Cannot add value to single tag');
    }

    /**
     * @return string
     */
    public function writerClass(): string
    {
        return SingleNodeWriter::class;
    }
}