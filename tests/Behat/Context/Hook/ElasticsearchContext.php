<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
