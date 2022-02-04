<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Behat\Service;

use FOS\ElasticaBundle\Event\PostIndexPopulateEvent;
use FOS\ElasticaBundle\Event\PreIndexPopulateEvent;
use FOS\ElasticaBundle\Index\IndexManager;
use FOS\ElasticaBundle\Index\Resetter;
use FOS\ElasticaBundle\Persister\PagerPersisterInterface;
use FOS\ElasticaBundle\Persister\PagerPersisterRegistry;
use FOS\ElasticaBundle\Provider\PagerProviderRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ElasticsearchCommands
{
    private EventDispatcherInterface $dispatcher;

    private IndexManager $indexManager;

    private PagerProviderRegistry $pagerProviderRegistry;

    private PagerPersisterRegistry $pagerPersisterRegistry;

    private PagerPersisterInterface $pagerPersister;

    private Resetter $resetter;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        IndexManager $indexManager,
        PagerProviderRegistry $pagerProviderRegistry,
        PagerPersisterRegistry $pagerPersisterRegistry,
        Resetter $resetter
    ) {
        $this->dispatcher = $dispatcher;
        $this->indexManager = $indexManager;
        $this->pagerProviderRegistry = $pagerProviderRegistry;
        $this->pagerPersisterRegistry = $pagerPersisterRegistry;
        $this->resetter = $resetter;
    }

    public function resetAllIndexes(): void
    {
        $this->resetter->resetAllIndexes(false, true);
    }

    public function populateAllIndexes(): void
    {
        $this->pagerPersister = $this->pagerPersisterRegistry->getPagerPersister('in_place');

        $indexes = array_keys($this->indexManager->getAllIndexes());

        $options = [
            'delete' => true,
            'reset' => true,
        ];

        foreach ($indexes as $index) {
            $event = new PreIndexPopulateEvent($index, true, $options);
            $this->dispatcher->dispatch($event, PreIndexPopulateEvent::class);

            if ($event->isReset()) {
                $this->resetter->resetIndex($index, true);
            }

            $providers = array_keys($this->pagerProviderRegistry->getProviders());
            $types = array_filter($providers, fn (string $provider) => $provider === $index);

            foreach ($types as $type) {
                $this->populateIndexType($index, $type, $event->getOptions());
            }

            $this->dispatcher->dispatch($event, PreIndexPopulateEvent::class);

            $this->refreshIndex($index);
        }
    }

    private function populateIndexType(
        string $index,
        string $type,
        array $options
    ): void {
        $event = new PostIndexPopulateEvent($index, false, $options);
        $this->dispatcher->dispatch($event, PostIndexPopulateEvent::class);

        if ($event->isReset()) {
            $this->resetter->resetIndex($index);
        }

        $provider = $this->pagerProviderRegistry->getProvider($index);

        $pager = $provider->provide($options);

        $options['indexName'] = $index;
        $options['typeName'] = $type;

        $this->pagerPersister->insert($pager, $options);

        $this->dispatcher->dispatch($event, PostIndexPopulateEvent::class);

        $this->refreshIndex($index);
    }

    private function refreshIndex(string $index): void
    {
        $this->indexManager->getIndex($index)->refresh();
    }
}
