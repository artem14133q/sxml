<?php

namespace Sxml\Documents;

use Exception;
use Sxml\Nodes\{AbstractNode, Node, NodeInterface, SingleNode, Traits\TagUuidTrait};
use Sxml\Parsers\XmlParser;
use Sxml\Writers\Xml\XmlDocumentWriter;

class XmlDocument implements NodeInterface
{
    use TagUuidTrait;

    /**
     * @var SingleNode
     */
    private SingleNode $xml;

    /**
     * @var Node
     */
    private Node $root;

    /**
     * @var array
     */
    private array $xmlns;

    /**
     * @param string $content
     * @throws Exception
     */
    public function __construct(string $content)
    {
        $this->createUuid();

        $parser = XmlParser::make($content);

        [$this->xml, $this->root] = $parser->parse();

        $this->xmlns = $parser->getXmlns();
    }

    /**
     * @return Node
     */
    public function getRootNode(): Node
    {
        return $this->root;
    }

    /**
     * @return SingleNode
     */
    public function getXmlNode(): SingleNode
    {
        return $this->xml;
    }

    /**
     * @param AbstractNode $node
     * @return $this
     */
    public function appendChild(AbstractNode $node): self
    {
        $this->root->appendChild($node);

        return $this;
    }

    /**
     * @param array $nodes
     * @return $this
     */
    public function setChildren(array $nodes): self
    {
        $this->root->setChildren($nodes);

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->root->getChildren();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->root->getAttributes();
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->root->setAttributes($attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute(string $name, string $value): self
    {
        $this->root->addAttribute($name, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        return [$this->xml->dump(), $this->root->dump()];
    }

    /**
     * @return XmlDocumentWriter
     */
    public function writer(): XmlDocumentWriter
    {
        return new XmlDocumentWriter($this);
    }

    public function getType(): int
    {
        return AbstractNode::NODE_TYPE_ROOT;
    }
}