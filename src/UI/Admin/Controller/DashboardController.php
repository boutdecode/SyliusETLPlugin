<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Controller;

use BoutDeCode\ETLCoreBundle\Statistics\Domain\Data\Provider\WorkflowStatisticProvider;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\PlannedTaskRepository;
use BoutDeCode\SyliusETLPlugin\Core\Infrastructure\Persistence\ORM\Repository\WorkflowRepository;
use BoutDeCode\SyliusETLPlugin\Statistics\Infrastructure\Persistence\ORM\Repository\WorkflowExecutionStatisticRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/etl/dashboard', name: 'bout_de_code_sylius_etl_plugin_admin_dashboard', methods: ['GET'])]
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly WorkflowStatisticProvider $statisticProvider,
        private readonly WorkflowRepository $workflowRepository,
        private readonly PlannedTaskRepository $plannedTaskRepository,
        private readonly WorkflowExecutionStatisticRepository $executionStatisticRepository,
    ) {
    }

    public function __invoke(): Response
    {
        $statistics = iterator_to_array($this->statisticProvider->findAll());

        $totalSuccess = 0;
        $totalFailure = 0;
        $byWorkflow = [];
        $globalTotalDurationMs = 0;
        $globalTotalCount = 0;
        $globalMinDurationMs = null;
        $globalMaxDurationMs = null;

        foreach ($statistics as $statistic) {
            $totalSuccess += $statistic->getSuccessCount();
            $totalFailure += $statistic->getFailureCount();

            $byWorkflow[] = [
                'name' => $statistic->getWorkflow()->getName(),
                'avgDuration' => $statistic->getAverageDurationMs() !== null
                    ? round($statistic->getAverageDurationMs() / 1000, 2)
                    : null,
            ];

            $globalTotalDurationMs += $statistic->getTotalDurationMs();
            $globalTotalCount += $statistic->getTotalCount();
            $min = $statistic->getMinDurationMs();
            $max = $statistic->getMaxDurationMs();
            if ($min !== null && ($globalMinDurationMs === null || $min < $globalMinDurationMs)) {
                $globalMinDurationMs = $min;
            }
            if ($max !== null && ($globalMaxDurationMs === null || $max > $globalMaxDurationMs)) {
                $globalMaxDurationMs = $max;
            }
        }

        $globalAvgDurationMs = $globalTotalCount > 0
            ? (int) round($globalTotalDurationMs / $globalTotalCount)
            : null;

        return $this->render('@BoutDeCodeSyliusETLPlugin/admin/dashboard/index.html.twig', [
            'totalSuccess' => $totalSuccess,
            'totalFailure' => $totalFailure,
            'byWorkflow' => $byWorkflow,
            'avgDuration' => $globalAvgDurationMs !== null ? round($globalAvgDurationMs / 1000, 2) : null,
            'minDuration' => $globalMinDurationMs !== null ? round($globalMinDurationMs / 1000, 2) : null,
            'maxDuration' => $globalMaxDurationMs !== null ? round($globalMaxDurationMs / 1000, 2) : null,
            'latestWorkflows' => $this->workflowRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'latestPlannedTasks' => $this->plannedTaskRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'historiesPerDay' => $this->executionStatisticRepository->countPerDayAndStatus(365),
        ]);
    }
}
