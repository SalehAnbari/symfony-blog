<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Event\CommentCreatedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $posts = $doctrine->getRepository(Post::class)->getAllPosts();
        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }

    #[Route('/article-{id}', name: 'read_post')]
    public function read(Post $post,User $user, Request $request, ManagerRegistry $doctrine): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if($this->isGranted('ROLE_USER')){
            $comment->setAuthor($this->getUser()->getUserIdentifier());
            $comment->setPost($post);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->persist($comment);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("read_post", ["id" => $post->getId()]);
        }

        return $this->render("blog/read.html.twig", [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }
}
