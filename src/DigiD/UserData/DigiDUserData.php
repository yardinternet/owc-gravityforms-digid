<?php

namespace Yard\DigiD\UserData;

use OWC\IdpUserData\DigiDUserDataInterface;

class DigiDUserData implements DigiDUserDataInterface{

	protected string $bsn;

	public function __construct(string $bsn)
	{
		$this->bsn = $bsn;
	}

    public function getBsn(): string {
		return $this->bsn;
	}
}
