<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Profile;
use App\Form\PostEditType;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PostController extends AbstractController
{

    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private ProfileRepository $profileRepository,
        private ImageUploaderService $imageUploaderService)
    {
    }

    #[Route('/posts/all/{id}', name: 'app_posts_index')]
    public function index(int $id, Request $request): Response
    {
        $profile = $this->profileRepository->find($id);
        if (!$profile)
            return new JsonResponse('no profile was found for this id!', 404);

        $currentProfile = $this->getUser()->getProfile();

        if ($currentProfile->getId() === $profile->getId())
            $posts = $this->postRepository->getForFollowers($profile, $request->get('page', 1), $request->get('limit', 12));
        else if ($currentProfile->getFollowing()->contains($profile, ))
            $posts = $this->postRepository->getForFollowers($profile, $request->get('page', 1), $request->get('limit', 12));
        else
            $posts = $this->postRepository->getPublicOnly($profile, $request->get('page', 1), $request->get('limit', 12));

        $resp = array();

        foreach ($posts as $post) {
            $resp[] = [
                'id' => $post->getId(),
                'show_link' => $this->generateUrl('app_posts_show', ['id' => $post->getId()]),
                'image' => '/images/posts/' . $profile->getId() . '/' . $post->getPicture(),
                'thumbnail' => '/images/posts/' . $profile->getId() . '/' . $post->getThumbnail(),
                'caption' => $post->getCaption(),
                'likes' => $post->getLikes()->count(),
                'liked' => $currentProfile->getLikedPosts()->contains($post) ? '1' : '0',
                'liking_link' => $this->generateUrl('app_posts_like', ['id' => $post->getId()])
            ];
        }

        return new JsonResponse($resp, 200);

    }


    #[Route('/posts/show/{id}', name: 'app_posts_show')]
    public function show(int $id): Response
    {
        $post = $this->postRepository->find($id);
        if (!$post)
            throw $this->createNotFoundException(
                'Post not found !'
            );
        return $this->render('post/index.html.twig', [
            'post' => $post,
            'posts_folder' => 'posts/' . $post->getProfile()->getId() .'/',
        ]);
    }

    #[Route('/posts/create', name: 'app_posts_create')]
    public function create(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirect(path('app_login'));

        $profile = $this->getUser()->getProfile();

        $post = new Post();
        $post->setProfile($profile);

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploaded = $form->get('uploadFile')->getData();
            if ($uploaded) {

                $post_image = $this->imageUploaderService->upload($uploaded, 'posts/'. $profile->getId());
                $post_thumbnail = $this->imageUploaderService->upload_thumbnail($uploaded, 'posts/'. $profile->getId());
                if (! $post_image || !$post_thumbnail) {
                    throw $this->createAccessDeniedException(
                        'could not upload the requested file'
                    );
                }
                $post->setPicture($post_image);
                $post->setThumbnail($post_thumbnail);
            }

            $post->setCreatedAt(new \DateTimeImmutable());
            $this->postRepository->save($post, true);
            return $this->redirectToRoute('app_home');

        }

        return $this->render('post/create.html.twig', [
            'create_form' => $form->createView(),
        ]);
    }

    #[Route('/posts/edit/{id}', name: 'app_posts_edit')]
    public function edit(Request $request, $id): Response
    {
        if (!$this->getUser())
            return new JsonResponse('you need to be login !', 403);

        $post = $this->postRepository->find($id);
        if (!$post)
            return new JsonResponse('post not found!', 404);

        $profile = $this->getUser()->getProfile();
        if ($post->getProfile() != $profile)
            return new JsonResponse('you can not edit a post that does not belong to you !', 403);

        $form = $this->createForm(PostEditType::class, $post, [
            'action' => $this->generateUrl('app_posts_edit', array('id' => $id)),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postRepository->save($post, true);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/edit.html.twig', [
            'edit_form' => $form->createView(),
            'post' => $post,
            'posts_folder' => 'posts/' . $post->getProfile()->getId() .'/',
        ]);
    }

    #[Route('/posts/delete/{id}', name: 'app_posts_delete')]
    public function delete(Request $request, $id): Response
    {
        if (!$this->getUser())
            return new JsonResponse('you need to be login !', 403);

        $post = $this->postRepository->find($id);
        if (!$post)
            return new JsonResponse('post not found!', 404);

        $profile = $this->getUser()->getProfile();
        if ($post->getProfile() != $profile)
            return new JsonResponse('you can not delete a post that does not belong to you !', 403);

        $this->postRepository->remove($post, true);
        return $this->redirectToRoute('app_home');
    }

    #[Route('/posts/like/{id}', name: 'app_posts_like')]
    public function like(int $id): Response
    {
        if (!$this->getUser())
            return new JsonResponse('you need to be login !', 403);
        $post = $this->postRepository->find($id);
        if (!$post)
            return new JsonResponse('post not found!', 404);
        if (!$this->getUser())
            return new JsonResponse('you need to be login !', 403);

        if ($post->getLikes()->contains($this->getUser()->getProfile()))
            $post->removeLike($this->getUser()->getProfile());
        else
            $post->addLike($this->getUser()->getProfile());
        $this->postRepository->save($post, true);
        return new JsonResponse(null, 200);
    }

}
