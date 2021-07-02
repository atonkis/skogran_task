<?php

namespace App\Entity;

use App\Repository\DataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DataRepository::class)
 */
class Data
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=18, name="transaction_id")
     * @Groups({"default"})
     */
    private $transactionId;

    /**
     * @ORM\Column(type="integer", name="tool_number")
     * @Groups({"default"})
     */
    private $toolNumber;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=7)
     * @Groups({"default"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=7)
     * @Groups({"default"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"default"})
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, name="bat_percentage")
     * @Groups({"default"})
     */
    private $batPercentage;

    /**
     * @ORM\Column(type="datetime", name="import_date")
     * @Groups({"default"})
     */
    private $importDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getToolNumber(): ?string
    {
        return $this->toolNumber;
    }

    public function setToolNumber(string $toolNumber): self
    {
        $this->toolNumber = $toolNumber;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }


    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBatPercentage(): ?string
    {
        return $this->batPercentage;
    }

    public function setBatPercentage(string $batPercentage): self
    {
        $this->batPercentage = $batPercentage;

        return $this;
    }

    public function getImportDate(): ?\DateTimeInterface
    {
        return $this->importDate;
    }

    public function setImportDate(\DateTimeInterface $importDate): self
    {
        $this->importDate = $importDate;

        return $this;
    }
}