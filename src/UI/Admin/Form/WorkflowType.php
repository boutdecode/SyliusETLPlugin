<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\UI\Admin\Form;

use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WorkflowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'akawaka_sylius_etl_plugin.form.name',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'akawaka_sylius_etl_plugin.form.description',
                'required' => false,
                'constraints' => [
                    new Length(max: 1000),
                ]
            ])
            ->add('stepConfiguration', TextareaType::class, [
                'mapped' => false,
                'label' => 'akawaka_sylius_etl_plugin.form.step_configuration',
                'required' => true,
            ])
            ->add('configuration', TextareaType::class, [
                'mapped' => false,
                'label' => 'akawaka_sylius_etl_plugin.form.configuration',
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var Workflow $data */
            $data = $event->getData();

            $form->get('stepConfiguration')->setData(json_encode($data->getStepConfiguration(), JSON_PRETTY_PRINT));
            $form->get('configuration')->setData(json_encode($data->getConfiguration(), JSON_PRETTY_PRINT));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();

            /** @var Workflow $data */
            $data = $event->getData();

            $data->setStepConfiguration(json_decode($form->get('stepConfiguration')->getData(), true));
            $data->setConfiguration(json_decode($form->get('configuration')->getData(), true));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workflow::class,
        ]);
    }
}
