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
namespace Arhitector\Transcoder;

use Arhitector\Transcoder\Event\EventProgress;
use Arhitector\Transcoder\Exception\ExecutionFailureException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Filter\Graph;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Protocol\FileSystem;
use Arhitector\Transcoder\Protocol\ProtocolInterface;
use Arhitector\Transcoder\Service\ServiceFactory;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Stream\AudioStream;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\FrameStream;
use Arhitector\Transcoder\Stream\StreamInterface;
use Arhitector\Transcoder\Stream\SubtitleStream;
use Arhitector\Transcoder\Stream\VideoStream;
use Arhitector\Transcoder\Traits\FilePathAwareTrait;
use Mimey\MimeTypes;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class TranscoderTrait.
 *
 * @package Arhitector\Transcoder
 * @mixin TranscodeInterface
 */
trait TranscodeTrait
{
	use FilePathAwareTrait;
	
	/**
	 * @var FormatInterface|mixed
	 */
	protected $format;
	
	/**
	 * @var Collection
	 */
	protected $streams;
	
	/**
	 * @var ServiceFactoryInterface Service factory instance.
	 */
	protected $service;
	
	/**
	 * @var \Arhitector\Transcoder\Filter\Graph List of filters.
	 */
	protected $filters;
	
	/**
	 * @var ProtocolInterface
	 */
	protected $source;
	
	/**
	 * The constructor.
	 *
	 * @param ProtocolInterface|string $source
	 * @param ServiceFactoryInterface  $service
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($source, ServiceFactoryInterface $service = null)
	{
		$this->setSource($source instanceof ProtocolInterface ? $source : new FileSystem($source));
		$this->setServiceFactory($service ?: new ServiceFactory());
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$demuxing = $this->getService()->getDecoderService()->demuxing($this);
		
		if (count($demuxing->streams) < 1 || ( ! $this->isSupportedFileType() && empty($demuxing->format['format'])))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
		
		$this->filters = new Graph();
		$this->initialize($demuxing);
	}
	
	/**
	 * Returns the current source.
	 *
	 * @return ProtocolInterface
	 */
	public function getSource()
	{
		return $this->source;
	}
	
	/**
	 * Get current format.
	 *
	 * @return FormatInterface
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * Get a list of streams.
	 *
	 * @param int|callable $filter
	 *
	 * @return Collection|StreamInterface[]
	 */
	public function getStreams($filter = null)
	{
		if ($filter !== null)
		{
			if ( ! is_callable($filter))
			{
				$filter = function (StreamInterface $stream) use ($filter) {
					return (bool) ($filter & $stream->getType());
				};
			}
			
			$streams = clone $this->streams;
			
			foreach ($streams as $index => $stream)
			{
				if ($filter($stream) === false)
				{
					$streams->offsetUnset($index);
				}
			}
			
			return $streams;
		}
		
		return $this->streams;
	}
	
	/**
	 * Reset filters.
	 *
	 * @return $this
	 */
	public function withoutFilters()
	{
		$self = clone $this;
		$self->filters = new Graph();
		
		return $self;
	}
	
	/**
	 * Get the service instance.
	 *
	 * @return ServiceFactoryInterface
	 */
	public function getService()
	{
		return $this->service;
	}
	
	/**
	 * Add a new stream.
	 *
	 * @param StreamInterface $stream
	 *
	 * @return static
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	public function addStream(StreamInterface $stream)
	{
		$this->getStreams()->offsetSet(null, $stream);
		
		return $this;
	}
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return TranscodeInterface
	 * @throws \Arhitector\Transcoder\Exception\ExecutionFailureException
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 * @throws \Symfony\Component\Process\Exception\LogicException
	 * @throws \Symfony\Component\Process\Exception\ProcessFailedException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true)
	{
		if ( ! $this->isSupportedFormat($format))
		{
			throw new TranscoderException(sprintf('This format unsupported in the "%s" wrapper.', static::class));
		}
		
		if ( ! is_string($filePath) || empty($filePath))
		{
			throw new \InvalidArgumentException('File path must not be an empty string.');
		}
		
		if ( ! $overwrite && file_exists($filePath))
		{
			throw new TranscoderException('File path already exists.');
		}
		
		if ($format->emit('before', $this, $format, $filePath)->isPropagationStopped())
		{
			return $this;
		}
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$processes = $this->getService()->getEncoderService()->transcoding($this, $format, ['output' => $filePath]);
		
		try
		{
			foreach ($processes as $pass => $process)
			{
				if ( ! $process->isTerminated() && $process->run(new EventProgress($pass, $this->getFormat())) !== 0)
				{
					throw new ProcessFailedException($process);
				}
			}
			
			$format->emit('success', $this, $format, $filePath);
		}
		catch (ProcessFailedException $exc)
		{
			$format->emit('failure', $exc);
			
			throw new ExecutionFailureException($exc->getMessage(), $exc->getProcess(), $exc->getCode(), $exc);
		}
		finally
		{
			$format->emit('after', $this, $format, $filePath);
		}
		
		return $this;
	}
	
	/**
	 * Set the service instance.
	 *
	 * @param ServiceFactoryInterface $service
	 *
	 * @return TranscodeTrait
	 */
	protected function setServiceFactory(ServiceFactoryInterface $service)
	{
		$this->service = $service;
		
		return $this;
	}
	
