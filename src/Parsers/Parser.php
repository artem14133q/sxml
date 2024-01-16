<?php

namespace Sxml\Parsers;

use Sxml\Nodes\Node;
use Sxml\Nodes\SingleNode;
use Exception;

abstract class Parser
{
    protected const BS = '/';

    protected const TAGS_PATTERN = "/<[^>]+>/";
    protected const TAG_ATTRIBUTES_PATTERN = "/\s[^=\s>]+(?:=\"[^\"]*\")?/";

    public const TYPE_OPEN = 1;
    public const TYPE_CLOSE = 2;
    public const TYPE_SINGLE = 3;

    /**
     * @var string
     */
    protected string $content;

    /**
     * @var ?callable
     */
    protected $callback = null;

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @param string $content
     * @param array $parameters
     */
    public function __construct(string $content, array $parameters = [])
    {
        $this->content = $content;
        $this->parameters = $parameters;
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function installCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @param string $content
     * @return static
     */
    public static function make(string $content): self
    {
        return new static($content);
    }

    /**
     * @return array
     */
    abstract public function parse(): array;

    /**
     * @return array<string, string|null>
     */
    protected function getOptions(array $additional = []): array
    {
        return $additional;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function parseTagsRecursive(): array
    {
        preg_match_all(self::TAGS_PATTERN, $this->content, $matches, PREG_OFFSET_CAPTURE);

        [$depth, $queue, $nodes, $tags] = [0, [], [], $matches[0]];

        foreach ($tags as $i => [$tagContent, $tagPos]) {
            if ((str_starts_with($tagContent, '<!--')) && (str_ends_with($tagContent, '-->'))) {
                continue;
            }

            $tagData = [$name, $type, $attributes] = $this->parseTag($tagContent);

            if ($type == self::TYPE_SINGLE) {
                $nodes[$depth][] = new SingleNode($name, $attributes, $this->getOptions(['depth' => $depth]));

                continue;
            }

            if ($type == self::TYPE_OPEN) {
                $queue[$depth++] = array_merge($tagData, [$tagPos, mb_strlen($tagContent)]);

                /** @var $callback callable */
                if (($i == 0) && ($callback = $this->callback)) {
                    $callback($tagData);
                }

                continue;
            }

            if ($depth !== count($queue)) {
                $this->parseTagsException($tagContent, $tagPos);
            }

            /** @var array $tagOpen */
            $tagOpen = empty($queue) ? null : array_pop($queue);

            if (empty($tagOpen) || ($name !== $tagOpen[0])) {
                $this->parseTagsException($tagContent, $tagPos);
            }

            if ($children = $nodes[$depth] ?? null) {
                unset($nodes[$depth]);
            }

            $nodes[--$depth][] = $this->createNode($tagOpen, $children, $tagPos, $depth);
        }

        return $nodes[0];
    }

    /**
     * @param array $tagData
     * @param array|null $children
     * @param int $closeTagPos
     * @param int $depth
     * @return Node
     * @throws Exception
     */
    protected function createNode(array $tagData, ?array $children, int $closeTagPos, int $depth): Node
    {
        [$name, /* $type */, $attributes, $pos, $len] = $tagData;

        $endPos = $pos + $len;

        $value = !$children ? substr($this->content, $endPos, $closeTagPos - $endPos) : null;

        if ($value) {
            $value = trim($value);
        }

        $node = new Node($name, $attributes, $value, $this->getOptions(['depth' => $depth]));

        $node->setChildren($children ?: []);

        return $node;
    }

    /**
     * @param string $tagName
     * @param int $pos
     * @return mixed
     * @throws Exception
     */
    protected function parseTagsException(string $tagName, int $pos): mixed
    {
        throw new Exception("Got close tag `$tagName` on $pos, but open tag not found.");
    }

    /**
     * @param string $content
     * @return array
     */
    protected function parseAttributesTag(string $content): array
    {
        preg_match_all(self::TAG_ATTRIBUTES_PATTERN, $content, $matches);

        $attributes = [];

        foreach ($matches[0] as $match) {
            $values = explode('=', substr($match, 1), 2);

            [$key, $value] = count($values) > 1 ? $values : [$values[0], true];

            $attributes[$key] = is_string($value) ? substr($value, 1, -1) : $value;
        }

        return $attributes;
    }

    /**
     * @param string $content
     * @return array
     */
    protected function parseTag(string $content): array
    {
        $content = substr($content, 1, -1);

        $hasStartChar = $content[0] == self::BS;
        $hasEndChar = substr($content, -1) == self::BS;

        $type = $hasStartChar ? self::TYPE_CLOSE : (
            $hasEndChar ? self::TYPE_SINGLE : self::TYPE_OPEN
        );

        $args = [$content, $hasStartChar ? 1 : 0];

        if ($hasEndChar) {
            $args[] = -1;
        }

        $name = explode(" ", substr(...$args))[0];

        if (in_array($name, $this->parameters['unpaired'] ?? [])) {
            $type = self::TYPE_SINGLE;
        }

        $attributes = $this->parseAttributesTag($content);

        return [$name, $type, $attributes];
    }
}