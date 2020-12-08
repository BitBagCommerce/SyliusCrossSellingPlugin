<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusUpsellingPlugin\Behat\Service\ElasticsearchCommands;

final class ElasticsearchContext implements Context
{
    /** @var ElasticsearchCommands */
    private $commands;

    public function __construct(ElasticsearchCommands $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @Given the data is populated to Elasticsearch
     */
    public function theDataIsPopulatedToElasticsearch(): void
    {
        $this->commands->populateAllIndexes();
    }
}
