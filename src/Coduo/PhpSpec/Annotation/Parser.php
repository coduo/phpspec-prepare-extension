<?php

namespace Coduo\PhpSpec\Annotation;

class Parser
{
    const BEFORE_EXAMPLE_PATTERN = '/@before ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';

    /**
     * @param \ReflectionMethod $reflection
     * @return bool
     */
    public function getBeforeMethodName(\ReflectionMethod $reflection)
    {
        if (false === ($docComment = $reflection->getDocComment())) {
            return null;
        }

        if (0 !== preg_match(self::BEFORE_EXAMPLE_PATTERN, $docComment, $matches)) {
            return $matches[1];
        }
    }
}
