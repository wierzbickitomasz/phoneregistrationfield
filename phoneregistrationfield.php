<?php

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class Phoneregistrationfield extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name          = 'phoneregistrationfield';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'Tomasz Wierzbicki';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('Add phone field');
        $this->description = $this->l('Add phone field');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        $result = true;
        if (!parent::install() ||
            !$this->registerHook('displayAtSpecificPlace') ||
            !$this->registerHook('rightColumn') ||
            !$this->installTab('AdminParentCustomer', 'AdminPhoneregistrationfield', 'Numer telefonu')
            || !$this->registerHook('additionalCustomerFormFields')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('actionAdminCustomersListingFieldsModifier')
            || !$this->registerHook('actionCustomerGridDefinitionModifier')
            || !$this->registerHook('actionCustomerGridQueryBuilderModifier')
        ) {
            $result = false;
        }

        $res =(bool)Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'customer`  ADD `phone` varchar(64) NULL'
        );

        return $result;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
        ) {
            return false;
        }
        $res =(bool)Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'customer` DROP `phone`'
        );
        return true;
    }

    public function hookActionCustomerGridDefinitionModifier(array $params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $definition
            ->getColumns()
            ->addAfter(
                'optin',
                (new DataColumn('phone'))
                    ->setName($this->l('telephone'))
                    ->setOptions([
                        'field' => 'phone',
                    ])
            )
        ;

        // For search filter
        $definition->getFilters()->add(
            (new Filter('phone', TextType::class))
                ->setAssociatedColumn('phone')
        );
    }

    public function hookActionCustomerGridQueryBuilderModifier(array $params)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var CustomerFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect(
            'IF(wcm.`phone` IS NULL,0,wcm.`phone`) AS `phone`'
        );

        $searchQueryBuilder->leftJoin(
            'c',
            '`' . pSQL(_DB_PREFIX_) . 'customer`',
            'wcm',
            'wcm.`id_customer` = c.`id_customer`'
        );

        if ('phone' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('wcm.`phone`', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('phone' === $filterName) {
                $searchQueryBuilder->andWhere('wcm.`phone` = :phone');
                $searchQueryBuilder->setParameter('phone', $filterValue);

                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('wcm.`phone` IS NULL');
                }
            }
        }
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        $formField = new FormField();
        $formField->setName('phone');
        $formField->setType('text');
        $formField->setLabel($this->l('Numer telefonu'));
        $formField->setRequired(true);
        return array($formField);
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $customerId =$params['newCustomer']->id;
        $phone= Tools::getValue('phone','');
        return (bool) Db::getInstance()->execute('update '._DB_PREFIX_.'customer set phone=\''.pSQL($phone)."' WHERE id_customer=".(int) $customerId);
    }

    public function hookActionAdminCustomersListingFieldsModifier($params)
    {
        $params['fields']['phone'] = array(
            'title' => $this->l('Phone'),
            'align' => 'center',
        );
    }

    public function installTab($parent, $class_name, $name)
    {
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $name;
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        // Retrieve Tab ID
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        // Load tab
        $tab = new Tab((int)$id_tab);
        // Delete it
        return $tab->delete();
    }

    public function getContent()
    {
        // TODO: Implement getWidgetVariables() method.
    }

    public function renderWidget($hookName, array $configuration)
    {
        // TODO: Implement getWidgetVariables() method.
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        // TODO: Implement getWidgetVariables() method.
    }



}
