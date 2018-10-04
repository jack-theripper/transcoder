<?php
/**
 * This file is part of the arhitector/transcoder library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Stream;

use Arhitector\Transcoder\Codec;
use Arhitector\Transcoder\Filter\SimpleFilter;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Protocol\ProtocolInterface;
use Arhitector\Transcoder\TimeInterval;
use Arhitector\Transcoder\Traits\FilePathAwareTrait;
use Arhitector\Transcoder\Traits\MetadataTrait;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Class StreamTrait.
 *
 * @package Arhitector\Transcoder\Stream
 */
trait StreamTrait
{
	use FilePathAwareTrait, MetadataTrait;
	
	/**
	 * Returns a new format instance.
	 *
	 * @param TranscodeInterface $media
	 * @param array              $options
	 *
	 * <code>
	 * array (size=8)
	 *  'channels' => int 2
	 *  'frequency' => int 44100
	 *  'codec' => object(Arhitector\Transcoder\Codec)[13]
	 *      protected 'codec' => string 'mp3' (length=3)
	 *      protected 'name' => string 'MP3 (MPEG audio layer 3)' (length=24)
	 *  'index' => int 1
	 *  'profile' => string 'base' (length=4)
	 *  'bitrate' => int 320000
	 *  'start_time' => float 0.025057
	 *  'duration' => float 208.53551
	 * </code>
	 *
	 * @return static
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public static function create(TranscodeInterface $media, array $options = [])
	{
		$self = new static($media);
		
		foreach ($options as $option => $value)
		{
			$parameter = str_replace('_', '', 'set'.ucwords($option, '_'));
			
			if (method_exists($self, $parameter))
			{
				$self->{$parameter}($value);
			}
		}
		
		return $self;
	}
	
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
	 * @var TimeInterval Duration value.
	 */
	protected $duration;
	
	/**
	 * @var TranscodeInterface
	 */
	protected $media;
	
	/**
	 * @var ProtocolInterface
	 */
	protected $source;
	
	/**
	 * Stream constructor.
	 *
	 * @param TranscodeInterface $media
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	private function __construct(TranscodeInterface $media)
	{
		$this->setSource($media->getSource());
		$this->media = $media;
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
	 * Get duration value.
	 *
	 * @return TimeInterval
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * @return ProtocolInterface
	 */
	public function getSource()
	{
		return $this->source;
	}
	
	/**
	 * @param ProtocolInterface $source
	 * @return StreamTrait
	 */
	public function setSource($source)
	{
		$this->source = $source;
		
		return $this;
	}
	
	/**
	 * Stream save.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return \Arhitector\Transcoder\TranscodeInterface
	 *
	 * @throws \Arhitector\Transcoder\Exception\InvalidFilterException
	 * @throws \Symfony\Component\Process\Exception\ProcessFailedException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true)
	{
		$media = $this->media->withoutFilters();
		$media->addFilter(new SimpleFilter([
			'map' => sprintf('0:%s', $this->getIndex())
		]));
		
		return $media->save($format, $filePath, $overwrite);
	}
	
	/**
	 * Get array of values.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$result = [];
		
		foreach (get_class_methods($this) as $method)
		{
			if (stripos($method, 'get') === 0)
			{
				$result[strtolower(substr($method, 3))] = $this->{$method}();
			}
		}
		
		return $result;
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
	
	/**
	 * Set duration value.
	 *
	 * @param TimeInterval|float $duration
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	protected function setDuration($duration)
	{
		if (is_numeric($duration) && $duration > 0)
		{
			$duration = new TimeInterval($duration);
		}
		
		if ( ! $duration instanceof TimeInterval)
		{
			throw new \InvalidArgumentException('Wrong the duration value.');
		}
		
		$this->duration = $duration;
		
		return $this;
	}
	
}
