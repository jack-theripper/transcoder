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
namespace Arhitector\Jumper\Stream;

use Arhitector\Jumper\TranscoderInterface;

/**
 * Class FrameStream.
 *
 * @package Arhitector\Jumper\Stream
 */
class FrameStream implements FrameStreamInterface
{
	use StreamTrait;
	
	/**
	 * @var int Width value.
	 */
	protected $width;
	
	/**
	 * @var int Height value.
	 */
	protected $height;
	
	/**
	 * FrameStream constructor.
	 *
	 * @param TranscoderInterface $media
	 * @param array               $parameters
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(TranscoderInterface $media, array $parameters)
	{
		$this->filePath = $media->getFilePath();
		
		if ( ! isset($parameters['index']) || $parameters['index'] < 0)
		{
			throw new \InvalidArgumentException('The index value is wrong.');
		}
		
		$this->setIndex($parameters['index']);
		
		if (isset($parameters['codec']))
		{
			$this->setCodec($parameters['codec']);
		}
		
		if (isset($parameters['profile']))
		{
			$this->setProfile((string) $parameters['profile']);
		}
		
		if (isset($parameters['bit_rate']))
		{
			$this->setBitrate((int) $parameters['bit_rate']);
		}
		
		if (isset($parameters['start_time']))
		{
			$this->setStartTime((float) $parameters['start_time']);
		}
		
		if (isset($parameters['width']))
		{
			$this->width = (int) $parameters['width'];
		}
		
		if (isset($parameters['height']))
		{
			$this->height = (int) $parameters['height'];
		}
	}
	
	/**
	 * Get width value.
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * Get height value.
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}
	
}
