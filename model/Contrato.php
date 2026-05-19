<?php

class Contrato
{
    private ?int $id = null;
    private int $id_imovel = 0;
    private int $id_cliente = 0;
    private int $id_corretor = 0;
    private string $tipo = '';
    private float $valor = 0.0;
    private string $data_inicio = '';
    private ?string $data_fim = null;
    private string $status = 'ativo';

    private string $imovel_titulo = '';
    private ?string $imovel_planta_baixa = null;
    private string $cliente_nome = '';
    private string $corretor_nome = '';

    public function getId(): ?int           { return $this->id; }
    public function getIdImovel(): int      { return $this->id_imovel; }
    public function getIdCliente(): int     { return $this->id_cliente; }
    public function getIdCorretor(): int    { return $this->id_corretor; }
    public function getTipo(): string       { return $this->tipo; }
    public function getValor(): float       { return $this->valor; }
    public function getDataInicio(): string { return $this->data_inicio; }
    public function getDataFim(): ?string   { return $this->data_fim; }
    public function getStatus(): string     { return $this->status; }
    public function getImovelTitulo(): string  { return $this->imovel_titulo; }
    public function getImovelPlantaBaixa(): ?string { return $this->imovel_planta_baixa; }
    public function getClienteNome(): string   { return $this->cliente_nome; }
    public function getCorretorNome(): string  { return $this->corretor_nome; }

    public function setId(?int $id): void              { $this->id = $id; }
    public function setIdImovel(int $id): void         { $this->id_imovel = $id; }
    public function setIdCliente(int $id): void        { $this->id_cliente = $id; }
    public function setIdCorretor(int $id): void       { $this->id_corretor = $id; }
    public function setTipo(string $tipo): void        { $this->tipo = $tipo; }
    public function setValor(float $valor): void       { $this->valor = $valor; }
    public function setDataInicio(string $data): void  { $this->data_inicio = $data; }
    public function setDataFim(?string $data): void    { $this->data_fim = $data; }
    public function setStatus(string $status): void    { $this->status = $status; }
    public function setImovelTitulo(string $t): void   { $this->imovel_titulo = $t; }
    public function setImovelPlantaBaixa(?string $arquivo): void { $this->imovel_planta_baixa = $arquivo; }
    public function setClienteNome(string $nome): void { $this->cliente_nome = $nome; }
    public function setCorretorNome(string $nome): void { $this->corretor_nome = $nome; }
}
