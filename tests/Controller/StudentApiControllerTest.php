<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Entity\ProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class StudentApiControllerTest extends WebTestCase
{
    private const API_STUDENTS = '/api/students';
    private const API_GROUPS = '/api/groups';

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testDeleteStudentSuccessfullyRemovesFromGroupAndProject(): void
    {
        $project = $this->createProject(['title' => 'Test Project', 'amountOfGroups' => 1, 'maxStudentsPerGroup' => 2]);
        $group = $this->createGroup(['name' => 'Test Group'], $project);
        $studentId = $this->createStudent(['name' => 'John', 'surname' => 'Doe']);

        $this->assignStudentToGroup($studentId, $group->getId());

        $this->deleteStudent($studentId);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->assertStudentDoesNotExist($studentId);

        $this->assertStudentNotInGroup($studentId, $group->getId());
    }

    public function testDeleteNonExistentStudent(): void
    {
        $this->deleteStudent(999);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = $this->getDecodedResponse();
        $this->assertArrayHasKey('errors', $response);
        $this->assertContains('Student not found.', $response['errors']);
    }

    private function createStudent(array $studentData): int
    {
        $this->client->request(
            'POST',
            self::API_STUDENTS,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($studentData)
        );
        $this->assertResponseIsSuccessful();

        $response = $this->getDecodedResponse();
        $this->assertArrayHasKey('student', $response);
        $this->assertArrayHasKey('id', $response['student']);

        return $response['student']['id'];
    }

    private function createGroup(array $groupData, Project $project): ProjectGroup
    {
        $group = new ProjectGroup();
        $group->setName($groupData['name']);
        $group->setProject($project);

        $this->persistEntity($group);

        return $group;
    }

    private function createProject(array $projectData): Project
    {
        $project = new Project();
        $project->setTitle($projectData['title']);
        $project->setAmountOfGroups($projectData['amountOfGroups']);
        $project->setMaxStudentsPerGroup($projectData['maxStudentsPerGroup']);

        $this->persistEntity($project);

        return $project;
    }

    private function assignStudentToGroup(int $studentId, int $groupId): void
    {
        $this->client->request(
            'POST',
            self::API_GROUPS . "/{$groupId}/assign",
            [ 'student' => $studentId ],
            [],
            ['content_type' => 'application/x-www-form-urlencoded']
        );

        $this->assertResponseIsSuccessful();
    }

    private function deleteStudent(int $studentId): void
    {
        $this->client->request('DELETE', self::API_STUDENTS . "/{$studentId}");
    }

    private function assertStudentDoesNotExist(int $studentId): void
    {
        $this->client->request('GET', self::API_STUDENTS);
        $students = $this->getDecodedResponse();

        foreach ($students as $student) {
            $this->assertNotEquals($studentId, $student['id'], 'Deleted student should not exist in the list');
        }
    }

    private function assertStudentNotInGroup(int $studentId, int $groupId): void
    {
        $this->client->request('GET', self::API_GROUPS . "/{$groupId}");
        $group = $this->getDecodedResponse();

        foreach ($group['students'] as $student) {
            $this->assertNotEquals($studentId, $student['id'], 'Deleted student should not exist in the group');
        }
    }

    private function getDecodedResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    private function persistEntity(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}