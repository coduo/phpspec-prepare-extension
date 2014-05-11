<?php

namespace spec\Coduo\PhpSpec\Annotation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_return_null_when_there_is_no_method_before_example(\ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getDocComment()->willReturn(false);

        $this->getBeforeMethodName($reflectionMethod)->shouldReturn(null);
    }

    function it_return_null_when_there_is_no_before_annotation_in_comment(\ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getDocComment()->willReturn(<<<DOC_BLOCK
/**
 *
 */
DOC_BLOCK
);
        $this->getBeforeMethodName($reflectionMethod)->shouldReturn(null);
    }

    function it_return_before_method_name_when_before_annotaion_exist_in_comment(\ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getDocComment()->willReturn(<<<DOC_BLOCK
/**
 * @before prepareExample
 */
DOC_BLOCK
        );
        $this->getBeforeMethodName($reflectionMethod)->shouldReturn('prepareExample');
    }
}
