<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form;

use BoutDeCode\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\ExecutableStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\ExtractorStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\LoaderStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\TransformerStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Resolver\StepResolver;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PipelineType extends AbstractType
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WorkflowProvider $workflowProvider,
        private readonly StepResolver $stepResolver,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $workflowId = $request?->query->get('workflowId');
        $workflow = null;
        if (is_string($workflowId) && $workflowId !== '') {
            $workflow = $this->workflowProvider->findWorkflowByIdentifier($workflowId);
        }

        $translator = $this->translator;

        $builder
            ->add('workflow', EntityType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.workflow',
                'class' => Workflow::class,
                'choice_label' => 'name',
                'data' => $workflow,
            ])
            ->add('configuration', TextareaType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.override_configuration',
                'required' => false,
                'attr' => [
                    'data-controller' => 'step-configuration-override',
                    'data-configuration' => json_encode(array_map(
                        static function (ExecutableStep $step) use ($translator) {
                            return [
                                'code' => $step->getCode(),
                                'name' => $translator->trans($step->getCode() . '.name'),
                                'description' => $translator->trans($step->getCode() . '.description'),
                                'category' => match (true) {
                                    $step instanceof ExtractorStep => 'extractor',
                                    $step instanceof TransformerStep => 'transformer',
                                    $step instanceof LoaderStep => 'loader',
                                    default => 'unknown',
                                },
                                'configuration_description' => $step->getConfigurationDescription(),
                            ];
                        },
                        $this->stepResolver->list(),
                    )),
                ],
                'data' => json_encode($workflow?->getStepConfiguration() ?? []),
            ])
            ->add('input', TextareaType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.input',
                'required' => false,
                'attr' => [
                    'data-controller' => 'pipeline-input',
                    'data-file-field-id' => 'pipeline_inputFile',
                ],
            ])
            ->add('inputFile', FileType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.input_file',
                'required' => false,
            ])
            ->add('scheduledAt', DateTimeType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.scheduled_at',
                'required' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'with_seconds' => true,
                'data' => new \DateTimeImmutable('+1 minute'),
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var Pipeline $data */
            $data = $event->getData();

            $form->get('configuration')->setData(json_encode($data->getConfiguration(), \JSON_PRETTY_PRINT));
            $form->get('input')->setData(json_encode($data->getInput(), \JSON_PRETTY_PRINT));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var Pipeline $data */
            $data = $event->getData();

            $rawConfiguration = $form->get('configuration')->getData();
            $data->setConfiguration(is_string($rawConfiguration) ? (array) json_decode($rawConfiguration, true) : []);

            /** @var UploadedFile|null $uploadedFile */
            $uploadedFile = $form->get('inputFile')->getData();

            if ($uploadedFile instanceof UploadedFile) {
                $filename = uniqid('pipeline_input_', true) . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->move(sys_get_temp_dir(), $filename);

                $data->setInput([
                    'type' => 'file',
                    'source' => sys_get_temp_dir() . \DIRECTORY_SEPARATOR . $filename,
                    'name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => $uploadedFile->getClientMimeType(),
                ]);

                return;
            }

            $rawInput = $form->get('input')->getData();
            if (!is_string($rawInput)) {
                $data->setInput([]);

                return;
            }

            $decoded = json_decode($rawInput, true);
            $data->setInput(is_array($decoded) ? $decoded : ['_raw' => $rawInput]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pipeline::class,
        ]);
    }
}
