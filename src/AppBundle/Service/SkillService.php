<?php

namespace AppBundle\Service;

class SkillService
{
    public function generateSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9_]/ui', '_', $slug);
        return $slug;
    }
}