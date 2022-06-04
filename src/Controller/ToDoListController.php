<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Task;

class ToDoListController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}
    #[Route('/', name: 'app_to_do_list')]
    public function index()
    {
        $tasks= $this->doctrine->getRepository(Task::class)->findBy([],['id' => 'DESC']);
        return $this->render('index.html.twig',['tasks'=> $tasks]);
    }

    #[Route('/create', name: 'create_task', methods: "POST")]
    public function create(Request $request)
    {
        $title = trim($request->request->get('title'));
        if(empty($title))
        return $this-> redirectToRoute('app_to_do_list');

        $entityManager = $this->doctrine-> getManager();

        $task = new Task();
        $task -> setTitle($title);

        $entityManager -> persist($task);
        $entityManager -> flush();

        return $this-> redirectToRoute('app_to_do_list');
    }

    #[Route('/switch-status/{id}', name: 'switch_status')]
    public function switchStatus($id)
    {
        $entityManager = $this->doctrine-> getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        $task->setStatus(! $task->isStatus());
        
        $entityManager->flush();

        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/delete/{id}', name: 'task_delete')]
    public function delete(Task $id)
    {
        $entityManager = $this->doctrine-> getManager();
        $entityManager->remove($id);
        $entityManager->flush($id);
        return $this->redirectToRoute('app_to_do_list');
    }
}
