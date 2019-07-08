<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public $manager;

    public function __construct(EntityManagerInterface $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/tasks", name="task_list")
     *
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll()
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'La tâche à bien été ajouté. '
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig', [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/task/{id}/edit", name="task_edit")
     * @param Task $task
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->flush();

            $this->addFlash(
                'success',
                'La tâche à bien été modifé. '
            );

            return $this->redirectToRoute('task_list');
        }
    }

    /**
     * @Route("/task/{id}/toggle", name="task_toggle")
     *
     * @param Task $task
     *
     * @return Response
     */
    public function toggleTask(Task $task): Response
    {
        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        $name = $task->getTitle();

        $this->addFlash(
            'success',
            'La tâche '.$name .' à bien été marquée comme faite. ');

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/task/{id}/delete", name="task_delete")
     *
     * @param Task $task
     *
     * @return Response
     */
    public function delete(Task $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();

        $this->addFlash(
            'success',
            'La tâche  à bien été supprimée. ');

        return $this->redirectToRoute('task_list');
    }
}
