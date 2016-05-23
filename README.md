NfqAdminBundle
=============================

## Installation

### Step 1: Download NfqAdminBundle using composer

Add NfqAdminBundle in to your composer.json:

	{
		"repositories": [
            {
	            "type": "vcs",
	            "url": "git@github.com:nfq-rho/admin-bundle.git"
	        },
		],
    	"require": {
        	"nfq-rho/admin-bundle": "~0.5"
    	}
	}

### Step 2: Enable the bundle

Enable the bundle in the kernel.:

	<?php
	// app/AppKernel.php

	public function registerBundles()
	{
	    $bundles = [
			// ...
        	new Nfq\AdminBundle\NfqAdminBundle(),
        	// ...
    	];
	}

## Usage

### Pagination

By it self NfqAdminBundle does not depend on any paginator. But it has a pagination layer which adds ability to
integrate any paginator you want. Out of the box KnpPaginator and Pagerfanta are supported (we recommend to use 
KnpPaginatorBundle), so all you have to do is to install one of them using composer. Then just override
`views/Base/_blocks/_pagination.html.twig` template and you will have working pagination.

### Admin menu builder

If you are using KnpMenuBundle, you can add event listener for building admin menus:

Side menu event tag: `nfq_admin.side_menu_configure`
Header menu event tag: `nfq_admin.header_menu_configure`

Add listener:


```

	<?php
	/**
     * This file is part of the "NFQ Bundles" package.
     *
     * (c) NFQ Technologies UAB <info@nfq.com>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */
	
	namespace Nfq\SomeBundle\EventListener;
	
	use Knp\Menu\ItemInterface;
	use Nfq\AdminBundle\Event\ConfigureMenuEvent;
	use Nfq\AdminBundle\Menu\AdminMenuListener as AdminMenuListenerBase;
	
	class AdminMenuListener extends AdminMenuListenerBase
	{
		/**
		 * {@inheritdoc}
		 */
		protected function doMenuConfigure(ConfigureMenuEvent $event)
		{
			$menu = $event->getMenu();
            $node = $this->getMenuNode();

			$menu->addChild($node);
		}
	
		/**
		 * @return ItemInterface
		 */
		private function getMenuNode()
		{
			return $this
				->factory
				->createItem('admin.menu.item_translation', ['route' => 'some_index_route'])
				->setExtras(
					[
						'orderNumber' => 13,
						'translation_domain' => 'adminInterface',
					]
				);
		}
	}
	
Service:
	
	#Admin menu listener
	some_bundle.admin_configure_menu_listener:
		parent: nfq_admin.menu.base_listener
		class: Nfq\SomeBundle\EventListener\AdminMenuListener
		tags:
		  - { name: kernel.event_listener, event: nfq_admin.side_menu_configure, method: onMenuConfigure }


There is possibility to show menu items by user roles.
Usage:

	nfq_admin.menu_security:
		nfq_payment:
			- ROLE_SUPER_ADMIN
		nfq_user:
			- ROLE_SUPER_ADMIN
		nfq_translation:
			- ROLE_TRANSLATOR

To add granted roles parameter to listener service name must follow format `config_node.admin_configure_menu_listener`
For example if security configuration is defined

	nfq_admin.menu_security:
		nfq_payment:
			- ROLE_SUPER_ADMIN

Then listener service id must be `nfq_payment.admin_configure_menu_listener`
