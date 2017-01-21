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
namespace Arhitector\Jumper\Filter;

use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\TranscoderInterface;

/**
 * Class SimpleFilter.
 *
 * @package Arhitector\Jumper\Filter
 */
class SimpleFilter implements AudioFilterInterface
{
	
	/**
	 * @var array List of parameters.
	 */
	protected $parameters = [];
	
	/**
	 * SimpleFilter constructor.
	 *
	 * @param array $parameters
	 */
	public function __construct(array $parameters = [])
	{
		$this->setParameters($parameters);
	}
	
	/**
	 * Set new array with parameters.
	 *
	 * @param array $parameters
	 *
	 * @return SimpleFilter
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		
		return $this;
	}
	
	
	/**
	 * Apply filter.
	 *
	 * @param TranscoderInterface $media
	 * @param FormatInterface     $format
	 *
	 * @return array
	 */
	public function apply(TranscoderInterface $media, FormatInterface $format)
	{
		return $this->toArray();
	}
	
	/**
	 * Get parameters.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->parameters;
	}
	
}
