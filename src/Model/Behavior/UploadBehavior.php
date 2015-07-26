<?php
/**
 * UploadBehavior
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Jean F Santos
 * @link          https://github.com/jeanfsantos/upload
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Upload\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class UploadBehavior extends Behavior
{
	protected $_uploadsTable;

	public $_defaultConfig = [
		'dir' => 'public',
		'table' => 'uploads'
	];

	public function initialize(array $config)
	{
		$this->_uploadsTable = TableRegistry::get($this->config('table'));

		$this->_setupAssociations($this->_config['table']);
	}

	protected function _setupAssociations($table)
	{
		$this->_table->addAssociations([
			'hasMany' => [
				'Uploads' => [
					'className' => 'Uploads',
					'foreignKey' => 'foreign_key',
					'dependent' => true,
					'conditions' => [
						'Uploads.model' => $this->_table->registryAlias()
					]
				]
			]
		]);
	}

	public function beforeSave(Event $event, Entity $entity, ArrayObject $options)
	{
		$uploadedImages = $entities = [];
		$alias = $this->_table->registryAlias();

		foreach ($entity->uploads as $image) {
			$uploadeImage = null;

			if (!empty($image['tmp_name'])) {
				$uploadeImage = $this->_upload($image['name'], $image['tmp_name']);
			}

			if (!empty($uploadeImage)) {
				$uploadedImages[] = $uploadeImage + [
					'model' => $this->_table->registryAlias()
				];
			}
		}

        if (!empty($uploadedImages)) {
            foreach ($uploadedImages as $image) {
                $entities[] = $this->_uploadsTable->newEntity($image);
            }
        }

		$entity->set('uploads', $entities);
	}

    protected function _upload($fileName, $filePath)
    {
        $data = [];

        $basePath = $this->basePath();
        $pathinfo = pathinfo($fileName);
        $fileName = $pathinfo['basename'];
        $fullPath = $basePath . DS . $fileName;
        $folder = new Folder($basePath, true, 0777);

        $cpt = 1;
        while (file_exists($fullPath)) {
	        $fileName = $pathinfo['filename'] . '_' . $cpt . '.' . $pathinfo['extension'];
	        $fullPath = $basePath . DS . $fileName;
	        $cpt++;
        }

        if (move_uploaded_file($filePath, $fullPath)) {
        	$file = new File($fullPath);
        	$data = [
        		'path' => $this->basePath(true) . DS . $file->name,
        		'extension' => $file->ext()
        	];
        }

        return $data;
    }

    protected function basePath($relative = false)
    {
    	return (!$relative ? WWW_ROOT : '') . $this->config('dir') . DS . $this->_table->alias();
    }
}
