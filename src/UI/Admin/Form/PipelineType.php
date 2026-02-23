<?php

declare(strict_types=1);

namespace Akawaka\SyliusETLPlugin\UI\Admin\Form;

use Akawaka\ETLCoreBundle\Core\Domain\Data\Provider\WorkflowProvider;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Pipeline;
use Akawaka\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Entity\Workflow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PipelineType extends AbstractType
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WorkflowProvider $workflowProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $workflowId = $request?->query->get('workflowId');
        $data = null;
        if ($workflowId) {
            $data = $this->workflowProvider->findWorkflowByIdentifier($workflowId);
        }

        $builder
            ->add('workflow', EntityType::class, [
                'label' => 'akawaka_sylius_etl_plugin.form.workflow',
                'class' => Workflow::class,
                'choice_label' => 'name',
                'data' => $data,
            ])
            ->add('configuration', TextareaType::class, [
                'mapped' => false,
                'label' => 'akawaka_sylius_etl_plugin.form.override_configuration',
                'required' => false,
            ])
            ->add('input', TextareaType::class, [
                'mapped' => false,
                'label' => 'akawaka_sylius_etl_plugin.form.input',
                'required' => false,
            ])
            ->add('scheduledAt', DateTimeType::class, [
                'label' => 'akawaka_sylius_etl_plugin.form.scheduled_at',
                'required' => false,
                'data' => new \DateTimeImmutable('+1 minute'),
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var Pipeline $data */
            $data = $event->getData();

            $form->get('configuration')->setData(json_encode($data->getConfiguration(), JSON_PRETTY_PRINT));
            $form->get('input')->setData(json_encode($data->getInput(), JSON_PRETTY_PRINT));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($builder) {
            $form = $event->getForm();

            /** @var Pipeline $data */
            $data = $event->getData();

            $data->setConfiguration(json_decode($form->get('configuration')->getData(), true));
            $data->setInput(json_decode($form->get('input')->getData(), true));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pipeline::class,
        ]);
    }
}
