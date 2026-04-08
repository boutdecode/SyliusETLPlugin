<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\ETL\Infrastructure\Step\Loader;

use BoutDeCode\ETLCoreBundle\ETL\Domain\Model\AbstractLoaderStep;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;

final class SyliusProductLoaderStep extends AbstractLoaderStep
{
    public const CODE = 'etl.loader.sylius_product';

    protected string $code = self::CODE;

    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     * @param ProductFactoryInterface<ProductInterface> $productFactory
     * @param ProductVariantFactoryInterface<\Sylius\Component\Product\Model\ProductVariantInterface> $productVariantFactory
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductFactoryInterface $productFactory,
        private readonly ProductVariantFactoryInterface $productVariantFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function load(mixed $data, mixed $destination, array $configuration = []): array
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        $code = $data['product']['code'];
        $slug = $data['product']['code'];

        if ($code === 'jean-skinny') {
            throw new \LogicException(sprintf('Any error for product %s', $code));
        }

        $action = 'update';
        $product = $this->productRepository->findOneBy(['code' => $code]);
        if (null === $product) {
            $action = 'create';
            $product = $this->productFactory->createNew();
            $product->setCode($code);
            $product->setSlug($slug);
        }

        $product->setName($data['product']['name'] ?? '');
        $product->setDescription($data['product']['description'] ?? '');

        $variantCode = $data['variant']['code'];

        $productVariant = $product->getVariants()->filter(function ($variant) use ($variantCode) {
            return $variant->getCode() === $variantCode;
        })->first();
        if (!$productVariant) {
            $productVariant = $this->productVariantFactory->createNew();
            $productVariant->setCode($variantCode);

            $product->addVariant($productVariant);
        }

        $productVariant->setName($data['variant']['name'] ?? '');

        $this->entityManager->persist($productVariant);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return [
            'action' => $action,
            'productId' => $product->getId(),
            'productVariantId' => $productVariant->getId(),
        ];
    }
}
