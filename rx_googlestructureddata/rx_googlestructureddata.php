<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Rx_googlestructureddata extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'rx_googlestructureddata';
        $this->tab = 'front_office_features';
        $this->version = '1.7.0';
        $this->author = 'lr';
        $this->need_instance = 0;
        $this->bootstrap = true;
		$this->mod_prefix = strtoupper($this->name).'_';

        parent::__construct();

        $this->displayName = $this->l('Google Structured Data');
        $this->description = $this->l('Adds some Google Structured Data in JSON-LD format to your Prestashop.');

        $this->confirmUninstall = $this->l('Sure to uninstall module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

		$this->defaults = array(
			$this->mod_prefix.'SHOW_WEBPAGE' , 1,
            $this->mod_prefix.'SHOW_WEBSITE' , 1,
			$this->mod_prefix.'SHOW_WEBSITE_SEARCHBOX' , 0,
			$this->mod_prefix.'SHOW_ORGANIZATION' , 0,
			$this->mod_prefix.'SHOW_ORGANIZATION_LOGO', 0,
			$this->mod_prefix.'SHOW_ORGANIZATION_CONTACT', 0,
			$this->mod_prefix.'ORGANIZATION_CONTACT_EMAIL', null,
			$this->mod_prefix.'ORGANIZATION_CONTACT_TELEPHONE', null,
			$this->mod_prefix.'SHOW_ORGANIZATION_FACEBOOK', null,
			$this->mod_prefix.'ORGANIZATION_FACEBOOK', '',

			$this->mod_prefix.'SHOW_LOCALBUSINESS' , 0,
			$this->mod_prefix.'LOCALBUSINESS_TYPE'  , 'Store',
			$this->mod_prefix.'LOCALBUSINESS_STORENAME' , '',
			$this->mod_prefix.'LOCALBUSINESS_VAT'  , 0,
			$this->mod_prefix.'LOCALBUSINESS_PHONE'  , '11111',
			$this->mod_prefix.'LOCALBUSINESS_PRANGE'  , '11111',
			$this->mod_prefix.'LOCALBUSINESS_STREET' , '1111',
			$this->mod_prefix.'LOCALBUSINESS_COUNTRY'  , '11111',
			$this->mod_prefix.'LOCALBUSINESS_REGION'  , '11111',
			$this->mod_prefix.'LOCALBUSINESS_CODE'  , '11111',
			$this->mod_prefix.'LOCALBUSINESS_LOCALITY' , '111',
			$this->mod_prefix.'STRIP', 1,
        );
    }

	public function install()
    {
		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_ALL);
		}

		$module_hooks = array(
            'header',
			'backOfficeHeader'.
			'displayFooter',
			'displayHeader'
        );

		if (!parent::install()
			|| !$this->setDefaults()
		  	|| !$this->registerHook($module_hooks)
		)
			{
				return false;
			}
		return true;
	}

	public function uninstall()
	{
		foreach ($this->defaults as $default => $value) {
			Configuration::deleteByName($this->mod_prefix . $default);
		}

		if ( parent::uninstall() ) {
            return true;
        } else {
            $this->_errors[] = $this->l('There was an error during the uninstallation. Please contact module support');
            return false;
        }
	}

	public function setDefaults()
	{
		foreach ($this->defaults as $default => $value) {
			Configuration::updateValue($this->mod_prefix . $default , $value);
		}
		return true;
	}

    /**
     * Load the configuration form
     */
    public function getContent()
    {

		$output = null;
		/**
         * If values have been submitted in the form, process.
         */
        if ((bool)Tools::isSubmit('submit'.$this->name))  {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

	/**
	 * Save form data.
	 */
	protected function postProcess()
	{
		$form_values = $this->getConfigFieldsValues();
		foreach (array_keys($form_values) as $key) {
			Configuration::updateValue($key, Tools::getValue($key));
		}
	}


    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
		$helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFieldsValues()
	{
		return array(
			$this->mod_prefix.'SHOW_WEBPAGE' => Configuration::get($this->mod_prefix.'SHOW_WEBPAGE'),
			$this->mod_prefix.'SHOW_WEBSITE' => Configuration::get($this->mod_prefix.'SHOW_WEBSITE'),
			$this->mod_prefix.'SHOW_WEBSITE_SEARCHBOX' => Configuration::get($this->mod_prefix.'SHOW_WEBSITE_SEARCHBOX'),
			$this->mod_prefix.'SHOW_ORGANIZATION' => Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION'),
			$this->mod_prefix.'SHOW_ORGANIZATION_LOGO' => Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION_LOGO'),
			$this->mod_prefix.'SHOW_ORGANIZATION_CONTACT' => Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION_CONTACT'),
			$this->mod_prefix.'ORGANIZATION_CONTACT_EMAIL' => Configuration::get($this->mod_prefix.'ORGANIZATION_CONTACT_EMAIL'),
			$this->mod_prefix.'ORGANIZATION_CONTACT_TELEPHONE' => Configuration::get($this->mod_prefix.'ORGANIZATION_CONTACT_TELEPHONE'),
			$this->mod_prefix.'SHOW_ORGANIZATION_FACEBOOK' => Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION_FACEBOOK'),
			$this->mod_prefix.'ORGANIZATION_FACEBOOK' => Configuration::get($this->mod_prefix.'ORGANIZATION_FACEBOOK'),
			$this->mod_prefix.'SHOW_LOCALBUSINESS' => Configuration::get($this->mod_prefix.'SHOW_LOCALBUSINESS'),
			$this->mod_prefix.'LOCALBUSINESS_TYPE' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_TYPE'),
			$this->mod_prefix.'LOCALBUSINESS_STORENAME' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_STORENAME'),
			$this->mod_prefix.'LOCALBUSINESS_VAT' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_VAT'),
			$this->mod_prefix.'LOCALBUSINESS_PHONE' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_PHONE'),
			$this->mod_prefix.'LOCALBUSINESS_PRANGE' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_PRANGE'),
			$this->mod_prefix.'LOCALBUSINESS_STREET' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_STREET'),
			$this->mod_prefix.'LOCALBUSINESS_COUNTRY' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_COUNTRY'),
			$this->mod_prefix.'LOCALBUSINESS_REGION' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_REGION'),
			$this->mod_prefix.'LOCALBUSINESS_CODE' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_CODE'),
			$this->mod_prefix.'LOCALBUSINESS_LOCALITY' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_LOCALITY'),
			$this->mod_prefix.'SHOW_LOCALBUSINESS_GPS' => Configuration::get($this->mod_prefix.'SHOW_LOCALBUSINESS_GPS'),
			$this->mod_prefix.'LOCALBUSINESS_GPS_LAT' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_GPS_LAT'),
			$this->mod_prefix.'LOCALBUSINESS_GPS_LON' => Configuration::get($this->mod_prefix.'LOCALBUSINESS_GPS_LON'),
			$this->mod_prefix.'STRIP' => Configuration::get($this->mod_prefix.'STRIP'),
		);
	}

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
		return array(
            'form' => array(
                'legend' => array(
	                'title' => $this->l('Settings'),
	                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Website'),
                        'name' => $this->mod_prefix.'SHOW_WEBSITE',
                        'is_bool' => true,
                        'desc' => $this->l('Show Website structured data'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'type'		=> 'switch',
                        'label'		=> $this->l('Show Website -> Sitelinks Searchbox '),
                        'name'		=> $this->mod_prefix.'SHOW_WEBSITE_SEARCHBOX',
                        'is_bool'	=> true,
                        'desc' 		=> $this->l('Show "Website - Sitelinks Searchbox" structured data?').'<br />'
							.$this->l('See:').'
								<a href="https://developers.google.com/search/docs/data-types/sitelinks-searchbox" target="_blank">
									https://developers.google.com/search/docs/data-types/sitelinks-searchbox
								</a>',
                        'values' 	=> array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'type' => 'separator',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Organization'),
                        'name' => $this->mod_prefix.'SHOW_ORGANIZATION',
                        'is_bool' => true,
                        'desc' => $this->l('Show Organization structured data'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show Organization -> Logo'),
                        'name' => $this->mod_prefix.'SHOW_ORGANIZATION_LOGO',
                        'is_bool' => true,
                        'desc' => $this->l('Show Organization Logo.').'<br />'
						.$this->l('See more at:').' <a href ="https://developers.google.com/search/docs/data-types/logo" target="_blank">https://developers.google.com/search/docs/data-types/logo</a> '
						.$this->l('or').'<a href="https://schema.org/logo" target="_blank">https://schema.org/logo</a>',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show Organization -> Contact'),
                        'name' => $this->mod_prefix.'SHOW_ORGANIZATION_CONTACT',
                        'is_bool' => true,
                        'desc' => $this->l('Show Organization Contact structured data').'<br />'.$this->l('See more at:').' <a href="https://developers.google.com/search/docs/data-types/corporate-contact">https://developers.google.com/search/docs/data-types/corporate-contact</a>',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('email address, if other than main').': '.Configuration::get('PS_SHOP_EMAIL'),
                        'name' => $this->mod_prefix.'ORGANIZATION_CONTACT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-phone"></i>',
                        'desc' => $this->l('telephone if other than main'),
                        'name' => $this->mod_prefix.'ORGANIZATION_CONTACT_TELEPHONE',
                        'label' => $this->l('Telephone:'),
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show Organization -> Facebook page?'),
                        'name' => $this->mod_prefix.'SHOW_ORGANIZATION_FACEBOOK',
                        'is_bool' => true,
                        'desc' => $this->l('Show Facebook page URL in Organization structured data.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-facebook"></i>',
                        'desc' => $this->l('Facebook page full URL eg.:').' https://www.facebook.com/YourPage',
                        'name' => $this->mod_prefix.'ORGANIZATION_FACEBOOK',
                        'label' => $this->l('FB page URL:'),
                    ),
					array(
                        'type' => 'separator',
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show "LocalBusiness" data?'),
                        'name' => $this->mod_prefix.'SHOW_LOCALBUSINESS',
                        'is_bool' => true,
                        'desc' => $this->l('Show LocalBusiness structured data'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'type' => 'select',
                        'label' => $this->l('Select LocalBusiness type:'),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_TYPE',
                        'desc' => $this->l(''),
                        'required' => true,
                        //'disabled' => true,
                        //'class' => '',
                        'options' => array(
                            'query' => array(
                                array(
                                    'option_id' => 1,
                                    'option_name' => 'Store'
                                ),
								array(
                                    'option_id' => 2,
                                    'option_name' => 'LocalBusiness'
                                )
                            ),
                            'id' => 'option_id',
                            'name' => 'option_name'
						)
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//    'prefix' => '<i class="icon icon- "></i>',
						//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_STORENAME',
                        'label' => $this->l('Name of store:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//    'prefix' => '<i class="icon icon- "></i>',
						//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_VAT',
                        'label' => $this->l('????:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						'prefix' => '<i class="icon icon-money"></i>',
						'desc' => $this->l('Range of products prices in this shop from min to max.'),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_PRANGE',
                        'label' => $this->l('Store price range:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//	'prefix' => '<i class="icon icon-address-book"></i>',
						//  'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_STREET',
                        'label' => $this->l('Street:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//    'prefix' => '<i class="icon icon- "></i>',
						//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_COUNTRY',
                        'label' => $this->l('Country:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//    'prefix' => '<i class="icon icon- "></i>',
						//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_REGION',
                        'label' => $this->l('Region:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
						//    'prefix' => '<i class="icon icon- "></i>',
						//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_CODE',
                        'label' => $this->l('Postal code:'),
                    ),
					array(
                        'col' => 3,
                        'type' => 'text',
                    //    'prefix' => '<i class="icon icon- "></i>',
					//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_LOCALITY',
                        'label' => $this->l('Locality:'),
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show "LocalBusiness" GPS lat , lon?'),
                        'name' => $this->mod_prefix.'SHOW_LOCALBUSINESS_GPS',
                        'is_bool' => true,
                        'desc' => $this->l('Show LocalBusiness GPS?'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hide')
                            )
                        ),
                    ),
					array(
                        'col' => 1,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-map-marker"></i>',
                    //    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_GPS_LAT',
                        'label' => $this->l('GPS lat:'),
                    ),
					array(
                        'col' => 1,
                        'type' => 'text',
						'prefix' => '<i class="icon icon-map-marker"></i>',
					//    'desc' => $this->l(''),
                        'name' => $this->mod_prefix.'LOCALBUSINESS_GPS_LON',
                        'label' => $this->l('GPS lon:'),
                    ),

				),
				// Submit form
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
    }


    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayFooter()
    {
		$priceRange = Configuration::get($this->mod_prefix.'LOCALBUSINESS_PRANGE');
		if ($priceRange = null){

			$sql ='SELECT
						MIN(`price`) as min_price,
						MAX(`price`) as max_price
					FROM
						`'._DB_PREFIX_.'product`
	 				WHERE
						`active` = 1
					AND	`show_price` = 1;
					';

			if ($results = Db::getInstance()->ExecuteS($sql))
			    foreach ($results as $row)
			        $priceRange = $row['min_price'].' - '.$row['max_price'];
			}

		$this->context->smarty->assign("localbusiness_prange", $priceRange);

		$vars = [
			'webpage_show' => (bool)Configuration::get($this->mod_prefix.'SHOW_WEBPAGE'),
			'website_show' => (bool)Configuration::get($this->mod_prefix.'SHOW_WEBSITE'),
			'website_show_searchbox' => (bool)Configuration::get($this->mod_prefix.'SHOW_WEBSITE_SEARCHBOX'),
			'organization_show' => (bool)Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION'),
			'organizationshow_logo' => (bool)Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION_LOGO'),
			'organizationshow_contact' => (bool)Configuration::get($this->mod_prefix.'ORGANIZATION_CONTACT'),
			'organization_contact_email_default' => Configuration::get('PS_SHOP_EMAIL'),
			'organization_contact_email' => Configuration::get($this->mod_prefix.'ORGANIZATION_CONTACT_EMAIL'),
			'organization_contact_telephone' => Configuration::get($this->mod_prefix.'ORGANIZATION_CONTACT_TELEPHONE'),
			'organization_show_facebookpage' => (bool)Configuration::get($this->mod_prefix.'SHOW_ORGANIZATION_FACEBOOK'),
			'organization_facebookpage' => Configuration::get($this->mod_prefix.'ORGANIZATION_FACEBOOK'),
			'localbusines_show' 		=> (bool)Configuration::get($this->mod_prefix.'SHOW_LOCALBUSINESS'),
			'localbusiness_type' 		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_TYPE'),
			'localbusiness_storename'	=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_STORENAME'),
			'localbusiness_vat'			=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_VAT'),
			'localbusiness_phone'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_PHONE'),
			//'localbusiness_prange'		=> $priceRange,
			'localbusiness_street'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_STREET'),
			'localbusiness_locality'	=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_LOCALITY'),
			'localbusiness_code'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_CODE'),
			'localbusiness_region'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_REGION'),
			'localbusiness_country'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_COUNTRY'),
			'localbusiness_gps_lat'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_GPS_LAT'),
			'localbusiness_gps_lon'		=> Configuration::get($this->mod_prefix.'LOCALBUSINESS_GPS_LON'),
			'strip'						=> Configuration::get($this->mod_prefix.'STRIP'),
		];

		$this->context->smarty->assign($vars);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/footer.tpl');
	}
}