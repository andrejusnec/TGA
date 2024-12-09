<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\StudentRepositoryInterface;
use App\Service\StudentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StudentApiController extends AbstractController
{
    #[Route('/api/students', name: 'api_add_student', methods: ['POST'])]
    public function addStudent(Request $request, StudentServiceInterface $studentService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $student = $studentService->createStudentFromRequestData($data);

        return new JsonResponse(
            [
                'message' => 'Student added successfully!',
                'student' => [
                    'id' => $student->getId(),
                    'name' => $student->getName(),
                    'surname' => $student->getSurname(),
                ],
            ],
            Response::HTTP_CREATED,
            ['Location' => '/api/students/' . $student->getId()]
        );
    }

    #[Route('/api/students/{id}', name: 'api_delete_student', methods: ['DELETE'])]
    public function deleteStudent(int $id, StudentServiceInterface $studentService): JsonResponse
    {
        $studentService->deleteStudent($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/students', name: 'api_get_all_students', methods: ['GET'])]
    public function getAllStudents(StudentRepositoryInterface $studentRepository): JsonResponse
    {
        $students = $studentRepository->findAll();

        $response = array_map(function ($student) {
            return [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'surname' => $student->getSurname(),
            ];
        }, $students);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
