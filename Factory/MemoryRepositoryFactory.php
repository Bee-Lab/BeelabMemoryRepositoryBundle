<?php

namespace Beelab\MemoryRepositoryBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;

class MemoryRepositoryFactory implements RepositoryFactory
{
    protected $repositoryList;

    /**
     * {@inheritdoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $entityName = ltrim($entityName, '\\');

        if (isset($this->repositoryList[$entityName])) {
            return $this->repositoryList[$entityName];
        }

        $repository = $this->createRepository($entityManager, $entityName);

        $this->repositoryList[$entityName] = $repository;

        return $repository;
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
     * @param string                               $entityName    The name of the entity.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $metadata            = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName;

        // TODO se l'Entity non specifica alcun Repository, viene usato EntityRepository standard di Doctrine
        if ($repositoryClassName === null) {
            $configuration       = $entityManager->getConfiguration();
            $repositoryClassName = $configuration->getDefaultRepositoryClassName();
        } else {
            // TODO qui bisogna seguire una convenzione sul nome del repository in memoria
            $repositoryClassName = str_replace('\\Repository\\', '\\MemoryRepository\\', $repositoryClassName);
        }

        return new $repositoryClassName($entityManager, $metadata);
    }
}
