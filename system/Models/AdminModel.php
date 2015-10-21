<?php

namespace App\Models;

class AdminModel extends \Abimo\Services
{
    public $config = array();
    public $configDir = APP_PATH.DIRECTORY_SEPARATOR.'Misc';
    
    public $menu = array();
    public $content = array();

	private $imageExtensions = array(
		IMAGETYPE_GIF => "gif",
		IMAGETYPE_JPEG => "jpg",
		IMAGETYPE_PNG => "png",
		IMAGETYPE_SWF => "swf",
		IMAGETYPE_PSD => "psd",
		IMAGETYPE_BMP => "bmp",
		IMAGETYPE_TIFF_II => "tiff",
		IMAGETYPE_TIFF_MM => "tiff",
		IMAGETYPE_JPC => "jpc",
		IMAGETYPE_JP2 => "jp2",
		IMAGETYPE_JPX => "jpx",
		IMAGETYPE_JB2 => "jb2",
		IMAGETYPE_SWC => "swc",
		IMAGETYPE_IFF => "iff",
		IMAGETYPE_WBMP => "wbmp",
		IMAGETYPE_XBM => "xbm",
		IMAGETYPE_ICO => "ico"
	);
    
    public function getConfig()
    {
        $scan = array_diff(scandir($this->configDir), array('..', '.'));
        
        foreach ($scan as $file) {
            $string = file_get_contents($this->configDir.DIRECTORY_SEPARATOR.$file);
            
            $table = basename($file, ".json");

            $this->config[$table] = json_decode($string, true);
        }
    }
    
    public function getMenu()
    {
        if (empty($this->config)) {
            throw new \ErrorException("No config found");
        }
        
        foreach ($this->config as $table => $config) {          
            $menu[$table] = array(
                //maybe some extra data here (user permissions, etc.)
                'name' => $config['name']
            );            
        }

        return $menu;
    }
    
    public function getContent($table, $action, $rowId)
    {
        $content = $this->service('template')
                ->set('router', $this->service('router'))
                ->set('table', $table);
        
        $this->setData($table, $action, $rowId);
        
        $data = $this->getData($table, $action, $rowId);
        
        if (!empty($data)) {
            $content
                ->file('Admin/Table')
                ->set('data', $data);

            if (!empty($action)) {
                $content
                    ->file('Admin/Row')
                    ->set('action', $action)
                    ->set('row', !empty($data['rows'][$rowId]) ? $data['rows'][$rowId] : null);
            }
        }
            
        return $content;    
    }
    
    public function setData($table, $action, $rowId)
    {
        if ($action === 'add' || $action === 'edit') {
            $this->createUpdateRow($table, $action);
        } elseif ($action === 'remove') {
            $this->deleteRow($table, $row_id);
        }
    }
    
    public function createUpdateRow($table, $action)
	{
        if (empty($_POST[$action])) {
            return;
        }
        
//        echo "<pre>"; print_r($_POST); exit;

//        $this->manageFiles();
        
		$db = $this->service('database');
        
        foreach ($this->config[$table]['columns'] as $columnName => $column) {
            $values[':'.$columnName.'Insert'] = $_POST[$action][$columnName];
            $values[':'.$columnName.'Update'] = $_POST[$action][$columnName];

            $columns[] = $db->prepareIdentifier($columnName);

            $valuesInsert[] = ':'.$columnName.'Insert';
            $valuesUpdate[] = $columnName.'=:'.$columnName.'Update';
        }
            
        $tableQuery = $db->prepareIdentifier($table);
        $columnsQuery = implode(',', $columns);
        $valuesInsertQuery = implode(',', $valuesInsert);
        $valuesUpdateQuery = implode(',', $valuesUpdate);

        $query = '
            INSERT
                INTO '.$tableQuery.' ('.$columnsQuery.')
            VALUES	('.$valuesInsertQuery.')
            ON DUPLICATE KEY UPDATE
                '.$valuesUpdateQuery.'
            '
            ;
        
        $statement = $db->prepare($query);
		$statement->execute($values);
        
        return $db->lastInsertId();
	}
    
    public function sortData($table)
	{
		if (!empty($_POST['sequence']))
		{
			foreach ($_POST['sequence'] as $sequence => $row_id)
			{
				$this->update_row($table, array('sequence' => array()), $row_id, array('sequence' => $sequence));
			}
		}
	}
    
