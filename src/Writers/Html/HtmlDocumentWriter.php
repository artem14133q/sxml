<?php

namespace Sxml\Writers\Html;

use Sxml\Documents\HtmlDocument;
use Sxml\Writers\Traits\BeautifyTrait;
use Sxml\Writers\Traits\ChildWriterGettingTrait;
use Sxml\Writers\WriterInterface;

class HtmlDocumentWriter implements WriterInterface
{
    use BeautifyTrait, ChildWriterGettingTrait;

    /**
     * @var HtmlDocument
     */
    protected HtmlDocument $document;

    /**
     * @var int
     */
    protected int $depth = -1;

    /**
     * @param HtmlDocument $document
     */
    public function __construct(HtmlDocument $document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        $xmlWriter = $this->getChildWriter($this->document->getDoctypeNode());
        $rootWriter = $this->getChildWriter($this->document->getHtmlNode());

        return implode("\n", [$xmlWriter->asText(), $rootWriter->asText()]);
    }
}