<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class AdminDiscountsController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'painter' => Phpfox::getService('instapaint.painter')

        ];

        // Allow access to admins only
        $services['security']->allowAccess([$services['security']::ADMIN_GROUP_ID]);

        // Variables to simplify code
        $template = $this->template();
        $search = $this->search();

        // Set view title
        $template->setTitle('Admin Dashboard » Discounts');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        // Set breadcrumb
        $template->setBreadCrumb("Discounts");

        // Button
        $template->menu('Add new discount', $this->url()->makeUrl('/admin-dashboard/discounts/add'));

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->getAdminDashboardMenu());

        $search->set([
            'type'           => 'discounts',
            'field'          => 'd.discount_id',
            'ignore_blocked' => true,
            'search_tool'    => [
                'when_field' => 'time_stamp',
                'when_end_field' => 'time_stamp',
                'table_alias' => 'd',
                'search'      => [
                    'action'        => $this->url()->makeUrl('/admin-dashboard/discounts/'),
                    'default_value' => 'Search Discounts...',
                    'name'          => 'search',
                    'field'         => ['d.name', 'd.coupon_code'],
                ],
                'sort'        => [
                    'latest'     => ['d.discount_id', _p('Latest Creation')],
                    'latest-expiration'     => ['d.expiration_timestamp', _p('Latest Expiration')],
                    'highest-discount'     => ['d.discount_percentage', _p('Highest Discount')]
                ],
                'show'        => [20, 40, 60],
            ],
        ]);

        // Configure search service
        $aBrowseParams = [
            'module_id' => 'instapaint-browse-discounts',
            'alias'     => 'd',
            'field'     => 'discount_id',
            'table'     => Phpfox::getT('instapaint_discount')
        ];



        $search->setContinueSearch(false);
        $search->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();

        // assign variables to template
        $this->template()->assign([
                'aItems'       => $aItems
            ]
        );
    }
}
