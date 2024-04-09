<?php

declare(strict_types=1);

namespace App\Turbo\Stream\Model;

use Override;
use Stringable;
use LogicException;
use function ltrim;
use function is_string;

abstract class AbstractStream implements StreamInterface, Stringable
{
    /**
     * @param non-empty-string|null $attributeName
     */
    public function __construct(
        private string $targetId,
        private ?string $html = null,
        private ?string $attributeName = null,
    )
    {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->generate();
    }

    #[Override]
    public function generate(): string
    {
        $action = $this->getAction();
        $targetId = ltrim($this->getTargetId(), '#');

        $attributeName = $this->attributeName;

        $target = is_string($attributeName) ? sprintf('targets="[%s=\'%s\']"', $attributeName, $targetId) : sprintf('target="%s"', $targetId);

        $html = (string)$this->getHtml();

        return <<<HTML
<turbo-stream action="$action" $target>
    <template>
        $html
    </template>
</turbo-stream>
HTML;
    }

    public function getTargetId(): string
    {
        return $this->targetId;
    }

    /**
     * @return 'update'|'replace'|'remove'|'append'|'prepend'|'before'|'after'
     */
    protected function getAction(): string
    {
        throw new LogicException();
    }

    protected function getHtml(): ?string
    {
        return $this->html;
    }
}
