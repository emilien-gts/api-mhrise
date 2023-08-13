<?php

namespace App\Synchronizer;

use App\Entity\Monster;
use App\Repository\ReferentialRepository;
use App\Service\ReferentialFactory;
use Doctrine\ORM\EntityManagerInterface;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use pcrov\JsonReader\JsonReader;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractSynchronizer
{
    public function __construct(
        protected readonly JsonReader $reader,
        protected readonly EntityManagerInterface $em,
        protected readonly SynchronizerHelper $helper,
        private readonly ReferentialRepository $referentialRepository,
        private readonly ReferentialFactory $referentialFactory,
        private readonly KernelInterface $kernel
    ) {
    }

    abstract public function sync(): void;

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Exception
     */
    protected function openJson(string $filename, string $fileDataRootName): void
    {
        $path = \sprintf('%s/src/Synchronizer/Data/%s', $this->kernel->getProjectDir(), $filename);
        if (!\file_exists($path)) {
            throw new \Exception(\sprintf('File at path "%s" does not exists.', $path));
        }

        $this->reader->open($path);
        $this->reader->read($fileDataRootName);
    }

    protected function syncReferentialList(array $data, object $object, string $repositoryMethod, string $factoryMethod): void
    {
        foreach ($data as $item) {
            $this->syncReferentialItem($item, $object, $repositoryMethod, $factoryMethod);
        }
    }

    protected function syncReferentialItem(string $item, object $object, string $repositoryMethod, string $factoryMethod): void
    {
        $element = $this->referentialRepository->{$repositoryMethod}($item);
        $element = $element ?? $this->referentialFactory->{$factoryMethod}($item);

        if ('findAilment' === $repositoryMethod && \is_a($object, Monster::class)) {
            $object->ailments->add($element);
        } elseif ('findElement' === $repositoryMethod && \is_a($object, Monster::class)) {
            $object->elements->add($element);
        } elseif ('findWeakness' === $repositoryMethod && \is_a($object, Monster::class)) {
            $object->weakness->add($element);
        } elseif ('findMonsterType' === $repositoryMethod && \is_a($object, Monster::class)) {
            $object->type = $element;
        }
    }
}
