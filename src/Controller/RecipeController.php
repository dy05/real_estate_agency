<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ->setDescription('Ah Ecouteur ehe eh.');
        $em->persist($recipe);
        $em->flush();
//*/

        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipes/{slug}-{id}', name: 'recipes.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(RecipeRepository $recipeRepo, string $slug, int $id): Response
    {
        $recipe = $recipeRepo->find($id);

        if (!$recipe) {
        }

        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipes.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }

        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
