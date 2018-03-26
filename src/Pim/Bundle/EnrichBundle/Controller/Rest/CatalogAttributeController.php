<?php

declare(strict_types=1);

namespace Pim\Bundle\EnrichBundle\Controller\Rest;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\UserBundle\Context\UserContext;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CatalogAttributeController
{
    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var UserContext */
    protected $userContext;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param NormalizerInterface          $normalizer
     * @param UserContext                  $userContext
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        NormalizerInterface $normalizer,
        UserContext $userContext
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->normalizer = $normalizer;
        $this->userContext = $userContext;
    }

    /**
     * Get attribute by code
     *
     * @AclAncestor("pim_enrich_attribute_edit")
     *
     * @param string $identifier
     *
     * @return JsonResponse
     */
    public function getAction(string $identifier): JsonResponse
    {
        $attribute = $this->attributeRepository->findOneByIdentifier($identifier);
        if (null === $attribute) {
            throw new NotFoundHttpException(sprintf('Attribute with code "%s" not found', $identifier));
        }

        return new JsonResponse($this->normalizer->normalize(
            $attribute,
            'internal_api',
            ['locale' => $this->userContext->getUiLocale()->getCode()]
        ));
    }
}
