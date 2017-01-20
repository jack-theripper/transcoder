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
namespace Arhitector\Jumper;

/**
 * Class Codec.
 *
 * @package Arhitector\Jumper
 */
class Codec
{
	
	/**
	 * @var string  codec code.
	 */
	protected $codec;
	
	/**
	 * @var string codec name.
	 */
	protected $name;
	
	/**
	 * Codec constructor.
	 *
	 * @param string $codec
	 * @param string $codecName
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($codec, $codecName = '')
	{
		$this->setCode($codec);
		$this->setName($codecName);
	}
	
	/**
	 * Get codec code.
	 *
	 * @return string
	 */
	public function getCode()
	{
		return (string) $this->codec;
	}
	
	/**
	 * Get Codec name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * The __toString method allows a class to decide how it will react when it is converted to a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getCode();
	}
	
	/**
	 * Set codec code value.
	 *
	 * @param string $codec
	 *
	 * @return Codec
	 * @throws \InvalidArgumentException
	 */
	protected function setCode($codec)
	{
		if (empty($codec) || ! is_string($codec))
		{
			throw new \InvalidArgumentException('The codec value must be a string type.');
		}
		
		$this->codec = $codec;
		
		return $this;
	}
	
	/**
	 * Set codec name value.
	 *
	 * @param string $name
	 *
	 * @return Codec
	 * @throws \InvalidArgumentException
	 */
	protected function setName($name)
	{
		if ( ! is_string($name))
		{
			throw new \InvalidArgumentException('The codec name value must be a string type.');
		}
		
		$this->name = $name;
		
		return $this;
	}
	
}
