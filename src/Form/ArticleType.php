<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Service\FileUploader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    private FileUploader $fileUploader;
    private string $imageDirectory;
    private Filesystem $filesystem;
    private string $publicDirectory;

    public function __construct(string $publicDirectory, string $imageDirectory, FileUploader $fileUploader, Filesystem $filesystem)
    {
        $this->fileUploader = $fileUploader;
        $this->imageDirectory = $imageDirectory;
        $this->filesystem = $filesystem;
        $this->publicDirectory = $publicDirectory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $constraints = [];
        if (!$options['oldImage']) {
            $constraints[] = new File([
                'maxSize' => '1024k',
                'mimeTypes' => [
                    "image/png",
                    "image/jpeg"
                ],
                'mimeTypesMessage' => 'Please upload a valid png or jpeg image',
            ]);
        }


        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Please select a category',
            ])
            ->add('name', TextType::class, [
                'label' => 'Article title: ',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image: ',
                'required' => !$options['oldImage'],
                'empty_data' => $options['oldImage'],
                'data_class' => null,
                'constraints' => $constraints,

            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Summary: ',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description: ',
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('Save', SubmitType::class)
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onImageUpload']
            );
    }

    public function onImageUpload(FormEvent $event): void
    {
        $oldImage =  $event->getForm()->getConfig()->getOption('oldImage');
        if ($event->getData()->getImage() !== $oldImage) {
            try {
                $uploadedFile = $this->fileUploader->upload($this->imageDirectory, $event->getForm()->get('image')->getData());
                $event->getData()->setImage($uploadedFile);

                if(isset($oldImage)){
                    $this->filesystem->remove($this->publicDirectory . $oldImage);
                }
            } catch (FileException $e) {
                $event->getForm()->get('image')->addError(new FormError('Error occurred while uploading image.'));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'oldImage' => null
        ]);
    }
}