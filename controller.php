<?php

namespace Movicon;

require('security.php');

class Controller
{
	protected $sec;

	public function __construct()
	{
		$this->sec = new Security();
	}
}

?>