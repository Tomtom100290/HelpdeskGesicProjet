<?php

namespace App\DTO;

use App\Entity\Logiciel;
use Doctrine\Common\Collections\ArrayCollection;

class InstallationMultipleDTO
{
    public ?Logiciel $logiciel = null;
    public ?string $version = null;

    /** @var array */
    public array $clients = []; // Recevra le tableau des clients cochés
}