	/**
	 * Gets the MIME Content-type value.
	 *
	 * @return string
	 */
	protected function getMimeType()
	{
		if ($this->getSource() instanceof FileSystem)
		{
			return mime_content_type($this->getSource()->getFilePath());
		}
		
		return '';
	}
	
	/**
	 *
	 * @param ProtocolInterface $source
	 *
	 * @return $this
	 */
	protected function setSource(ProtocolInterface $source)
	{
		$this->source = $source;
		
		return $this;
	}
	
	/**
	 * Find a format class.
	 *
	 * @param string $possibleFormat
	 * @param mixed  $default
	 *
	 * @return string|mixed
	 */
	protected function findFormatClass($possibleFormat = null, $default = null)
	{
		static $mimeTypes = null;
		
		if ($possibleFormat !== null)
		{
			$className = __NAMESPACE__.'\\Format\\'.ucfirst($possibleFormat);
			
			if (class_exists($className))
			{
				return $className;
			}
		}
		
		if ( ! $mimeTypes)
		{
			$mimeTypes = new MimeTypes();
		}
		
		$extensions = $mimeTypes->getAllExtensions($this->getMimeType());
		$extensions[] = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
		
		foreach ($extensions as $extension)
		{
			$classString = __NAMESPACE__.'\\Format\\'.ucfirst($extension);
			
			if (class_exists($classString))
			{
				return $classString;
			}
		}
		
		return $default;
	}
	
	/**
	 * Set the format instance.
	 *
	 * @param FormatInterface $format
	 *
	 * @return static
	 */
	protected function setFormat(FormatInterface $format)
	{
		$this->format = $format;
		
		return $this;
	}
	
	/**
	 * Set the stream collection instance.
	 *
	 * @param Collection $streams
	 *
	 * @return static
	 */
	protected function setStreams(Collection $streams)
	{
		$this->streams = $streams;
		
		return $this;
	}
	
	/**
	 * Returns the stream instances.
	 *
	 * @param array $rawStreams
	 *
	 * @return StreamInterface[]
	 * @throws \InvalidArgumentException
	 * @throws TranscoderException
	 */
	protected function ensureStreams(array $rawStreams)
	{
		$streams = [];
		
		foreach ($rawStreams as $stream)
		{
			$stream['type'] = isset($stream['type']) ? strtolower($stream['type']) : null;
			
			if ($stream['type'] == 'audio')
			{
				$streams[] = AudioStream::create($this, $stream);
			}
			else if ($stream['type'] == 'video')
			{
				if ($this instanceof AudioInterface && $this instanceof VideoInterface)
				{
					$streams[] = VideoStream::create($this, $stream);
				}
				else
				{
					$streams[] = FrameStream::create($this, $stream);
				}
			}
			else if ($stream['type'] == 'subtitle')
			{
				$streams[] = SubtitleStream::create($this, $stream);
			}
			else
			{
				throw new TranscoderException('This stream unsupported.');
			}
		}
		
		return $streams;
	}
	
	/**
	 * Initializing.
	 *
	 * @param \StdClass $demuxing
	 *
	 * @return void
	 */
	protected function initialize(\StdClass $demuxing)
	{
	
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	abstract protected function isSupportedFileType();
	
	/**
	 * Checks is supported the encoding in format.
	 *
	 * @param FormatInterface $format
	 *
	 * @return bool
	 */
	abstract protected function isSupportedFormat(FormatInterface $format);
	
}
