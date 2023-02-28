<?php

namespace App\Command;

use App\DataFixtures\Factory\UserFactory;
use App\Entity\Lesson\LessonStatus;
use App\Entity\SchoolClass\SchoolClassStatus;
use App\Enum\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:init
class InitDatabaseCommand extends Command
{
    protected static $defaultName = 'db:init';
    protected static $defaultDescription = 'The command adds basic data to database';
    private $entityManager;
    private $userFactory;

    public function __construct(EntityManagerInterface $entityManager, UserFactory $userFactory)
    {
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createOwner();
        $this->initSchoolClassStatuses();
        $this->initLessonStatuses();

        $io->success('OK');
        return Command::SUCCESS;
    }

    private function createOwner()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\UserType\Owner')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE owner AUTO_INCREMENT = 1");

        $this->userFactory->create([
            'email' => "owner@wp.pl",
            'roles' => UserType::OWNER,
            'password' => "owner"
        ], true);
    }

    private function initSchoolClassStatuses()
    {
        $repository = $this->entityManager->getRepository(SchoolClassStatus::class);

        if (empty($repository->findAll())) {
            $this->entityManager->createQuery('DELETE FROM App\Entity\SchoolClass\SchoolClassStatus')->execute();
            $this->entityManager->getConnection()->exec("ALTER TABLE school_class_status AUTO_INCREMENT = 1");


            $repository->add((new SchoolClassStatus())->setName(SchoolClassStatus::ACTIVE));
            $repository->add((new SchoolClassStatus())->setName(SchoolClassStatus::ARCHIVED), true);
        }
    }

    private function initLessonStatuses()
    {
        $repository = $this->entityManager->getRepository(LessonStatus::class);

        if (empty($repository->findAll())) {

            $this->entityManager->createQuery('DELETE FROM App\Entity\Lesson\LessonStatus')->execute();
            $this->entityManager->getConnection()->exec("ALTER TABLE lesson_status AUTO_INCREMENT = 1");

            $data = [
                [
                    "id" => 1,
                    "description" => "zgodnie z planem",
                    "background" => "bg-success"
                ],
                [
                    "id" => 2,
                    "description" => "odwołane",
                    "background" => "bg-danger"
                ],
                [
                    "id" => 3,
                    "description" => "zmodyfikowane",
                    "background" => "bg-info"
                ]
            ];

            foreach ($data as $index => $item) {
                $lessonStatus = new LessonStatus($item['id'], $item['description'], $item['background']);
                $repository->add($lessonStatus, $index + 1 == count($data));
            }
        }
    }
}