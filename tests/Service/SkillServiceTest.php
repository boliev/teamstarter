<?php

use PHPUnit\Framework\TestCase;

class SkillServiceTest extends TestCase
{
    /**
     * @dataProvider GenerateSlugProvider
     *
     * @param string $input
     * @param string $output
     */
    public function testGenerateSlug(string $input, string $output)
    {
        $entityManager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $skillService = new \App\Service\SkillService($entityManager);
        $slug = $skillService->generateSlug($input);
        $this->assertEquals($slug, $output);
    }

    /**
     * @return array
     */
    public function GenerateSlugProvider(): array
    {
        return [
            ['PHP', 'php'],
            ['Team lead', 'team_lead'],
            ['MySql 5.6', 'mysql_5_6'],
            ['МонгоDB', '_____db'],
            ['+-', '__'],
            [' JQuery ', 'jquery'],
        ];
    }
}
