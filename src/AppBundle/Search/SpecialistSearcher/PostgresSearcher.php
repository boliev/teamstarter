<?php

namespace AppBundle\Search\SpecialistSearcher;

use Doctrine\ORM\EntityManagerInterface;

class PostgresSearcher implements SpecialistSearcherInterface
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
        $sql = 'SELECT id, ts_rank( search::tsvector, to_tsquery(:query), 0) as rank
FROM users
WHERE to_tsquery(:query) @@ search::tsvector
ORDER BY rank desc';

        $stmt = $conn->prepare($sql);

        $stmt->execute(['query' => $query]);

        $res = $stmt->fetchAll();

        return array_column($res, 'id');
    }

    private function prepareQuery(string $query): string
    {
        $query = explode(' ', $query);
        $query = implode(' | ', $query);

        return $query;
    }
}
