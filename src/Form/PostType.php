<?php


namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostType extends AbstractType
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'Title',
                'required' => false
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Summary',
                'required' => false
            ])
            ->add('content', null, [
                'attr' => ['rows' => 10],
                'label' => 'Content',
                'required' => false
            ])
            ->add('publishedAt', DateTimeType::class, array(
                'input' => 'datetime_immutable',
            ))
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Post */
                $post = $event->getData();
                if (null !== $postTitle = $post->getTitle()) {
                    $post->setSlug($this->slugger->slug($postTitle)->lower());
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
