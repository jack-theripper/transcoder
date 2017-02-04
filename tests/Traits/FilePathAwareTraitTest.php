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
namespace Arhitector\Jumper\Tests\Traits;

use Arhitector\Jumper\Traits\FilePathAwareTrait;

/**
 * Class FilePathAwareTraitTest.
 *
 * @package Arhitector\Jumper\Tests\Traits
 */
class FilePathAwareTraitTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @var FilePathAwareTrait
	 */
	protected $awareTrait;
	
	public function setUp()
	{
		$this->awareTrait = $this->getObjectForTrait(FilePathAwareTrait::class);
	}
	
	public function testSuccess()
	{
		$this->getReflection(__FILE__);
		$this->assertEquals($this->awareTrait->getFilePath(), __FILE__);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedException \Arhitector\Jumper\Exception\TranscoderException
	 */
	public function testFailure()
	{
		$this->getReflection(new \stdClass);
		$this->getReflection(__FILE__.mt_rand());
	}
	
	/**
	 * Get the ReflectionMethod instance.
	 *
	 * @param string $filePath
	 *
	 * @return \ReflectionMethod
	 */
	protected function getReflection($filePath)
	{
		$reflection = new \ReflectionMethod($this->awareTrait, 'setFilePath');
		$reflection->setAccessible(true);
		$reflection->invoke($this->awareTrait, $filePath);
		
		return $reflection;
	}
	
}
