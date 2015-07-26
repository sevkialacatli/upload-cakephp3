<?php
/**
 * UploadHelper
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Jean F Santos
 * @link          https://github.com/jeanfsantos/upload
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Upload\View\Helper;

use Cake\View\Helper;
use Cake\Routing\Router;

class UploadHelper extends Helper
{
    public $helpers = ['Form', 'Html'];

	public function input($options = []) {
		return $this->Form->input('uploads', [
			'class' => 'form-control',
			'type' => 'file',
			'name' => 'uploads[]',
			'multiple'
		] + $options);
	}

	public function render($image, $options = []) {
		return $this->Html->image(
			Router::url(sprintf('/%s', $image['path']), true),
			$options
		);
	}
}
