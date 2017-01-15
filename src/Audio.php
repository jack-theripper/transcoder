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

use Arhitector\Jumper\Exception\TranscoderException;
use Arhitector\Jumper\Format\AudioFormatInterface;
use Arhitector\Jumper\Service\Decoder;
use Arhitector\Jumper\Stream\Collection;
use Arhitector\Jumper\Stream\StreamInterface;

/**
 * Class Audio.
 *
 * @package Arhitector\Jumper
 */
class Audio implements AudioInterface
{
	
	/**
	 * @var string  The full path to the file.
	 */
	protected $filePath;
	
	/**
	 * @var AudioFormatInterface
	 */
	protected $format;
	
	/**
	 * @var Collection
	 */
	protected $streams;
	
	/**
	 * @var Decoder
	 */
	protected $decoder;
	
	/**
	 * @var string The MIME Content-type for a file.
	 */
	protected $mimeType;
	
	/**
	 * Audio constructor.
	 *
	 * @param string $filePath
	 * @param array  $options
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
	 * @throws \Arhitector\Jumper\Exception\ExecutableNotFoundException
	 */
	public function __construct($filePath, array $options = [])
	{
		$this->setFilePath($filePath);
		$this->decoder = new Decoder($options);
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$demuxing = $this->decoder->demuxing($this);
		
		if (count($demuxing->streams) < 1 || ( ! $this->isSupportedFileType() && empty($demuxing->format['format'])))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
		
		
		
		/* class stdClass#4 (2) {
  public $format =>
  array(12) {
    'filename' =>
    string(68) "E:\OpenServer_525\OpenServer\domains\localhost\git\jumper\audio1.mp3"
    'nb_streams' =>
    int(1)
    'nb_programs' =>
    int(0)
    'format_name' =>
    string(3) "mp3"
    'format_long_name' =>
    string(28) "MP2/3 (MPEG audio layer 2/3)"
    'start_time' =>
    string(8) "0.000000"
    'duration' =>
    string(10) "194.063675"
    'size' =>
    string(7) "7762599"
    'bit_rate' =>
    string(6) "320002"
    'probe_score' =>
    int(51)
    'tags' =>
    array(3) {
      'TBPM' =>
      string(1) "0"
      'encoded_by' =>
      string(4) "LAME"
      'date' =>
      string(4) "2015"
    }
    'format' =>
    string(3) "mp3"
  }
  public $streams =>
  array(1) {
    [0] =>
    array(24) {
      'frequency' =>
      int(44100)
      'channels' =>
      int(2)
      'index' =>
      int(0)
      'type' =>
      string(5) "audio"
      'profile' =>
      string(0) ""
      'bit_rate' =>
      string(6) "320000"
      'start_time' =>
      string(8) "0.000000"
      'duration' =>
      string(10) "194.063675"
      'properties' =>
      array(0) {
      }
      'codec_name' =>
      string(3) "mp3"
      'codec_long_name' =>
      string(24) "MP3 (MPEG audio layer 3)"
      'codec_type' =>
      string(5) "audio"
      'codec_time_base' =>
      string(7) "1/44100"
      'codec_tag_string' =>
      string(12) "[0][0][0][0]"
      'codec_tag' =>
      string(6) "0x0000"
      'sample_fmt' =>
      string(4) "s16p"
      'channel_layout' =>
      string(6) "stereo"
      'bits_per_sample' =>
      int(0)
      'r_frame_rate' =>
      string(3) "0/0"
      'avg_frame_rate' =>
      string(3) "0/0"
      'time_base' =>
      string(10) "1/14112000"
      'start_pts' =>
      int(0)
      'duration_ts' =>
      double(2738626582)
      'codec' =>
      class Arhitector\Jumper\Codec#6 (2) {
        protected $codec =>
        string(3) "mp3"
        protected $name =>
        string(24) "MP3 (MPEG audio layer 3)"
      }
    }
  }
 */
		
		var_dump($demuxing);
		
	}
	
	/**
	 * Gets the audio channels value.
	 *
	 * @return int
	 */
	public function getAudioChannels()
	{
		return $this->getFormat()->getAudioChannels();
	}
	
	/**
	 * Gets the audio kilo bitrate value.
	 *
	 * @return int
	 */
	public function getAudioKiloBitrate()
	{
		return (int) ($this->getFormat()->getAudioBitrate() / 1000);
	}
	
	/**
	 * Returns the audio codec.
	 *
	 * @return Codec|null
	 */
	public function getAudioCodec()
	{
		return $this->getFormat()->getAudioCodec();
	}
	
	/**
	 * Get sample frequency value.
	 *
	 * @return int
	 */
	public function getFrequency()
	{
		return $this->getFormat()->getAudioFrequency();
	}
	
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
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		return $this->getFormat()->getDuration();
	}
	
	/**
	 * Get current format.
	 *
	 * @return AudioFormatInterface
	 * @throws TranscoderException
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * Get a list of streams.
	 *
	 * @return Collection|StreamInterface[]
	 * @throws TranscoderException
	 */
	public function getStreams()
	{
		return $this->streams;
	}
	
	/**
	 * Gets the MIME Content-type value.
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return (string) $this->mimeType;
	}
	
	/**
	 * Set file path.
	 *
	 * @param string $filePath
	 *
	 * @return Audio
	 * @throws \InvalidArgumentException
	 * @throws TranscoderException
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
		$this->mimeType = mime_content_type($this->getFilePath());
		
		return $this;
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		if (stripos($this->getMimeType(), 'audio/') !== 0)
		{
			return false;
		}
		
		return true;
	}
	
}