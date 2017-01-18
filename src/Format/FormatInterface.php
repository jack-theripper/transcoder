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
namespace Arhitector\Jumper\Format;

/**
 * Interface FormatInterface.
 *
 * @package Arhitector\Jumper\Format
 */
interface FormatInterface extends \Iterator
{
	
	/**
	 * Get the duration value.
	 *
	 * @return float
	 */
	public function getDuration();
	
	/**
	 * Gets the tags.
	 *
	 * @return array
	 */
	public function getTags();
	
	/**
	 * Sets the metadata.
	 *
	 * @param string $tag
	 * @param mixed  $value
	 *
	 * @return FormatInterface
	 */
	public function setTagValue($tag, $value);
	
	/**
	 * Returns the number of passes.
	 *
	 * @return int
	 */
	public function getPasses();
	
	/**
	 * Get the format extensions.
	 *
	 * @return array
	 */
	public function getExtensions();
	
}
