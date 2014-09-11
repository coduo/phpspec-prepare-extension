<?php

use Behat\Behat\Context\Context;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters = array())
    {

    }
}
