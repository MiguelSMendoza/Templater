<?php
	
class TemplaterTest extends \PHPUnit_Framework_TestCase
{
	public function testGetMapping() {
		$templater = new Templater();
		$html = $templater->getTemplate("envios",array("CONTENIDO"=>"Hello Test"));
		echo $html;
		$this->assertNotNull($html);

	}
}