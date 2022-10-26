<?php

namespace App\Controller\Admin\Ajax;

use App\Entity\Task\Task;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @throws JsonException
     */
    #[Route('/oversight/ajax/add-task', name: 'ajax.add-task', methods: 'post')]
    public function addTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $task = (new Task())
            ->setTodo($data['todo']);

        $this->em->persist($task);
        $this->em->flush();

        $response = [
            'id' => $task->getId(),
            'todo' => $task->getTodo()
        ];

        return new JsonResponse($response);
    }

    /**
     * @throws JsonException
     */
    #[Route('/oversight/ajax/remove-task', name: 'ajax.remove-task', methods: 'post')]
    public function removeTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $task = ($this->em->getRepository(Task::class))
            ->find($data['todo']);

        if (!$task) {
            return new JsonResponse('ERROR', '500');
        }
        $this->em->remove($task);
        $this->em->flush();

        return new JsonResponse('DELETE');
    }
}
