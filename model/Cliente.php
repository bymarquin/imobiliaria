<?php

class Cliente
{
    private ?int $id = null;
    private string $nome = '';
    private string $cpf = '';
    private string $telefone = '';
    private string $email = '';
    private string $interesse = '';

    public function getId(): ?int         { return $this->id; }
    public function getNome(): string     { return $this->nome; }
    public function getCpf(): string      { return $this->cpf; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string    { return $this->email; }
    public function getInteresse(): string { return $this->interesse; }

    public function setId(?int $id): void             { $this->id = $id; }
    public function setNome(string $nome): void       { $this->nome = $nome; }
    public function setCpf(string $cpf): void         { $this->cpf = $cpf; }
    public function setTelefone(string $t): void      { $this->telefone = $t; }
    public function setEmail(string $email): void     { $this->email = $email; }
    public function setInteresse(string $i): void     { $this->interesse = $i; }
}
