<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('description')
            ->add('slug', TextType::class, [
                'required' => false
            ])
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('duration', NumberType::class, [
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->autoTimestamp(...))
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['label']));
        }
        $event->setData($data);
    }

    public function autoTimestamp(PostSubmitEvent $event): void
    {
        $recipe = $event->getData();

        if (!($recipe instanceof Recipe)) {
            return;
        }

        if (!$recipe->getId()) {
            $recipe->setCreatedAt(new DateTimeImmutable());
        }

        $recipe->setUpdatedAt(new DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
