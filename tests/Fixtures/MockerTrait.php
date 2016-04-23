<?php

namespace Youthweb\BBCodeParser\Tests\Fixtures;

/**
 * Helper Trait
 */
trait MockerTrait
{
	/**
	 * Builds a ElementNode Mock
	 */
	public function buildElementNodeMock($content, $attribute)
	{
		$child = $this->getMockBuilder('JBBCode\TextNode')
			->setConstructorArgs([$content])
			->setMethods(null)
			->getMock();

		$elementNode = $this->getMockBuilder('JBBCode\ElementNode')
			->setMethods(['getAttribute', 'getChildren'])
			->getMock();

		$elementNode->method('getAttribute')
			->willReturn($attribute);

		$elementNode->method('getChildren')
			->willReturn([$child]);

		return $elementNode;
	}
}
