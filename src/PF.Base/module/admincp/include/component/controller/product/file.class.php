<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: file.class.php 5296 2013-01-31 12:37:12Z Miguel_Espinoza $
 */
class Admincp_Component_Controller_Product_File extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$oArchiveExport = Phpfox::getLib('archive.export')->set(array('zip'));
		$oArchiveImport = Phpfox::getLib('archive.import')->set(array('zip'));
		
		if ($this->request()->get('req4') == 'process')
		{
			$aData = unserialize(base64_decode($this->request()->get('step')));
			if ($mReturn = Phpfox::getService('admincp.module.process')->processInstall($this->request()->get('id'), $aData, $this->request()->get('overwrite')))
			{
				if (is_array($mReturn))
				{
					$this->url()->send('admincp', array('product', 'file', 'process', 'overwrite' => $this->request()->get('overwrite'), 'id' => $this->request()->get('id'), 'step' => base64_encode(serialize($mReturn))));
				}
				else 
				{
					Phpfox_Module::instance()->_cacheModules();
					Phpfox_Plugin::set();
					if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_file_1')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
					if ($this->request()->get('overwrite'))
					{
						if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_file_2')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
						$this->url()->send('admincp', array('product'), _p('product_successfully_installed'));
					}
					else 
					{
						if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_file_3')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
						$this->url()->send('admincp', array('product'), _p('product_successfully_installed'));
					}
				}
			}
		}

		// Run the export routine
		if ($sExportId = $this->request()->get('export'))
		{
			if ($mData = Phpfox::getService('admincp.product')->export($sExportId))
			{
				$oArchiveExport->download('phpfox-product-' . $mData['name'], 'zip', $mData['folder']);
			}
		}
		
		if (($sProduct = $this->request()->get('install')))
		{
			// Import the settings
			if (($aInstall = Phpfox::getService('admincp.product.process')->import($sProduct, $this->request()->get('overwrite'))))
			{
				$this->url()->send('admincp', array('product', 'file', 'process', 'overwrite' => $this->request()->get('overwrite'), 'id' => $aInstall['product_id'], 'step' => base64_encode(serialize($aInstall['files']))));
			}
		}
		
		$aProducts = Phpfox::getService('admincp.product')->get();
		foreach ($aProducts as $iKey => $aProduct)
		{
			if ($aProduct['product_id'] == 'phpfox' || $aProduct['product_id'] == 'phpfox_installer')
			{
				unset($aProducts[$iKey]);
			}
		}

		// Assign needed vars to the template
		$this->template()->setTitle(_p('import_products'))
				->setBreadCrumb(_p('import_products'), null, true)
                ->setActiveMenu('admincp.techie.product')
				->assign(array(
				'aArchives' => $oArchiveExport->getSupported(),
				'sSupported' => $oArchiveImport->getSupported(),
				'sFtpEditLink' => $this->url()->makeUrl('admincp.setting.edit', array('group-id' => 'ftp')),
				'aNewProducts' => Phpfox::getService('admincp.product')->getNewProductsForInstall()
			)
		);

	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_file_clean')) ? eval($sPlugin) : false);
	}
}