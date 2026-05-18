<?php

/**
 * Representa um cliente da imobiliária — a pessoa que quer comprar ou alugar.
 *
 * No MVC, o Model é basicamente um molde. Ele define quais informações existem
 * e como acessá-las. Nada de lógica de banco aqui; isso fica no DAO.
 *
 * Os atributos são todos privados — é encapsulamento. Pra mudar o nome de um
 * cliente, você não acessa $cliente->nome direto; usa $cliente->setNome().
 * Parece redundante, mas evita que uma parte do código mexa nos dados de
 * outra de forma inesperada.
 */
class Cliente
{
    private ?int $id = null;        // null enquanto o cliente não tiver sido salvo no banco
    private string $nome = '';
    private string $cpf = '';
    private string $telefone = '';
    private string $email = '';
    private string $interesse = ''; // "compra" ou "aluguel" — o que o cliente tá buscando

    // ─── Getters: pra ler os dados do objeto ────────────────────────────────

    public function getId(): ?int       { return $this->id; }
    public function getNome(): string   { return $this->nome; }
    public function getCpf(): string    { return $this->cpf; }
    public function getTelefone(): string { return $this->telefone; }
    public function getEmail(): string  { return $this->email; }
    public function getInteresse(): string { return $this->interesse; }

    // ─── Setters: pra gravar dados no objeto ────────────────────────────────

    public function setId(?int $id): void           { $this->id = $id; }
    public function setNome(string $nome): void     { $this->nome = $nome; }
    public function setCpf(string $cpf): void       { $this->cpf = $cpf; }
    public function setTelefone(string $telefone): void { $this->telefone = $telefone; }
    public function setEmail(string $email): void   { $this->email = $email; }
    public function setInteresse(string $interesse): void { $this->interesse = $interesse; }
}
