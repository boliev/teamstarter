<?php

namespace App\Search\SpecialistIndexer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PostgresIndexer implements SpecialistIndexerInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function indexAll(): void
    {
        $this->logger->info(sprintf('Indexing all specialists to PostgreSql'));
        $sql = $this->getSql();

        try {
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error while indexing all specialists: %d %s',
                $e->getCode(),
                $e->getMessage()
            ));
        }
    }

    public function indexOne(User $user): void
    {
        $this->logger->info(sprintf('Indexing specialist %s to PostgreSql', $user->getFullName()));
        $sql = $this->getSql();
        $sql .= ' AND u.id = :userId';
        try {
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->bindValue('userId', $user->getId());
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error while indexing specialist %s(%d): %d %s',
                $user->getFullName(),
                $user->getId(),
                $e->getCode(),
                $e->getMessage()
            ));
        }
    }

    protected function getSql(): string
    {
        return "UPDATE users u 
SET search = srch.search_str
FROM (
       SELECT u.id,
         setweight(to_tsvector(coalesce(u.city, '')), 'A') ||
         setweight(to_tsvector(coalesce(c2.name, '')), 'A') ||
         setweight(to_tsvector(coalesce(c2.ru, '')), 'A') ||
         setweight(to_tsvector(coalesce(string_agg(s.title, ' '),'')), 'A') ||
         setweight(to_tsvector(coalesce(string_agg(a.name, ' '),'')), 'A') ||
         setweight(to_tsvector(coalesce(string_agg(s3.title, ' '),'')), 'A') 
           AS search_str
       FROM users u
       LEFT JOIN user_skills us on u.id = us.user_id
       LEFT JOIN skill s on s.id = us.skill_id
       LEFT JOIN country c2 on u.country = c2.code
       LEFT JOIN countries_alias a on c2.code = a.country
       LEFT JOIN user_specializations usp on usp.user_id = u.id
       LEFT JOIN specialization s3 on usp.specialization_id = s3.id
       GROUP BY u.id, c2.name, c2.ru
     ) AS srch
     WHERE srch.id = u.id
     ";
    }
}
