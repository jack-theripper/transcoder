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

use Arhitector\Jumper\Codec;

/**
 * Class StreamTrait.
 *
 * @package Arhitector\Jumper\Stream
 */
trait StreamTrait
{
	
	/**
	 * @var string  The full path to the file.
	 */
	protected $filePath;
	
	/**
	 * @var Codec Codec instance.
	 */
	protected $codec;
	
	/**
	 * @var int The stream index value.
	 */
	protected $index = 0;
	
	/**
	 * @var string Profile value.
	 */
	protected $profile;
	
	/**
	 * @var int Bit rate.
	 */
	protected $bitrate = 0;
	
	/**
	 * @var float Start time.
	 */
	protected $startTime;
	
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
	 * Get Codec instance or NULL.
	 *
	 * @return Codec|null
	 */
	public function getCodec()
	{
		return $this->codec;
	}
	
	/**
	 * Set a new Codec instance.
	 *
	 * @param Codec $codec
	 *
	 * @return $this
	 */
	public function setCodec(Codec $codec)
	{
		$this->codec = $codec;
		
		return $this;
	}
	
	/**
	 * Get stream index value.
	 *
	 * @return int
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * Set a new order.
	 *
	 * @param int $position
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setIndex($position)
	{
		if ( ! is_int($position) || $position < 0)
		{
			throw new \InvalidArgumentException('Wrong index value.');
		}
		
		$this->index = $position;
		
		return $this;
	}
	
	/**
	 * Get profile value.
	 *
	 * @return string
	 */
	public function getProfile()
	{
		return $this->profile;
	}
	
	/**
	 * Get bit rate value.
	 *
	 * @return int
	 */
	public function getBitrate()
	{
		return $this->bitrate;
	}
	
	/**
	 * Get stream start time.
	 *
	 * @return float
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}
	
	/**
	 * Set profile value.
	 *
	 * @param string $profile
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setProfile($profile)
	{
		if ( ! is_string($profile) && $profile !== null)
		{
			throw new \InvalidArgumentException('Wrong profile value.');
		}
		
		$this->profile = $profile;
		
		return $this;
	}
	
	/**
	 * Set bit rate value.
	 *
	 * @param int $bitrate
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setBitrate($bitrate)
	{
		if ( ! is_numeric($bitrate) || $bitrate < 0)
		{
			throw new \InvalidArgumentException('Wrong bitrate value.');
		}
		
		$this->bitrate = (int) $bitrate;
		
		return $this;
	}
	
	/**
	 * Set start time value.
	 *
	 * @param float $startTime
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setStartTime($startTime)
	{
		if ( ! is_numeric($startTime))
		{
			throw new \InvalidArgumentException('Wrong start time value.');
		}
		
		$this->startTime = (float) $startTime;
		
		return $this;
	}
	
}