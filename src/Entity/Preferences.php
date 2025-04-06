<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Preferences entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__preferences')]
class Preferences {

    /**
     * Identifier of the Preferences.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Kurs of the currency.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 4)]
    protected $kurs;

    /**
     * Tax.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 4)]
    protected $tax;

    /**
     * Local backup folder.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 128)]
    protected $local_backup_folder;

    public function getId() {
        return $this->id;
    }

    public function setKurs($kurs) {
        $this->kurs = $kurs;
    }

    public function getKurs() {
        return $this->kurs;
    }

    public function setTax($tax) {
        $this->tax = $tax;
    }

    public function getTax() {
        return $this->tax;
    }

    public function setLocalBackupFolder($local_backup_folder) {
        $this->local_backup_folder = $local_backup_folder;
    }

    public function getLocalBackupFolder() {
        return $this->local_backup_folder;
    }

}
