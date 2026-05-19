<?php

// Este arquivo define o "molde" de uma visita.
// Pense como uma ficha: ele guarda os dados, mas nao salva no banco sozinho.
// imovel_titulo nao vem da tabela visitas; ele so aparece quando fazemos JOIN para listar.
class Visita
{
    // ID da visita (chave primaria no banco).
    // Comeca null porque uma visita nova ainda nao foi salva.
    private ?int $id = null;

    // ID do imovel escolhido para a visita.
    private int $id_imovel = 0;

    // Nome da pessoa interessada.
    private string $nome = '';

    // E-mail de contato da pessoa interessada.
    private string $email = '';

    // Celular de contato da pessoa interessada.
    private string $celular = '';

    // Dia da semana escolhido: segunda, terca, quarta...
    private string $dia_semana = '';         // segunda, terca, quarta...

    // Periodo do dia: manha, tarde ou noite.
    private string $periodo = '';            // manha, tarde ou noite

    // Horario escolhido no formato HH:MM.
    private string $horario_preferencia = ''; // formato HH:MM

    // Titulo do imovel.
    // Este campo e so para mostrar na tela.
    // Ele e preenchido com JOIN na consulta de listagem.
    private string $imovel_titulo = '';

    // GETTERS: metodos para ler os valores.
    // Eles retornam o conteudo da ficha sem expor os atributos diretamente.
    public function getId(): ?int              { return $this->id; }
    public function getIdImovel(): int         { return $this->id_imovel; }
    public function getNome(): string          { return $this->nome; }
    public function getEmail(): string         { return $this->email; }
    public function getCelular(): string       { return $this->celular; }
    public function getDiaSemana(): string     { return $this->dia_semana; }
    public function getPeriodo(): string       { return $this->periodo; }
    public function getHorarioPreferencia(): string { return $this->horario_preferencia; }
    public function getImovelTitulo(): string  { return $this->imovel_titulo; }

    // SETTERS: metodos para escrever/atualizar valores na ficha.
    // O controller usa estes metodos quando recebe dados do formulario.
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
