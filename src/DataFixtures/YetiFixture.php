<?php

namespace App\DataFixtures;

use App\Entity\Yeti;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class YetiFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../assets/data/YetiFixture.json';
        if (!file_exists($jsonFile)) {
            throw new \RuntimeException("Could not find JSON file: $jsonFile");
        }

        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);

        foreach ($data as $row) {
            $yeti = new Yeti();
            $yeti->setName($row['name'] ?? null);
            $yeti->setDescription($row['description'] ?? null);
            $yeti->setHeight($row['height'] ?? null);
            $yeti->setWeight($row['weight'] ?? null);
            $yeti->setImage($row['image'] ?? null);
            $yeti->setVotes($row['votes'] ?? 0);

            $manager->persist($yeti);
        }

        $manager->flush();
    }
}
