<?php
/**
 * @copyright   2018 Magentando (http://www.magentando.com.br)
 * @license     http://www.magentando.com.br  Copyright
 * @author      Leandro Rosa <dev.leandrorosa@gmail.com>
 *
 * @link        http://www.magentando.com.br
 */

namespace Magentando\CustomerAttributes\Model\Source\Config;


use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class AddressType extends AbstractSource
{
    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if (null === $this->_options) {
            $this->_options = [
                ['label' => __('Residential'), 'value' => 'residential'],
                ['label' => __('Business'), 'value' => 'business']
            ];
        }

        return $this->_options;
    }
}
