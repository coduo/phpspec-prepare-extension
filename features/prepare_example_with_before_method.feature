Feature: Prepare example
  In order to prepare examples before phpspec execute them
  I need to enable PhpSpecPrepareExtension in phpspec.yml file

  Scenario: Prepare example with before method
    Given the PhpSpecPrepareExtension is enabled
    When I write a spec "spec/Coduo/Packagist/ClientSpec.php" with following code
    """
<?php

namespace spec\Coduo\Packagist;

use PhpSpec\ObjectBehavior;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client);
    }

    function prepareClientForSearch(ClientInterface $client, Response $response)
    {
        $client->get(
            'https://api.com/search.json',
            Argument::allOf(
                Argument::type('array'),
                Argument::withKey('q')
            )
        )->willReturn($response);

        $response->getBody(true)->willReturn(json_encode(array(
            'coduo/php-to-string',
            'coduo/php-matcher'
        )));
    }

    /**
     *  @before prepareClientForSearch
     */
    function it_return_list_of_packages(Response $response)
    {
        $this->search('coduo')->shouldReturn(array(
            'coduo/php-to-string',
            'coduo/php-matcher'
        ));
    }

    /**
     *  @before prepareClientForSearch
     */
    function it_return_list_of_packages_when_filter_is_not_a_string(Response $response)
    {
        $this->search('coduo', new \DateTime())->shouldReturn(array(
            'coduo/php-to-string',
            'coduo/php-matcher'
        ));
    }

    /**
     *  @before prepareClientForSearch
     */
    function it_return_list_of_filtered_packages(Response $response)
    {
        $this->search('coduo', 'string')->shouldReturn(array(
            'coduo/php-to-string',
        ));
    }
}
    """
    And I write a class "src/Coduo/Packagist/Client.php" with following code
    """
<?php

namespace Coduo\Packagist;

use Guzzle\Http\ClientInterface;

class Client
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function search($package, $filter = null)
    {
        $response = $this->client->get(
            'https://api.com/search.json',
            array('q' => $package)
        );

        $packages = json_decode($response->getBody(true), true);

        if (isset($filter) && is_string($filter)) {
            foreach ($packages as $index => $package) {
                if (false === strpos($package, $filter)) {
                    unset($packages[$index]);
                }
            }
        }

        return $packages;
    }
}
    """
    And I run phpspec
    Then it should pass
