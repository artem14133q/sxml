<?php

namespace Sxml\Nodes;

use Sxml\Nodes\Enums\NodeType;
use Sxml\Writers\WriterInterface;

interface NodeInterface
{
    /**
     * @return AbstractNode[]
     */
    public function getChildren(): array;

    /**
     * @param array $nodes
     * @return $this
     */
    public function setChildren(array $nodes): self;

    /**
     * @param AbstractNode $node
     * @return $this
     */
    public function appendChild(AbstractNode $node): self;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self;

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute(string $name, string $value): self;

    /**
     * @return array
     */
    public function dump(): array;

    /**
     * @return WriterInterface
     */
    public function writer(): WriterInterface;

    /**
     * @return NodeType
     */
    public function getType(): NodeType;
}