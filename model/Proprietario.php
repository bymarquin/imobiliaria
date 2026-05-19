<?php

// Representa o dono de um imóvel cadastrado no sistema.
class Proprietario
{
    private ?int $id = null;
    private string $nome = '';
    private string $cpf = '';
    private string $telefone = '';
    private string $email = '';

    // Getters: retornam os dados do objeto
    public function getId(): ?int         { return $this->id; }
    public function getNome(): string     { return $this->nome; }
    public function getCpf(): string      { return $this->cpf; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string    { return $this->email; }

    // Setters: gravam dados no objeto
    public function setId(?int $id): void         { $this->id = $id; }
    public function setNome(string $nome): void   { $this->nome = $nome; }
    public function setCpf(string $cpf): void     { $this->cpf = $cpf; }
    public function setTelefone(string $t): void  { $this->telefone = $t; }
    public function setEmail(string $email): void { $this->email = $email; }
}
