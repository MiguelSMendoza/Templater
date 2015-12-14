<?php
if (!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
if (!defined('TEMPLATER_PATH')) define('TEMPLATER_PATH', dirname(preg_replace('/\\\\/','/',__FILE__)) . '/');
if (!defined('TEMPLATER_URL')) define('TEMPLATER_URL', $_SERVER['SERVER_NAME'].str_replace(array($_SERVER['DOCUMENT_ROOT'],basename(__FILE__)),'', __FILE__));

class Templater {

	private $Templates = array();
	private $templateToken = "##";
	
	private function getdate()
	{
		$dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
		$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		$fecha = date('d')." de ".$meses[date('n')-1]. " de ".date('Y');
		return $fecha;
	}

	private function toLog($mensaje)
	{
		$log = TEMPLATER_PATH.'logs'.DS.'errors.log';
		$msj = date('m/d/Y h:i:s a', time())." - ".$mensaje.PHP_EOL;
		echo $msj;
		file_put_contents($log, $msj, FILE_APPEND | LOCK_EX);
	}
	
	public function Templater()
	{
		$this->loadTemplates();
	}
	
	private function loadTemplates()
	{
		$this->MailTemplates = array();
		$templateDir = TEMPLATER_PATH.DS.'templates';
		$scanned_directory = array_diff(scandir($templateDir), array('..', '.'));
		foreach($scanned_directory as $file)
		{
			if(is_dir($templateDir.DS.$file))
				array_push($this->Templates, $file);				
		}
	}

	public function getTemplates()
	{
		return $this->Templates;
	}

	public function checkTemplate($temp)
	{
		return in_array($temp,$this->Templates);
	}

	public function getTemplateTokens($temp)
	{
		if(!$this->checkTemplate($temp))
		{
			$this->raiseError(__FUNCTION__. " Invalid Template");
			return false;
		}
		$php_file = file_get_contents(TEMPLATER_PATH.'templates'.DS.$temp.DS.$temp.'.html');
		$tokens = array_filter(explode($this->templateToken, $php_file), array($this, "isToken"));
		array_map("trim", $tokens);
		return array_unique($tokens);
	}
	
	public function getTemplate($template, $params)
	{
		$html = "";
		if(!$this->checkTemplate($template))
		{
			$this->raiseError(__FUNCTION__. " Invalid Template");
			return false;
		}
		$templateFile = TEMPLATER_PATH.'templates'.DS.$template.DS.$template.'.html';
		if (is_readable($templateFile))
			$cuerpo = file_get_contents($templateFile);
		else
		{
			return false;
			$this->raiseError(__FUNCTION__. " Invalid Template");
		}

		$html = str_replace($this->templateToken."FECHA".$this->templateToken, $this->getDate(), $cuerpo);
		$templatePath = "http://".TEMPLATER_URL.'templates'.DS.$template;
		$html = str_replace($this->templateToken."PATH".$this->templateToken, $templatePath, $html);
		foreach($params as $key => $value)
		{
			$html = str_replace($this->templateToken.$key.$this->templateToken, $value, $html);
		}
		return mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
	}
	
	private function isToken($token)
	{
		if (strlen($token)<=8 && !strpos($token, " ") ) return true;
		else return false;
	}
	
	private function raiseError($m)
	{
		$this->toLog("ERROR - ".$_SERVER['REMOTE_ADDR']." ".$m);
	}
}

?>