    public function manageFiles()
    {
        if (!empty($_FILES))
			{
				if (array_key_exists('type', $column_info) && $column_info['type'] == 'file' && !empty($_FILES['add']['tmp_name'][$column_name_clean]))
				{
					$image_type = exif_imagetype($_FILES['add']['tmp_name'][$column_name_clean]);

					if (!empty($image_type))
					{
						//manual setting post variable
						$image_name = substr($_FILES['add']['name'][$column_name_clean], 0, strlen($_FILES['add']['name'][$column_name_clean]) - strlen(strchr($_FILES['add']['name'][$column_name_clean], '.')));
							
						$image_name = $this->create_name_clean($image_name);

						$image_extension = $this->image_extensions[$image_type];

						$post[$column_name_clean] = $image_name.'.'.$image_extension;

						move_uploaded_file($_FILES['add']['tmp_name'][$column_name_clean], dirname(APP_PATH).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$table.DIRECTORY_SEPARATOR.$image_name.'.'.$image_extension);
					}
				}
			}
    }
    
    public function deleteRow($table, $row_id)
	{
		$db = $this->service('database');
		
		$query = '
				DELETE FROM
					'.$db->prepareIdentifier($table).'
				WHERE
					id = :row_id
				'
				;
		
		$statement = $db->prepare($query);
		$statement->bindValue(':row_id', $row_id);
		return $statement->execute();
	}

    public function getData($table)
    {        
        $content = $this->config[$table];
        
        $db = $this->service('database');
        
        //regular query
        $columnsQuery = '';
        $tableQuery = '';
        $orderQuery = '';
 
        //loop the columns, the structure for main columns and join columns
        //are equal intentionally to maintain readability
        foreach ($this->config[$table]['columns'] as $columnName => $column) {
            $columnQueryName = $db->prepareIdentifier($columnName);
            
            $columnsArray[] = $columnQueryName;
            
            if (!empty($column['order'])) {
                $orderArray[$column['order']] = $columnQueryName;
            }
                
            if (!empty($column['join'])) {
                foreach ($column['join'] as $joinTable => $join) {

                    //special join - override db if values already given
                    if (!empty($join['values'])) {
                        foreach ($join['values'] as $joinValueId => $joinValueValue) {
                            $content['rowsJoin'][$columnName][$joinValueId] = $joinValueValue;
                        }

                        continue;
                    }
                    
                    //regular join query
                    $joinColumnsQuery = '';
                    $joinTableQuery = '';
                    $joinOrderQuery = '';
                    
                    foreach ($join['columns'] as $joinColumnName => $joinColumn) {
                        $joinColumnQueryName = $db->prepareIdentifier($joinColumnName);
                        
                        $joinColumnsArray[] = $joinColumnQueryName;
                        
                        if (!empty($joinColumn['order'])) {
                            $joinOrderArray[$column['order']] = $joinColumnQueryName;
                        }
                    }
                     
                    if (!empty($joinColumnsArray)) {
                        $joinColumnsQuery = 'SELECT '.implode(',', $joinColumnsArray);
                    }

                    $joinTableQuery = ' FROM '.$db->prepareIdentifier($joinTable);
                    
                    if (!empty($joinOrderArray)) {
                        ksort($joinOrderArray);
                        $joinOrderQuery = ' ORDER BY '.implode(',', $joinOrderArray);
                    }

                    $query = $joinColumnsQuery.$joinTableQuery;
                    
                    $statement = $db->prepare($query);

                    if ($statement->execute()) {
                        while ($row = $statement->fetch()) {
                            //special join logic
                            $rowId = $row['id'];
                            unset($row['id']);

                            $content['rowsJoin'][$columnName][$rowId] = implode(', ', $row);
                        }
                    }
                }
            }
            
            if (!empty($column['type']) && $column['type'] == 'file') {
                $content['plugins']['image'] = $this->managePlugins('image');
            }
        }
 
        if (!empty($columnsArray)) {
            $columnsQuery = 'SELECT '.implode(',', $columnsArray);
        }
        
        $tableQuery = ' FROM '.$db->prepareIdentifier($table);

        if (!empty($orderArray)) {
            ksort($orderArray);
            $orderQuery = ' ORDER BY '.implode(',', $orderArray);
        }

        $query = $columnsQuery.$tableQuery.$orderQuery;
//      echo $query;exit;
        $statement = $db->prepare($query);
        
        if ($statement->execute()) {
			while ($row = $statement->fetch()) {
                $content['rows'][$row['id']] = $row;
            }
        }
        
        return $content;
    }
    
    public function managePlugins($plugin)
	{
        switch ($plugin) {
            case 'image' :
                return $this->pluginImage();
                break;
        }
    }
    
    public function pluginImage()
    {
        $structure = array();
        
        $publicPath = dirname(APP_PATH).DIRECTORY_SEPARATOR.'public';
        
        $path = $publicPath.DIRECTORY_SEPARATOR.'images';
       
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            $pathname = $file->getPathname();
            $dir = basename($file->getPath());
            $structure[$dir][] = str_replace(dirname(APP_PATH).DIRECTORY_SEPARATOR.'public', '', $pathname);
        }
        
        $content = $this->service('template')
            ->file('Admin/Plugins/Image')
            ->set('structure', $structure)
            ->render();
        
        return $content;        
    }
}