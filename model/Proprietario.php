<?php

/**
 * Representa o dono de um imóvel cadastrado no sistema.
 *
 * O vínculo com o imóvel é feito pelo id_proprietario na tabela de imóveis —
 * aqui a gente só guarda os dados de contato: nome, CPF, telefone e email.
 */
class Proprietario
{
    private ?int $id = null; // null antes de ser salvo no banco
    private string $nome = '';
    private string $cpf = '';
    private string $telefone = '';
    private string $email = '';

    // ─── Getters ─────────────────────────────────────────────────────────────

    public function getId(): ?int       { return $this->id; }
    public function getNome(): string   { return $this->nome; }
    public function getCpf(): string    { return $this->cpf; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string  { return $this->email; }

    // ─── Setters ─────────────────────────────────────────────────────────────

    public function setId(?int $id): void           { $this->id = $id; }
    public function setNome(string $nome): void     { $this->nome = $nome; }
    public function setCpf(string $cpf): void       { $this->cpf = $cpf; }
    public function setTelefone(string $telefone): void { $this->telefone = $telefone; }
    public function setEmail(string $email): void   { $this->email = $email; }
}
