<?php

namespace Sxml\Nodes;

use Sxml\Nodes\Traits\TagUuidTrait;
use Sxml\Writers\AbstractWriter;
use Exception;

abstract class AbstractNode implements NodeInterface
{
    public const SEARCH_NAME = 1;
    public const SEARCH_ATTRIBUTES = 2;
    public const SEARCH_VALUE = 3;
    public const SEARCH_TYPE = 4;

    public const NODE_TYPE_SINGLE = 1;
    public const NODE_TYPE_FULL = 2;
    public const NODE_TYPE_ROOT = 3;

    use TagUuidTrait;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array<string, string>
     */
    protected array $attributes = [];

    /**
     * @var AbstractNode[]
     */
    protected array $children = [];

    /**
     * @var string|null
     */
    protected ?string $value = null;

    /**
     * @var int
     */
    protected int $type;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @param int $type
     * @param string $name
     * @param array $attributes
     * @param string|null $value
     * @param array $options
     * @throws Exception
     */
    public function __construct(
        int $type, string $name, array $attributes = [], ?string $value = null, array $options = []
    ) {
        $this->options = $options;
        $this->type = $type;

        $this->parseName($name);
        $this->attributes = $attributes;
        $this->value = $value;

        $this->createUuid();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption(string $name, mixed $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $content
     * @return void
     * @throws Exception
     */
    protected function parseName(string $content): void
    {
        $prefix = null;
        $name = explode(":", $content, 2);

        count($name) == 2 ? [$prefix, $name] = $name : $name = $name[0];

        if ($prefix && !($this->options['xmlns'][$prefix] ?? false)) {
            throw new Exception("Cannot parse name '$content'. Prefix not found.");
        }

        $this->name = $name;
        $this->options['prefix'] = $prefix;
    }

    /**
     * @return array
     */
    protected function parentOptions(): array
    {
        return [
            'xmlns' => $this->options['xmlns'] ?? null,
            'depth' => ($this->options['depth'] ?? 0) + 1,
        ];
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return $this->name;
    }

    /**
     * @return AbstractNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $nodes
     * @return $this
     */
    public function setChildren(array $nodes): self
    {
        $this->children = $nodes;

        return $this;
    }

    /**
     * @param AbstractNode $node
     * @return $this
     */
    public function appendChild(AbstractNode $node): self
    {
        $this->children[] = $node;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function dump(): array
    {
        return [
            'name' => $this->name,
            'attributes' => $this->attributes,
            'options' => $this->options,
        ];
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param string|null $value
     * @param bool $single
     * @return AbstractNode
     * @throws Exception
     */
    public function createChildNode(
        string $name, array $attributes = [], ?string $value = null, bool $single = true
    ): AbstractNode {
        $node = ($value || !$single)
            ? new Node($name, $attributes, $value, $this->parentOptions())
            : new SingleNode($name, $attributes, $this->parentOptions());

        $this->appendChild($node);

        return $node;
    }

    /**
     * @param string $line
     * @return string[]
     */
    private function arrayString(string $line): array
    {
        $firstChar = substr($line, 0, 1);
        $lastChar = substr($line, -1);

        if ($firstChar == '[' && $lastChar == ']') {
            return explode(',', substr($line, 1, -1));
        }

        if ($line) {
            return [$line];
        }

        return [];
    }

    /**
     * Find by name or prefix.
     *
     * Logic operators:
     *  1. "|" - Or
     *  2. ":" - And
     *
     * Example:
     *  1. "div" => Find children with tag name 'div'.
     *  2. "w:div" => Find children with tag name 'div' and prefix 'w'.
     *  3. "w:" => Find children with prefix 'w'.
     *  4. "[a,b,c]:" => Find children with prefixes 'a', 'b', 'c'.
     *  5. "[a,b,c]:[div,span]" => Find children with prefixes 'a', 'b', 'c' and names 'div', 'span'.
     *  6. "[a,b,c]|[div,span]" => Find children with prefixes 'a', 'b', 'c' or names 'div', 'span'.
     *
     * @param string $name
     * @return array
     */
    public function findByName(string $name): array
    {
        $logicOperation = strpos($name, '|') ? '|' : ':';

        $parameters = explode($logicOperation, $name, 2);

        if (count($parameters) == 1) {
            return array_values(
                array_filter($this->children, fn (AbstractNode $node) => $node->getTagName() == $parameters[0])
            );
        }

        $prefixes = $this->arrayString($parameters[0]);
        $names = $this->arrayString($parameters[1]);

        return array_values(
            array_filter($this->children, function (AbstractNode $node) use ($prefixes, $names, $logicOperation) {
                $hasPrefix = in_array($node->options['prefix'] ?? '', $prefixes);

                if (empty($names)) {
                    return $hasPrefix;
                }

                $hasNames = in_array($node->getTagName(), $names);

                if ($logicOperation == '|') {
                    return $hasPrefix || $hasNames;
                }

                return $hasPrefix && $hasNames;
            })
        );
    }

    public function multipleInArray(array $find, array $in): bool
    {
        foreach ($find as $value) {
            $onStart = $value[0] == "%";
            $onEnd = str_ends_with($value, "%");

            if ($onStart || $onEnd) {
                $value = str_replace("%", "", $value);
                $countValue = count($value);

                $result = array_map(function (string $data) use($value, $countValue, $onStart, $onEnd) {
                    return str_contains($value, $data)
                        && ($onStart || substr($data, 0, $countValue) == $value)
                        && ($onEnd || substr($data, -$countValue) == $value);
                }, $in);

                if (in_array(true, $result)) {
                    return true;
                }
            }

            $result = array_map(fn (string $data) => $data == $value, $in);

            if (in_array(true, $result)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Find by attributes.
     *
     * Examples:
     *  1. "['title', 'pages']" => Find children with attributes 'title' and 'pages'.
     *  2. "['title' => 'Pages']" => Find children with attribute 'title' with value 'Pages'.
     *  3. "['title' => 'Pages,News']" => Find children with attribute 'title' with values 'Pages' or 'News'.
     *  4. "['cols,rows' => '3']" => Find children with attributes 'cols' or 'rows' with value '3'.
     *  5. "['?' => 'Page']" => Find children with all attributes with value 'Page'.
     *  5. "['%itle%' => '%ag%']" => Find children with all attributes with regex.
     *
     * @param array $attributes
     * @return array
     */
    public function findByAttributes(array $attributes): array
    {
        $filters = [];

        foreach ($attributes as $keys => $values) {
            if (is_int($keys)) {
                $keys = $values;
            }

            $filters[] = [explode($keys, ','), explode($values, ',')];
        }

        return array_filter($this->children, function (AbstractNode $node) use ($filters) {
            $keys = array_keys($node->getAttributes());

            foreach ($filters as $filter) {
                $hasKeys = true;

                if ($filter[0][0] == "?") {
                    $hasKeys = $this->multipleInArray($filter[0], $keys);
                }

                $hasValues = $this->multipleInArray($filter[1], $keys);

                if ($hasKeys || $hasValues) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Examples:
     *  1. "%value%" => Find by template.
     *  1. "value" => Find by value strict.
     *
     * NOTE: This filter can find full nodes (not single) only.
     *
     * @param string $value
     * @param bool $empty
     * @return array
     */
    public function findByValue(string $value, bool $empty = false): array
    {
        return array_filter($this->children, function (AbstractNode $node) use($empty, $value) {
            if ($this->type != self::NODE_TYPE_FULL) {
                return false;
            }

            if (!($data = $node->value)) {
                return $empty;
            }

            $onStart = $value[0] == "%";
            $onEnd = str_ends_with($value, "%");

            if ($onStart || $onEnd) {
                $value = str_replace("%", "", $value);
                $countValue = count($value);

                if (str_contains($value, $data)
                    && ($onStart || substr($data, 0, $countValue) == $value)
                    && ($onEnd || substr($data, -$countValue) == $value)
                ) {
                    return true;
                }
            }

            return $value == $data;
        });
    }

    /**
     * Search by type (AbstractNode::NODE_TYPE_SINGLE, AbstractNode::NODE_TYPE_FULL).
     *
     * @param int $type
     * @return array
     */
    public function findByType(int $type): array
    {
        return array_filter($this->children, fn (AbstractNode $node) => $node->type == $type);
    }

    /**
     * @param int|array|string $value
     * @param int $searchType
     * @return array
     * @throws Exception
     */
    public function find(int|array|string $value, int $searchType = self::SEARCH_NAME): array
    {
        return match ($searchType) {
            self::SEARCH_NAME => $this->findByName($value),
            self::SEARCH_ATTRIBUTES => $this->findByAttributes($value),
            self::SEARCH_VALUE => $this->findByValue($value),
            self::SEARCH_TYPE => $this->findByType($value),
            default => throw new Exception("Search type '$searchType' not defined"),
        };
    }

    /**
     * @return string
     */
    abstract public function writerClass(): string;

    /**
     * @return AbstractWriter
     */
    public function writer(): AbstractWriter
    {
        $class = $this->writerClass();

        return new $class($this);
    }
}