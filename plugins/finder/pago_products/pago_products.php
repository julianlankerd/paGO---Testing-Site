<?php
defined('JPATH_BASE') or die;
jimport('joomla.application.component.helper');
jimport('joomla.filesystem.file');
// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';


class PlgFinderPago_products extends FinderIndexerAdapter
{

	protected $context = 'PagoItems';
	protected $extension = 'com_pago';
	protected $layout = 'items';
	protected $type_title = 'PagoItems';
	protected $table = '#__pago_items';
	protected $state_field = 'published';

	protected $autoloadLanguage = true;

 	public function onFinderAfterDelete($context, $table)
    {
        if ($context == 'com_pago.items')
        {
            $id = $table->id;
        }
        elseif ($context == 'com_finder.index')
        {
            $id = $table->link_id;
        }
        else
        {
            return true;
        }
        // Remove the items.
        return $this->remove($id);
    }

    public function onFinderAfterSave($context, $row, $isNew)
    {
		//$this->reindex($context['id']);
        // We only want to handle items here
		if ($context == 'com_pago.items')
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access)
			{
				// Process the change.
				$this->itemAccessChange($row);

				// Reindex the item
				$this->reindex($row->id);
			}

			// Check if the parent access level is different
			if (!$isNew && $this->old_cataccess != $row->access)
			{
				$this->categoryAccessChange($row);
			}

		}
		return true;
    }

    public function onFinderBeforeSave($context, $row, $isNew)
    {
        // We only want to handle categories here
		if ($context == 'com_pago.items')
		{
			// Query the database for the old access level and the parent if the item isn't new
			if (!$isNew)
			{
				$this->checkItemAccess($row);
			}
		}

		return true;
    }

    public function onFinderChangeState($context, $pks, $value)
    {

   		if ($context == 'com_pago.items')
		{
			$this->itemStateChange($pks, $value);
		}

		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}

    }

    protected function index(FinderIndexerResult $item, $format = 'html')
    {
		$item->url = $this->getURL($item->id, $this->extension, $this->layout);
		$item->route = 'index.php?option='.$this->extension.'&view=item&id='.$item->id;
		$item->path = FinderIndexerHelper::getContentPath($item->route);

		// Translate the state. Items should only be published if the category is published.
		$item->state = $this->translateState($item->published, $item->cat_state);

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Items');

		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}


	protected function setup()
	{
		return true;
	}

	protected function getListQuery($sql = null)
	{

		$db = JFactory::getDbo();

		// Check if we can use the supplied SQL query.
		$sql = $sql instanceof JDatabaseQuery ? $sql : $db->getQuery(true);
		$sql->select('a.id As id, a.name AS title, a.created AS indexdate,  a.description AS summary, price As list_price, discount_amount As sale_price, a.published As published, a.access As access ');
		$sql->select('kc.name As category, kc.path As path, kc.published AS cat_state, kc.access AS cat_access');

		$case_when_item_alias = ' CASE WHEN ';
		$case_when_item_alias .= $sql->charLength('a.alias');
		$case_when_item_alias .= ' THEN ';
		$a_id = $sql->castAsChar('a.id');
		$case_when_item_alias .= $sql->concatenate(array($a_id, 'a.alias'), ':');
		$case_when_item_alias .= ' ELSE ';
		$case_when_item_alias .= $a_id.' END as slug';
		$sql->select($case_when_item_alias);

		$case_when_category_alias = ' CASE WHEN ';
		$case_when_category_alias .= $sql->charLength('kc.alias');
		$case_when_category_alias .= ' THEN ';
		$c_id = $sql->castAsChar('kc.id');
		$case_when_category_alias .= $sql->concatenate(array($c_id, 'kc.alias'), ':');
		$case_when_category_alias .= ' ELSE ';
		$case_when_category_alias .= $c_id.' END as catslug';
		$sql->select($case_when_category_alias);


		$sql->from('#__pago_items AS a');
		$sql->join('LEFT', $db->quoteName('#__pago_categoriesi', 'kc') . ' ON (' . $db->quoteName('a.primary_category') . ' = ' . $db->quoteName('kc.id') . ')');
		$sql->where($db->quoteName('a.published') ." = ".$db->quote(1));
		$sql->order($db->quoteName('a.created') . ' DESC');

		return $sql;
	}

}
