<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class TodoController extends AbstractController
{
    public function __construct(
        private readonly TodoRepository $todoRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'todo_index', methods: ['GET'])]
    public function index(): Response
    {
        $todos = $this->todoRepository->findAllOrderedByPriorityAndDate();
        $stats = $this->todoRepository->getStatistics();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoRepository->save($todo, true);

            $this->addFlash('success', 'Todo created successfully!');

            return $this->redirectToRoute('todo_index');
        }

        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'todo_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Todo $todo): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Todo updated successfully!');

            return $this->redirectToRoute('todo_index');
        }

        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'todo_toggle', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggle(Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('toggle'.$todo->getId(), $request->request->get('_token'))) {
            $todo->toggle();
            $this->entityManager->flush();

            $this->addFlash('success', $todo->isCompleted() ? 'Todo completed!' : 'Todo reopened!');
        }

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/{id}/delete', name: 'todo_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $this->todoRepository->remove($todo, true);

            $this->addFlash('success', 'Todo deleted successfully!');
        }

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/api/todos', name: 'api_todo_list', methods: ['GET'])]
    public function apiList(): JsonResponse
    {
        $todos = $this->todoRepository->findAllOrderedByPriorityAndDate();

        return $this->json([
            'todos' => array_map(fn(Todo $todo) => $this->serializeTodo($todo), $todos),
            'stats' => $this->todoRepository->getStatistics(),
        ]);
    }

    #[Route('/api/todos/{id}/toggle', name: 'api_todo_toggle', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function apiToggle(Todo $todo): JsonResponse
    {
        $todo->toggle();
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'todo' => $this->serializeTodo($todo),
        ]);
    }

    #[Route('/health', name: 'health_check', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            // Test database connection
            $this->entityManager->getConnection()->executeQuery('SELECT 1');
            $dbStatus = 'healthy';
        } catch (\Throwable $e) {
            $dbStatus = 'unhealthy: ' . $e->getMessage();
        }

        return $this->json([
            'status' => $dbStatus === 'healthy' ? 'healthy' : 'unhealthy',
            'database' => $dbStatus,
            'timestamp' => (new \DateTime())->format(\DATE_ATOM),
        ], $dbStatus === 'healthy' ? 200 : 503);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeTodo(Todo $todo): array
    {
        return [
            'id' => $todo->getId(),
            'title' => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'completed' => $todo->isCompleted(),
            'priority' => $todo->getPriority(),
            'priorityLabel' => $todo->getPriorityLabel(),
            'dueDate' => $todo->getDueDate()?->format('Y-m-d'),
            'isOverdue' => $todo->isOverdue(),
            'createdAt' => $todo->getCreatedAt()->format(\DATE_ATOM),
            'updatedAt' => $todo->getUpdatedAt()->format(\DATE_ATOM),
            'completedAt' => $todo->getCompletedAt()?->format(\DATE_ATOM),
        ];
    }
}
