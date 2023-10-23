<?php

namespace Sxml\Writers;

use Sxml\Nodes\AbstractNode;
use Sxml\Writers\Traits\BeautifyTrait;
use Sxml\Writers\Traits\ChildWriterGettingTrait;

abstract class AbstractWriter implements WriterInterface
{
    public const TAB = "\t";
    public const NEW_LINE = "\n";

    use BeautifyTrait, ChildWriterGettingTrait;

    /**
     * @var AbstractNode
     */
    protected AbstractNode $node;

    /**
     * @var int
     */
    protected int $depth = 0;

    /**
     * @param AbstractNode $node
     * @param int $depth
     * @param bool $beautify
     */
    public function __construct(AbstractNode $node, int $depth = 0, bool $beautify = false)
    {
        $this->node = $node;
        $this->depth = $depth;
        $this->beautify = $beautify;
    }

    /**
     * @return string
     */
    protected function tab(): string
    {
        return $this->beautify ? self::TAB : "";
    }

    /**
     * @return string
     */
    protected function tabs(): string
    {
        return str_repeat($this->tab(), $this->depth);
    }

    /**
     * @return string
     */
    protected function newLine(): string
    {
        return $this->beautify ? self::NEW_LINE : "";
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $prefix = $this->node->getOptions()['prefix'] ?? null;
        $name = $this->node->getTagName();

        return $prefix ? implode(":", [$prefix, $name]) : $name;
    }

    /**
     * @return string
     */
    protected function attributesAsText(): string
    {
        $content = "";

        foreach ($this->node->getAttributes() as $key => $value) {
            if ($value === true) {
                $content .= " $key";

                continue;
            }

            if ($value === false) {
                $value = 'false';
            }

            $content .= " $key=\"$value\"";
        }

        return $content;
    }
}