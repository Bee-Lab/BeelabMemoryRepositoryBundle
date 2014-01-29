BeelabMemoryRepositoryBundle
============================

The purpose of this bundle is using in-memory repositories with Doctrine.
The main usage is during tests, when you can avoid to interact with database,
without changing any implementation.

Installation
------------

Add these lines to your composer.json:
```json
    "require": {
        "doctrine/orm": "2.5.*@dev",
        "doctrine/dbal": "2.5.*@dev",
        "doctrine/doctrine-bundle": "~1.3@beta",
        "beelab/memory-repository-bundle": "0.1.*@dev"
    }
```
then run ``composer update``.

Usage
-----

Suppose you are using an Entity named Article. In your entity, you must add
``repositoryClass`` value to ``@ORM\Entity`` annotation. For example:
```php
<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Acme\DemoBundle\Repository\ArticleRepository")
 */
class Article
{
    // ...
}
```
So, you need an ArticleRepositoryClass, that extends Doctrine's EntityRepository.
Now, create a new class, like this:
```php
<?php

namespace Acme\DemoBundle\MemoryRepository;

use Acme\DemoBundle\Entity\Article;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class ArticleRepository implements ObjectRepository
{
    protected $_entityName, $_em, $_class;

    /**
     * @param EntityManager         $em
     * @param Mapping\ClassMetadata $class
     */
    public function __construct($em, ClassMetadata $class)
    {
        $this->_entityName = $class->name;
        $this->_em         = $em;
        $this->_class      = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        // implement as you prefer...
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        // implement as you prefer...
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // implement as you prefer...
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        // this is just an example...
        if (isset($criteria['slug']) && $criteria['slug'] == 'an-article') {
            $article = new Article();
            $article
                ->setTitle('An article')
                ->setText('Lorem ipsum dolor sit amet.')
            ;

            return $article;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getClassName()
    {
        return $this->_entityName;
    }
}
```
For now, you must use directories (and namespaces) named "Repository" and "EntityRepository". This is a bit ugly, we hope to improve.

Finally, add this to your config_test.yml:
```yaml
doctrine:
    orm:
        repository_factory: beelab.repository.factory
```
Done! Now, when an Article object is retrieved in a test, it's not retrieved from database, but from your ArticleRepository, depending on your logic.
