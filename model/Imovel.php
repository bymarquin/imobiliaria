<?php

/**
 * Representa um imóvel cadastrado na imobiliária.
 *
 * É a entidade central do sistema — tudo acaba girando em torno do imóvel.
 * Ele pertence a um proprietário (guardamos o id_proprietario) e pode
 * ser associado a contratos de venda ou aluguel.
 *
 * O campo nome_proprietario não vem da tabela de imóveis diretamente.
 * O DAO preenche ele via JOIN quando faz a listagem — é um atalho pra
 * mostrar o nome do dono na tela sem precisar de uma segunda consulta.
 */
class Imovel
{
    private ?int $id = null;
    private string $titulo = '';           // nome/descrição curta do imóvel
    private string $tipo = '';             // casa, apartamento, terreno, comercial
    private string $endereco = '';
    private float $valor = 0.0;            // valor de venda ou aluguel mensal
    private string $status = '';           // disponivel, alugado ou vendido
    private string $finalidade = '';       // venda ou aluguel
    private ?float $metros_quadrados = null;
    private ?string $planta_baixa = null;  // nome do arquivo salvo na pasta uploads/
    private int $id_proprietario = 0;
    private string $nome_proprietario = ''; // preenchido via JOIN no DAO

    // ─── Getters ─────────────────────────────────────────────────────────────

    public function getId(): ?int                  { return $this->id; }
    public function getTitulo(): string            { return $this->titulo; }
    public function getTipo(): string              { return $this->tipo; }
    public function getEndereco(): string          { return $this->endereco; }
    public function getValor(): float              { return $this->valor; }
    public function getStatus(): string            { return $this->status; }
    public function getFinalidade(): string        { return $this->finalidade; }
    public function getMetrosQuadrados(): ?float   { return $this->metros_quadrados; }
    public function getPlantaBaixa(): ?string      { return $this->planta_baixa; }
    public function getIdProprietario(): int       { return $this->id_proprietario; }
    public function getNomeProprietario(): string  { return $this->nome_proprietario; }

    // ─── Setters ─────────────────────────────────────────────────────────────

    public function setId(?int $id): void                    { $this->id = $id; }
    public function setTitulo(string $titulo): void          { $this->titulo = $titulo; }
    public function setTipo(string $tipo): void              { $this->tipo = $tipo; }
    public function setEndereco(string $endereco): void      { $this->endereco = $endereco; }
    public function setValor(float $valor): void             { $this->valor = $valor; }
    public function setStatus(string $status): void          { $this->status = $status; }
    public function setFinalidade(string $finalidade): void  { $this->finalidade = $finalidade; }
    public function setMetrosQuadrados(?float $m): void      { $this->metros_quadrados = $m; }
    public function setPlantaBaixa(?string $arquivo): void   { $this->planta_baixa = $arquivo; }
    public function setIdProprietario(int $id): void         { $this->id_proprietario = $id; }
    public function setNomeProprietario(string $nome): void  { $this->nome_proprietario = $nome; }
}
