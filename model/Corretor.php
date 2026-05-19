<?php

class Corretor
{
    private ?int $id = null;
    private string $nome = '';
    private string $creci = '';
    private string $telefone = '';
    private string $email = '';

    public function getId(): ?int         { return $this->id; }
    public function getNome(): string     { return $this->nome; }
    public function getCreci(): string    { return $this->creci; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string    { return $this->email; }

    public function setId(?int $id): void          { $this->id = $id; }
    public function setNome(string $nome): void    { $this->nome = $nome; }
    public function setCreci(string $creci): void  { $this->creci = $creci; }
    public function setTelefone(string $t): void   { $this->telefone = $t; }
    public function setEmail(string $email): void  { $this->email = $email; }
}
