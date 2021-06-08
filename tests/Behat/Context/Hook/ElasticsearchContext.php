<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusCrossSellingPlugin\Behat\Service\ElasticsearchCommands;

final class ElasticsearchContext implements Context
{
    /** @var ElasticsearchCommands */
    private $commands;

    public function __construct(ElasticsearchCommands $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @BeforeScenario
     */
    public function purgeElasticsearch(): void
    {
        $this->commands->resetAllIndexes();
    }
}
