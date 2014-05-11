# PhpSpec prepare extension

Prepare examples before phpspec execute them.

## Installation

```
require: {
   "coduo/phpspec-prepare-extension": "dev-master"
}
```

## Usage

Enable exntesion in phpspec.yml file

```
extensions:
  - Coduo\PhpSpec\PrepareExtension
```

Write a spec:

```php
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
}
```

Write class for spec:

```php
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
```

Run php spec

```
$ console bin/phpspec run -f pretty
```

It should pass!
