<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    public $manager;

    public function __construct(EntityManagerInterface $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/tasks", name="task_list", methods={"GET"})
     *
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        // $this->denyAccessUnlessGranted('LIST', $tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
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

            $user = $this->getUser();

            $task->setUser($user);

            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'La tâche à bien été ajoutée. '
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig', [
                'taskForm' => $form->createView()
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
        $this->denyAccessUnlessGranted('EDIT', $task);

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

        return $this->render('task/edit.html.twig', [
            'taskForm' => $form->createView()
        ]);
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
        $this->denyAccessUnlessGranted('TOGGLE', $task);

        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        $name = $task->getName();

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
        $this->denyAccessUnlessGranted('DELETE', $task);

        $this->manager->remove($task);
        $this->manager->flush();

        $this->addFlash(
            'success',
            'La tâche  à bien été supprimée. ');

        return $this->redirectToRoute('task_list');
    }
}
