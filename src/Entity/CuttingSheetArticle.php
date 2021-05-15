<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="Roloffice\Repository\CuttingSheetArticleRepository")
 * @ORM\Table(name="v6_cutting_sheets_article")
 */
class CuttingSheetArticle {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny CuttingSheetArticle belong to one CuttingSheet
   * @ORM\ManyToOne(targetEntity="CuttingSheet")
   * @ORM\JoinColumn(name="cutting_sheet_id", referencedColumnName="id")
   * @var int
   */
  protected $cutting_sheet;

  /**
   * Meny CuttingSheetArticle has one FenceModel
   * @ORM\ManyToOne(targetEntity="FenceModel")
   * @ORM\JoinColumn(name="fence_model_id", referencedColumnName="id")
   * @var int
   */
  protected $fence_model;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $picket_width;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $width;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $height;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $mid_height;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $space;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
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