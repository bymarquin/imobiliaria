<?php

class Visita
{
    private ?int $id = null;

    private int $id_imovel = 0;

    private string $nome = '';

    private string $email = '';

    private string $celular = '';

    private string $dia_semana = '';

    private string $periodo = '';

    private string $horario_preferencia = '';

    private string $imovel_titulo = '';

    public function getId(): ?int              { return $this->id; }
    public function getIdImovel(): int         { return $this->id_imovel; }
    public function getNome(): string          { return $this->nome; }
    public function getEmail(): string         { return $this->email; }
    public function getCelular(): string       { return $this->celular; }
    public function getDiaSemana(): string     { return $this->dia_semana; }
    public function getPeriodo(): string       { return $this->periodo; }
    public function getHorarioPreferencia(): string { return $this->horario_preferencia; }
    public function getImovelTitulo(): string  { return $this->imovel_titulo; }

    public function setId(?int $id): void              { $this->id = $id; }
    public function setIdImovel(int $id): void         { $this->id_imovel = $id; }
    public function setNome(string $nome): void        { $this->nome = $nome; }
    public function setEmail(string $email): void      { $this->email = $email; }
    public function setCelular(string $celular): void  { $this->celular = $celular; }
    public function setDiaSemana(string $dia): void    { $this->dia_semana = $dia; }
    public function setPeriodo(string $periodo): void  { $this->periodo = $periodo; }
    public function setHorarioPreferencia(string $h): void { $this->horario_preferencia = $h; }
    public function setImovelTitulo(string $t): void   { $this->imovel_titulo = $t; }
}
