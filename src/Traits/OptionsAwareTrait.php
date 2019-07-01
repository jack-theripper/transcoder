<?php
/**
 * This file is part of the arhitector/transcoder-ffmpeg library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017-2019 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Traits;

/**
 * Class OptionsAwareTrait.
 *
 * @package Arhitector\Transcoder\Traits
 */
trait OptionsAwareTrait
{
	
	/**
	 * @var array The options.
	 */
	protected $options = [];
	
	/**
	 * Gets the options.
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}
	
	/**
	 * Sets the options value.
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
		
		return $this;
	}
	
}
