<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form;

use BoutDeCode\ETLBundle\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use BoutDeCode\SyliusETLPlugin\UI\Admin\Dto\PlannedTaskData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlannedTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.name',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.enabled',
            ])
            ->add('workflow', EntityType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.workflow',
                'class' => Workflow::class,
                'choice_label' => 'name',
            ])
            ->add('configuration', TextareaType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.override_configuration',
                'required' => false,
            ])
            ->add('input', TextareaType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.input',
                'required' => false,
                'attr' => [
                    'data-controller' => 'pipeline-input',
                    'data-file-field-id' => 'planned_task_inputFile',
                ],
            ])
            ->add('inputFile', FileType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.input_file',
                'required' => false,
            ])
            ->add('schedule', TextType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.schedule',
                'help' => 'bout_de_code_sylius_etl_plugin.form.schedule_help',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var PlannedTaskData $data */
            $data = $event->getData();

            $form->get('configuration')->setData(json_encode($data->configuration, \JSON_PRETTY_PRINT));
            $form->get('input')->setData(json_encode($data->input, \JSON_PRETTY_PRINT));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var PlannedTaskData $data */
            $data = $event->getData();

            $rawConfiguration = $form->get('configuration')->getData();
            $data->configuration = JsonArray::decode($rawConfiguration);

            /** @var UploadedFile|null $uploadedFile */
            $uploadedFile = $form->get('inputFile')->getData();

            if ($uploadedFile instanceof UploadedFile) {
                $filename = uniqid('pipeline_input_', true) . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->move(sys_get_temp_dir(), $filename);

                $data->input = [
                    'type' => 'file',
                    'source' => sys_get_temp_dir() . \DIRECTORY_SEPARATOR . $filename,
                    'name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => $uploadedFile->getClientMimeType(),
                ];

                return;
            }

            $rawInput = $form->get('input')->getData();
            if (!is_string($rawInput)) {
                $data->input = [];

                return;
            }

            $data->input = is_array(json_decode($rawInput, true)) ? JsonArray::decode($rawInput) : ['_raw' => $rawInput];
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlannedTaskData::class,
        ]);
    }
}
