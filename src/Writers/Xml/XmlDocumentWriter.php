<?php

namespace Sxml\Writers\Xml;

use Sxml\Documents\XmlDocument;
use Sxml\Writers\Traits\BeautifyTrait;
use Sxml\Writers\Traits\ChildWriterGettingTrait;
use Sxml\Writers\WriterInterface;

class XmlDocumentWriter implements WriterInterface
{
    use BeautifyTrait, ChildWriterGettingTrait;

    /**
     * @var XmlDocument
     */
    protected XmlDocument $document;

    /**
     * @var int
     */
    protected int $depth = -1;

    /**
     * @param XmlDocument $document
     */
    public function __construct(XmlDocument $document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function asText(): string
    {
        $xmlWriter = $this->getChildWriter($this->document->getXmlNode());
        $rootWriter = $this->getChildWriter($this->document->getRootNode());

        return implode("\n", [$xmlWriter->asText(), $rootWriter->asText()]);
    }
}