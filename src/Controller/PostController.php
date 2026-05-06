<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/', name: 'app_blog_index')]
    public function index(PostRepository $postRepository, Request $request, CategoryRepository $categoryRepository): Response
    {
        $search = $request->query->get('q');
        $categoryId = $request->query->get('category');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 6;

        $paginator = $postRepository->findBySearch($search, $categoryId, $page, $limit);
        $totalPages = (int) ceil(count($paginator) / $limit);

        return $this->render('post/index.html.twig', [
            'posts' => $paginator,
            'categories' => $categoryRepository->findAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();

        $post->setCreatedAt(new \DateTimeImmutable());

        $user = $this->getUser();

        $post->setAuthor($user);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => false
        ]);
    }
    #[Route('/blog/edit/{id}', name: 'app_blog_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        if ($post->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Modification interdite : vous n’êtes pas l’auteur de cet article.');
            return $this->redirectToRoute('app_blog_index');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Article mis à jour avec succès !');
            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => true
        ]);
    }
    #[Route('/delete/{id}', name: 'app_post_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($post->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('danger', 'Votre post a bien été supprimé');
        }

        return $this->redirectToRoute('app_blog_index');
    }
}