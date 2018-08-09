<?php

namespace AppBundle\Search\SpecialistSearcher;

use AppBundle\Entity\User;
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
     * @return array|User[]
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
ORDER BY rank desc, updated_at desc';

        $stmt = $conn->prepare($sql);

        $stmt->execute(['query' => $query]);

        $userRepository = $this->entityManager->getRepository(User::class);
        $specialists = [];
        while ($specialist = $stmt->fetch()) {
            $specialists[] = $userRepository->find($specialist['id']);
        }

        return $specialists;
    }

    private function prepareQuery(string $query): string
    {
        $query = explode(' ', $query);
        $query = implode(' | ', $query);

        return $query;
    }
}
