<?php

namespace Dedoc\Scramble\Support\Type\Reference;

use Dedoc\Scramble\Support\Type\Reference\Dependency\MethodDependency;
use Dedoc\Scramble\Support\Type\Type;

class StaticMethodCallReferenceType extends AbstractReferenceType
{
    public function __construct(
        public string|Type $callee,
        public string $methodName,
        /** @var Type[] $arguments */
        public array $arguments,
    ) {}

    public function nodes(): array
    {
        return ['arguments'];
    }

    public function toString(): string
    {
        $argsTypes = implode(
            ', ',
            array_map(fn ($t) => $t->toString(), $this->arguments),
        );

        $calleeType = is_string($this->callee) ? $this->callee : $this->callee->toString();

        return "(#{$calleeType})::{$this->methodName}($argsTypes)";
    }

    public function dependencies(): array
    {
        if ($this->callee instanceof AbstractReferenceType) {
            return $this->callee->dependencies();
        }

        if (is_string($this->callee)) {
            return [
                new MethodDependency($this->callee, $this->methodName),
            ];
        }

        return [];
    }
}
