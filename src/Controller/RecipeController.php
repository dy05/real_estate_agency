<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipes.index')]
    public function index(RecipeRepository $recipeRepo, EntityManagerInterface $em): Response
    {
        $recipes = $recipeRepo->findAll();
//        $recipes = $recipeRepo->findWithMaxDuration(30);

//        $em->remove($recipes[0]);
//        $em->flush();
//        $recipes = $recipeRepo->findAll();


//        $recipes[0]->setLabel('h oh oh oh lol lolo');
//        $em->flush();

/*
        $recipe = new Recipe();
        $recipe->setLabel('Bosse')
            ->setSlug('bosse')
            ->setDuration(16)
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdatedAt(new DateTimeImmutable())
            ->setDescription('Ah Écouteur ehe eh.');
        $em->persist($recipe);
        $em->flush();
//*/

        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipes/create', name: 'recipes.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', "La recipe a été créée !");
            return $this->redirectToRoute('recipes.index');
        }

        return $this->render('recipes/create.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipes/{slug}-{id}', name: 'recipes.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(RecipeRepository $recipeRepo, string $slug, int $id): Response
    {
        $recipe = $recipeRepo->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException();
        }

        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipes.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }

        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipes/{id}/edit', name: 'recipes.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "La recipe a été modifiée !");
            return $this->redirectToRoute('recipes.index');
        }

        return $this->render('recipes/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipes/{id}', name: 'recipes.delete', methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', "La recipe a été supprimée !");
        return $this->redirectToRoute('recipes.index');
    }
}
