<?php

namespace Kutny\DoctrineBundle\Metadata;

use Doctrine\ORM\EntityManager;

class AttributesToColumnsTranslator
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function translate(array $attributes, $entityClass)
    {
        $quoteStrategy = $this->entityManager->getConfiguration()->getQuoteStrategy();
        $platform = $this->entityManager->getConnection()->getDatabasePlatform();
        $metadata = $this->entityManager->getClassMetadata($entityClass);

        $output = [];

        foreach ($attributes as $attribute) {
            $output[$attribute] = $quoteStrategy->getColumnName($attribute, $metadata, $platform);
        }

        return $output;
    }
}
