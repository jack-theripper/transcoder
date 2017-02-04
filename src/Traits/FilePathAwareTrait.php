<?php
/**
 * This file is part of the arhitector/jumper library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Jumper\Traits;

use Arhitector\Jumper\Exception\TranscoderException;

/**
 * Class FilePathAware.
 *
 * @package Arhitector\Jumper\Traits
 */
trait FilePathAwareTrait
{
	
	/**
	 * @var string  The full path to the file.
	 */
	protected $filePath;
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	/**
	 * Set file path.
	 *
	 * @param string $filePath
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
	 */
	protected function setFilePath($filePath)
	{
		if ( ! is_string($filePath))
		{
			throw new \InvalidArgumentException('File path must be a string type.');
		}
		
		$filePath = realpath($filePath);
		
		if ( ! is_file($filePath))
		{
			throw new TranscoderException('File path not found.');
		}
		
		$this->filePath = $filePath;
		
		return $this;
	}
	
}
