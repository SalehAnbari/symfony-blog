<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin'), IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[
        Route('/', name: 'admin_post_index', methods: ['GET']),
    ]
    public function index(PostRepository $posts): Response
    {
        $authorPosts = $posts->findBy(array('author' => $this->getUser()), ['publishedAt' => 'DESC']);

        return $this->render('admin/dashboard/index.html.twig', ['posts' => $authorPosts]);
    }

    #[Route('/filter', name: 'admin_post_filter', methods: ['GET'])]
    public function filter(PostRepository $posts): Response
    {
        $lessThanFiveCommentPosts = $posts->filterPostsByComment(6, 2);
        return $this->render('admin/dashboard/show_filtered_posts.html.twig', ['posts' => $lessThanFiveCommentPosts]);
    }

    #[Route('/new', name: 'admin_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/dashboard/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'admin_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        $this->denyAccessUnlessGranted('show', $post, 'Posts can only be shown to their authors.');

        return $this->render('admin/dashboard/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'admin_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'post', message: 'Posts can only be edited by their authors.')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('admin/dashboard/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_post_delete', methods: ['POST'])]
    #[IsGranted('delete', subject: 'post')]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_post_index');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('admin_post_index');
    }


}
