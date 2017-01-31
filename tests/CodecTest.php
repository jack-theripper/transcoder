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
namespace Arhitector\Jumper\Tests;

use Arhitector\Jumper\Codec;

/**
 * Class CodecTest.
 *
 * @package Arhitector\Jumper\Tests
 */
class CodecTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructor()
	{
		$this->assertEquals('codec string', (new Codec('codec string'))->getCode());
		$this->assertEquals('codec name', (new Codec('codec string', 'codec name'))->getName());
		
		new Codec([]);
	}
	
	/**
	 * Getters testing.
	 */
	public function testGetter()
	{
		$codec = new Codec('codec', 'codec name');
		
		$this->assertEquals('codec', $codec->getCode());
		$this->assertEquals('codec name', $codec->getName());
		$this->assertEquals('codec', (string) $codec);
	}
	
}
