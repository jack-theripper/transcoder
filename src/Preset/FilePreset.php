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
namespace Arhitector\Transcoder\Preset;

use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Traits\FilePathAwareTrait;

/**
 * Class FilePreset.
 *
 * @package Arhitector\Transcoder\Preset
 */
class FilePreset extends Preset
{
	use FilePathAwareTrait;
	
	/**
	 * @var string Delimiter character.
	 */
	protected $separator = '=';
	
	/**
	 * FilePreset constructor.
	 *
	 * @param string $filePath
	 * @param string $type
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($filePath, $type = 'plain')
	{
		$this->setFilePath($filePath);
		
		if ( ! ($handle = fopen($this->getFilePath(), 'rb')))
		{
			throw new TranscoderException('Unable to open preset file for reading.');
		}
		
		$container = [];
		
		while ( ! feof($handle))
		{
			$line = fgets($handle);
			
			if ( ! trim($line) || $line{0} == '#')
			{
				continue;
			}
			
			list($key, $value) = array_map('trim', explode($this->separator, $line, 2) + [1 => null]);
			
			if (array_key_exists($key, $container))
			{
				if ( ! is_array($container[$key]))
				{
					$container[$key] = [$container[$key]];
				}
				
				$container[$key][] = $value;
			}
			else
			{
				$container[$key] = $value;
			}
		}
		
		fclose($handle);
		parent::__construct($container);
	}
	
}
