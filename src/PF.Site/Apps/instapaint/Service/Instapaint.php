<?php

namespace Apps\Instapaint\Service;

class Instapaint extends \Phpfox_Service
{
    const FONT_AWESOME_LINK = '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">';

    /**
     * Returns the array that $template->buildSectionMenu() needs
     * to build the menu.
     *
     * @return array The array representing the menu
     */
    public function getAdminDashboardMenu() {
        return [
            'Home' => '',
            'Orders' => 'admin-dashboard.orders',
            'Painters' => 'admin-dashboard.painters',
            'Discounts' => 'admin-dashboard.discounts',
            'Packages' => 'admin-dashboard.packages',
            'Settings' => 'admin-dashboard.settings'
        ];
    }

    /**
     * Returns the array that $template->buildSectionMenu() needs
     * to build the menu.
     *
     * @return array The array representing the menu
     */
    public function getClientDashboardMenu() {
        return [
            'Home' => 'client-dashboard.',
            'My Orders' => 'client-dashboard.orders',
            'My Addresses' => 'client-dashboard.addresses',
        ];
    }

    /**
     * Returns the array that $template->buildSectionMenu() needs
     * to build the menu.
     *
     * @return array The array representing the menu
     */
    public function getPainterDashboardMenu() {
        return [
            'Home' => 'painter-dashboard.',
            'Available Orders' => 'painter-dashboard.available-orders',
            'My Orders' => 'painter-dashboard.orders'
        ];
    }

    /**
     * Inserts a menu after $refMenuName in the $menus array,
     * and returns a new menu array with the newly inserted menu.
     *
     * @param $menus array The array representing the menus
     * @param $refMenuName string The key representing the reference menu
     * @param $newMenu array The menu to be inserted
     *
     * @return array The new menu array
     */
    public function insertMenuAfter($menus, $refMenuName, $newMenu) {
        $newMenus = []; // Empty array to store new menus array

        // Foreach menu, check if it's the reference menu:
        foreach ($menus as $name => $link) {
            $newMenus[$name] = $link;
            // Insert new menu after reference menu:
            if ($name == $refMenuName) {
                $newMenus[array_keys($newMenu)[0]] = $newMenu[array_keys($newMenu)[0]];
            }
        }

        return $newMenus;
    }
}
