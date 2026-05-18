<?php

/**
 * Representa um corretor de imóveis.
 *
 * O CRECI é o registro no Conselho Regional de Corretores. Sem ele,
 * o corretor não pode atuar legalmente — então faz sentido ter como campo.
 *
 * Fora isso, é um model direto: nome, contato e o número do registro.
 */
class Corretor
{
    private ?int $id = null;
    private string $nome = '';
    private string $creci = ''; // registro profissional obrigatório por lei
    private string $telefone = '';
    private string $email = '';

    // ─── Getters ─────────────────────────────────────────────────────────────

    public function getId(): ?int       { return $this->id; }
    public function getNome(): string   { return $this->nome; }
    public function getCreci(): string  { return $this->creci; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string  { return $this->email; }

    // ─── Setters ─────────────────────────────────────────────────────────────

    public function setId(?int $id): void           { $this->id = $id; }
    public function setNome(string $nome): void     { $this->nome = $nome; }
    public function setCreci(string $creci): void   { $this->creci = $creci; }
    public function setTelefone(string $telefone): void { $this->telefone = $telefone; }
    public function setEmail(string $email): void   { $this->email = $email; }
}
