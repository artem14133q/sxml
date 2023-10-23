<?php

namespace Sxml\Documents;

use Exception;
use Sxml\Nodes\{AbstractNode, Html\DoctypeNode, Node, NodeInterface, SingleNode, Traits\TagUuidTrait};
use Sxml\Parsers\HtmlParser;
use Sxml\Writers\Html\HtmlDocumentWriter;

class HtmlDocument implements NodeInterface
{
    use TagUuidTrait;

    /**
     * @var SingleNode
     */
    private SingleNode $doctype;

    /**
     * @var Node
     */
    private Node $html;

    /**
     * @param string $content
     * @throws Exception
     */
    public function __construct(string $content)
    {
        $this->createUuid();

        $parser = HtmlParser::make($content);

        [$this->doctype, $this->html] = $parser->parse();
    }

    /**
     * @return Node
     */
    public function getHtmlNode(): Node
    {
        return $this->html;
    }

    /**
     * @return DoctypeNode
     */
    public function getDoctypeNode(): DoctypeNode
    {
        return $this->doctype;
    }

    /**
     * @param AbstractNode $node
     * @return $this
     */
    public function appendChild(AbstractNode $node): self
    {
        $this->html->appendChild($node);

        return $this;
    }

    /**
     * @param array $nodes
     * @return $this
     */
    public function setChildren(array $nodes): self
    {
        $this->html->setChildren($nodes);

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->html->getChildren();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->html->getAttributes();
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->html->setAttributes($attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute(string $name, string $value): self
    {
        $this->html->addAttribute($name, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        return [$this->html->dump(), $this->html->dump()];
    }

    /**
     * @return HtmlDocumentWriter
     */
    public function writer(): HtmlDocumentWriter
    {
        return new HtmlDocumentWriter($this);
    }

    public function getType(): int
    {
        return AbstractNode::NODE_TYPE_ROOT;
    }
}