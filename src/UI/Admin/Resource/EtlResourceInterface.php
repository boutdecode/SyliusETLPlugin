<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\UI\Admin\Resource;

use Sylius\Resource\Model\ResourceInterface;

interface EtlResourceInterface extends ResourceInterface
{
    /**
     * @return class-string the etl-bundle entity the resource stands for
     */
    public static function getEntityClass(): string;
}
