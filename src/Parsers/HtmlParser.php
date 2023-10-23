<?php

namespace Sxml\Parsers;

use Exception;
use Sxml\Nodes\Html\DoctypeNode;

class HtmlParser extends Parser
{
    const UNPAIRED = [
        'area',     'base',     'br',       'col',
        'command',  'embed',    'hr',       'img',
        'input',    'keygen',   'param',    'source',
        'wbr',      'meta',     'link',
    ];

    /**
     * @var string
     */
    protected string $doctypeContent;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        [$this->doctypeContent, $content] = explode("\n", $content, 2);

        parent::__construct($content, ['unpaired' => self::UNPAIRED]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function parse(): array
    {
        return [$this->parseDoctypeTag(), $this->parseTagsRecursive()[0]];
    }

    /**
     * @return DoctypeNode
     * @throws Exception
     */
    public function parseDoctypeTag(): DoctypeNode
    {
        return new DoctypeNode($this->parseAttributesTag($this->doctypeContent));
    }
}