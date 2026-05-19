<?php
require_once __DIR__ . '/../dao/ClienteDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
class ClienteController
{
    private ClienteDAO $dao;

    public function __construct()
    {
        $this->dao = new ClienteDAO();
    }

    // Retorna todos os clientes
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca um cliente pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Cliente
    {
        return $this->dao->buscarPorId($id);
    }

    // Recebe os dados do formulário e salva o cliente
    public function salvar(array $data): void
    {
        $cliente = new Cliente();
        if (!empty($data['id'])) $cliente->setId((int) $data['id']); // se tem ID é edição
        $cliente->setNome(trim($data['nome'] ?? ''));
        $cliente->setCpf(trim($data['cpf'] ?? ''));
        $cliente->setTelefone(trim($data['telefone'] ?? ''));
        $cliente->setEmail(trim($data['email'] ?? ''));
        $cliente->setInteresse($data['interesse'] ?? 'compra');
        $this->dao->salvar($cliente);
    }

    // Remove o cliente pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
