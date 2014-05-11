<?php

namespace spec\Coduo\PhpSpec\Runner\Maintainer;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Loader\Node\SpecificationNode;
use PhpSpec\ObjectBehavior;
use PhpSpec\Runner\Maintainer\CollaboratorsMaintainer;
use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Argument;

class BeforeMaintainerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Unwrapper());
    }

    function it_does_not_support_examples_without_doc_blocks(ExampleNode $exampleNode, \ReflectionMethod $method)
    {
        $exampleNode->getFunctionReflection()->willReturn($method);
        $method->getDocComment()->willReturn(false);

        $this->supports($exampleNode)->shouldReturn(false);
    }

    /**
     * @before prepareExampleBeforeAnnotation
     */
    function it_does_not_support_examples_with_before_annotation_that_point_to_non_existing_method(ExampleNode $exampleNode,\ReflectionClass $specClass)
    {
        $specClass->hasMethod('prepareMethod')->willReturn(false);
        $this->supports($exampleNode)->shouldReturn(false);
    }

    /**
     * @before prepareExampleBeforeAnnotation
     */
    function it_support_examples_with_before_annotation_that_point_to_existing_method(
        ExampleNode $exampleNode,
        \ReflectionClass $specClass
    ) {
        $specClass->hasMethod('prepareMethod')->willReturn(true);
        $this->supports($exampleNode)->shouldReturn(true);
    }

    function prepareExampleBeforeAnnotation(ExampleNode $exampleNode, \ReflectionMethod $method, SpecificationNode $specificationNode, \ReflectionClass $specClass)
    {
        $exampleNode->getFunctionReflection()->willReturn($method);
        $method->getDocComment()->willReturn(<<<ANNOTATION
/**
 * @before prepareMethod
 */
ANNOTATION
        );
        $exampleNode->getSpecification()->willReturn($specificationNode);
        $specificationNode->getClassReflection()->willReturn($specClass);
    }

    function it_has_lower_priority_than_collabolators_maintainer()
    {
        $collabolatorsMaintainer = new CollaboratorsMaintainer(new Unwrapper());
        $this->getPriority()->shouldBeLowerThan($collabolatorsMaintainer->getPriority());
    }

    public function getMatchers()
    {
        return array(
            'beLowerThan' => function($subject, $value) {
                    return (int) $subject < (int) $value;
                }
        );
    }
}
