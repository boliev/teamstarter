<?php

namespace AppBundle\Search\ProjectIndexer;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PostgresIndexer implements ProjectIndexerInterface
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
        $this->logger->info(sprintf('Indexing all projects to PostgreSql'));
        $sql = $this->getSql();

        try {
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error while indexing all projects: %d %s',
                $e->getCode(),
                $e->getMessage()
            ));
        }
    }

    public function indexOne(Project $project): void
    {
        $this->logger->info(sprintf('Indexing project %s to PostgreSql', $project->getName()));
        $sql = $this->getSql();
        $sql .= ' AND p.id = :projectId';
        try {
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->bindValue('projectId', $project->getId());
            $query->execute();
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error while indexing project %s(%d): %d %s',
                $project->getName(),
                $project->getId(),
                $e->getCode(),
                $e->getMessage()
            ));
        }
    }

    protected function getSql(): string
    {
        return "UPDATE projects p 
SET search = srch.search_str
FROM (
       SELECT pr.id,
         setweight(to_tsvector(coalesce(pr.name,'')), 'A') ||
         setweight(to_tsvector(coalesce(pr.description,'')), 'B') ||
         setweight(to_tsvector(coalesce(string_agg(s2.title, ' '),'')), 'C')
           AS search_str
       FROM projects pr
       LEFT JOIN project_open_roles por on pr.id = por.project_id
       LEFT JOIN project_open_role_skills skill on por.id = skill.open_role_id
       LEFT JOIN skill s2 on skill.skill_id = s2.id
       WHERE por.vacant = true 
       GROUP BY pr.id
     ) AS srch
     WHERE srch.id = p.id
     ";
    }
}
