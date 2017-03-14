<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\TodoType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * Class TodoController
 * @package AppBundle\Controller
 */
class TodoController extends Controller
{
    /**
     * Index action.
     */
    public function indexAction()
    {
        $todos = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['trashed' => false], ['date' => 'DESC']);

        $form = $this
            ->createForm(new TodoType(), null, [
                'action' => $this->generateUrl('todo_add'),
            ])
            ->add('submit', 'submit', [
                'label' => 'Ajouter',
                'attr'  => [
                    'class' => 'btn btn-success'
                ]]);

        return $this->render('AppBundle:Todo:index.html.twig', [
            'todos' => $todos,
            'form'=>$form->createView()
        ]);
    }

    public function addAction(Request $request)
    {
        $todo = new Todo();

        $form = $this
            ->createForm(new TodoType(), $todo, [
                'action' => $this->generateUrl('todo_index'),
            ])
            ->add('submit', 'submit', [
                'label' => 'Modifier',
                'attr'  => [
                    'class' => 'btn btn-warning'
                ]]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash('success', 'Le Todo a bien été créé.');

            return $this->redirectToRoute('todo_index', [
                'todo' => $todo->getId(),
            ]);
        }
        $todos = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['trashed' => false], ['date' => 'DESC']);

        return $this->render('AppBundle:Todo:index.html.twig', [
            'form' => $form->createView(),
            'todos' => $todos,
        ]);
    }

    public function editAction(Request $request)
    {
        $todo = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($request->attributes->get('todoId'));

        $formAction = $this->generateUrl('todo_edit', [
           'todoId' => $todo->getId(),
        ]);

        $form = $this
            ->createForm(new TodoType(), $todo, [
                'action' => $formAction,
            ])
            ->add('submit', 'submit', [
                'label' => 'Modifier',
                'attr'  => [
                'class' => 'btn btn-warning'
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash('success', 'Le todo a bien été modifié.');

            return $this->redirectToRoute('todo_index');
        }

        $todos = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['trashed' => false], ['date' => 'DESC']);

        return $this->render('AppBundle:Todo:index.html.twig', [
            'todos' => $todos,
            'form' => $form->createView(),
        ]);
    }

    public function trashAction(Request $request)
    {
        //à faire

        $todo = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(array(
                'trashed'=> 1,
                'id'=>$request->attributes->get('todoId')
            ));

        return $this->redirectToRoute('todo_trash');
    }

    public function trashList(Request $request)
    {

        $todo = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(array(
                'trashed'=> 1,
                'id'=>$request->attributes->get('todoId')
            ));

        return $this->render('AppBundle:Todo:_trashed_list.html.twig', [
            'todos' => $todos]);
    }

}
