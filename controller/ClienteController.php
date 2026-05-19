<?php
require_once __DIR__ . '/../dao/ClienteDAO.php';

class ClienteController
{
    private ClienteDAO $dao;

    public function __construct()
    {
        $this->dao = new ClienteDAO();
    }

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function buscarPorId(int $id): ?Cliente
    {
        return $this->dao->buscarPorId($id);
    }

    public function salvar(array $data): void
    {
        $cliente = new Cliente();
        if (!empty($data['id'])) $cliente->setId((int) $data['id']);
        $cliente->setNome(trim($data['nome'] ?? ''));
        $cliente->setCpf(trim($data['cpf'] ?? ''));
        $cliente->setTelefone(trim($data['telefone'] ?? ''));
        $cliente->setEmail(trim($data['email'] ?? ''));
        $cliente->setInteresse($data['interesse'] ?? 'compra');
        $this->dao->salvar($cliente);
    }

    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
