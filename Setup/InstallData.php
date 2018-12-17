<?php
/**
 *
 * @copyright   2018 Magentando (http://www.magentando.com.br)
 * @license     http://www.magentando.com.br  Copyright
 * @author      Leandro Rosa <dev.leandrorosa@gmail.com>
 *
 * @link        http://www.magentando.com.br
 */

namespace Magentando\CustomerAttributes\Setup;


use Magentando\CustomerAttributes\Model\Source\Config\AddressType;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Attribute\Set;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /** @var CustomerSetupFactory  */
    protected $customerSetupFactory;

    /** @var SetFactory  */
    protected $attributeSetFactory;

    /**
     * InstallData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.0') < 0) {
            $this->install010($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function install010(ModuleDataSetupInterface $setup)
    {
        $data =  [
            'type' => 'varchar',
            'label' => __('Address Type'),
            'input' => 'select',
            'source' => AddressType::class,
            'backend' => ArrayBackend::class,
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 900,
            'position' => 900,
            'system' => 0,
        ];

        $forms = [
            'adminhtml_checkout',
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ];

        /** @var CustomerSetup $customerSetup */
        $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity     = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId     = $customerEntity->getDefaultAttributeSetId();

        /** @var Set $attributeSet */
        $attributeSet       = $this->attributeSetFactory->create();
        $attributeGroupId   = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'address_type', $data);

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'address_type')
            ->addData([
                'attribute_set_id'      => $attributeSetId,
                'attribute_group_id'    => $attributeGroupId,
                'used_in_forms'         => $forms,
            ]);

        $attribute->save();
    }
}
