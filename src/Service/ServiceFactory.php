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
namespace Arhitector\Jumper\Service;

use Arhitector\Jumper\Traits\OptionsAwareTrait;

/**
 * Class ServiceFactory.
 *
 * @package Arhitector\Jumper\Service
 */
class ServiceFactory implements ServiceFactoryInterface
{
	use OptionsAwareTrait;
	
	/**
	 * ServiceFactory constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->setOptions($options);
	}
	
	/**
	 * Get the decoder instance.
	 *
	 * @param array $options
	 *
	 * @return DecoderInterface
	 */
	public function getDecoderService(array $options = [])
	{
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new Decoder($options ?: $this->getOptions());
	}
	
	/**
	 * Get the encoder instance.
	 *
	 * @param array $options
	 *
	 * @return EncoderInterface
	 */
	public function getEncoderService(array $options = [])
	{
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new Encoder($options ?: $this->getOptions());
	}
}
