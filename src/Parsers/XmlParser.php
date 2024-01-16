<?php

namespace Sxml\Parsers;

use Exception;
use Sxml\Nodes\Xml\XmlNode;

class XmlParser extends Parser
{
    /**
     * @var string
     */
    protected string $xmlContent;

    /**
     * @var array
     */
    protected array $xmlns;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        [$this->xmlContent, $content] = explode("\n", $content, 2);

        parent::__construct($content);

        $this->installCallback([$this, 'parseXmlns']);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function parse(): array
    {
        return [$this->parseXmlTag(), $this->parseTagsRecursive()[0]];
    }

    /**
     * @return array<string, string|null>
     */
    protected function getOptions(array $additional = []): array
    {
        return array_merge(['xmlns' => $this->xmlns], $additional);
    }

    /**
     * @return array
     */
    public function getXmlns(): array
    {
        return $this->xmlns;
    }

    /**
     * @return XmlNode
     * @throws Exception
     */
    protected function parseXmlTag(): XmlNode
    {
        return new XmlNode($this->parseAttributesTag($this->xmlContent));
    }

    /**
     * @param array $tagData
     * @return void
     */
    protected function parseXmlns(array $tagData): void
    {
        $xmlns = [];

        foreach ($tagData[2] as $key => $value) {
            if (!str_starts_with($key, 'xmlns:')) {
                continue;
            }

            $xmlns[explode(":", $key)[1]] = $value;
        }

        $this->xmlns = $xmlns;
    }
}