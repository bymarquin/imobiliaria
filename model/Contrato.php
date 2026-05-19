<?php

// Representa um contrato fechado entre cliente, imóvel e corretor.
// data_fim pode ser nula: contrato de venda não tem prazo de término.
// Os campos imovel_titulo, cliente_nome e corretor_nome vêm do JOIN — só pra exibição.
class Contrato
{
    private ?int $id = null;
    private int $id_imovel = 0;
    private int $id_cliente = 0;
    private int $id_corretor = 0;
    private string $tipo = '';        // venda ou aluguel
    private float $valor = 0.0;
    private string $data_inicio = '';
    private ?string $data_fim = null; // opcional — pode ser null

    // Campos auxiliares preenchidos pelo DAO via JOIN (só pra exibição)
    private string $imovel_titulo = '';
    private string $cliente_nome = '';
    private string $corretor_nome = '';

    // Getters: retornam os dados do objeto
    public function getId(): ?int           { return $this->id; }
    public function getIdImovel(): int      { return $this->id_imovel; }
    public function getIdCliente(): int     { return $this->id_cliente; }
    public function getIdCorretor(): int    { return $this->id_corretor; }
    public function getTipo(): string       { return $this->tipo; }
    public function getValor(): float       { return $this->valor; }
    public function getDataInicio(): string { return $this->data_inicio; }
    public function getDataFim(): ?string   { return $this->data_fim; }
    public function getImovelTitulo(): string  { return $this->imovel_titulo; }
    public function getClienteNome(): string   { return $this->cliente_nome; }
    public function getCorretorNome(): string  { return $this->corretor_nome; }

    // Setters: gravam dados no objeto
    public function setId(?int $id): void              { $this->id = $id; }
    public function setIdImovel(int $id): void         { $this->id_imovel = $id; }
    public function setIdCliente(int $id): void        { $this->id_cliente = $id; }
    public function setIdCorretor(int $id): void       { $this->id_corretor = $id; }
    public function setTipo(string $tipo): void        { $this->tipo = $tipo; }
    public function setValor(float $valor): void       { $this->valor = $valor; }
    public function setDataInicio(string $data): void  { $this->data_inicio = $data; }
    public function setDataFim(?string $data): void    { $this->data_fim = $data; }
    public function setImovelTitulo(string $t): void   { $this->imovel_titulo = $t; }
    public function setClienteNome(string $nome): void { $this->cliente_nome = $nome; }
    public function setCorretorNome(string $nome): void { $this->corretor_nome = $nome; }
}
