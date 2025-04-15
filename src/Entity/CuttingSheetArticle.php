<?php

namespace App\Entity;

use App\Entity\CuttingSheet;
use App\Entity\FenceModel;
use App\Repository\CuttingSheetArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CuttongSheetArticle Entity.
 */
#[ORM\Entity(repositoryClass: CuttingSheetArticleRepository::class)]
#[ORM\Table(name: 'v6__cutting_sheets__article')]
class CuttingSheetArticle
{

    /**
     * Identifier of the CuttingSheetArticle.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many CuttingSheetArticle belong to one CuttingSheet
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: CuttingSheet::class)]
    #[ORM\JoinColumn(name: 'cutting_sheet_id', referencedColumnName: 'id')]
    protected $cutting_sheet;

    /**
     * Many CuttingSheetArticle has one FenceModel
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: FenceModel::class)]
    #[ORM\JoinColumn(name: 'fence_model_id', referencedColumnName: 'id')]
    protected $fence_model;

    /**
     * CuttingSheetArticle PicketWidth.
     *
     * @var float
     */
    #[ORM\Column(name: 'picket_width', type: 'decimal', precision: 11, scale: 0)]
    protected $picket_width;

    /**
     * CuttingSheetArticle Width.
     *
     * @var float
     */
    #[ORM\Column(name: 'width', type: 'decimal', precision: 11, scale: 0)]
    protected $width;

    /**
     * CuttingSheetArticle Height.
     *
     * @var float
     */
    #[ORM\Column(name: 'height', type: 'decimal', precision: 11, scale: 0)]
    protected $height;

    /**
     * CuttingSheetArticle MidHeight.
     *
     * @var float
     */
    #[ORM\Column(name: 'mid_height', type: 'decimal', precision: 11, scale: 0)]
    protected $mid_height;

    /**
     * CuttingSheetArticle Space.
     *
     * @var float
     */
    #[ORM\Column(name: 'space', type: 'decimal', precision: 11, scale: 0)]
    protected $space;

    /**
     * CuttingSheetArticle NumberOfFields.
     *
     * @var int
     */
    #[ORM\Column(name: 'number_of_fields', type: 'integer')]
    protected $number_of_fields;

    public function getId() {
        return $this->id;
    }

    public function setCuttingSheet($cutting_sheet) {
        $this->cutting_sheet = $cutting_sheet;
    }

    public function getCuttingSheet() {
        return $this->cutting_sheet;
    }

    public function setFenceModel($fence_model) {
        $this->fence_model = $fence_model;
    }

    public function getFenceModel() {
        return $this->fence_model;
    }

    public function setPicketWidth($picket_width) {
        $this->picket_width = $picket_width;
    }

    public function getPicketWidth() {
        return $this->picket_width;
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setMidHeight($mid_height) {
        $this->mid_height = $mid_height;
    }

    public function getMidHeight() {
        return $this->mid_height;
    }

    public function setSpace($space) {
        $this->space = $space;
    }

    public function getSpace() {
        return $this->space;
    }

    public function setNumberOfFields($number_of_fields) {
        $this->number_of_fields = $number_of_fields;
    }

    public function getNumberOfFields() {
        return $this->number_of_fields;
    }

}