<?php

namespace Coduo\PhpSpec\Prepare\Runner\Maintainer;

use Coduo\PhpSpec\Prepare\Annotation\Parser;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Wrapper\Collaborator;
use PhpSpec\Wrapper\Unwrapper;
use Prophecy\Prophet;

class BeforeMaintainer implements MaintainerInterface
{
    private $beforeMethod;

    /**
     * @var Prophet
     */
    private $prophet;

    /**
     * @param Unwrapper $unwrapper
     */
    public function __construct(Unwrapper $unwrapper)
    {
        $this->prophet = new Prophet(null, $unwrapper, null);
    }

    /**
     * @param ExampleNode $example
     *
     * @return bool
     */
    public function supports(ExampleNode $example)
    {
        $parser = new Parser();
        $this->beforeMethod = $parser->getBeforeMethodName($example->getFunctionReflection());
        if (!isset($this->beforeMethod)) {
            return false;
        }

        return $example->getSpecification()->getClassReflection()->hasMethod($this->beforeMethod);
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        $spec = $example->getSpecification()->getClassReflection()->newInstance();
        $beforeMethod = $example->getSpecification()->getClassReflection()->getMethod($this->beforeMethod);
        $this->createMissingCollabolators($collaborators, $beforeMethod);
        $beforeMethod->invokeArgs($spec, $collaborators->getArgumentsFor($beforeMethod));
    }

    /**
     * @param ExampleNode            $example
     * @param SpecificationInterface $context
     * @param MatcherManager         $matchers
     * @param CollaboratorManager    $collaborators
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context,
                             MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 49;
    }

    /**
     * @param CollaboratorManager $collaborators
     * @param \ReflectionMethod $beforeMethod
     */
    private function createMissingCollabolators(CollaboratorManager $collaborators, \ReflectionMethod $beforeMethod)
    {
        foreach ($beforeMethod->getParameters() as $parameter) {
            if (!$collaborators->has($parameter->getName())) {
                $collaborator = new Collaborator($this->prophet->prophesize());
                if (null !== $class = $parameter->getClass()) {
                    $collaborator->beADoubleOf($class->getName());
                }

                $collaborators->set($parameter->getName(), $collaborator);
            }
        }
    }
}
