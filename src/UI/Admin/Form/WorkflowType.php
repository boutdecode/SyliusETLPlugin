<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Form;

use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\ExecutableStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\ExtractorStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\LoaderStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\TransformerStep;
use BoutDeCode\ETLCoreBundle\ETL\Domain\Resolver\StepResolver;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkflowType extends AbstractType
{
    public function __construct(
        private readonly StepResolver $stepResolver,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->translator;

        $builder
            ->add('name', TextType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.name',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'bout_de_code_sylius_etl_plugin.form.description',
                'required' => false,
                'constraints' => [
                    new Length(max: 1000),
                ],
            ])
            ->add('stepConfiguration', TextareaType::class, [
                'mapped' => false,
                'label' => 'bout_de_code_sylius_etl_plugin.form.step_configuration',
                'required' => true,
                'attr' => [
                    'data-controller' => 'step-configurator',
                    'data-configuration' => json_encode(array_map(
                        static function (ExecutableStep $step) use ($translator) {
                            return [
                                'code' => $step->getCode(),
                                'name' => $translator->trans('bout_de_code_sylius_etl_plugin.' . $step->getCode() . '.name'),
                                'description' => $translator->trans('bout_de_code_sylius_etl_plugin.' . $step->getCode() . '.description'),
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
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var Workflow $data */
            $data = $event->getData();

            $form->get('stepConfiguration')->setData(json_encode($data->getStepConfiguration(), \JSON_PRETTY_PRINT));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var Workflow $data */
            $data = $event->getData();

            $rawStepConfig = $form->get('stepConfiguration')->getData();
            $data->setStepConfiguration(is_string($rawStepConfig) ? (array) json_decode($rawStepConfig, true) : []);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workflow::class,
        ]);
    }
}
