<?php
declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\Command;

use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TODO: WRITE CLASS PHPDOC
 *
 * @author    Adrien Pétremann <adrien.petremann@akeneo.com>
 * @copyright 2018 Akeneo SAS (https://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class RemoveWrongBooleanValuesOnVariantProductsCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setHidden(true)
            ->setName('pim:catalog:remove-wrong-values-on-variant-products')
            ->setDescription('TODO.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get all variant products
        $pqbFactory = $this->getContainer()->get('pim_catalog.query.product_and_product_model_query_builder_factory')
            ->create();

        $pqbFactory->addFilter('parent', Operators::IS_NOT_EMPTY, null);
        $variantProducts = $pqbFactory->execute();

        $totalProducts = 0;
        $cleanProducts = 0;

        /** @var ProductInterface $variantProduct */
        foreach ($variantProducts as $variantProduct) {
            $totalProducts++;
            $family = $variantProduct->getFamily();
            $attributes = $family->getAttributes();
            $familyVariant = $variantProduct->getFamilyVariant();
            $nbOfLevels = $familyVariant->getNumberOfLevel();
            $isModified = false;

            foreach ($attributes as $attribute) {
                if ($attribute->getType() === AttributeTypes::BOOLEAN) {

                    $attributeIsOnProductModels = $familyVariant->getLevelForAttributeCode($attribute->getCode()) !== $nbOfLevels;
                    $productVariantHasValue = null !== $variantProduct->getValuesForVariation()->getByCodes($attribute->getCode());

                    // If attribute is not on the product but on higher levels
                    if ($attributeIsOnProductModels && $productVariantHasValue) {
                        $values = $variantProduct->getValues();
                        $values->removeByAttribute($attribute);
                        $variantProduct->setValues($values);
                        $isModified = true;
                    }
                }
            }

            if ($isModified) {
                $violations = $this->getContainer()->get('pim_catalog.validator.product')->validate($variantProduct);

                if ($violations->count() > 0) {
                    throw new \LogicException(
                        sprintf(
                            'Product "%s" is not valid and cannot be saved',
                            $variantProduct->getIdentifier()
                        )
                    );
                }

                $this->getContainer()->get('pim_catalog.saver.product')->save($variantProduct);
                $cleanProducts++;
            }
        }

        $output->writeln(sprintf('<info>%s products cleaned (%s products parsed)</info>', $cleanProducts, $totalProducts));
    }
}
