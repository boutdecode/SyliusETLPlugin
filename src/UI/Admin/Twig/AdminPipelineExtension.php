<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Twig;

use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Pipeline;
use BoutDeCode\ETLCoreBundle\Core\Domain\Model\Step;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminPipelineExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('step_configuration', [$this, 'getStepConfiguration']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStepConfiguration(Pipeline $pipeline, Step $step): array
    {
        $overrideStepConfiguration = null;
        foreach ($pipeline->getConfiguration() as $config) {
            if (!is_array($config)) {
                continue;
            }

            if (($config['name'] ?? $config['code'] ?? 'unknow') === $step->getName()) {
                $overrideStepConfiguration = $config;
                break;
            }
        }

        /** @var array<string, mixed> $overrideStepConfiguration */
        $overrideStepConfiguration = is_array($overrideStepConfiguration) ? ($overrideStepConfiguration['configuration'] ?? []) : [];

        $result = [];
        foreach ($step->getConfiguration() as $key => $value) {
            if (isset($overrideStepConfiguration[$key])) {
                $result[$key] = [
                    'default' => $value,
                    'override' => $overrideStepConfiguration[$key],
                ];
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
