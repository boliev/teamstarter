<?php

namespace AppBundle\Search\ProjectSearcher;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class PostgresSearcher implements ProjectSearcherInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $query
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function search(string $query): array
    {
        $query = $this->prepareQuery($query);
        $conn = $this->entityManager->getConnection();
        $sql = sprintf('SELECT DISTINCT p.id, ts_rank( search::tsvector, to_tsquery(:query), 0) as rank, created_at
FROM projects p
INNER JOIN project_open_roles por on p.id = por.project_id
WHERE to_tsquery(:query) @@ search::tsvector
AND p.progress_status = \'%s\'
AND por.vacant = true 
ORDER BY rank desc, created_at desc',
        Project::STATUS_PUBLISHED
        );

        $stmt = $conn->prepare($sql);

        $stmt->execute(['query' => $query]);

        $projects = [];
        $projectRepository = $this->entityManager->getRepository(Project::class);
        while ($project = $stmt->fetch()) {
            $projects[] = $projectRepository->find($project['id']);
        }

        return $projects;
    }

    private function prepareQuery(string $query): string
    {
        $query = explode(' ', $query);
        $query = implode(' | ', $query);

        return $query;
    }
}
