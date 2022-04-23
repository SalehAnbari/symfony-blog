<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
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

    #[Route('/article/{slug}', name: 'read_post')]
    public function read(Post $post, Request $request, ManagerRegistry $doctrine): Response
    {
        $views = $post->getPostViews();
        $post->setPostViews($views + 1);
        $doctrine->getManager()->persist($post);
        $doctrine->getManager()->flush();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if($this->isGranted('ROLE_USER')){
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->persist($comment);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("read_post", ['slug' => $post->getSlug()]);
        }

        return $this->render("blog/read.html.twig", [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }
}
