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
namespace Arhitector\Transcoder\Tests\Format;

use Arhitector\Transcoder\Codec;
use Arhitector\Transcoder\Format\AudioFormat;

/**
 * Class AudioFormatTest.
 *
 * @package Arhitector\Transcoder\Tests\Format
 */
class AudioFormatTest extends \PHPUnit_Framework_TestCase
{
	
	public function testConstructor()
	{
		new AudioFormat();
		new AudioFormat('codec string');
		new AudioFormat(new Codec('codec string'));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFailureConstructor()
	{
		new AudioFormat([]);
		new AudioFormat(new \stdClass);
	}
	
	public function testGetters()
	{
		$format = new AudioFormat('codec string');

		$this->assertInstanceOf(Codec::class, $format->getAudioCodec());
	}
	
}
